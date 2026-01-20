<?php
// Analysis.php - Optimized API for fetching Invoices, Customers, and Daily Spends

date_default_timezone_set('Asia/Kolkata');

// Include database configuration
include './includes/db_config.php';

// Set Headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: $SecureURL");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$entity = isset($_GET['entity']) ? $_GET['entity'] : '';

// Main router for GET requests
if ($method === 'GET') {
    switch ($entity) {
        case 'invoices':
            getInvoices();
            break;
        case 'customers':
            getCustomers();
            break;
        case 'dailyspends':
            getDailySpends();
            break;
        default:
            sendError("Invalid entity for GET");
            break;
    }
} else {
    sendError("Invalid request method");
}

$conn->close();

// Helper to send error JSON and exit
function sendError($msg) {
    echo json_encode(["message" => $msg]);
    exit;
}

/** INVOICES **/
/** INVOICES **/
function getInvoices() {
    global $conn;
    $startDate = isset($_GET['startDate']) ? $conn->real_escape_string($_GET['startDate']) : '';
    $endDate = isset($_GET['endDate']) ? $conn->real_escape_string($_GET['endDate']) : '';

    if ($startDate && $endDate) {
        $sql = "SELECT * FROM invoice WHERE invoice_date BETWEEN '$startDate' AND '$endDate'";
    } else {
        $sql = "SELECT * FROM invoice";
    }

    $result = $conn->query($sql);
    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }
    echo json_encode($invoices);
}


/** CUSTOMERS **/
function getCustomers() {
    global $conn;
    $sql = "SELECT * FROM customers";
    $result = $conn->query($sql);
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    echo json_encode($customers);
}

/** DAILY SPENDS **/
function getDailySpends() {
    global $conn;
    $startDate = isset($_GET['startDate']) ? $conn->real_escape_string($_GET['startDate']) : '';
    $endDate = isset($_GET['endDate']) ? $conn->real_escape_string($_GET['endDate']) : '';

    if ($startDate && $endDate) {
        $sql = "SELECT * FROM dailyspends WHERE created_at BETWEEN '$startDate' AND '$endDate'";
    } else {
        $sql = "SELECT * FROM dailyspends";
    }

    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}
?>
