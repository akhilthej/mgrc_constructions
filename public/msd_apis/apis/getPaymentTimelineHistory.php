<?php
// getPaymentTimelineHistory.php
include './includes/db_config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: $SecureURL");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get filter parameters
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
        $type = isset($_GET['type']) ? $_GET['type'] : 'all';
        $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
        $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;
        
        // Build base query
        $sql_where = [];
        $params = [];
        $count_params = [];
        $types = "";
        $count_types = "";
        
        // 1. Daily Payouts
        $payouts_sql = "
            SELECT 
                'daily_payout' as transaction_type,
                'Daily Payout' as transaction_category,
                dpl.id as transaction_id,
                dpl.user_id,
                u.name as user_name,
                u.phonenumber as user_phone,
                dpl.payout_date as transaction_date,
                dpl.calculated_amount as amount,
                'CREDIT' as credit_debit,
                dpl.created_at,
                CONCAT('Daily payout for investment #', dpl.investment_id) as description,
                i.amount as investment_amount,
                NULL as reference_id,
                'wallet' as source,
                NULL as status
            FROM daily_payout_log dpl
            JOIN users u ON dpl.user_id = u.user_id
            LEFT JOIN investments i ON dpl.investment_id = i.id
            WHERE 1=1
        ";
        
        // 2. Commission Transactions
        $commissions_sql = "
            SELECT 
                'commission' as transaction_type,
                'Referral Commission' as transaction_category,
                wt.id as transaction_id,
                wt.user_id,
                u.name as user_name,
                u.phonenumber as user_phone,
                DATE(wt.created_at) as transaction_date,
                wt.amount as amount,
                wt.transaction_type as credit_debit,
                wt.created_at,
                CONCAT('Commission from level ', cr.level) as description,
                NULL as investment_amount,
                wt.source_id as reference_id,
                wt.source_table as source,
                NULL as status
            FROM wallet_transactions wt
            JOIN users u ON wt.user_id = u.user_id
            LEFT JOIN commission_rates cr ON wt.source_id = cr.level
            WHERE wt.source_table LIKE '%commission%'
        ";
        
        // 3. Withdrawal Requests - IMPORTANT: Include status field
        $withdrawals_sql = "
            SELECT 
                'withdrawal' as transaction_type,
                'Withdrawal Request' as transaction_category,
                w.id as transaction_id,
                w.user_id,
                u.name as user_name,
                u.phonenumber as user_phone,
                DATE(w.created_at) as transaction_date,
                w.amount as amount,
                'DEBIT' as credit_debit,
                w.created_at,
                CONCAT('Withdrawal request #', w.id, ' - Status: ', w.status) as description,
                NULL as investment_amount,
                w.id as reference_id,
                'withdrawal' as source,
                w.status as status  -- Include status field
            FROM withdrawals w
            JOIN users u ON w.user_id = u.user_id
            WHERE 1=1
        ";
        
        // 4. Investment Transactions
        $investments_sql = "
            SELECT 
                'investment' as transaction_type,
                'Investment Adjustment' as transaction_category,
                wt.id as transaction_id,
                wt.user_id,
                u.name as user_name,
                u.phonenumber as user_phone,
                DATE(wt.created_at) as transaction_date,
                wt.amount as amount,
                wt.transaction_type as credit_debit,
                wt.created_at,
                CONCAT('Manual adjustment - ', wt.source_table) as description,
                NULL as investment_amount,
                wt.source_id as reference_id,
                wt.source_table as source,
                NULL as status
            FROM wallet_transactions wt
            JOIN users u ON wt.user_id = u.user_id
            WHERE wt.source_table NOT LIKE '%commission%' 
              AND wt.source_table NOT IN ('daily_payout_log', 'withdrawals')
        ";
        
        // Combine all queries
        $union_sql = "($payouts_sql) UNION ALL ($commissions_sql) UNION ALL ($withdrawals_sql) UNION ALL ($investments_sql)";
        
        // Apply filters for main query
        $final_sql = "SELECT * FROM ($union_sql) as combined WHERE 1=1";
        
        if ($user_id) {
            $final_sql .= " AND user_id = ?";
            $params[] = $user_id;
            $types .= "i";
        }
        
        if ($type !== 'all') {
            $final_sql .= " AND transaction_type = ?";
            $params[] = $type;
            $types .= "s";
        }
        
        if ($date_from) {
            $final_sql .= " AND transaction_date >= ?";
            $params[] = $date_from;
            $types .= "s";
        }
        
        if ($date_to) {
            $final_sql .= " AND transaction_date <= ?";
            $params[] = $date_to;
            $types .= "s";
        }
        
        // Order and pagination
        $final_sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $types .= "i";
        $params[] = $offset;
        $types .= "i";
        
        // Count total records
        $count_sql = "SELECT COUNT(*) as total FROM ($union_sql) as combined WHERE 1=1";
        
        if ($user_id) {
            $count_sql .= " AND user_id = ?";
            $count_params[] = $user_id;
            $count_types .= "i";
        }
        
        if ($type !== 'all') {
            $count_sql .= " AND transaction_type = ?";
            $count_params[] = $type;
            $count_types .= "s";
        }
        
        if ($date_from) {
            $count_sql .= " AND transaction_date >= ?";
            $count_params[] = $date_from;
            $count_types .= "s";
        }
        
        if ($date_to) {
            $count_sql .= " AND transaction_date <= ?";
            $count_params[] = $date_to;
            $count_types .= "s";
        }
        
        // Execute count query
        $count_stmt = $conn->prepare($count_sql);
        if (!empty($count_params)) {
            $count_stmt->bind_param($count_types, ...$count_params);
        }
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $total_records = $count_row['total'] ?? 0;
        $count_stmt->close();
        
        // Execute main query
        $stmt = $conn->prepare($final_sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            // Format the data
            $transactions[] = [
                'id' => $row['transaction_id'],
                'user_id' => $row['user_id'],
                'user_name' => $row['user_name'],
                'user_phone' => $row['user_phone'],
                'transaction_date' => $row['transaction_date'],
                'created_at' => $row['created_at'],
                'type' => $row['transaction_type'],
                'category' => $row['transaction_category'],
                'amount' => floatval($row['amount']),
                'credit_debit' => $row['credit_debit'],
                'description' => $row['description'],
                'investment_amount' => $row['investment_amount'] ? floatval($row['investment_amount']) : null,
                'reference_id' => $row['reference_id'],
                'source' => $row['source'],
                'status' => $row['status'] ? $row['status'] : ($row['credit_debit'] === 'CREDIT' ? 'Credited' : 'Debited'),
                'formatted_date' => date('d M Y', strtotime($row['transaction_date'])),
                'formatted_time' => date('h:i A', strtotime($row['created_at']))
            ];
        }
        
        // Get summary statistics
