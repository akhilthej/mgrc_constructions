<?php
date_default_timezone_set('Asia/Kolkata');

// Include database configuration
include '../includes/db_config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: $SecureURL");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch settings
        $sql = "SELECT * FROM settings WHERE id = 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(['error' => 'No settings found']);
        }
        break;

    case 'POST':
        // First, get existing settings to preserve values not being updated
        $existingSettings = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();
        if (!$existingSettings) {
            echo json_encode(['error' => 'No settings found']);
            break;
        }

        // Update only the fields that are provided
        $companyName = isset($_POST['companyName']) ? $conn->real_escape_string($_POST['companyName']) : $existingSettings['companyName'];
        $companyPhone = isset($_POST['companyPhone']) ? $conn->real_escape_string($_POST['companyPhone']) : $existingSettings['companyPhone'];
        $companyPhone2 = isset($_POST['companyPhone2']) ? $conn->real_escape_string($_POST['companyPhone2']) : $existingSettings['companyPhone2'];
        $companyEmail = isset($_POST['companyEmail']) ? $conn->real_escape_string($_POST['companyEmail']) : $existingSettings['companyEmail'];
        $companyWebsite = isset($_POST['companyWebsite']) ? $conn->real_escape_string($_POST['companyWebsite']) : $existingSettings['companyWebsite'];
        $companyAddress = isset($_POST['companyAddress']) ? $conn->real_escape_string($_POST['companyAddress']) : $existingSettings['companyAddress'];
        $GSTnumber = isset($_POST['GSTnumber']) ? $conn->real_escape_string($_POST['GSTnumber']) : $existingSettings['GSTnumber'];
        $Tax = isset($_POST['Tax']) ? $conn->real_escape_string($_POST['Tax']) : $existingSettings['Tax'];
        $invoice_number = isset($_POST['invoice_number']) ? $conn->real_escape_string($_POST['invoice_number']) : $existingSettings['invoice_number'];
        $upi_id = isset($_POST['upi_id']) ? $conn->real_escape_string($_POST['upi_id']) : $existingSettings['upi_id'];
        
        // Handle tax_inclusive field - default to 'no' if not provided
        $tax_inclusive = isset($_POST['tax_inclusive']) ? $conn->real_escape_string($_POST['tax_inclusive']) : 
                        (isset($existingSettings['tax_inclusive']) ? $existingSettings['tax_inclusive'] : 'no');

        // Handle company logo upload
        $companylogo = $existingSettings['companylogo'];
        if (isset($_FILES['companylogo']) && $_FILES['companylogo']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['companylogo']['tmp_name'];
            $fileExtension = strtolower(pathinfo($_FILES['companylogo']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExts)) {
                $newFileName = 'CompanyLogo.' . $fileExtension;
                $uploadFileDir = './uploads/';
                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $companylogo = $baseurl . 'apis/uploads/' . $newFileName;
                } else {
                    echo json_encode(['error' => 'Error moving the uploaded company logo']);
                    exit;
                }
            } else {
                echo json_encode(['error' => 'Invalid company logo file type']);
                exit;
            }
        }

        // Use INSERT INTO ... ON DUPLICATE KEY UPDATE for inserting/updating settings
        $sql = "INSERT INTO settings (id, companyName, companyPhone, companyPhone2, companyEmail, companyWebsite, companyAddress, GSTnumber, Tax, companylogo, invoice_number, upi_id, tax_inclusive) 
                VALUES (1, '$companyName', '$companyPhone', '$companyPhone2', '$companyEmail', '$companyWebsite', '$companyAddress', '$GSTnumber', '$Tax', '$companylogo', '$invoice_number', '$upi_id', '$tax_inclusive') 
                ON DUPLICATE KEY UPDATE 
                companyName='$companyName', 
                companyPhone='$companyPhone', 
                companyPhone2='$companyPhone2', 
                companyEmail='$companyEmail', 
                companyWebsite='$companyWebsite', 
                companyAddress='$companyAddress', 
                GSTnumber='$GSTnumber', 
                Tax='$Tax', 
                companylogo='$companylogo', 
                invoice_number='$invoice_number', 
                upi_id='$upi_id',
                tax_inclusive='$tax_inclusive'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => 'Settings updated successfully']);
        } else {
            echo json_encode(['error' => 'Error updating settings: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        // Delete settings
        $sql = "DELETE FROM settings WHERE id = 1";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => 'Settings deleted successfully']);
        } else {
            echo json_encode(['error' => 'Error deleting settings: ' . $conn->error]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid request method']);
        break;
}

$conn->close();
?>