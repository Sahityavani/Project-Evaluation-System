<?php
include('../db.php'); // Include your database connection
include('../auth.php'); // Include authentication

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : '';
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';

    // Validate inputs
    if (empty($id) || empty($name) || empty($username) || empty($password)) {
        header('Location: add-mrgevaluator.php?error=All fields are required');
        exit;
    }

    // Check if username already exists
    $checkUsernameQuery = "SELECT * FROM evaluators WHERE username = '$username'";
    $checkUsernameResult = $conn->query($checkUsernameQuery);

    if ($checkUsernameResult->num_rows > 0) {
        header('Location: add-mrgevaluator.php?error=Username already exists');
        exit;
    }

    // Check if ID already exists
    $checkIdQuery = "SELECT * FROM evaluators WHERE evaluator_id = '$id'";
    $checkIdResult = $conn->query($checkIdQuery);

    if ($checkIdResult->num_rows > 0) {
        header('Location: add-mrgevaluator.php?error=Evaluator ID already exists');
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new evaluator into the database
    $sql = "INSERT INTO evaluators (evaluator_id, name, username, password) VALUES ('$id', '$name', '$username', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        header('Location: add-mrgevaluator.php?success=Evaluator added successfully');
    } else {
        header('Location: add-mrgevaluator.php?error=' . $conn->error);
    }
    exit;
}
?>
