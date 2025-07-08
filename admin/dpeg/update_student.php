<?php
include('../db.php');

// Get POST data
$rollno = isset($_POST['rollno']) ? $_POST['rollno'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : null;

// Validate input
if (!$rollno || !$name || !$email || !$mobile) {
    header('Location: edit-batch.php?error=All fields are required');
    exit();
}

// Prepare the SQL statement
$sql = "UPDATE students SET name = ?, email = ?, mobile = ? WHERE rollno = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    header('Location: edit-batch.php?error=Error updating student');
    exit();
}

// Bind parameters and execute
$stmt->bind_param('ssss', $name, $email, $mobile, $rollno); 

if ($stmt->execute()) {
    header('Location: edit-batch.php?success=Student updated successfully');
} else {
    header('Location: edit-batch.php?error=Error updating student');
}

$stmt->close();
$conn->close();
exit(); // Ensure script termination after redirect
?>
