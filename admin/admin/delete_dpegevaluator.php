<?php
include('../db.php'); // Include your database connection
include('../auth.php'); // Include authentication

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : '';

    if (empty($id)) {
        echo json_encode(['error' => 'ID is required']);
        exit;
    }

    // Check if the evaluator is associated with an DPEG
    $checkDpegQuery = "SELECT COUNT(*) as count FROM dpeg_evaluators WHERE evaluator_id = '$id'";
    $result = $conn->query($checkDpegQuery);
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            echo json_encode(['error' => 'Unable to delete evaluator due to association with DPEG team']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Error checking DPEG association: ' . $conn->error]);
        exit;
    }

    // Delete evaluator from the database
    $sql = "DELETE FROM dept_evaluators WHERE evaluator_id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => 'Evaluator deleted successfully']);
    } else {
        echo json_encode(['error' => $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
