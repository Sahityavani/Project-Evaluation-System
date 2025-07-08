<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batchId = $conn->real_escape_string($_POST['batch_id']);

    $sql = "SELECT rollno, name, batch_id FROM students WHERE batch_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $batchId);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode($students);
}
?>
