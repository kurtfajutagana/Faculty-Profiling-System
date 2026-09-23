<?php
require_once '../db_connection.php';
header('Content-Type: application/json');

if (!isset($_GET['faculty_id'])) {
    echo json_encode(['success' => false, 'message' => 'Faculty ID not provided']);
    exit();
}

$facultyId = $_GET['faculty_id'];

// Get the base URL dynamically
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

// Get verified credentials
$sql = "SELECT 
            credential_id as id, 
            credential_name as name, 
            credential_type as type, 
            issued_date, 
            expiry_date, 
            file_path,
            'Credential' as source_type
        FROM credentials 
        WHERE faculty_id = ? AND status = 'Verified'
        UNION ALL
        SELECT 
            load_id as id,
            file_name as name,
            'Teaching Load' as type,
            NULL as issued_date,
            NULL as expiry_date,
            file_path,
            'TeachingLoad' as source_type
        FROM teaching_load
        WHERE faculty_id = ? AND status = 'Verified'
        ORDER BY type, name";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $facultyId, $facultyId);
$stmt->execute();
$result = $stmt->get_result();

$credentials = [];
while ($row = $result->fetch_assoc()) {
    // Convert path to properly reference the auth_2/uploads directory
    $relativePath = '../auth_2/' . ltrim($row['file_path'], '/');
    $row['file_path'] = $relativePath;
    $credentials[] = $row;
}

echo json_encode([
    'success' => true,
    'credentials' => $credentials
]);
?>