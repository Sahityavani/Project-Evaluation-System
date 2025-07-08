<?php
include('../db.php');

// Fetch POST data
$id = $_POST['id'];
$name = $_POST['name'];
$section = $_POST['section'];
$rollno = strtoupper($_POST['rollno']); // Convert roll number to uppercase
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

// Check if a student with the same roll number already exists
$sql = "SELECT * FROM students WHERE rollno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $rollno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Student with the same roll number already exists
    header('Location: add-student.php?error=Student with this Roll Number already exists');
} else {
    // Insert new student into the database
    $sql = "INSERT INTO students (id, name, rollno, password, section) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $id, $name, $rollno, $password, $section);

    if ($stmt->execute()) {
        header('Location: add-student.php?success=Student added successfully');
    } else {
        header('Location: add-student.php?error=Failed to add student');
    }

    $stmt->close();
}

$conn->close();
?>
