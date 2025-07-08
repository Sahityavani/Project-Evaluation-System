<?php
include('../db.php'); // Include your database connection
include('auth.php'); // Include authentication

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : '';
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';

    // Validate inputs
    if (empty($id) || empty($name) || empty($username) || empty($password)) {
        header('Location: add-dpegevaluator.php?error=All fields are required');
        exit;
    }

    // Check if username already exists
    $checkUsernameQuery = "SELECT * FROM dept_evaluators WHERE username = '$username'";
    $checkUsernameResult = $conn->query($checkUsernameQuery);

    if ($checkUsernameResult->num_rows > 0) {
        header('Location: add-dpegevaluator.php?error=Username already exists');
        exit;
    }

    // Check if ID already exists
    $checkIdQuery = "SELECT * FROM dept_evaluators WHERE evaluator_id = '$id'";
    $checkIdResult = $conn->query($checkIdQuery);

    if ($checkIdResult->num_rows > 0) {
        header('Location: add-dpegevaluator.php?error=Evaluator ID already exists');
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new evaluator into the database
    $sql = "INSERT INTO dept_evaluators (evaluator_id, name, username, password) VALUES ('$id', '$name', '$username', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        header('Location: add-dpegevaluator.php?success=Evaluator added successfully');
    } else {
        header('Location: add-dpegevaluator.php?error=' . $conn->error);
    }
    exit;
}
?>
