<?php
// auto_withdraw.php - Automated script to withdraw all wallet balances upon execution.

// --- CONFIGURATION ---

include('../includes/db_config.php');

// --- BUSINESS LOGIC CONFIGURATION ---
$min_withdrawal_amount = 1000.00; // Minimum balance required for automated withdrawal
// ------------------------------------

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Check if today is Saturday (Day 6)
$day_of_week = (int)date('N'); // 1 (Mon) through 7 (Sun)

if ($day_of_week !== 6) {
    echo "Automated withdrawal is scheduled for **Saturday** only. Today is " . date('l') . ". Exiting.\n";
    $conn->close();
    exit();
}

echo "Starting automated withdrawal process (It's Saturday!)...\n";

// 2. Fetch all users with a positive wallet balance AND minimum required amount
// The SQL query is updated to include the $min_withdrawal_amount
$users_query = "
    SELECT user_id, wallet_balance 
    FROM users 
    WHERE wallet_balance >= ? 
    FOR UPDATE
";

$stmt_fetch_users = $conn->prepare($users_query);
$stmt_fetch_users->bind_param("d", $min_withdrawal_amount);
$stmt_fetch_users->execute();
$users_result = $stmt_fetch_users->get_result();

if ($users_result->num_rows === 0) {
    echo "No users with a wallet balance of $min_withdrawal_amount or more found. Exiting.\n";
    $conn->close();
    exit();
}

$processed_count = 0;
$total_withdrawn = 0.00;

// Prepare statements outside the loop for efficiency and security
$stmt_withdrawal = $conn->prepare("
    INSERT INTO withdrawals (user_id, amount, status)
    VALUES (?, ?, 'PENDING')
");

$stmt_wallet_update = $conn->prepare("
    UPDATE users SET wallet_balance = 0.00 WHERE user_id = ?
");

// Pre-define the structure for the transaction log INSERT
$stmt_transaction_log = $conn->prepare("
    INSERT INTO wallet_transactions 
    (user_id, transaction_type, source_table, source_id, amount, current_balance_after)
    VALUES (?, 'DEBIT', 'withdrawals', ?, ?, 0.00)
");

while ($user = $users_result->fetch_assoc()) {
    $user_id = (int)$user['user_id'];
    $withdrawal_amount = (float)$user['wallet_balance'];

    // 3. Start Transaction for this user
    $conn->begin_transaction();
    try {
        echo "Processing User ID $user_id (Balance: " . number_format($withdrawal_amount, 2) . ")\n";

        // a. Create Withdrawal Request (status: PENDING)
        // Bind: (int, double)
        $stmt_withdrawal->bind_param("id", $user_id, $withdrawal_amount);
        $stmt_withdrawal->execute();
        $withdrawal_id = $conn->insert_id;
        
        // b. Update Wallet (Debit the full amount, setting balance to 0.00)
        // Bind: (int)
        $stmt_wallet_update->bind_param("i", $user_id);
        $stmt_wallet_update->execute();
        
        // c. Log the Wallet Transaction (Final balance is 0.00)
        // Bind: (user_id=int, withdrawal_id=int, amount=double)
        $stmt_transaction_log->bind_param("iid", $user_id, $withdrawal_id, $withdrawal_amount);
        $stmt_transaction_log->execute();
        
        // Final step in the user loop: Commit
        $conn->commit();
        
        $processed_count++;
        $total_withdrawn += $withdrawal_amount;
        echo " -> SUCCESS: Withdrawal ID $withdrawal_id recorded. Wallet cleared.\n";

    } catch (Exception $e) {
        // If any step failed, roll back all changes for this user
        $conn->rollback();
        // Log the error for debugging purposes
        error_log("Auto-Withdrawal Transaction Failed for User $user_id: " . $e->getMessage());
        echo " -> FAILED: Transaction rolled back. Error: " . $e->getMessage() . "\n";
    }
}

$stmt_fetch_users->close();
$stmt_withdrawal->close();
$stmt_wallet_update->close();
$stmt_transaction_log->close();

echo "--------------------------------------------------\n";
echo "Automated Withdrawal Complete.\n";
echo "Users Processed: $processed_count\n";
echo "Total Amount Withdrawn (Pending): " . number_format($total_withdrawn, 2) . "\n";
echo "--------------------------------------------------\n";

$conn->close();
?>