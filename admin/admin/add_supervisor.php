<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $batch_id = isset($_POST['batch_id']) ? $conn->real_escape_string($_POST['batch_id']) : null;

    // Check if the username already exists
    $userCheckSql = "SELECT * FROM supervisors WHERE username = '$username'";
    $userCheckResult = $conn->query($userCheckSql);

    if ($userCheckResult->num_rows > 0) {
        header('Location: add-supervisor.php?error=Username+already+exists');
        exit();
    }

    // Prepare insert query
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO supervisors (id, name, username, password" . ($batch_id !== null ? ', batch_id' : '') . ") VALUES ('$id', '$name', '$username', '$hashedPassword'" . ($batch_id !== null ? ", '$batch_id'" : '') . ")";

    if ($conn->query($sql) === TRUE) {
        if ($batch_id !== null) {
            // Update batches table
            $batchUpdateSql = "UPDATE batches SET supervisor = '$username' WHERE batch_id = '$batch_id'";
            if ($conn->query($batchUpdateSql) !== TRUE) {
                header('Location: add-supervisor.php?error=Error+updating+batch+supervisor%3A+' . $conn->error);
                exit();
            }

            // Update students table
            $studentsUpdateSql = "UPDATE students SET supervisor = '$username' WHERE batch_id = '$batch_id'";
            if ($conn->query($studentsUpdateSql) !== TRUE) {
                header('Location: add-supervisor.php?error=Error+updating+students+supervisor%3A+' . $conn->error);
                exit();
            }
        }
        header('Location: add-supervisor.php?success=Supervisor+added+successfully');
    } else {
        header('Location: add-supervisor.php?error=Error+adding+supervisor%3A+' . $conn->error);
    }
} else {
    header('Location: add-supervisor.php?error=Invalid+request.');
}

$conn->close();
?>