$summary_sql = "
    SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN credit_debit = 'CREDIT' THEN amount ELSE 0 END) as total_credits,
        SUM(CASE WHEN credit_debit = 'DEBIT' THEN amount ELSE 0 END) as total_debits,
        COUNT(DISTINCT user_id) as total_users,
        -- Withdrawal specific stats - ONLY use your actual statuses
        SUM(CASE WHEN transaction_type = 'withdrawal' AND status = 'completed' THEN amount ELSE 0 END) as completed_withdrawals,
        SUM(CASE WHEN transaction_type = 'withdrawal' AND status = 'pending' THEN amount ELSE 0 END) as pending_withdrawals,
        SUM(CASE WHEN transaction_type = 'withdrawal' AND status = 'rejected' THEN amount ELSE 0 END) as rejected_withdrawals,
        SUM(CASE WHEN transaction_type = 'withdrawal' THEN amount ELSE 0 END) as total_withdrawals_amount,
        COUNT(CASE WHEN transaction_type = 'withdrawal' AND status = 'completed' THEN 1 END) as completed_withdrawals_count,
        COUNT(CASE WHEN transaction_type = 'withdrawal' AND status = 'pending' THEN 1 END) as pending_withdrawals_count,
        COUNT(CASE WHEN transaction_type = 'withdrawal' AND status = 'rejected' THEN 1 END) as rejected_withdrawals_count,
        COUNT(CASE WHEN transaction_type = 'withdrawal' THEN 1 END) as total_withdrawals_count
    FROM ($union_sql) as combined
    WHERE 1=1
