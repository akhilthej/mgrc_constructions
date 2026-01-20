<?php
// generate_daily_logs.php - Updated with 20-Level Commission System & Weekend Exclusion
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/db_config.php');
include('../includes/config.php');

date_default_timezone_set(TIMEZONE);

// Log file configuration
$log_file = LOG_DIR . 'daily_payout_' . date('Y-m-d') . '.log';
$error_log_file = LOG_DIR . 'daily_payout_error.log';

function writeLog($message, $is_error = false) {
    global $log_file, $error_log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "$timestamp - $message\n";

    if ($is_error) {
        file_put_contents($error_log_file, $log_message, FILE_APPEND);
        echo "<span style='color: red;'>ERROR: $message</span><br>";
    } else {
        file_put_contents($log_file, $log_message, FILE_APPEND);
        echo "$message<br>";
    }
}

writeLog("Starting Daily Payout & Commission Generation");
writeLog("--------------------------------------------------");

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    writeLog("Connection failed: " . $conn->connect_error, true);
    exit;
}

// --- 1. PRE-FETCH COMMISSION RATES ---
$commission_rates = [];
$rate_sql = "SELECT level, percentage FROM commission_rates ORDER BY level ASC";
$rate_res = $conn->query($rate_sql);
while ($r = $rate_res->fetch_assoc()) {
    $commission_rates[$r['level']] = (float)$r['percentage'];
}
writeLog("Loaded " . count($commission_rates) . " commission levels.");

$today_dt = new DateTime('now', new DateTimeZone(TIMEZONE));
$today_dt->setTime(0, 0, 0);

$total_logs_added = 0;
$total_credits_issued = 0.0;
$processed_investments = 0;
$errors_encountered = 0;
$total_commissions_paid = 0.0;
$weekend_skips = 0;

