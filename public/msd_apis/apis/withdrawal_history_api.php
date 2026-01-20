<?php
// withdrawal_history_api.php
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

// Function to get withdrawal history
function getWithdrawalHistory($conn) {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        http_response_code(400);
        return json_encode([
            "status" => false,
            "message" => "Invalid user ID"
        ]);
    }

    // Get pagination parameters
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = ($page - 1) * $limit;
    
    // Get filter parameters
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $min_amount = isset($_GET['min_amount']) ? floatval($_GET['min_amount']) : 0;
    $max_amount = isset($_GET['max_amount']) ? floatval($_GET['max_amount']) : 0;
    
    try {
        // Get user info
        $stmt = $conn->prepare("
            SELECT 
                user_id,
                name,
                emailaddress,
                phonenumber,
                wallet_balance
            FROM users 
            WHERE user_id = ?
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

        // Build query for withdrawals
        $query = "
            SELECT 
                w.id,
                w.amount,
                w.status,
                w.notes,
                w.admin_notes,
                w.created_at,
                w.updated_at,
                CASE 
                    WHEN w.status = 'completed' THEN 'Completed'
                    WHEN w.status = 'pending' THEN 'Pending'
                    WHEN w.status = 'rejected' THEN 'Rejected'
                    ELSE w.status
                END as status_display,
                DATE(w.created_at) as request_date,
                TIME(w.created_at) as request_time
            FROM withdrawals w
            WHERE w.user_id = ?
        ";
        
        $params = [$user_id];
        $param_types = "i";
        
        // Apply filters
        if ($status && $status !== 'all') {
            $query .= " AND w.status = ?";
            $params[] = $status;
            $param_types .= "s";
        }
        
        if ($date_from) {
            $query .= " AND DATE(w.created_at) >= ?";
            $params[] = $date_from;
            $param_types .= "s";
        }
        
        if ($date_to) {
            $query .= " AND DATE(w.created_at) <= ?";
            $params[] = $date_to;
            $param_types .= "s";
        }
        
        if ($min_amount > 0) {
            $query .= " AND w.amount >= ?";
            $params[] = $min_amount;
            $param_types .= "d";
        }
        
        if ($max_amount > 0 && $max_amount >= $min_amount) {
            $query .= " AND w.amount <= ?";
            $params[] = $max_amount;
            $param_types .= "d";
        }
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM withdrawals w WHERE w.user_id = ?";
        if ($status && $status !== 'all') {
            $count_query .= " AND w.status = ?";
        }
        if ($date_from) {
            $count_query .= " AND DATE(w.created_at) >= ?";
        }
        if ($date_to) {
            $count_query .= " AND DATE(w.created_at) <= ?";
        }
        if ($min_amount > 0) {
            $count_query .= " AND w.amount >= ?";
        }
        if ($max_amount > 0 && $max_amount >= $min_amount) {
            $count_query .= " AND w.amount <= ?";
        }
        
        $count_stmt = $conn->prepare($count_query);
        if (!empty($params)) {
            $count_stmt->bind_param($param_types, ...$params);
        }
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        $total_count = $count_data['total'];
        $count_stmt->close();
        
        // Add pagination to main query
        $query .= " ORDER BY w.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $param_types .= "ii";
        
        // Execute main query
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($param_types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $withdrawals = [];
        
        while ($row = $result->fetch_assoc()) {
            $withdrawals[] = $row;
        }
        $stmt->close();
        
        // Calculate statistics
        $stats_query = "
            SELECT 
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                SUM(amount) as total_requested,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_completed,
                SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as total_pending,
                SUM(CASE WHEN status = 'rejected' THEN amount ELSE 0 END) as total_rejected,
                AVG(amount) as average_amount,
                MIN(amount) as min_amount,
                MAX(amount) as max_amount,
                MIN(created_at) as first_request,
                MAX(created_at) as last_request
            FROM withdrawals 
            WHERE user_id = ?
        ";
        
        $stmt = $conn->prepare($stats_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stats_result = $stmt->get_result();
        $stats_data = $stats_result->fetch_assoc();
        $stmt->close();
        
        // Get recent completed withdrawals (last 5)
        $recent_query = "
            SELECT 
                id,
                amount,
                DATE(created_at) as completed_date,
                notes
            FROM withdrawals 
            WHERE user_id = ? 
            AND status = 'completed'
            ORDER BY created_at DESC 
            LIMIT 5
        ";
        
        $stmt = $conn->prepare($recent_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $recent_result = $stmt->get_result();
        $recent_withdrawals = [];
        
        while ($row = $recent_result->fetch_assoc()) {
            $recent_withdrawals[] = $row;
        }
        $stmt->close();
        
        // Get monthly breakdown
        $monthly_query = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount
            FROM withdrawals 
            WHERE user_id = ?
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month DESC
            LIMIT 6
        ";
        
        $stmt = $conn->prepare($monthly_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $monthly_result = $stmt->get_result();
        $monthly_breakdown = [];
        
        while ($row = $monthly_result->fetch_assoc()) {
            $monthly_breakdown[] = $row;
        }
        $stmt->close();

        return json_encode([
            "status" => true,
            "data" => [
                "user_info" => [
                    "user_id" => $user_data['user_id'],
                    "name" => $user_data['name'],
                    "email" => $user_data['emailaddress'],
                    "phone" => $user_data['phonenumber'],
                    "current_balance" => floatval($user_data['wallet_balance'])
                ],
                "withdrawals" => $withdrawals,
                "pagination" => [
                    "total_records" => intval($total_count),
                    "current_page" => $page,
                    "total_pages" => ceil($total_count / $limit),
                    "limit" => $limit,
                    "has_next" => ($page * $limit) < $total_count,
                    "has_prev" => $page > 1
                ],
                "statistics" => [
                    "total_requests" => intval($stats_data['total_requests'] ?? 0),
                    "completed_count" => intval($stats_data['completed_count'] ?? 0),
                    "pending_count" => intval($stats_data['pending_count'] ?? 0),
                    "rejected_count" => intval($stats_data['rejected_count'] ?? 0),
                    "total_requested" => floatval($stats_data['total_requested'] ?? 0),
                    "total_completed" => floatval($stats_data['total_completed'] ?? 0),
                    "total_pending" => floatval($stats_data['total_pending'] ?? 0),
                    "total_rejected" => floatval($stats_data['total_rejected'] ?? 0),
                    "average_amount" => floatval($stats_data['average_amount'] ?? 0),
                    "min_amount" => floatval($stats_data['min_amount'] ?? 0),
                    "max_amount" => floatval($stats_data['max_amount'] ?? 0),
                    "success_rate" => $stats_data['total_requests'] > 0 ? 
                        round(($stats_data['completed_count'] / $stats_data['total_requests']) * 100, 2) : 0,
                    "first_request" => $stats_data['first_request'] ?: null,
                    "last_request" => $stats_data['last_request'] ?: null
                ],
                "recent_completed" => $recent_withdrawals,
                "monthly_breakdown" => $monthly_breakdown,
                "filters_applied" => [
                    "status" => $status,
                    "date_from" => $date_from,
                    "date_to" => $date_to,
                    "min_amount" => $min_amount,
                    "max_amount" => $max_amount
                ]
            ],
            "message" => "Withdrawal history retrieved successfully"
        ]);

    } catch (Exception $e) {
        error_log("Withdrawal History API Error: " . $e->getMessage());
        return json_encode([
            "status" => false,
            "message" => "Failed to retrieve withdrawal history. Please try again."
        ]);
    }
}

// Route requests
switch ($method) {
    case 'GET':
        echo getWithdrawalHistory($conn);
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