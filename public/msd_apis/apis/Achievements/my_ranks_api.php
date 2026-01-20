<?php
// myrank_api.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

include('../includes/db_config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Get user ID from query parameter OR from Authorization header
$user_id = 0;

// Method 1: From query parameter
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
}

// Method 2: From Authorization header (for token-based auth)
if ($user_id <= 0 && isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    // Extract token and decode if needed
    // This depends on your token structure
}

// Method 3: From POST data
if ($user_id <= 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['user_id'])) {
        $user_id = intval($input['user_id']);
    }
}

if ($user_id <= 0) {
    die(json_encode([
        'success' => false,
        'error' => 'Invalid user ID. Please provide a valid user_id parameter.',
        'received_id' => $_GET['user_id'] ?? 'none'
    ]));
}

// Get all ranks from rank_thresholds
$ranks_query = "SELECT 
    id, 
    rank_name, 
    required_direct_investment 
    FROM rank_thresholds 
    ORDER BY required_direct_investment ASC";
    
$ranks_result = $conn->query($ranks_query);
if (!$ranks_result) {
    die(json_encode(['success' => false, 'error' => 'Failed to fetch ranks']));
}

$all_ranks = [];
$rank_thresholds = [];

while ($row = $ranks_result->fetch_assoc()) {
    $all_ranks[] = [
        'id' => $row['id'],
        'rank_name' => $row['rank_name'],
        'required_volume' => floatval($row['required_direct_investment']),
        'required_volume_formatted' => '₹' . number_format($row['required_direct_investment'], 2)
    ];
    $rank_thresholds[$row['rank_name']] = floatval($row['required_direct_investment']);
}

// Add Customer as base rank if not already present
$customerRankExists = false;
foreach ($all_ranks as $rank) {
    if ($rank['rank_name'] === 'Customer') {
        $customerRankExists = true;
        break;
    }
}

if (!$customerRankExists) {
    array_unshift($all_ranks, [
        'id' => 0,
        'rank_name' => 'Customer',
        'required_volume' => 0,
        'required_volume_formatted' => '₹0.00'
    ]);
    $rank_thresholds['Customer'] = 0;
}

// Sort by required volume
usort($all_ranks, function($a, $b) {
    return $a['required_volume'] <=> $b['required_volume'];
});

// Get user's current data
$user_query = "SELECT 
    name, 
    current_rank, 
    refered_investment_volume,
    user_id
    FROM users 
    WHERE user_id = ?";
    
$stmt = $conn->prepare($user_query);
if (!$stmt) {
    die(json_encode(['success' => false, 'error' => 'Database query preparation failed']));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    $stmt->close();
    die(json_encode([
        'success' => false, 
        'error' => 'User not found with ID: ' . $user_id,
        'suggestions' => 'Please check if the user exists in the database'
    ]));
}

$user_data = $user_result->fetch_assoc();
$stmt->close();

$current_volume = floatval($user_data['refered_investment_volume']);
$current_rank = $user_data['current_rank'];

// ===========================================
// CRITICAL: Update user's rank based on thresholds
// ===========================================
$new_rank = 'Customer'; // Default rank

// Find the highest rank the user qualifies for
foreach ($all_ranks as $rank) {
    if ($current_volume >= $rank['required_volume']) {
        $new_rank = $rank['rank_name'];
    } else {
        break; // Stop when volume is less than required
    }
}

// If current rank is different, update it
if ($new_rank !== $current_rank) {
    $update_query = "UPDATE users SET current_rank = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_rank, $user_id);
    $update_stmt->execute();
    
    if ($update_stmt->affected_rows > 0) {
        // Rank was updated
        $current_rank = $new_rank;
    }
    $update_stmt->close();
}

// Find user's current rank index and calculate progress
$current_rank_index = -1;
$next_rank = null;

foreach ($all_ranks as $index => $rank) {
    if ($rank['rank_name'] === $current_rank) {
        $current_rank_index = $index;
        
        // Get next rank if exists
        if (isset($all_ranks[$index + 1])) {
            $next_rank = $all_ranks[$index + 1];
        }
        break;
    }
}

// If current rank not found in list (shouldn't happen with update), set to first rank
if ($current_rank_index === -1) {
    $current_rank_index = 0;
    $current_rank = $all_ranks[0]['rank_name'];
}

// Calculate progress percentage
$progress_percentage = 0;
if ($next_rank) {
    $current_rank_volume = $all_ranks[$current_rank_index]['required_volume'];
    $next_rank_volume = $next_rank['required_volume'];
    
    if ($next_rank_volume > $current_rank_volume) {
        $progress_percentage = round(
            (($current_volume - $current_rank_volume) / 
             ($next_rank_volume - $current_rank_volume)) * 100, 
            2
        );
        
        if ($progress_percentage > 100) {
            $progress_percentage = 100;
        } elseif ($progress_percentage < 0) {
            $progress_percentage = 0;
        }
    }
}

// Prepare response
$response = [
    'success' => true,
    'user_info' => [
        'name' => $user_data['name'],
        'user_id' => $user_data['user_id'],
        'current_rank' => $current_rank,
        'referral_volume' => $current_volume,
        'referral_volume_formatted' => '₹' . number_format($current_volume, 2)
    ],
    'progress' => [
        'current_rank_index' => $current_rank_index,
        'progress_percentage' => $progress_percentage,
        'next_rank' => $next_rank ? [
            'name' => $next_rank['rank_name'],
            'required_volume' => $next_rank['required_volume'],
            'required_volume_formatted' => $next_rank['required_volume_formatted'],
            'volume_needed' => max(0, $next_rank['required_volume'] - $current_volume),
            'volume_needed_formatted' => '₹' . number_format(max(0, $next_rank['required_volume'] - $current_volume), 2)
        ] : null
    ],
    'all_ranks' => $all_ranks,
    'total_ranks' => count($all_ranks),
    'rank_updated' => ($new_rank !== $user_data['current_rank']),
    'previous_rank' => $user_data['current_rank']
];

echo json_encode($response);
$conn->close();
?>