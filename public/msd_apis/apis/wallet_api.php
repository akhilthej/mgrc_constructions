<?php
// wallet_api.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include './includes/db_config.php';

// Validate Database Connection
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get method
$method = $_SERVER['REQUEST_METHOD'];

// Function to get wallet balance and transactions
function getWalletData($conn) {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        http_response_code(400);
        return json_encode([
            "status" => false,
            "message" => "Invalid user ID"
        ]);
    }

    try {
        // Get user wallet balance
        $stmt = $conn->prepare("
            SELECT 
                u.user_id,
                u.name,
                u.emailaddress,
                u.phonenumber,
                u.wallet_balance,
                u.total_investment,
                COALESCE(u.refered_investment_volume, 0) as refered_investment_volume,
                uba.bank_name,
                uba.upi_id,
                uba.bank_account_number,
                uba.ifsc_code
            FROM users u
            LEFT JOIN user_bank_account uba ON u.user_id = uba.user_id
            WHERE u.user_id = ?
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $stmt->close();

        if (!$user_data) {
            return json_encode([
                "status" => false,
                "message" => "User not found"
            ]);
        }

        // Calculate totals from wallet transactions
        $totals_stmt = $conn->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN transaction_type = 'CREDIT' THEN amount ELSE 0 END), 0) as total_credited,
                COALESCE(SUM(CASE WHEN transaction_type = 'DEBIT' THEN amount ELSE 0 END), 0) as total_debited
            FROM wallet_transactions 
            WHERE user_id = ?
        ");
        $totals_stmt->bind_param("i", $user_id);
        $totals_stmt->execute();
        $totals_result = $totals_stmt->get_result();
        $totals_data = $totals_result->fetch_assoc();
        $totals_stmt->close();

        // Get wallet transactions
        $stmt = $conn->prepare("
            SELECT 
                wt.id,
                wt.transaction_type,
                wt.source_table,
                wt.source_id,
                wt.amount,
                wt.current_balance_after,
                wt.created_at,
                CASE 
                    WHEN wt.source_table = 'daily_payout_log' THEN 'Daily ROI'
                    WHEN wt.source_table = 'commission' THEN 'Referral Commission'
                    WHEN wt.source_table = 'withdrawals' THEN 'Withdrawal'
                    ELSE wt.source_table
                END as source_name,
                CASE 
                    WHEN wt.source_table = 'withdrawals' THEN (
                        SELECT CONCAT('Withdrawal #', w.id, ' - ', w.status)
                        FROM withdrawals w 
                        WHERE w.id = wt.source_id
                    )
                    WHEN wt.source_table = 'daily_payout_log' THEN (
                        SELECT CONCAT('Daily Payout for Investment #', dpl.investment_id)
                        FROM daily_payout_log dpl
                        WHERE dpl.id = wt.source_id
                    )
                    WHEN wt.source_table = 'commission' THEN 'Referral Commission'
                    ELSE wt.source_table
                END as description
            FROM wallet_transactions wt
            WHERE wt.user_id = ?
            ORDER BY wt.created_at DESC
            LIMIT 100
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $transactions = [];
        
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        $stmt->close();

        // Get pending withdrawals
        $stmt = $conn->prepare("
            SELECT 
                w.id,
                w.amount,
                w.status,
                w.created_at,
                w.notes,
                w.admin_notes
            FROM withdrawals w
            WHERE w.user_id = ?
            AND w.status = 'pending'
            ORDER BY w.created_at DESC
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pending_withdrawals = [];
        
        while ($row = $result->fetch_assoc()) {
            $pending_withdrawals[] = $row;
        }
        $stmt->close();

        // Check if today is Saturday
        $today = date('N'); // 1=Monday, 7=Sunday
        $is_saturday = ($today == 6); // 6=Saturday
        $next_saturday = date('Y-m-d', strtotime('next saturday'));
        $last_saturday = date('Y-m-d', strtotime('last saturday'));

        // Calculate weekly stats
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));
        
        $stmt = $conn->prepare("
            SELECT 
                SUM(CASE WHEN transaction_type = 'CREDIT' THEN amount ELSE 0 END) as weekly_credits,
                SUM(CASE WHEN transaction_type = 'DEBIT' THEN amount ELSE 0 END) as weekly_debits,
                COUNT(*) as weekly_transactions
            FROM wallet_transactions 
            WHERE user_id = ?
            AND DATE(created_at) BETWEEN ? AND ?
        ");
        
        $stmt->bind_param("iss", $user_id, $week_start, $week_end);
        $stmt->execute();
        $result = $stmt->get_result();
        $weekly_stats = $result->fetch_assoc();
        $stmt->close();

        return json_encode([
            "status" => true,
            "data" => [
                "user_info" => [
                    "user_id" => $user_data['user_id'],
                    "name" => $user_data['name'],
                    "email" => $user_data['emailaddress'],
                    "phone" => $user_data['phonenumber'],
                    "current_balance" => floatval($user_data['wallet_balance']),
                    "total_investment" => floatval($user_data['total_investment']),
                    "refered_investment" => floatval($user_data['refered_investment_volume']),
                    "total_credited" => floatval($totals_data['total_credited'] ?? 0),
                    "total_debited" => floatval($totals_data['total_debited'] ?? 0),
                    "bank_info" => [
                        "bank_name" => $user_data['bank_name'] ?? "",
                        "upi_id" => $user_data['upi_id'] ?? "",
                        "account_number" => $user_data['bank_account_number'] ?? "",
                        "ifsc_code" => $user_data['ifsc_code'] ?? ""
                    ]
                ],
                "transactions" => $transactions,
                "pending_withdrawals" => $pending_withdrawals,
                "weekly_stats" => [
                    "credits" => floatval($weekly_stats['weekly_credits'] ?? 0),
                    "debits" => floatval($weekly_stats['weekly_debits'] ?? 0),
                    "transaction_count" => intval($weekly_stats['weekly_transactions'] ?? 0),
                    "week_start" => $week_start,
                    "week_end" => $week_end
                ],
                "withdrawal_schedule" => [
                    "is_saturday" => $is_saturday,
                    "next_saturday" => $next_saturday,
                    "last_saturday" => $last_saturday,
                    "auto_withdrawal_enabled" => true,
                    "withdrawal_time" => "Every Saturday at 6:00 PM",
                    "minimum_balance_for_withdrawal" => 100.00
                ]
            ],
            "message" => "Wallet data retrieved successfully"
        ]);

    } catch (Exception $e) {
        error_log("Wallet API Error: " . $e->getMessage());
        return json_encode([
            "status" => false,
            "message" => "Failed to retrieve wallet data. Please try again."
        ]);
    }
}

// Route requests
switch ($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] == 'auto-withdraw') {
            // This should be called by cron job
            echo processAutomaticWithdrawal($conn);
        } else {
            echo getWalletData($conn);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            "status" => false,
            "message" => "Method not allowed"
        ]);
        break;
}

$conn->close();
?>