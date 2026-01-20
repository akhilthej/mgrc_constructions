<?php

// power_logout_api.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include './includes/db_config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: $SecureURL");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
try {
    // ✅ Auto-cleanup: delete sessions older than 1 hour
    $conn->query("DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Cleanup error: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request for fetching sessions
    try {
        $stmt = $conn->prepare("
            SELECT us.*, u.name, u.emailaddress as email, u.role 
            FROM user_sessions us 
            JOIN users u ON us.user_id = u.user_id 
            WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY us.last_activity DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $sessions[] = [
                'user_id' => $row['user_id'],
                'userId' => $row['user_id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'role' => $row['role'],
                'lastActivity' => $row['last_activity'],
                'ip' => $row['ip_address'],
                'userAgent' => $row['user_agent']
            ];
        }
        
        echo json_encode(["success" => true, "sessions" => $sessions]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['action'])) {
        echo json_encode(["success" => false, "message" => "Action is required"]);
        exit;
    }

    try {
        switch ($input['action']) {
            case 'logout_all':
                // Delete all sessions
                $stmt = $conn->prepare("DELETE FROM user_sessions");
                $stmt->execute();
                
                echo json_encode([
                    "success" => true, 
                    "message" => "All users have been logged out successfully",
                    "sessions_cleared" => $stmt->affected_rows
                ]);
                break;

            case 'logout_selected':
                if (!isset($input['userIds']) || !is_array($input['userIds'])) {
                    echo json_encode(["success" => false, "message" => "User IDs are required"]);
                    exit;
                }
                
                // Create placeholders for the IN clause
                $placeholders = implode(',', array_fill(0, count($input['userIds']), '?'));
                $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id IN ($placeholders)");
                
                // Bind parameters
                $types = str_repeat('i', count($input['userIds']));
                $stmt->bind_param($types, ...$input['userIds']);
                $stmt->execute();
                
                echo json_encode([
                    "success" => true, 
                    "message" => count($input['userIds']) . " user(s) have been logged out successfully",
                    "sessions_cleared" => $stmt->affected_rows
                ]);
                break;

            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
                break;
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

$conn->close();
?>