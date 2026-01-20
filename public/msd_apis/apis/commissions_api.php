<?php
// commissions_api.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include './includes/db_config.php';

// Validate Database Connection
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get method
$method = $_SERVER['REQUEST_METHOD'];

// Function to get commission data
function getCommissionData($conn) {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        http_response_code(400);
        return json_encode([
            "status" => false,
            "message" => "Invalid user ID"
        ]);
    }

    try {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $current_month = date('Y-m');
        $current_year = date('Y');

        // Get user info
        $stmt = $conn->prepare("
            SELECT 
                user_id,
                name,
                emailaddress,
                phonenumber,
                wallet_balance,
                refered_investment_volume,
                total_investment,
                current_rank
            FROM users 
            WHERE user_id = ?
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_info = $result->fetch_assoc();
        $stmt->close();

        if (!$user_info) {
            return json_encode([
                "status" => false,
                "message" => "User not found"
            ]);
        }

        // Get commission rates
        $rate_result = $conn->query("SELECT level, percentage FROM commission_rates ORDER BY level ASC");
        $commission_rates = [];
        while ($row = $rate_result->fetch_assoc()) {
            $commission_rates[$row['level']] = floatval($row['percentage']) * 100; // Convert to percentage
        }

        // Get all commission transactions
        $stmt = $conn->prepare("
            SELECT 
                wt.id,
                wt.transaction_type,
                wt.source_table,
                wt.source_id,
                wt.amount,
                wt.current_balance_after,
                wt.created_at,
                dpl.payout_date,
                dpl.calculated_amount as investment_payout,
                inv.amount as investment_amount,
                inv.user_id as investor_id,
                u.name as investor_name
            FROM wallet_transactions wt
            LEFT JOIN daily_payout_log dpl ON wt.source_id = dpl.id AND wt.source_table = 'referral_commission'
            LEFT JOIN investments inv ON dpl.investment_id = inv.id
            LEFT JOIN users u ON inv.user_id = u.user_id
            WHERE wt.user_id = ?
            AND wt.source_table = 'referral_commission'
            AND wt.transaction_type = 'CREDIT'
            ORDER BY wt.created_at DESC
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $all_commissions = [];
        
        while ($row = $result->fetch_assoc()) {
            $row['amount'] = floatval($row['amount']);
            $row['investment_amount'] = floatval($row['investment_amount']);
            $row['investment_payout'] = floatval($row['investment_payout']);
            
            // Calculate commission level
            if ($row['investment_payout'] > 0) {
                $commission_rate = ($row['amount'] / $row['investment_payout']) * 100;
                $row['commission_level'] = array_search(round($commission_rate, 2), $commission_rates) ?: 1;
            } else {
                $row['commission_level'] = 1;
            }
            
            $all_commissions[] = $row;
        }
        $stmt->close();

        // Calculate today's commissions
        $today_commissions = array_filter($all_commissions, function($commission) use ($today) {
            return date('Y-m-d', strtotime($commission['created_at'])) === $today;
        });

        // Calculate yesterday's commissions
        $yesterday_commissions = array_filter($all_commissions, function($commission) use ($yesterday) {
            return date('Y-m-d', strtotime($commission['created_at'])) === $yesterday;
        });

        // Calculate this month's commissions
        $monthly_commissions = array_filter($all_commissions, function($commission) use ($current_month) {
            return date('Y-m', strtotime($commission['created_at'])) === $current_month;
        });

        // Calculate this year's commissions
        $yearly_commissions = array_filter($all_commissions, function($commission) use ($current_year) {
            return date('Y', strtotime($commission['created_at'])) === $current_year;
        });

        // Group commissions by level
        $commissions_by_level = [];
        foreach ($all_commissions as $commission) {
            $level = $commission['commission_level'];
            if (!isset($commissions_by_level[$level])) {
                $commissions_by_level[$level] = [
                    'level' => $level,
                    'percentage' => $commission_rates[$level] ?? 0,
                    'total_amount' => 0,
                    'transaction_count' => 0,
                    'latest_date' => $commission['created_at']
                ];
            }
            $commissions_by_level[$level]['total_amount'] += $commission['amount'];
            $commissions_by_level[$level]['transaction_count']++;
            
            // Keep latest date
            if (strtotime($commission['created_at']) > strtotime($commissions_by_level[$level]['latest_date'])) {
                $commissions_by_level[$level]['latest_date'] = $commission['created_at'];
            }
        }

        // Sort by level
        ksort($commissions_by_level);

        // Get daily commission trend (last 7 days)
        $seven_days_ago = date('Y-m-d', strtotime('-6 days'));
        
        $stmt = $conn->prepare("
            SELECT 
                DATE(wt.created_at) as commission_date,
                SUM(wt.amount) as daily_total,
                COUNT(wt.id) as transaction_count
            FROM wallet_transactions wt
            WHERE wt.user_id = ?
            AND wt.source_table = 'referral_commission'
            AND wt.transaction_type = 'CREDIT'
            AND DATE(wt.created_at) >= ?
            GROUP BY DATE(wt.created_at)
            ORDER BY commission_date DESC
        ");
        
        $stmt->bind_param("is", $user_id, $seven_days_ago);
        $stmt->execute();
        $result = $stmt->get_result();
        $daily_trend = [];
        
        while ($row = $result->fetch_assoc()) {
            $daily_trend[] = [
                'date' => $row['commission_date'],
                'amount' => floatval($row['daily_total']),
                'count' => intval($row['transaction_count'])
            ];
        }
        $stmt->close();

        // Fill missing days with zero values
        $complete_trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $found = false;
            
            foreach ($daily_trend as $day) {
                if ($day['date'] === $date) {
                    $complete_trend[] = $day;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $complete_trend[] = [
                    'date' => $date,
                    'amount' => 0,
                    'count' => 0
                ];
            }
        }

        // Calculate statistics
        $total_commissions = array_sum(array_column($all_commissions, 'amount'));
        $today_total = array_sum(array_column($today_commissions, 'amount'));
        $yesterday_total = array_sum(array_column($yesterday_commissions, 'amount'));
        $monthly_total = array_sum(array_column($monthly_commissions, 'amount'));
        $yearly_total = array_sum(array_column($yearly_commissions, 'amount'));
        $average_daily = count($all_commissions) > 0 ? $total_commissions / count(array_unique(array_column($all_commissions, 'payout_date'))) : 0;
        $largest_commission = count($all_commissions) > 0 ? max(array_column($all_commissions, 'amount')) : 0;

        // Find largest commission details
        $largest_commission_details = null;
        if ($largest_commission > 0) {
            foreach ($all_commissions as $commission) {
                if ($commission['amount'] == $largest_commission) {
                    $largest_commission_details = $commission;
                    break;
                }
            }
        }

        // Get top investors (by commission generated)
        $stmt = $conn->prepare("
            SELECT 
                inv.user_id as investor_id,
                u.name as investor_name,
                COUNT(DISTINCT dpl.id) as investment_count,
                SUM(wt.amount) as total_commission,
                MAX(wt.created_at) as latest_commission_date
            FROM wallet_transactions wt
            JOIN daily_payout_log dpl ON wt.source_id = dpl.id
            JOIN investments inv ON dpl.investment_id = inv.id
            JOIN users u ON inv.user_id = u.user_id
            WHERE wt.user_id = ?
            AND wt.source_table = 'referral_commission'
            AND wt.transaction_type = 'CREDIT'
            GROUP BY inv.user_id, u.name
            ORDER BY total_commission DESC
            LIMIT 5
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $top_investors = [];
        
        while ($row = $result->fetch_assoc()) {
            $top_investors[] = [
                'investor_id' => $row['investor_id'],
                'name' => $row['investor_name'],
                'investment_count' => intval($row['investment_count']),
                'total_commission' => floatval($row['total_commission']),
                'latest_date' => $row['latest_commission_date']
            ];
        }
        $stmt->close();

        return json_encode([
            "status" => true,
            "data" => [
                "user_info" => [
                    "user_id" => $user_info['user_id'],
                    "name" => $user_info['name'],
                    "email" => $user_info['emailaddress'],
                    "phone" => $user_info['phonenumber'],
                    "wallet_balance" => floatval($user_info['wallet_balance']),
                    "refered_investment" => floatval($user_info['refered_investment_volume']),
                    "total_investment" => floatval($user_info['total_investment']),
                    "current_rank" => $user_info['current_rank']
                ],
                "statistics" => [
                    "total_commissions" => $total_commissions,
                    "today_commissions" => $today_total,
                    "yesterday_commissions" => $yesterday_total,
                    "monthly_commissions" => $monthly_total,
                    "yearly_commissions" => $yearly_total,
                    "average_daily" => $average_daily,
                    "largest_commission" => $largest_commission,
                    "total_transactions" => count($all_commissions),
                    "today_transactions" => count($today_commissions),
                    "monthly_transactions" => count($monthly_commissions)
                ],
                "commission_breakdown" => [
                    "by_level" => array_values($commissions_by_level),
                    "daily_trend" => $complete_trend,
                    "commission_rates" => $commission_rates
                ],
                "largest_commission_details" => $largest_commission_details,
                "top_investors" => $top_investors,
                "recent_commissions" => array_slice($all_commissions, 0, 10), // Last 10 transactions
                "time_periods" => [
                    "today" => $today,
                    "yesterday" => $yesterday,
                    "current_month" => date('F Y'),
                    "current_year" => $current_year,
                    "seven_days_ago" => $seven_days_ago
                ]
            ],
            "message" => "Commission data retrieved successfully"
        ]);

    } catch (Exception $e) {
        return json_encode([
            "status" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
}

// Route requests
switch ($method) {
    case 'GET':
        echo getCommissionData($conn);
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            "status" => false,
            "message" => "Method not allowed"
        ]);
        break;
}

$conn->close();
?>