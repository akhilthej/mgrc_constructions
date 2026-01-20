<?php
//my_investment_api.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include('../includes/db_config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($user_id === 0) {
    echo json_encode(["status" => "error", "message" => "User ID missing"]);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Fetch investment history only
$invQuery = $conn->prepare("SELECT id, amount, daily_rate, initial_daily_payout, created_at FROM investments WHERE user_id = ? ORDER BY created_at DESC");
if (!$invQuery) {
    echo json_encode(["status" => "error", "message" => "Investments query preparation failed"]);
    $conn->close();
    exit;
}

$invQuery->bind_param("i", $user_id);
$invQuery->execute();
$invResult = $invQuery->get_result();

$investments = [];
while ($row = $invResult->fetch_assoc()) {
    $investments[] = $row;
}

$invQuery->close();
$conn->close();

echo json_encode([
    "status" => "success",
    "investment_history" => $investments
]);
?>