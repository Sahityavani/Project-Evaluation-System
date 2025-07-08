<?php
include('../db.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : '';
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';

    // Validate input
    if (empty($id) || empty($name) || empty($username)) {
        header('Location: add-dpegevaluator.php?error=All fields are required'); // Redirect to the previous page
        exit;
    }

    // Check if the evaluator exists
    $sql = "SELECT * FROM dept_evaluators WHERE evaluator_id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        header('Location: add-dpegevaluator.php?error=Evaluator not found'); // Redirect to the previous page
        exit;
    }

    // Update evaluator details
    $sql = "UPDATE dept_evaluators SET name = '$name', username = '$username' WHERE evaluator_id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: add-dpegevaluator.php?success=Evaluator updated successfully');
        exit;
    } else {
        header('Location: add-dpegevaluator.php?error=' . $conn->error);
    }

    $conn->close();
} else {
    header('Location: add-dpegevaluator.php?error=Error Requested Method');
    exit;
}
?>
