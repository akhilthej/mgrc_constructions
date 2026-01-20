<?php
// usercrud.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include './includes/db_config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: $SecureURL");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'POST':
        if (isset($data['identifier'], $data['password'])) {
            $identifier = $conn->real_escape_string($data['identifier']);
            $password = $conn->real_escape_string($data['password']);

            // Determine if identifier is email or phone number
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                // ADD pannel_access to SELECT query
                $sql = "SELECT user_id, name, emailaddress, phonenumber, role, address, password, pannel_access,reference_code FROM users WHERE emailaddress = '$identifier'";
            } else {
                // ADD pannel_access to SELECT query
                $sql = "SELECT user_id, name, emailaddress, phonenumber, role, address, password, pannel_access,reference_code FROM users WHERE phonenumber = '$identifier'";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Validate password
                if ($user['password'] === $password) {
                    // Create session token
                    $sessionToken = bin2hex(random_bytes(32));
                    $ipAddress = $_SERVER['REMOTE_ADDR'];
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];

                    // Delete any existing sessions for this user
                    $deleteStmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ?");
                    $deleteStmt->bind_param("i", $user['user_id']);
                    $deleteStmt->execute();
                    $deleteStmt->close();

                    // Expire after 24 hours
                    $expiresAt = date("Y-m-d H:i:s", time() + (24 * 60 * 60));

                    // Create new session
                    $stmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("issss", $user['user_id'], $sessionToken, $ipAddress, $userAgent, $expiresAt);

                    if ($stmt->execute()) {
                        echo json_encode([
                            "status" => "success",
                            "message" => "Login successful",
                            "session_token" => $sessionToken,
                            "data" => [
                                "user_id" => $user['user_id'],
                                "name" => $user['name'],
                                "emailaddress" => $user['emailaddress'],
                                "phonenumber" => $user['phonenumber'],
                                "role" => $user['role'],
                                "address" => $user['address'],
                                "pannel_access" => $user['pannel_access'] ,
                                "reference_code" => $user['reference_code'],
                            ]
                        ]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Session creation failed"]);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Identifier and password are required"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        break;
}

$conn->close();
?>