try {

    // 1. Insert Daily ROI Log
    $stmt_log = $conn->prepare(
        "INSERT IGNORE INTO daily_payout_log (investment_id, user_id, payout_date, calculated_amount)
         VALUES (?, ?, ?, ?)"
    );

    // 2. Update Wallet Balance
    $stmt_wallet_update = $conn->prepare(
        "UPDATE users SET wallet_balance = wallet_balance + ? WHERE user_id = ?"
    );

    // 3. Get Current Balance
    $stmt_get_balance = $conn->prepare(
        "SELECT wallet_balance FROM users WHERE user_id = ?"
    );

  // 4. Transaction Log - WITH EXPLICIT CREATED_AT DATE
$stmt_transaction_log = $conn->prepare(
    "INSERT INTO wallet_transactions (user_id, transaction_type, source_table, source_id, amount, current_balance_after, created_at)
     VALUES (?, 'CREDIT', ?, ?, ?, ?, ?)"
);

    // 5. Get Upline User
    $stmt_get_parent = $conn->prepare("
        SELECT parent.user_id, parent.reference_by
        FROM users AS child
        JOIN users AS parent ON child.reference_by = parent.reference_code
        WHERE child.user_id = ?
    ");

    $offset = 0;
    $batch_size = BATCH_SIZE;

    do {
        // Fetch investments
        $inv_query = "SELECT id, user_id, amount, created_at 
                      FROM investments ORDER BY id ASC 
                      LIMIT $offset, $batch_size";

        $inv_result = $conn->query($inv_query);
        $batch_count = $inv_result->num_rows;

        if ($batch_count > 0) {
            while ($inv = $inv_result->fetch_assoc()) {

                $processed_investments++;

                $investment_id = (int)$inv['id'];
                $investor_user_id = (int)$inv['user_id'];
                $amount = (float)$inv['amount'];

                $daily_payout_amount = round($amount * DAILY_RATE, 2);

                $start_dt = new DateTime($inv['created_at'], new DateTimeZone(TIMEZONE));
                $start_dt->setTime(0, 0, 0);
                $start_dt->modify('+1 day');

                $current_log_dt = clone $start_dt;

                while ($current_log_dt <= $today_dt) {

                    $payout_date_str = $current_log_dt->format('Y-m-d');

                    // Weekend Skip
                    $day_of_week = (int)$current_log_dt->format('N');
                    if ($day_of_week >= 6) {
                        $weekend_skips++;
                        writeLog("Skipping ROI/Commission for $payout_date_str (Weekend).");
                        $current_log_dt->modify('+1 day');
                        continue;
                    }

                    // Insert Daily ROI Log
                    $stmt_log->bind_param(
                        "iisd",
                        $investment_id,
                        $investor_user_id,
                        $payout_date_str,
                        $daily_payout_amount
                    );

                    if (!$stmt_log->execute()) {
                        writeLog("DB Error Log Insert: " . $stmt_log->error, true);
                        $errors_encountered++;
                        $current_log_dt->modify('+1 day');
                        continue;
                    }

                    if ($stmt_log->affected_rows > 0) {

                        $daily_payout_log_id = $conn->insert_id;

                        $total_logs_added++;
                        $total_credits_issued += $daily_payout_amount;

                        $conn->begin_transaction();

                        try {
                            // Credit Investor Wallet
                            $stmt_wallet_update->bind_param(
                                "di",
                                $daily_payout_amount,
                                $investor_user_id
                            );
                            $stmt_wallet_update->execute();

                            // Get New Balance
                            $stmt_get_balance->bind_param("i", $investor_user_id);
                            $stmt_get_balance->execute();
                            $bal_res = $stmt_get_balance->get_result()->fetch_assoc();
                            $new_bal = $bal_res['wallet_balance'];

                            // Transaction Log
                            $source_tbl = 'daily_payout_log';
                           // Transaction Log for Investor
$payout_date_for_transaction = $current_log_dt->format('Y-m-d H:i:s'); // Get the actual payout date
$stmt_transaction_log->bind_param(
    "isidds",  // Note the added 's' at the end
    $investor_user_id,
    $source_tbl,
    $daily_payout_log_id,
    $daily_payout_amount,
    $new_bal,
    $payout_date_for_transaction  // Add this parameter
);
                            $stmt_transaction_log->execute();

                            // ---- COMMISSION (20 LEVELS) ----
                            $current_level_user_id = $investor_user_id;

                            for ($level = 1; $level <= 20; $level++) {

                                if (!isset($commission_rates[$level])) break;

                                // Get Parent
                                $stmt_get_parent->bind_param("i", $current_level_user_id);
                                $stmt_get_parent->execute();
                                $parent_res = $stmt_get_parent->get_result();

                                if ($parent_res->num_rows == 0) break;

                                $parent_data = $parent_res->fetch_assoc();
                                $parent_user_id = $parent_data['user_id'];

                                // Commission Amount
                                $rate = $commission_rates[$level];
                                $commission_amount = round($daily_payout_amount * $rate, 2);

                                if ($commission_amount > 0) {

                                    // Credit Parent
                                    $stmt_wallet_update->bind_param(
                                        "di",
                                        $commission_amount,
                                        $parent_user_id
                                    );
                                    $stmt_wallet_update->execute();

                                    // Get New Balance
                                    $stmt_get_balance->bind_param("i", $parent_user_id);
                                    $stmt_get_balance->execute();
                                    $p_bal_res = $stmt_get_balance->get_result()->fetch_assoc();
                                    $p_new_bal = $p_bal_res['wallet_balance'];

                                   // Transaction Log for Commission
$comm_source_tbl = 'referral_commission';
$stmt_transaction_log->bind_param(
    "isidds",  // Note the added 's' at the end
    $parent_user_id,
    $comm_source_tbl,
    $daily_payout_log_id,
    $commission_amount,
    $p_new_bal,
    $payout_date_for_transaction  // Add this parameter - same date as the payout
);
                                    $stmt_transaction_log->execute();

                                    $total_commissions_paid += $commission_amount;
                                }

                                // Move up the chain
                                $current_level_user_id = $parent_user_id;
                            }

                            $conn->commit();

                        } catch (Exception $e) {
                            $conn->rollback();
                            writeLog("Transaction Failed (Inv: $investment_id): " . $e->getMessage(), true);
                            $errors_encountered++;
                        }
                    }

                    $current_log_dt->modify('+1 day');
                }

                // Summary Update
                $summary_url = "http://" . $_SERVER['HTTP_HOST'] .
                               "/apis/test/calculate_adjusted_plan.php?user_id=$investor_user_id";

                @file_get_contents($summary_url);
            }
        }

        $offset += $batch_size;

    } while ($batch_count == $batch_size);

    // Close Statements
    $stmt_log->close();
    $stmt_wallet_update->close();
    $stmt_get_balance->close();
    $stmt_transaction_log->close();
    $stmt_get_parent->close();

} catch (Exception $e) {
    writeLog("FATAL ERROR: " . $e->getMessage(), true);
    $errors_encountered++;
}

writeLog("--------------------------------------------------");
writeLog("PROCESSING COMPLETE");
writeLog("Investments Processed: $processed_investments");
writeLog("Daily ROI Paid: INR" . number_format($total_credits_issued, 2));
writeLog("Referral Commissions Paid: INR" . number_format($total_commissions_paid, 2));
writeLog("Total Payouts Skipped (Weekends): $weekend_skips");
writeLog("Errors: $errors_encountered");

$conn->close();
?>
