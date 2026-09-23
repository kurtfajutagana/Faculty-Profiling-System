<?php
// At the VERY TOP of your file, before any output
session_start();
require_once '../db_connection.php';

// Enable error reporting but don't display errors to users
error_reporting(E_ALL);
ini_set('display_errors', 0); // Changed to 0 to prevent HTML output
ini_set('log_errors', 1);

// Set headers first
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    if ($json === false) {
        throw new Exception('Failed to read input data');
    }
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $required = ['faculty_id', 'full_name', 'email', 'college_id'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception(ucfirst($field).' is required');
        }
    }

    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Validate faculty ID format (XX-XXXXX)
    if (!preg_match('/^\d{2}-\d{5}$/', $data['faculty_id'])) {
        throw new Exception('Faculty ID must be in format XX-XXXXX');
    }

    // Begin transaction
    $conn->begin_transaction();

    // Check for duplicates
    $checkSql = "SELECT faculty_id FROM faculty WHERE faculty_id = ? OR email = ?";
    $checkStmt = $conn->prepare($checkSql);
    if (!$checkStmt) {
        throw new Exception("Prepare failed: ".$conn->error);
    }
    
    $checkStmt->bind_param("ss", $data['faculty_id'], $data['email']);
    if (!$checkStmt->execute()) {
        throw new Exception("Execute failed: ".$checkStmt->error);
    }
    
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows > 0) {
        throw new Exception("Faculty ID or email already exists");
    }

    // Insert faculty
    $facultySql = "INSERT INTO faculty (faculty_id, college_id, full_name, email, employment_type, specialization, contact_number, status) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $facultyStmt = $conn->prepare($facultySql);
    if (!$facultyStmt) {
        throw new Exception("Prepare failed: ".$conn->error);
    }

            $employmentType = $data['employment_type'] ?? 'Full-Time';
        $specialization = $data['specialization'] ?? null;
        $contactNumber  = $data['contact_number'] ?? null;
        $status         = $data['status'] ?? 'Active';

        $facultyStmt->bind_param(
            "sissssss",
            $data['faculty_id'],
            $data['college_id'],
            $data['full_name'],
            $data['email'],
            $employmentType,
            $specialization,
            $contactNumber,
            $status
        );

    if (!$facultyStmt->execute()) {
        throw new Exception("Execute failed: ".$facultyStmt->error);
    }
    $facultyStmt->close();

    // Initialize personal info record
    $gender = !empty($data['gender']) ? $data['gender'] : null;
    $pInfoSql = "INSERT INTO faculty_personal_info (faculty_id, gender) VALUES (?, ?) 
                 ON DUPLICATE KEY UPDATE gender = COALESCE(VALUES(gender), gender)";
    $pInfoStmt = $conn->prepare($pInfoSql);
    if ($pInfoStmt) {
        $pInfoStmt->bind_param("ss", $data['faculty_id'], $gender);
        $pInfoStmt->execute();
        $pInfoStmt->close();
    }

    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Faculty added successfully',
        'faculty_id' => $data['faculty_id']
    ]);
    exit();

} catch (Exception $e) {
    // Ensure any open transaction is rolled back
    if (isset($conn) && method_exists($conn, 'rollback')) {
        $conn->rollback();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Faculty addition error: ".$e->getMessage());
    exit();
}
?>