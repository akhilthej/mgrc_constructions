<?php
/**
 * update_user_ranks.php - Optimized Final Version
 * Calculates the total investment volume brought in by a user's 
 * direct referrals and updates the user's rank (designation) 
 * based on the thresholds defined in the 'rank_thresholds' table.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- CONFIGURATION ---
// NOTE: Ensure these paths are correct in your environment
include('../includes/db_config.php');
include('../includes/config.php');

// Establish Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error);
}


// 1. Fetch Rank Thresholds (Highest required_direct_investment first)
$ranks = [];
$rank_sql = "SELECT rank_name, required_direct_investment FROM rank_thresholds ORDER BY required_direct_investment DESC";
$rank_res = $conn->query($rank_sql);

if ($rank_res === FALSE) {
    die("SQL Error fetching rank thresholds: " . $conn->error);
}

if ($rank_res->num_rows > 0) {
    while ($row = $rank_res->fetch_assoc()) {
        $ranks[] = $row;
    }
}
// Ensure 'Customer' is always the fallback/base rank
$ranks[] = ['rank_name' => 'Customer', 'required_direct_investment' => 0.00];


// 2. Calculate Total Direct Investment for ALL ACTIVE REFERRERS
// This query efficiently finds users who have AT LEAST ONE direct referral with an investment.
$user_rank_data_sql = "
    SELECT
        ref.user_id AS referrer_id,
        ref.reference_code AS referrer_code,
        SUM(inv.amount) AS total_direct_investment
    FROM
        investments AS inv 
    JOIN
        users AS referred_user ON inv.user_id = referred_user.user_id 
    JOIN 
        users AS ref ON referred_user.reference_by = ref.reference_code 
    WHERE
        ref.user_id IS NOT NULL 
        AND ref.user_id > 0 -- Exclude Company
    GROUP BY
        ref.user_id, ref.reference_code
";

$user_rank_data_res = $conn->query($user_rank_data_sql);

if ($user_rank_data_res === FALSE) {
    die("SQL Error calculating ranks: " . $conn->error);
}

// Store results in an array for quick lookup
$active_referrers_data = [];
while ($data = $user_rank_data_res->fetch_assoc()) {
    $active_referrers_data[(int)$data['referrer_id']] = (float)$data['total_direct_investment'];
}


// 3. Prepare Update Statement
// We use 'current_rank <> ?' to avoid redundant updates where possible.
$stmt_update_rank = $conn->prepare("
    UPDATE users 
    SET current_rank = ?, refered_investment_volume = ? 
    WHERE user_id = ? AND (current_rank IS NULL OR current_rank <> ? OR refered_investment_volume <> ?)
");

$updated_count = 0;

// 4. Loop through ALL Users (including those with 0 volume)
$all_users_sql = "SELECT user_id, name, current_rank, refered_investment_volume FROM users WHERE user_id > 0";
$all_users_res = $conn->query($all_users_sql);

if ($all_users_res === FALSE) {
    die("SQL Error fetching all users: " . $conn->error);
}

while ($user = $all_users_res->fetch_assoc()) {
    $user_id = (int)$user['user_id'];
    $current_rank = $user['current_rank'];
    $current_volume = (float)$user['refered_investment_volume'];
    $name = $user['name'];
    
    // Determine investment volume (0.00 if not in active_referrers_data)
    $investment_volume = $active_referrers_data[$user_id] ?? 0.00;
    
    // Default rank is 'Customer'
    $new_rank = 'Customer';
    
    // Find the highest rank the user qualifies for (ranks are in DESC order)
    foreach ($ranks as $rank) {
        if ($investment_volume >= (float)$rank['required_direct_investment']) {
            $new_rank = $rank['rank_name'];
            break; 
        }
    }
    
    // Check if an update is needed (rank or volume changed)
    if ($current_rank != $new_rank || $current_volume != $investment_volume) {
        
        $stmt_update_rank->bind_param("sdsid", $new_rank, $investment_volume, $user_id, $new_rank, $investment_volume);
        
        if ($stmt_update_rank->execute()) {
            if ($stmt_update_rank->affected_rows > 0) {
                echo "✅ User <strong>{$name}</strong> (ID: {$user_id}) - ";
                echo "Volume: ₹" . number_format($investment_volume, 2) . " - ";
                echo "New Rank: <strong>{$new_rank}</strong><br>";
                $updated_count++;
            } else {
                 // This means the row was checked but the data was already the desired value.
                echo "👉 User {$name} (ID: {$user_id}) - Data Confirmed.<br>";
            }
        } else {
            echo "❌ Error updating rank for User ID {$user_id}: " . $stmt_update_rank->error . "<br>";
        }
    } else {
        echo "👉 User {$name} (ID: {$user_id}) - Already up-to-date (Rank: {$current_rank}, Volume: ₹" . number_format($current_volume, 2) . ")<br>";
    }
}

// --- Cleanup ---
$stmt_update_rank->close();
$conn->close();

echo "<hr>✨ **Ranking Process Complete.** Total users updated: **{$updated_count}**";
?>