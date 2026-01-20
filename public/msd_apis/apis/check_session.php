<?php
// checksession.php
date_default_timezone_set('Asia/Kolkata');
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Include DB config properly
require_once __DIR__ . "/includes/db_config.php";

// Handle preflight
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$response = ["valid" => false, "message" => "Initial state"];

try {
    // Auto cleanup expired sessions
    $conn->query("DELETE FROM user_sessions WHERE expires_at < NOW()");

    // Validate token
    if (empty($_GET["token"])) {
        $response = ["valid" => false, "message" => "Missing session token"];
        echo json_encode($response);
        exit;
    }

    $sessionToken = trim($_GET["token"]);

    // Validate token format
    if (strlen($sessionToken) < 10) {
        $response = ["valid" => false, "message" => "Invalid token format"];
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, user_id, expires_at FROM user_sessions WHERE session_token = ? AND expires_at > NOW() LIMIT 1");
    $stmt->bind_param("s", $sessionToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response = ["valid" => false, "message" => "Session not found or expired"];
        echo json_encode($response);
        exit;
    }

    $session = $result->fetch_assoc();

    // Check if session will expire in less than 1 hour (optional: for early warning)
    $currentTime = time();
    $expiryTime = strtotime($session["expires_at"]);
    $timeUntilExpiry = $expiryTime - $currentTime;

    // Valid session
    $response = [
        "valid" => true,
        "message" => "Session active",
        "expires_at" => $session["expires_at"],
        "expires_in_hours" => round($timeUntilExpiry / 3600, 2)
    ];

    // Optional: Extend session if it's about to expire (uncomment if needed)
    // if ($timeUntilExpiry < 3600) { // Less than 1 hour remaining
    //     $newExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
    //     $updateStmt = $conn->prepare("UPDATE user_sessions SET expires_at = ? WHERE session_token = ?");
    //     $updateStmt->bind_param("ss", $newExpiry, $sessionToken);
    //     $updateStmt->execute();
    //     $response["expires_at"] = $newExpiry;
    //     $response["message"] = "Session extended";
    // }

} catch (Exception $e) {
    error_log("Session check error: " . $e->getMessage());
    $response = ["valid" => false, "message" => "Server error"];
}

echo json_encode($response);

// Close connection properly
if (isset($conn)) {
    $conn->close();
}
?>