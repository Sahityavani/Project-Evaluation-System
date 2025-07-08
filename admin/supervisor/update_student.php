<?php
include('../db.php');

// Set header to return JSON
header('Content-Type: application/json');

// Get POST data
$rollno = isset($_POST['rollno']) ? $_POST['rollno'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : null;

// Initialize response array
$response = array('status' => 'error', 'message' => 'An unexpected error occurred.');

// Validate input
if (!$rollno || !$name || !$email || !$mobile) {
    $response['message'] = 'All fields are required.';
    echo json_encode($response);
    exit();
}

// Prepare the SQL statement
$sql = "UPDATE students SET name = ?, email = ?, mobile = ? WHERE rollno = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Database prepare statement failed.';
    echo json_encode($response);
    exit();
}

// Bind parameters and execute
$stmt->bind_param('ssss', $name, $email, $mobile, $rollno);

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Student details have been updated.';
} else {
    $response['message'] = 'Failed to update student details.';
}

$stmt->close();
$conn->close();

// Send JSON response
echo json_encode($response);
?>
