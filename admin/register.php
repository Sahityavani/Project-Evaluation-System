<?php
include 'db.php'; // Ensure this file contains the database connection setup

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input data
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $type = trim($_POST['type']);

    // Basic validation
    if (empty($name) || empty($username) || empty($password) || empty($email) || empty($type)) {
        header('Location: sign-up.php?error=All fields are required.');
        exit();
    }

    // Check if the username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: sign-up.php?error=Username or email already exists.');
        exit();
    }
    $stmt->close();

    // Hash the password using bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the query to insert the new user
    $stmt = $conn->prepare("INSERT INTO users (name, username, password, email, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $username, $hashed_password, $email, $type);
    
    if ($stmt->execute()) {
        $stmt->close();
        header('Location: sign-up.php?success=Registration successful! You can now log in.');
    } else {
        $stmt->close();
        header('Location: sign-up.php?error=Registration failed. Please try again.');
    }

    $conn->close();
    exit();
}
?>
