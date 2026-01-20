<?php
// calculate_adjusted_plan.php - Final fixed version
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/db_config.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { 
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 1; 
$daily_rate = 0.0025;

// 1. Get Total Investment Amount
$stmt = $conn->prepare("SELECT SUM(amount) AS total_inv FROM investments WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$total_investment = $row['total_inv'] ? (float)$row['total_inv'] : 0.0;
$stmt->close();

if ($total_investment == 0) {
    die(json_encode(["error" => "No investments found for User ID: " . $user_id]));
}

// 2. Get Total Paid (From Logs)
$stmt = $conn->prepare("SELECT SUM(calculated_amount) AS total_paid_logged FROM daily_payout_log WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$total_paid = $row['total_paid_logged'] ? (float)$row['total_paid_logged'] : 0.0;
$stmt->close();

// 3. Calculate Days Completed (Calendar days since first investment, INCLUSIVE)
$stmt = $conn->prepare("SELECT MIN(created_at) AS first_inv_date FROM investments WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$first_inv_date_str = $row['first_inv_date'];
$stmt->close();

$first_inv_dt = new DateTime($first_inv_date_str);
$first_inv_dt->setTime(0,0,0);

// Match payout logic: payouts start from NEXT day
$first_inv_dt->modify('+1 day');

// Calculate actual calendar days from day after investment to TODAY (INCLUSIVE)
$today_dt = new DateTime();
$today_dt->setTime(0,0,0);

// Ensure we don't count future days
if ($first_inv_dt > $today_dt) {
    $total_days_passed = 0;
} else {
    // +1 to make it inclusive (count both start and end)
    $total_days_passed = $today_dt->diff($first_inv_dt)->days + 1;
}

// 4. Forecast Calculations
$remaining_payout = $total_investment - $total_paid;
$new_daily_payout = $total_investment * $daily_rate;

$remaining_days_raw = ($new_daily_payout > 0) ? $remaining_payout / $new_daily_payout : 0;

// *** MODIFICATION START ***
// Round the remaining days down to the nearest whole number (integer) using floor()
$remaining_days = floor($remaining_days_raw);
// *** MODIFICATION END ***

$final_end_day = $total_days_passed + $remaining_days;

// 5. Save Summary using INSERT ... ON DUPLICATE KEY UPDATE
// NOTE: We change the SQL placeholder and bind type for remaining_days 
// from DECIMAL(10,2) to INT(11) to match the floor() calculation result. 
// However, the column type in the DDL is DECIMAL(10,2), so we will bind 
// it as a double/float (d) to ensure compatibility.
$stmt = $conn->prepare("
    INSERT INTO payout_summary
    (user_id, total_investment, days_completed, total_paid, remaining_payout, daily_payout, remaining_days, final_end_day)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        total_investment = VALUES(total_investment),
        days_completed = VALUES(days_completed),
        total_paid = VALUES(total_paid),
        remaining_payout = VALUES(remaining_payout),
        daily_payout = VALUES(daily_payout),
        remaining_days = VALUES(remaining_days),
        final_end_day = VALUES(final_end_day),
        updated_at = CURRENT_TIMESTAMP
");

// NOTE: Changed binding for remaining_days and final_end_day to 'd' 
// as they are calculated floats/integers derived from math operations.
$stmt->bind_param(
    "iidddddd", 
    $user_id, 
    $total_investment,
    $total_days_passed,
    $total_paid,
    $remaining_payout,
    $new_daily_payout,
    $remaining_days, // Now an integer (float representation)
    $final_end_day
);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Payout summary created/updated successfully in payout_summary table.",
        "user_id" => $user_id,
        "total_investment" => round($total_investment, 2),
        "days_completed" => $total_days_passed,
        "amount_paid_by_system" => round($total_paid, 2),
        "remaining_payout" => round($remaining_payout, 2),
        "new_daily_payout" => round($new_daily_payout, 2),
        // *** MODIFICATION START: remaining_days is now an integer ***
        "remaining_days" => (int)$remaining_days, // Cast to int for output clarity
        "final_end_day" => round($final_end_day, 0) // Round final end day for consistency
        // *** MODIFICATION END ***
    ]);
} else {
    echo json_encode(["error" => "SQL Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>