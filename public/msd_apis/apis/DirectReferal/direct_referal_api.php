<?php
// -------------------------------
// CORS + HEADERS
// -------------------------------
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

// -------------------------------
// Include DB Config
// -------------------------------
// adjust the path if needed
include '../includes/db_config.php';

// -------------------------------
// Validate Database Connection
// -------------------------------
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "data" => [],
        "message" => "Database connection failed: " . ($conn->connect_error ?? 'unknown error')
    ]);
    exit;
}

// -------------------------------
// Validate USER ID
// -------------------------------
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "data" => [],
        "message" => "Invalid user_id"
    ]);
    exit;
}

try {

    // -----------------------------------------------------
    // Step 1: Get user's reference_code
    // -----------------------------------------------------
    $getRefCode = $conn->prepare("
        SELECT reference_code 
        FROM users 
        WHERE user_id = ?
        LIMIT 1
    ");

    if (!$getRefCode) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $getRefCode->bind_param("i", $user_id);
    if (!$getRefCode->execute()) {
        throw new Exception("Execute failed (ref code): " . $getRefCode->error);
    }
    $resultRef = $getRefCode->get_result();
    $userData = $resultRef ? $resultRef->fetch_assoc() : null;
    $getRefCode->close();

    if (!$userData || empty($userData['reference_code'])) {
        echo json_encode([
            "status" => true,
            "data" => [],
            "message" => "User has no referral code or no referrals"
        ]);
        exit;
    }

    $reference_code = $userData['reference_code'];

    // -----------------------------------------------------
    // Step 2: Fetch users referred by this code
    // -----------------------------------------------------
    $stmt = $conn->prepare("
        SELECT 
            user_id,
            name,
            emailaddress AS email,
            phonenumber AS phone,
            role,
            current_rank,
            total_investment,
            wallet_balance,
            pannel_access,
            DATE(created_at) AS joined_date
        FROM users
        WHERE reference_by = ?
        ORDER BY created_at DESC
    ");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $reference_code);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed (referrals): " . $stmt->error);
    }
    $result = $stmt->get_result();

    $referrals = [];

    while ($row = $result->fetch_assoc()) {
        $row['status'] = (isset($row['pannel_access']) && intval($row['pannel_access']) === 1) ? "active" : "pending";
        $row['investment'] = isset($row['total_investment']) ? floatval($row['total_investment']) : 0.0;

        unset($row['pannel_access']);
        unset($row['total_investment']);

        $referrals[] = $row;
    }

    $stmt->close();

    echo json_encode([
        "status" => true,
        "data" => $referrals,
        "message" => count($referrals) . " referral(s) found"
    ]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "status" => false,
        "data" => [],
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>
