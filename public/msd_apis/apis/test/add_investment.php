<?php
// add_investment_api.php - REST API VERSION

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/db_config.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { 
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Set timezone if needed
date_default_timezone_set('Asia/Kolkata');

// Helper function to send JSON response
function sendResponse($status, $message, $data = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($data) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Handle phone number search API
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search_user') {
    if (!isset($_GET['phone']) || empty($_GET['phone'])) {
        http_response_code(400);
        sendResponse('error', 'Phone number is required');
    }
    
    $phone = trim($_GET['phone']);
    
    if (strlen($phone) !== 10 || !is_numeric($phone)) {
        http_response_code(400);
        sendResponse('error', 'Invalid phone number format. Must be 10 digits.');
    }
    
    // Search user by phone number
    $stmt = $conn->prepare("SELECT 
        user_id, 
        name, 
        emailaddress, 
        phonenumber, 
        wallet_balance, 
        reference_code, 
        reference_by,
        total_investment
    FROM users WHERE phonenumber = ?");
    
    if (!$stmt) {
        http_response_code(500);
        sendResponse('error', 'Database preparation failed: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        sendResponse('success', 'User found successfully', [
            'user_data' => $user_data
        ]);
    } else {
        sendResponse('error', 'No user found with phone number: ' . $phone);
    }
    
    $stmt->close();
    exit;
}

// Handle investment submission API
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'add_investment') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        sendResponse('error', 'Invalid JSON input');
    }
    
    $user_id = isset($input['user_id']) ? intval($input['user_id']) : 0;
    $amount = isset($input['amount']) ? floatval($input['amount']) : 0;
    
    // Validate inputs
    if ($amount <= 0 || $amount < 1000) {
        http_response_code(400);
        sendResponse('error', 'Investment amount must be at least ₹1000');
    }
    
    if ($user_id <= 0) {
        http_response_code(400);
        sendResponse('error', 'Invalid user ID');
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        $daily_rate = 0.0025;
        $initial_daily_payout = $amount * $daily_rate;
        
        // 1. Insert into investments table
        $stmt1 = $conn->prepare("INSERT INTO investments 
            (user_id, amount, daily_rate, initial_daily_payout, created_at)
            VALUES (?, ?, ?, ?, NOW())");
        
        if (!$stmt1) {
            throw new Exception("Investment insert preparation failed: " . $conn->error);
        }
        
        $stmt1->bind_param("iddd", $user_id, $amount, $daily_rate, $initial_daily_payout);
        
        if (!$stmt1->execute()) {
            throw new Exception("Investment insert failed: " . $stmt1->error);
        }
        
        $investment_id = $stmt1->insert_id;
        $stmt1->close();
        
        // 2. Update user's total_investment
        $stmt2 = $conn->prepare("UPDATE users 
            SET total_investment = total_investment + ?
            WHERE user_id = ?");
        
        if (!$stmt2) {
            throw new Exception("User update preparation failed: " . $conn->error);
        }
        
        $stmt2->bind_param("di", $amount, $user_id);
        
        if (!$stmt2->execute()) {
            throw new Exception("User update failed: " . $stmt2->error);
        }
        
        $stmt2->close();
        
        // 3. Get referral chain and calculate commissions
        $commission_calculated = false;
        
        // Get the user's referrer
        $stmt3 = $conn->prepare("SELECT reference_by FROM users WHERE user_id = ?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();
        $stmt3->bind_result($reference_by);
        $stmt3->fetch();
        $stmt3->close();
        
        if ($reference_by) {
            // Get referrer's user_id from reference_code
            $stmt4 = $conn->prepare("SELECT user_id FROM users WHERE reference_code = ?");
            $stmt4->bind_param("s", $reference_by);
            $stmt4->execute();
            $stmt4->bind_result($referrer_id);
            $stmt4->fetch();
            $stmt4->close();
            
            if ($referrer_id) {
                // Calculate commission (25% of daily payout for level 1)
                $commission_amount = $initial_daily_payout * 0.25;
                
                // Add commission to referrer's wallet
                $stmt5 = $conn->prepare("UPDATE users 
                    SET wallet_balance = wallet_balance + ? 
                    WHERE user_id = ?");
                $stmt5->bind_param("di", $commission_amount, $referrer_id);
                $stmt5->execute();
                $stmt5->close();
                
                // Log wallet transaction for commission
                $stmt6 = $conn->prepare("INSERT INTO wallet_transactions 
                    (user_id, transaction_type, source_table, source_id, amount, current_balance_after)
                    SELECT 
                        ?, 
                        'CREDIT', 
                        'referral_commission', 
                        ?, 
                        ?, 
                        wallet_balance 
                    FROM users 
                    WHERE user_id = ?");
                
                $stmt6->bind_param("iidi", $referrer_id, $investment_id, $commission_amount, $referrer_id);
                $stmt6->execute();
                $stmt6->close();
                
                // Update referrer's referred_investment_volume
                $stmt7 = $conn->prepare("UPDATE users 
                    SET refered_investment_volume = refered_investment_volume + ?
                    WHERE user_id = ?");
                $stmt7->bind_param("di", $amount, $referrer_id);
                $stmt7->execute();
                $stmt7->close();
                
                $commission_calculated = true;
            }
        }
        
        // 4. Update payout summary (call existing procedure if available, or update directly)
        $summary_updated = false;
        
        try {
            // Call the existing calculate_adjusted_plan.php
$summary_url = "http://" . $_SERVER['HTTP_HOST'] . "/apis/test/calculate_adjusted_plan.php?user_id=$user_id";
@file_get_contents($summary_url);
$summary_updated = true;
        } catch (Exception $e) {
            // Fallback: Direct update if the external script fails
            $stmt8 = $conn->prepare("
                INSERT INTO payout_summary 
                (user_id, total_investment, daily_payout, remaining_payout, remaining_days, final_end_day)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    total_investment = VALUES(total_investment),
                    daily_payout = VALUES(daily_payout),
                    remaining_payout = VALUES(remaining_payout),
                    remaining_days = VALUES(remaining_days),
                    final_end_day = VALUES(final_end_day),
                    updated_at = NOW()
            ");
            
            if ($stmt8) {
                // Calculate for 400 days payout (amount * 1)
                $total_payout = $amount; // 100% return
                $daily_payout = $initial_daily_payout;
                $remaining_payout = $total_payout; // Start with full amount
                $remaining_days = floor($total_payout / $daily_payout);
                $final_end_day = $remaining_days;
                
                $stmt8->bind_param("iddddd", 
                    $user_id, 
                    $amount, 
                    $daily_payout,
                    $remaining_payout,
                    $remaining_days,
                    $final_end_day
                );
                
                if ($stmt8->execute()) {
                    $summary_updated = true;
                }
                $stmt8->close();
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Prepare investment details for response
        $investment_details = [
            'id' => $investment_id,
            'user_id' => $user_id,
            'amount' => $amount,
            'daily_rate' => $daily_rate,
            'daily_payout' => $initial_daily_payout,
            'added_on' => date('Y-m-d H:i:s'),
            'commission_calculated' => $commission_calculated,
            'summary_updated' => $summary_updated
        ];
        
        // Return success response
        sendResponse('success', 'Investment added successfully', [
            'investment_details' => $investment_details
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        http_response_code(500);
        sendResponse('error', 'Transaction failed: ' . $e->getMessage());
    }
    
    exit;
}

// Handle invalid API calls
http_response_code(404);
sendResponse('error', 'Invalid API endpoint or method');

// Close connection
$conn->close();
?>