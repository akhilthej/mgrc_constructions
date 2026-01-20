<?php
// Include database configuration
include '../includes/db_config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: $SecureURL");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Define all valid roles and fields
$valid_roles = ['superadmin', 'admin', 'manager', 'sales_executive', 'project_manager', 'employee', 'accounts', 'hr', 'support', 'inventory_manager', 'customer', 'vendor'];
$valid_fields = [
    'showInvoices', 'showDownload', 'showSpend', 'showCustomers', 'showEmployees', 
    'showProducts', 'showLeads', 'showInventory', 'showGoogleMap', 'showCoupons', 
    'showAccounts', 'showWorkflow', 'showReminder', 'showSettings'
];

// =====================================================
// GET METHOD - FIXED FOR GLOBAL OVERRIDE
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch ALL settings
        $sql = "SELECT * FROM theme_settings";
        $result = $conn->query($sql);

        $output = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $roletype = $row['roletype'];
                unset($row['id'], $row['created_at'], $row['updated_at']);
                
                // Initialize all fields with default values
                foreach ($valid_fields as $field) {
                    if (!isset($row[$field])) {
                        $row[$field] = 'on';
                    }
                }
                
                $output[$roletype] = $row;
            }
        }

        // Ensure all roles exist with default values
        foreach ($valid_roles as $role) {
            if (!isset($output[$role])) {
                $output[$role] = [];
                foreach ($valid_fields as $field) {
                    $output[$role][$field] = 'on';
                }
            }
        }

        echo json_encode($output, JSON_PRETTY_PRINT);

    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// =====================================================
// PUT METHOD (Update) - SIMPLIFIED AND FIXED
// =====================================================
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !is_array($data)) {
            echo json_encode(['error' => 'Invalid input']);
            exit();
        }

        $response = ['success' => true, 'updated' => []];

        // ✅ Check if superadmin is turning off any features globally
        $globallyDisabledFeatures = [];
        if (isset($data['superadmin']) && is_array($data['superadmin'])) {
            foreach ($data['superadmin'] as $field => $value) {
                if (in_array($field, $valid_fields) && $value === 'off') {
                    $globallyDisabledFeatures[$field] = true;
                }
            }
        }

        // Handle role-specific updates
        foreach ($data as $roletype => $settings) {
            if ($roletype === 'success' || $roletype === 'updated') {
                continue;
            }

            // Validate role
            if (!in_array($roletype, $valid_roles)) {
                continue;
            }

            // Filter and validate settings
            $valid_settings = [];
            foreach ($settings as $key => $value) {
                if (in_array($key, $valid_fields)) {
                    // ✅ If feature is globally disabled by superadmin, force it to be OFF
                    if (isset($globallyDisabledFeatures[$key])) {
                        $valid_settings[$key] = 'off';
                    } else {
                        $valid_settings[$key] = in_array($value, ['on', 'off']) ? $value : 'on';
                    }
                }
            }

            if (!empty($valid_settings)) {
                updateRoleSettings($conn, $roletype, $valid_settings);
                $response['updated'][$roletype] = $valid_settings;
            }
        }

        // ✅ If superadmin turned off any features, ensure they are off for ALL roles
        foreach ($globallyDisabledFeatures as $field => $isDisabled) {
            foreach ($valid_roles as $role) {
                if ($role !== 'superadmin') {
                    $current_settings = getCurrentRoleSettings($conn, $role);
                    $current_settings[$field] = 'off';
                    updateRoleSettings($conn, $role, $current_settings);
                    $response['updated'][$role] = $current_settings;
                }
            }
        }

        if (empty($response['updated'])) {
            echo json_encode(['error' => 'No valid settings to update']);
            exit();
        }

        echo json_encode($response);

    } catch (Exception $e) {
        echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
    }
}

// =====================================================
// Helper function to get current role settings
// =====================================================
function getCurrentRoleSettings($conn, $roletype) {
    $sql = "SELECT * FROM theme_settings WHERE roletype = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $roletype);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $settings = [];
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $settings = $row;
        unset($settings['id'], $settings['roletype'], $settings['created_at'], $settings['updated_at']);
    }
    
    $stmt->close();
    return $settings;
}

// =====================================================
// Helper function to update role settings
// =====================================================
function updateRoleSettings($conn, $roletype, $settings) {
    // Check if record exists
    $check_sql = "SELECT id FROM theme_settings WHERE roletype = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $roletype);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing record
        $fields = [];
        $types = "";
        $values = [];
        
        foreach ($settings as $key => $value) {
            $fields[] = "$key = ?";
            $types .= "s";
            $values[] = $value;
        }
        
        $sql = "UPDATE theme_settings SET " . implode(", ", $fields) . " WHERE roletype = ?";
        $types .= "s";
        $values[] = $roletype;
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();
        
    } else {
        // Insert new record
        $fields = ['roletype'];
        $placeholders = ['?'];
        $types = "s";
        $values = [$roletype];
        
        foreach ($settings as $key => $value) {
            $fields[] = $key;
            $placeholders[] = "?";
            $types .= "s";
            $values[] = $value;
        }
        
        $sql = "INSERT INTO theme_settings (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();
    }
    
    $check_stmt->close();
}

$conn->close();
?>