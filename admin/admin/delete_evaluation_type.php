<?php
error_reporting(0);
include('../db.php');
include('auth.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Check if any evaluations exist for the given ID in mrg_evaluations and supervisor_evaluations
    $sql = "SELECT * FROM mrg_evaluations WHERE evaluation_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $mrgResult = $stmt->get_result();

    $sql = "SELECT * FROM supervisor_evaluations WHERE evaluation_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $supervisorResult = $stmt->get_result();

    // Check if any evaluations exist
    if ($mrgResult->num_rows > 0 || $supervisorResult->num_rows > 0) {
        echo json_encode(array('success' => false, 'message' => 'Evaluations already exist for this evaluation type'));
        exit;
    } else {
        $sql = "DELETE FROM evaluation_type WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(array('success' => true, 'message' => 'Evaluation type deleted successfully'));
            exit;
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to delete evaluation type'));
            exit;
        }
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Invalid request'));
    exit;
}
?>