<?php
// customer_dashboard_api.php
// API to fetch complete customer dashboard data
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include './includes/db_config.php';

// Validate Database Connection
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get method
$method = $_SERVER['REQUEST_METHOD'];

// Function to get complete customer dashboard data
function getCustomerDashboardData($conn) {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        http_response_code(400);
        return json_encode([
            "status" => "error",
            "message" => "Invalid user ID"
        ]);
    }

    try {
        // 1. Get user basic information
        $stmt = $conn->prepare("
            SELECT 
                u.user_id,
                u.name,
                u.emailaddress,
                u.phonenumber,
                u.role,
                u.current_rank,
                u.wallet_balance,
                u.total_investment,
                u.refered_investment_volume,
                u.reference_code,
                u.created_at,
                u.address,
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
                "status" => "error",
                "message" => "User not found"
            ]);
        }

        // 2. Get active investments
        $stmt = $conn->prepare("
            SELECT 
                id,
                user_id,
                amount,
                daily_rate,
                initial_daily_payout,
                last_payout_date,
                created_at
            FROM investments
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $investments = [];
        
        while ($row = $result->fetch_assoc()) {
            $investments[] = [
                "id" => intval($row['id']),
                "amount" => floatval($row['amount']),
                "daily_rate" => floatval($row['daily_rate']),
                "initial_daily_payout" => floatval($row['initial_daily_payout']),
                "last_payout_date" => $row['last_payout_date'],
                "created_at" => $row['created_at']
            ];
        }
        $stmt->close();

        // 3. Get payout summary
        $stmt = $conn->prepare("
            SELECT 
                id,
                user_id,
                total_investment,
                days_completed,
                total_paid,
                remaining_payout,
                daily_payout,
                remaining_days,
                final_end_day,
                total_payout_days,
                created_at,
                updated_at,
                completed_at
            FROM payout_summary
            WHERE user_id = ?
            LIMIT 1
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $payout_summary = $result->fetch_assoc();
        $stmt->close();

        if ($payout_summary) {
            $payout_summary = [
                "id" => intval($payout_summary['id']),
                "total_investment" => floatval($payout_summary['total_investment']),
                "days_completed" => intval($payout_summary['days_completed']),
                "total_paid" => floatval($payout_summary['total_paid']),
                "remaining_payout" => floatval($payout_summary['remaining_payout']),
                "daily_payout" => floatval($payout_summary['daily_payout']),
                "remaining_days" => floatval($payout_summary['remaining_days']),
                "final_end_day" => floatval($payout_summary['final_end_day']),
                "total_payout_days" => intval($payout_summary['total_payout_days'] ?? 0),
                "created_at" => $payout_summary['created_at'],
                "updated_at" => $payout_summary['updated_at'],
                "completed_at" => $payout_summary['completed_at']
            ];
        }

        // 4. Calculate total credits from wallet_transactions
        $stmt = $conn->prepare("
            SELECT SUM(amount) as total_credits
            FROM wallet_transactions
            WHERE user_id = ?
            AND transaction_type = 'CREDIT'
            AND status = 'completed'
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $credit_result = $stmt->get_result()->fetch_assoc();
        $total_credits = floatval($credit_result['total_credits'] ?? 0);
        $stmt->close();

        // 5. Calculate total debits from wallet_transactions
        $stmt = $conn->prepare("
            SELECT SUM(amount) as total_debits
            FROM wallet_transactions
            WHERE user_id = ?
            AND transaction_type = 'DEBIT'
            AND status = 'completed'
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $debit_result = $stmt->get_result()->fetch_assoc();
        $total_debits = floatval($debit_result['total_debits'] ?? 0);
        $stmt->close();

        // 6. Calculate payouts vs commissions breakdown
        $stmt = $conn->prepare("
            SELECT 
                COALESCE(SUM(
                    CASE 
                        WHEN source_table IN ('daily_payout_log', 'payouts', 'daily_payout') THEN amount
                        ELSE 0 
                    END
                ), 0) as total_payouts,
                COALESCE(SUM(
                    CASE 
                        WHEN source_table LIKE '%commission%' OR source_table = 'referral_commission' THEN amount
                        ELSE 0 
                    END
                ), 0) as total_commissions,
                COALESCE(SUM(
                    CASE 
                        WHEN source_table IN ('daily_payout_log', 'payouts', 'daily_payout') THEN 1
                        ELSE 0 
                    END
                ), 0) as payout_count,
                COALESCE(SUM(
                    CASE 
                        WHEN source_table LIKE '%commission%' OR source_table = 'referral_commission' THEN 1
                        ELSE 0 
                    END
                ), 0) as commission_count
            FROM wallet_transactions
            WHERE user_id = ?
            AND transaction_type = 'CREDIT'
            AND status = 'completed'
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $breakdown_result = $stmt->get_result()->fetch_assoc();
        $total_payouts = floatval($breakdown_result['total_payouts'] ?? 0);
        $total_commissions = floatval($breakdown_result['total_commissions'] ?? 0);
        $payout_count = intval($breakdown_result['payout_count'] ?? 0);
        $commission_count = intval($breakdown_result['commission_count'] ?? 0);
        $stmt->close();

        // 7. Get recent wallet transactions (last 10)
        $stmt = $conn->prepare("
            SELECT 
                id,
                user_id,
                transaction_type,
                source_table,
                source_id,
                amount,
                current_balance_after,
                status,
                created_at
            FROM wallet_transactions
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 10
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recent_transactions = [];
        
        while ($row = $result->fetch_assoc()) {
            $recent_transactions[] = [
                "id" => intval($row['id']),
                "transaction_type" => $row['transaction_type'],
                "source_table" => $row['source_table'],
                "source_id" => intval($row['source_id']),
                "amount" => floatval($row['amount']),
                "current_balance_after" => floatval($row['current_balance_after']),
                "status" => $row['status'],
                "created_at" => $row['created_at']
            ];
        }
        $stmt->close();

        // 8. Get pending withdrawals
        $stmt = $conn->prepare("
            SELECT 
                id,
                user_id,
                amount,
                status,
                notes,
                admin_notes,
                created_at,
                processed_at,
                updated_at
            FROM withdrawals
            WHERE user_id = ?
            AND (status = 'pending' OR status = 'PENDING')
            ORDER BY created_at DESC
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pending_withdrawals = [];
        
        while ($row = $result->fetch_assoc()) {
            $pending_withdrawals[] = [
                "id" => intval($row['id']),
                "amount" => floatval($row['amount']),
                "status" => $row['status'],
                "notes" => $row['notes'],
                "admin_notes" => $row['admin_notes'],
                "created_at" => $row['created_at'],
                "processed_at" => $row['processed_at'],
                "updated_at" => $row['updated_at']
            ];
        }
        $stmt->close();

        // 9. Get daily payout logs (last 30 days)
        $stmt = $conn->prepare("
            SELECT 
                id,
                investment_id,
                user_id,
                payout_date,
                calculated_amount,
                created_at
            FROM daily_payout_log
            WHERE user_id = ?
            ORDER BY payout_date DESC
            LIMIT 30
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $payout_logs = [];
        
        while ($row = $result->fetch_assoc()) {
            $payout_logs[] = [
                "id" => intval($row['id']),
                "investment_id" => intval($row['investment_id']),
                "payout_date" => $row['payout_date'],
                "amount" => floatval($row['calculated_amount']),
                "created_at" => $row['created_at']
            ];
        }
        $stmt->close();

        // 10. Calculate summary statistics
        $remaining_payout = floatval($payout_summary['remaining_payout'] ?? 0);
        $total_investment = floatval($user_data['total_investment']);
        $daily_payout = floatval($payout_summary['daily_payout'] ?? 0);
        $net_earnings = $total_credits - $total_debits;

        return json_encode([
            "status" => "success",
            "data" => [
                "user" => [
                    "user_id" => intval($user_data['user_id']),
                    "name" => $user_data['name'],
                    "emailaddress" => $user_data['emailaddress'],
                    "phonenumber" => $user_data['phonenumber'],
                    "role" => $user_data['role'],
                    "current_rank" => $user_data['current_rank'],
                    "address" => $user_data['address'],
                    "reference_code" => $user_data['reference_code'],
                    "created_at" => $user_data['created_at']
                ],
                "financial" => [
                    "wallet_balance" => floatval($user_data['wallet_balance']),
                    "total_investment" => $total_investment,
                    "refered_investment_volume" => floatval($user_data['refered_investment_volume']),
                    "total_credits" => $total_credits,
                    "total_debits" => $total_debits,
                    "total_payouts" => $total_payouts,
                    "total_commissions" => $total_commissions,
                    "net_earnings" => $net_earnings,
                    "remaining_payout" => $remaining_payout,
                    "daily_payout" => $daily_payout,
                    "payout_count" => $payout_count,
                    "commission_count" => $commission_count
                ],
                "bank_details" => [
                    "bank_name" => $user_data['bank_name'] ?? "",
                    "upi_id" => $user_data['upi_id'] ?? "",
                    "account_number" => $user_data['bank_account_number'] ?? "",
                    "ifsc_code" => $user_data['ifsc_code'] ?? ""
                ],
                "investments" => $investments,
                "payout_summary" => $payout_summary,
                "recent_transactions" => $recent_transactions,
                "pending_withdrawals" => $pending_withdrawals,
                "payout_logs" => $payout_logs,
                "summary_stats" => [
                    "total_investments_count" => count($investments),
                    "active_investments_amount" => array_sum(array_column($investments, 'amount')),
                    "pending_withdrawals_count" => count($pending_withdrawals),
                    "pending_withdrawals_amount" => array_sum(array_column($pending_withdrawals, 'amount')),
                    "last_transaction_date" => !empty($recent_transactions) ? $recent_transactions[0]['created_at'] : null
                ]
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        return json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}

// Handle requests
if ($method === 'GET') {
    echo getCustomerDashboardData($conn);
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed"
    ]);
}

$conn->close();
?>