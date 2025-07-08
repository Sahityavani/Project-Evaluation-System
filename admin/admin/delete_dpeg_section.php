<?php
include('auth.php');
include('../db.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch ID from request
$id = isset($_POST['id']) ? $_POST['id'] : null;

// Check if ID is set
if ($id === null) {
    echo json_encode(['success' => false, 'message' => 'ID is not set.']);
    exit();
}

// Prepare the SQL statements
$sql = "UPDATE batches SET dpeg_id = NULL WHERE dpeg_id = ?";
$sql2 = "DELETE FROM dpeg_batches WHERE dpeg_id = ?";
$sql3 = "UPDATE students SET dpeg_id = NULL WHERE dpeg_id = ?";

// Begin transaction
$conn->begin_transaction();

try {
    // Prepare and execute the first statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Prepare and execute the second statement
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    // Prepare and execute the third statement
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    $stmt3->close();

    // Commit transaction
    $conn->commit();

    // Output success JSON
    echo json_encode(['success' => true, 'message' => 'DPEG-Section deleted successfully']);
} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();

    // Output error JSON
    echo json_encode(['success' => false, 'message' => 'Failed to delete DPEG-Section']);
}

// Close connection
$conn->close();
?>
