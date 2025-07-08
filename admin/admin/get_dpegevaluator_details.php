<?php
include('../db.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : '';

    if (empty($id)) {
        echo json_encode(['error' => 'ID is required']);
        exit;
    }

    // Fetch evaluator details from the database
    $sql = "SELECT * FROM dept_evaluators WHERE evaluator_id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $evaluator = $result->fetch_assoc();
        
        // Return the evaluator details including the hashed password
        echo json_encode([
            'evaluator_id' => $evaluator['evaluator_id'],
            'name' => $evaluator['name'],
            'username' => $evaluator['username']
        ]);
    } else {
        echo json_encode(['error' => 'Evaluator not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
