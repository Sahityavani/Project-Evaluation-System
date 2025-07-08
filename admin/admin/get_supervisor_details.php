<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string($_POST['id']);

    $sql = "SELECT * FROM supervisors WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $supervisor = $result->fetch_assoc();
        echo json_encode($supervisor);
    } else {
        echo json_encode(['error' => 'Supervisor not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}

$conn->close();
?>
