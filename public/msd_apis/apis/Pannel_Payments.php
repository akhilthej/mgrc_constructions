<?php
//pannel_payment.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include('./includes/db_config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status"=>"error","message"=>"DB connection failed"]);
    exit();
}

function sendActivationToDiscord($name, $phone, $code) {
    $webhook = "https://discord.com/api/webhooks/1447547861553123409/vtvGc_o09P6UZViWG6uV-MgXMsvk5bY812J8cZz3r39gMYA120jwUTm26mJHma-E38yR";

    $embed = [
        "title" => "✅ Panel Access Activated",
        "color" => 65280,
        "fields" => [
            ["name" => "👤 User", "value" => $name, "inline"=>true],
            ["name" => "📱 Phone", "value" => $phone, "inline"=>true],
            ["name" => "🔓 Code Used", "value" => "**`$code`**"],
            ["name" => "⏰ Time", "value" => date("Y-m-d H:i:s")]
        ],
        "timestamp" => date("c")
    ];

    $data = [
        "embeds" => [$embed],
        "username" => "Activation Bot"
    ];

    $c = curl_init($webhook);
    curl_setopt($c, CURLOPT_POST, 1);
    curl_setopt($c, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_TIMEOUT, 5);
    curl_exec($c);
    curl_close($c);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents("php://input"), true);

    if (!isset($body['activation_code'], $body['user_id'])) {
        echo json_encode(["status"=>"error","message"=>"Missing required fields"]);
        exit();
    }

    $code = trim($body['activation_code']);
    $userId = intval($body['user_id']);
    $phone = isset($body['phonenumber']) ? trim($body['phonenumber']) : '';

    if (!preg_match("/^\d{4}$/", $code)) {
        echo json_encode(["status"=>"error","message"=>"Invalid code format. Must be 4 digits."]);
        exit();
    }

    $conn->begin_transaction();

    try {
        // Check if user already has access
        $checkUser = $conn->prepare("SELECT pannel_access FROM users WHERE user_id = ?");
        $checkUser->bind_param("i", $userId);
        $checkUser->execute();
        $checkUser->bind_result($existingAccess);
        $checkUser->fetch();
        $checkUser->close();
        
        if ($existingAccess == 1) {
            echo json_encode(["status"=>"error","message"=>"Panel access already granted"]);
            exit();
        }

        // Check code exists & unused
        $chk = $conn->prepare("SELECT user_id FROM users WHERE access_code = ? AND pannel_access = 0");
        $chk->bind_param("s", $code);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows === 0) {
            // Check if code was already used
            $chkUsed = $conn->prepare("SELECT user_id FROM users WHERE access_code = ? AND pannel_access = 1");
            $chkUsed->bind_param("s", $code);
            $chkUsed->execute();
            $chkUsed->store_result();
            
            if ($chkUsed->num_rows > 0) {
                throw new Exception("This activation code has already been used");
            } else {
                throw new Exception("Invalid or expired activation code");
            }
        }
        $chk->close();

        // Update user - grant access and clear code
        $up = $conn->prepare("UPDATE users SET pannel_access = 1, access_code = NULL WHERE user_id = ?");
        $up->bind_param("i", $userId);
        $up->execute();

        if ($up->affected_rows === 0) {
            throw new Exception("Failed to activate panel access");
        }

        // Get updated user details
        $userQ = $conn->prepare("SELECT user_id, name, emailaddress, phonenumber, role, pannel_access FROM users WHERE user_id = ?");
        $userQ->bind_param("i", $userId);
        $userQ->execute();
        $userQ->bind_result($uid, $name, $email, $phone, $role, $pannelAccess);
        $userQ->fetch();
        $userQ->close();

        // Send Discord notification
        sendActivationToDiscord($name, $phone, $code);

        $conn->commit();

        // Return success with user data
        echo json_encode([
            "status" => "success",
            "message" => "Panel access activated successfully!",
            "user_data" => [
                "user_id" => $uid,
                "name" => $name,
                "emailaddress" => $email,
                "phonenumber" => $phone,
                "role" => $role,
                "pannel_access" => $pannelAccess
            ]
        ]);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        exit();
    }
}

echo json_encode(["status"=>"error","message"=>"Invalid request"]);
$conn->close();
?>