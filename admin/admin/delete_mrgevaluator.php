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

    // Check if the evaluator is associated with an MRG
    $checkMrgQuery = "SELECT COUNT(*) as count FROM mrg_evaluators WHERE evaluator_id = '$id'";
    $result = $conn->query($checkMrgQuery);
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            echo json_encode(['error' => 'Unable to delete evaluator due to association with MRG team']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Error checking MRG association: ' . $conn->error]);
        exit;
    }

    // Delete evaluator from the database
    $sql = "DELETE FROM evaluators WHERE evaluator_id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => 'Evaluator deleted successfully']);
    } else {
        echo json_encode(['error' => $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