";
        
        if ($user_id) {
            $summary_sql .= " AND user_id = ?";
        }
        
        if ($type !== 'all') {
            $summary_sql .= " AND transaction_type = ?";
        }
        
        if ($date_from) {
            $summary_sql .= " AND transaction_date >= ?";
        }
        
        if ($date_to) {
            $summary_sql .= " AND transaction_date <= ?";
        }
        
        $summary_stmt = $conn->prepare($summary_sql);
        if (!empty($count_params)) {
            $summary_stmt->bind_param($count_types, ...$count_params);
        }
        $summary_stmt->execute();
        $summary_result = $summary_stmt->get_result();
        $summary = $summary_result->fetch_assoc();
        $summary_stmt->close();
        
        // Ensure all summary values are set
        $summary = array_merge([
            'total_transactions' => 0,
            'total_credits' => 0,
            'total_debits' => 0,
            'total_users' => 0,
            'completed_withdrawals' => 0,
            'pending_withdrawals' => 0,
            'rejected_withdrawals' => 0,
            'total_withdrawals_amount' => 0,
            'completed_withdrawals_count' => 0,
            'pending_withdrawals_count' => 0,
            'rejected_withdrawals_count' => 0,
            'total_withdrawals_count' => 0
        ], $summary ?: []);
        
        $response['status'] = 'success';
        $response['data'] = $transactions;
        $response['pagination'] = [
            'total_records' => intval($total_records),
            'total_pages' => $limit > 0 ? ceil($total_records / $limit) : 1,
            'current_page' => $page,
            'limit' => $limit,
            'has_next' => ($page * $limit) < $total_records,
            'has_prev' => $page > 1
        ];
        $response['summary'] = [
    'total_transactions' => intval($summary['total_transactions']),
    'total_credits' => floatval($summary['total_credits']),
    'total_debits' => floatval($summary['total_debits']),
    'net_flow' => floatval($summary['total_credits'] - $summary['total_debits']),
    'total_users' => intval($summary['total_users']),
    'withdrawal_stats' => [
        'total_amount' => floatval($summary['total_withdrawals_amount']),
        'total_count' => intval($summary['total_withdrawals_count']),
        'completed_amount' => floatval($summary['completed_withdrawals']),
        'completed_count' => intval($summary['completed_withdrawals_count']),
        'pending_amount' => floatval($summary['pending_withdrawals']),
        'pending_count' => intval($summary['pending_withdrawals_count']),
        'rejected_amount' => floatval($summary['rejected_withdrawals']),
        'rejected_count' => intval($summary['rejected_withdrawals_count'])
        // Remove 'approved_amount' and 'approved_count' since you don't have this status
    ]
];
        
        $stmt->close();
        
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
        error_log("Payment Timeline Error: " . $e->getMessage());
    }
} 
// Handle POST request for updating withdrawal status
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the raw POST data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['withdrawal_id']) || !isset($input['status'])) {
            throw new Exception("Missing required parameters");
        }
        
        $withdrawal_id = intval($input['withdrawal_id']);
        $status = $conn->real_escape_string($input['status']);
        $notes = isset($input['notes']) ? $conn->real_escape_string($input['notes']) : '';
        $admin_notes = isset($input['admin_notes']) ? $conn->real_escape_string($input['admin_notes']) : '';
        
        // Validate status - only these three statuses are valid
        $valid_statuses = ['pending', 'completed', 'rejected'];
        if (!in_array($status, $valid_statuses)) {
            throw new Exception("Invalid status value. Allowed: pending, completed, rejected");
        }
        
        // Check if withdrawal exists
        $check_sql = "SELECT id, status FROM withdrawals WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $withdrawal_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception("Withdrawal request not found");
        }
        
        $withdrawal = $check_result->fetch_assoc();
        $check_stmt->close();
        
        // Update withdrawal status
        $update_sql = "UPDATE withdrawals SET status = ?, notes = ?, admin_notes = CONCAT(COALESCE(admin_notes, ''), '\n', ?), updated_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        
        // Format admin notes properly
        $formatted_admin_notes = date('Y-m-d H:i:s') . " - Status changed to: " . $status . 
                                 (empty($admin_notes) ? "" : " - " . $admin_notes);
        
        $update_stmt->bind_param("sssi", $status, $notes, $formatted_admin_notes, $withdrawal_id);
        
        if ($update_stmt->execute()) {
            // If status is completed, also update wallet_transactions if needed
            if ($status === 'completed') {
                // First check if wallet transaction exists for this withdrawal
                $check_wallet_sql = "SELECT id FROM wallet_transactions WHERE source_table = 'withdrawals' AND source_id = ?";
                $check_wallet_stmt = $conn->prepare($check_wallet_sql);
                $check_wallet_stmt->bind_param("i", $withdrawal_id);
                $check_wallet_stmt->execute();
                $check_wallet_result = $check_wallet_stmt->get_result();
                
                if ($check_wallet_result->num_rows > 0) {
                    // Update existing wallet transaction
                    $wallet_sql = "UPDATE wallet_transactions SET status = 'completed' WHERE source_table = 'withdrawals' AND source_id = ?";
                    $wallet_stmt = $conn->prepare($wallet_sql);
                    $wallet_stmt->bind_param("i", $withdrawal_id);
                    $wallet_stmt->execute();
                    $wallet_stmt->close();
                } else {
                    // Create a new wallet transaction for the withdrawal
                    $user_sql = "SELECT user_id FROM withdrawals WHERE id = ?";
                    $user_stmt = $conn->prepare($user_sql);
                    $user_stmt->bind_param("i", $withdrawal_id);
                    $user_stmt->execute();
                    $user_result = $user_stmt->get_result();
                    $user_row = $user_result->fetch_assoc();
                    $user_id = $user_row['user_id'];
                    $user_stmt->close();
                    
                    // Get withdrawal amount
                    $amount_sql = "SELECT amount FROM withdrawals WHERE id = ?";
                    $amount_stmt = $conn->prepare($amount_sql);
                    $amount_stmt->bind_param("i", $withdrawal_id);
                    $amount_stmt->execute();
                    $amount_result = $amount_stmt->get_result();
                    $amount_row = $amount_result->fetch_assoc();
                    $amount = $amount_row['amount'];
                    $amount_stmt->close();
                    
                    // Insert wallet transaction
                    $insert_wallet_sql = "INSERT INTO wallet_transactions (user_id, amount, transaction_type, description, source_table, source_id, status, created_at) 
                                         VALUES (?, ?, 'DEBIT', 'Withdrawal request #{$withdrawal_id}', 'withdrawals', ?, 'completed', NOW())";
                    $insert_wallet_stmt = $conn->prepare($insert_wallet_sql);
                    $insert_wallet_stmt->bind_param("idi", $user_id, $amount, $withdrawal_id);
                    $insert_wallet_stmt->execute();
                    $insert_wallet_stmt->close();
                }
                $check_wallet_stmt->close();
            }
            
            $response['status'] = 'success';
            $response['message'] = 'Withdrawal status updated successfully';
            $response['data'] = [
                'withdrawal_id' => $withdrawal_id,
                'old_status' => $withdrawal['status'],
                'new_status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } else {
            throw new Exception("Failed to update withdrawal status: " . $update_stmt->error);
        }
        
        $update_stmt->close();
        
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
        error_log("Withdrawal Update Error: " . $e->getMessage());
    }
}
else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
$conn->close();
?>