<?php
// SIGNUP_API.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include('./includes/db_config.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Function to generate unique 4-digit code
function generateAccessCode($conn) {
    $code = null;
    $attempts = 0;
    $maxAttempts = 10;
    
    while ($attempts < $maxAttempts) {
        // Generate random 4-digit number with leading zeros if needed
        $code = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if code is already in use
        $check = $conn->prepare("SELECT COUNT(*) FROM users WHERE access_code = ?");
        $check->bind_param("s", $code);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();
        
        if ($count == 0) {
            return $code;
        }
        
        $attempts++;
    }
    
    // If we couldn't find a unique code, generate a longer one
    return uniqid();
}

// Function to send Discord webhook notification
function sendToDiscord($name, $phone, $accessCode) {
    $webhookUrl = "https://discord.com/api/webhooks/1447547861553123409/vtvGc_o09P6UZViWG6uV-MgXMsvk5bY812J8cZz3r39gMYA120jwUTm26mJHma-E38yR";
    
    // Create embed message
    $embed = [
        "title" => "🎯 New User Registration - Activation Code",
        "color" => 3447003, // Blue color
        "fields" => [
            [
                "name" => "👤 User Name",
                "value" => $name,
                "inline" => true
            ],
            [
                "name" => "📱 Phone Number",
                "value" => $phone,
                "inline" => true
            ],
            [
                "name" => "🔐 Activation Code",
                "value" => "**`" . $accessCode . "`**",
                "inline" => false
            ],
            [
                "name" => "📅 Registration Time",
                "value" => date("Y-m-d H:i:s"),
                "inline" => false
            ]
        ],
        "footer" => [
            "text" => "MSD Ventures - Panel Access System"
        ],
        "timestamp" => date("c")
    ];
    
    $data = [
        "embeds" => [$embed],
        "username" => "Registration Bot",
        "avatar_url" => "https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
    ];
    
    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode == 200 || $httpCode == 204;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate required fields
    $required_fields = ['name', 'phonenumber', 'password', 'referenceBy'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            echo json_encode(["status" => "error", "message" => ucfirst($field) . " is required"]);
            exit();
        }
    }

    // Extract data
    $name = trim($data['name']);
    $phone = trim($data['phonenumber']);
    $password = trim($data['password']);
    $referenceBy = trim($data['referenceBy']);
    $sex = isset($data['sex']) ? trim($data['sex']) : 'Male';
    $role = isset($data['role']) ? trim($data['role']) : 'customer';

    // Validate phone number (10 digits)
    if (!preg_match('/^\d{10}$/', $phone)) {
        echo json_encode(["status" => "error", "message" => "Phone number must be 10 digits"]);
        exit();
    }

    // Validate password length
    if (strlen($password) < 6) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters"]);
        exit();
    }

    // Check if phone number already exists
    $checkPhone = $conn->prepare("SELECT user_id FROM users WHERE phonenumber = ?");
    $checkPhone->bind_param("s", $phone);
    $checkPhone->execute();
    $checkPhone->store_result();
    
    if ($checkPhone->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Phone number already registered"]);
    exit();
}

    $checkPhone->close();

    // Check if reference code exists
    $checkRef = $conn->prepare("SELECT user_id FROM users WHERE reference_code = ?");
    $checkRef->bind_param("s", $referenceBy);
    $checkRef->execute();
    $checkRef->store_result();
    
    if ($checkRef->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid reference code"]);
        exit();
    }
    $checkRef->close();

    // Generate unique reference code for new user
   $referenceCode = $phone;


    // Generate email from phone number
    $email = $phone . '@msdventures.com';

    // Generate unique 4-digit access code
    $accessCode = generateAccessCode($conn);

    // REMOVED: Password hashing - keeping plain text as requested
    $plainPassword = $password;

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert new user with pannel_access = 0 and access_code
        $insertUser = $conn->prepare("INSERT INTO users (
            name, 
            emailaddress, 
            phonenumber, 
            role, 
            password, 
            reference_code, 
            reference_by,
            pannel_access,
            access_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)");
        
        $insertUser->bind_param("ssssssss", $name, $email, $phone, $role, $plainPassword, $referenceCode, $referenceBy, $accessCode);
        $insertUser->execute();
        
        if ($insertUser->affected_rows > 0) {
            $newUserId = $conn->insert_id;
            
            // ✅ REMOVED: No longer updating refered_investment_volume
            
            // Send to Discord (non-blocking - don't fail registration if Discord fails)
            try {
                sendToDiscord($name, $phone, $accessCode);
            } catch (Exception $discordError) {
                // Log Discord error but don't stop registration
                error_log("Discord webhook failed: " . $discordError->getMessage());
            }
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                "status" => "success", 
                "message" => "Registration successful",
                "data" => [
                    "user_id" => $newUserId,
                    "name" => $name,
                    "phonenumber" => $phone,
                    "reference_code" => $referenceCode,
                    "access_code" => $accessCode
                ]
            ]);
            
        } else {
            throw new Exception("Failed to create user account");
        }
        
        $insertUser->close();
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>