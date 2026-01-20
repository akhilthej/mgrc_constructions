<?php
// users_CRUD.php - SHOW ALL NON-ADMIN USERS
date_default_timezone_set('Asia/Kolkata');
include '../includes/db_config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getUsers();
        break;
    case 'POST':
        addUser();
        break;
    case 'PUT':
        updateUser();
        break;
    case 'DELETE':
        deleteUser();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getUsers() {
    global $conn;

    // Fetch all non-admin users
    $sql = "
        SELECT 
            user_id,
            name,
            emailaddress,
            phonenumber,
            address,
            wallet_balance,
            refered_investment_volume,
            role,
            current_rank,
            total_investment,
            reference_code,
            reference_by,
            created_at
        FROM users
        WHERE role != 'admin'
        ORDER BY created_at DESC
    ";

    $result = $conn->query($sql);

    $users = [];

    while ($row = $result->fetch_assoc()) {
        // SEND BOTH FORMATS TO MATCH REACT CODE
        $row['email'] = $row['emailaddress'];
        $row['phone'] = $row['phonenumber'];

        $users[] = $row;
    }

    echo json_encode($users);
}

function addUser() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['name']) || empty($data['email']) || empty($data['phone'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing required fields"]);
        return;
    }

    $name = $conn->real_escape_string($data['name']);
    $email = $conn->real_escape_string($data['email']);
    $phone = $conn->real_escape_string($data['phone']);
    $password = $data['password'] ?? null;

    if (!$password) {
        http_response_code(400);
        echo json_encode(["message" => "Password required"]);
        return;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (name, emailaddress, phonenumber, password, role, created_at)
        VALUES (?, ?, ?, ?, 'customer', NOW())
    ");
    $stmt->bind_param("ssss", $name, $email, $phone, $hashed);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User added"]);
    } else {
        echo json_encode(["message" => "Insert failed"]);
    }
}

function updateUser() {
    global $conn;

    $user_id = intval($_GET['user_id']);
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$user_id) {
        echo json_encode(["message" => "User ID missing"]);
        return;
    }

    $fields = [];
    $params = [];
    $types = "";

    if (isset($data['name'])) {
        $fields[] = "name=?";
        $params[] = $data['name'];
        $types .= "s";
    }
    if (isset($data['email'])) {
        $fields[] = "emailaddress=?";
        $params[] = $data['email'];
        $types .= "s";
    }
    if (isset($data['phone'])) {
        $fields[] = "phonenumber=?";
        $params[] = $data['phone'];
        $types .= "s";
    }
    if (isset($data['role'])) {
        $fields[] = "role=?";
        $params[] = $data['role'];
        $types .= "s";
    }

    // Password update optional
    if (!empty($data['password'])) {
        $fields[] = "password=?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        $types .= "s";
    }

    $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE user_id=?";
    $types .= "i";
    $params[] = $user_id;

    $stmt = $conn->prepare($sql);

    $bind = [$types];
    foreach ($params as $k => $p) {
        $bind[] = &$params[$k];
    }

    call_user_func_array([$stmt, 'bind_param'], $bind);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Updated"]);
    } else {
        echo json_encode(["message" => "Update failed"]);
    }
}

function deleteUser() {
    global $conn;
    $user_id = intval($_GET['user_id']);

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role != 'admin'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo json_encode(["message" => "User deleted"]);
}

$conn->close();
