<?php
include '../includes/db_config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: $SecureURL");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");

$response = array();
$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        // Check if user_id is provided
        if (!isset($data->user_id)) {
            throw new Exception("User ID is required");
        }

        $user_id = $data->user_id;
        
        // Check if this is a profile update or bank details update
        $update_type = isset($data->update_type) ? $data->update_type : 'profile';
        
        if ($update_type === 'profile') {
            // PROFILE UPDATE
            $update_fields = array();
            $params = array();
            $types = "";

            // Check what fields need to be updated
            if (isset($data->name) && !empty(trim($data->name))) {
                $update_fields[] = "name = ?";
                $params[] = trim($data->name);
                $types .= "s";
            }

            if (isset($data->emailaddress) && !empty(trim($data->emailaddress))) {
                // Validate email format
                if (!filter_var($data->emailaddress, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Invalid email format");
                }
                
                // Check if email already exists (excluding current user)
                $check_email = $conn->prepare("SELECT user_id FROM users WHERE emailaddress = ? AND user_id != ?");
                $check_email->bind_param("si", $data->emailaddress, $user_id);
                $check_email->execute();
                $check_email->store_result();
                
                if ($check_email->num_rows > 0) {
                    throw new Exception("Email already exists");
                }
                
                $update_fields[] = "emailaddress = ?";
                $params[] = trim($data->emailaddress);
                $types .= "s";
            }

            if (isset($data->phonenumber) && !empty(trim($data->phonenumber))) {
                // Validate phone number (10 digits)
                if (!preg_match('/^\d{10}$/', $data->phonenumber)) {
                    throw new Exception("Phone number must be 10 digits");
                }
                
                // Check if phone already exists (excluding current user)
                $check_phone = $conn->prepare("SELECT user_id FROM users WHERE phonenumber = ? AND user_id != ?");
                $check_phone->bind_param("si", $data->phonenumber, $user_id);
                $check_phone->execute();
                $check_phone->store_result();
                
                if ($check_phone->num_rows > 0) {
                    throw new Exception("Phone number already exists");
                }
                
                $update_fields[] = "phonenumber = ?";
                $params[] = trim($data->phonenumber);
                $types .= "s";
            }

            if (isset($data->date_of_birth)) {
                // Validate date format if provided
                if (!empty(trim($data->date_of_birth))) {
                    $date = DateTime::createFromFormat('Y-m-d', $data->date_of_birth);
                    if (!$date || $date->format('Y-m-d') !== $data->date_of_birth) {
                        throw new Exception("Invalid date format. Use YYYY-MM-DD");
                    }
                    $update_fields[] = "date_of_birth = ?";
                    $params[] = $data->date_of_birth;
                    $types .= "s";
                } else {
                    $update_fields[] = "date_of_birth = NULL";
                }
            }

            if (isset($data->address)) {
                $update_fields[] = "address = ?";
                $params[] = trim($data->address);
                $types .= "s";
            }

            // Add user_id to params for WHERE clause
            $params[] = $user_id;
            $types .= "i";

            if (count($update_fields) > 0) {
                $sql = "UPDATE users SET " . implode(", ", $update_fields) . ", updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt === false) {
                    throw new Exception("Failed to prepare statement: " . $conn->error);
                }

                // Bind parameters dynamically
                $stmt->bind_param($types, ...$params);
                
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $response['status'] = 'success';
                        $response['message'] = 'Profile updated successfully';
                        
                        // Get updated user data
                        $select_sql = "SELECT user_id, name, emailaddress, phonenumber, date_of_birth, address, role, reference_code, created_at FROM users WHERE user_id = ?";
                        $select_stmt = $conn->prepare($select_sql);
                        $select_stmt->bind_param("i", $user_id);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        $user_data = $result->fetch_assoc();
                        
                        $response['data'] = $user_data;
                    } else {
                        $response['status'] = 'info';
                        $response['message'] = 'No changes made';
                    }
                } else {
                    throw new Exception("Failed to update profile: " . $stmt->error);
                }
                
                $stmt->close();
            } else {
                $response['status'] = 'info';
                $response['message'] = 'No fields to update';
            }
            
        } elseif ($update_type === 'bank') {
            // BANK DETAILS UPDATE
            
            // Check if bank account exists for user
            $check_sql = "SELECT id, allow_edit FROM user_bank_account WHERE user_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                // Create bank account entry if it doesn't exist
                $insert_sql = "INSERT INTO user_bank_account (user_id, bank_name, upi_id, bank_account_number, ifsc_code) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                
                $bank_name = isset($data->bank_name) ? trim($data->bank_name) : '';
                $upi_id = isset($data->upi_id) ? trim($data->upi_id) : '';
                $bank_account_number = isset($data->bank_account_number) ? trim($data->bank_account_number) : '';
                $ifsc_code = isset($data->ifsc_code) ? trim($data->ifsc_code) : '';
                
                $insert_stmt->bind_param("issss", $user_id, $bank_name, $upi_id, $bank_account_number, $ifsc_code);
                
                if ($insert_stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Bank details created successfully';
                } else {
                    throw new Exception("Failed to create bank details: " . $insert_stmt->error);
                }
                
                $insert_stmt->close();
            } else {
                $bank_account = $check_result->fetch_assoc();
                
                // Check if editing is allowed
                if ($bank_account['allow_edit'] == 0) {
                    throw new Exception("Bank details cannot be edited. Please contact support.");
                }
                
                // Validate and prepare update fields
                $update_fields = array();
                $params = array();
                $types = "";
                
                if (isset($data->bank_name)) {
                    $bank_name = trim($data->bank_name);
                    if (empty($bank_name)) {
                        throw new Exception("Bank name is required");
                    }
                    $update_fields[] = "bank_name = ?";
                    $params[] = $bank_name;
                    $types .= "s";
                }
                
                if (isset($data->upi_id)) {
                    $upi_id = trim($data->upi_id);
                    // Basic UPI validation
                    if (!empty($upi_id) && !preg_match('/^[\w\.\-]+@[\w]+$/', $upi_id)) {
                        throw new Exception("Invalid UPI ID format");
                    }
                    $update_fields[] = "upi_id = ?";
                    $params[] = $upi_id;
                    $types .= "s";
                }
                
                if (isset($data->bank_account_number)) {
                    $account_number = trim($data->bank_account_number);
                    if (empty($account_number)) {
                        throw new Exception("Bank account number is required");
                    }
                    if (!preg_match('/^\d{9,18}$/', $account_number)) {
                        throw new Exception("Invalid bank account number (9-18 digits required)");
                    }
                    $update_fields[] = "bank_account_number = ?";
                    $params[] = $account_number;
                    $types .= "s";
                }
                
                if (isset($data->ifsc_code)) {
                    $ifsc_code = trim($data->ifsc_code);
                    if (empty($ifsc_code)) {
                        throw new Exception("IFSC code is required");
                    }
                    
                    $ifsc_upper = strtoupper($ifsc_code);
                    
                    // Updated IFSC validation to match frontend
                    // Allow formats like: SBIN0001234 (11 chars), KKBK007704 (10 chars), etc.
                    // Standard pattern: 4 letters + 7 alphanumeric (11 total) OR 4 letters + 6 alphanumeric (10 total)
                    if (!preg_match('/^[A-Z]{4}[A-Z0-9]{6,7}$/', $ifsc_upper)) {
                        throw new Exception("Invalid IFSC code format (e.g., SBIN0001234, KKBK007704)");
                    }
                    
                    $update_fields[] = "ifsc_code = ?";
                    $params[] = $ifsc_upper;
                    $types .= "s";
                }
                
                if (count($update_fields) > 0) {
                    // Add ID for WHERE clause
                    $params[] = $bank_account['id'];
                    $types .= "i";
                    
                    $sql = "UPDATE user_bank_account SET " . implode(", ", $update_fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                    
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt === false) {
                        throw new Exception("Failed to prepare statement: " . $conn->error);
                    }
                    
                    $stmt->bind_param($types, ...$params);
                    
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $response['status'] = 'success';
                            $response['message'] = 'Bank details updated successfully';
                        } else {
                            $response['status'] = 'info';
                            $response['message'] = 'No changes made to bank details';
                        }
                    } else {
                        throw new Exception("Failed to update bank details: " . $stmt->error);
                    }
                    
                    $stmt->close();
                } else {
                    $response['status'] = 'info';
                    $response['message'] = 'No bank details to update';
                }
            }
            
            // Get updated bank details
            $select_sql = "SELECT bank_name, upi_id, bank_account_number, ifsc_code, allow_edit FROM user_bank_account WHERE user_id = ?";
            $select_stmt = $conn->prepare($select_sql);
            $select_stmt->bind_param("i", $user_id);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $bank_data = $result->fetch_assoc();
            
            $response['data'] = $bank_data;
            
        } else {
            throw new Exception("Invalid update type. Use 'profile' or 'bank'");
        }
        
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET REQUEST - Fetch user data or bank details
    try {
        if (!isset($_GET['user_id'])) {
            throw new Exception("User ID is required");
        }

        $user_id = intval($_GET['user_id']);
        $data_type = isset($_GET['type']) ? $_GET['type'] : 'profile';
        
        if ($data_type === 'profile') {
            // Get user profile data
            $sql = "SELECT user_id, name, emailaddress, phonenumber, date_of_birth, address, role, reference_code, created_at 
                    FROM users 
                    WHERE user_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                
                $response['status'] = 'success';
                $response['data'] = $user_data;
            } else {
                throw new Exception("User not found");
            }
            
            $stmt->close();
            
        } elseif ($data_type === 'bank') {
            // Get bank details
            $sql = "SELECT bank_name, upi_id, bank_account_number, ifsc_code, allow_edit, created_at, updated_at 
                    FROM user_bank_account 
                    WHERE user_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $bank_data = $result->fetch_assoc();
                
                $response['status'] = 'success';
                $response['data'] = $bank_data;
            } else {
                // Return empty structure if no bank details exist
                $response['status'] = 'success';
                $response['data'] = [
                    'bank_name' => '',
                    'upi_id' => '',
                    'bank_account_number' => '',
                    'ifsc_code' => '',
                    'allow_edit' => 1,
                    'created_at' => null,
                    'updated_at' => null
                ];
            }
            
            $stmt->close();
            
        } else {
            throw new Exception("Invalid data type. Use 'profile' or 'bank'");
        }
        
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
$conn->close();
?>