<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $new_batch_id = isset($_POST['batch_id']) ? $conn->real_escape_string($_POST['batch_id']) : null;

    // Check if the supervisor exists
    $supervisorCheckSql = "SELECT * FROM supervisors WHERE id = '$id'";
    $supervisorCheckResult = $conn->query($supervisorCheckSql);

    if ($supervisorCheckResult->num_rows === 0) {
        header('Location: add-supervisor.php?error=Supervisor+does+not+exist');
        exit();
    }

    // Get the current batch_id of the supervisor
    $currentBatchSql = "SELECT batch_id FROM supervisors WHERE id = '$id'";
    $currentBatchResult = $conn->query($currentBatchSql);
    $currentBatch = $currentBatchResult->fetch_assoc();
    $current_batch_id = $currentBatch['batch_id'];

    // Check if the username already exists (except for the current supervisor)
    $userCheckSql = "SELECT * FROM supervisors WHERE username = '$username' AND id != '$id'";
    $userCheckResult = $conn->query($userCheckSql);

    if ($userCheckResult->num_rows > 0) {
        header('Location: add-supervisor.php?error=Username+already+exists');
        exit();
    }

    // Prepare update query
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updateSupervisorSql = "UPDATE supervisors SET name = '$name', username = '$username', password = '$hashedPassword'" . ($new_batch_id !== null ? ", batch_id = '$new_batch_id'" : '') . " WHERE id = '$id'";
    } else {
        $updateSupervisorSql = "UPDATE supervisors SET name = '$name', username = '$username'" . ($new_batch_id !== null ? ", batch_id = '$new_batch_id'" : '') . " WHERE id = '$id'";
    }

    if ($conn->query($updateSupervisorSql) === TRUE) {
        // Update batches table if batch_id changed
        if ($current_batch_id !== $new_batch_id) {
            // Set previous batch_id supervisor to NULL
            if ($current_batch_id !== null) {
                $clearPreviousBatchSql = "UPDATE batches SET supervisor = NULL WHERE batch_id = '$current_batch_id'";
                if ($conn->query($clearPreviousBatchSql) !== TRUE) {
                    header('Location: add-supervisor.php?error=Error+clearing+previous+batch+supervisor%3A+' . $conn->error);
                    exit();
                }

                // Clear previous batch_id in students table
                $clearPreviousStudentsSql = "UPDATE students SET supervisor = NULL WHERE batch_id = '$current_batch_id'";
                if ($conn->query($clearPreviousStudentsSql) !== TRUE) {
                    header('Location: add-supervisor.php?error=Error+clearing+students+supervisor%3A+' . $conn->error);
                    exit();
                }
            }

            // Set new batch_id supervisor
            if ($new_batch_id !== null) {
                $updateNewBatchSql = "UPDATE batches SET supervisor = '$username' WHERE batch_id = '$new_batch_id'";
                if ($conn->query($updateNewBatchSql) !== TRUE) {
                    header('Location: add-supervisor.php?error=Error+updating+new+batch+supervisor%3A+' . $conn->error);
                    exit();
                }

                // Update students table with new supervisor username
                $updateNewStudentsSql = "UPDATE students SET supervisor = '$username' WHERE batch_id = '$new_batch_id'";
                if ($conn->query($updateNewStudentsSql) !== TRUE) {
                    header('Location: add-supervisor.php?error=Error+updating+students+supervisor%3A+' . $conn->error);
                    exit();
                }
            }
        } elseif ($new_batch_id !== null) {
            // If batch_id did not change but is provided, ensure it is correctly set
            $updateNewBatchSql = "UPDATE batches SET supervisor = '$username' WHERE batch_id = '$new_batch_id'";
            if ($conn->query($updateNewBatchSql) !== TRUE) {
                header('Location: add-supervisor.php?error=Error+updating+batch+supervisor%3A+' . $conn->error);
                exit();
            }

            // Update students table with new supervisor username
            $updateNewStudentsSql = "UPDATE students SET supervisor = '$username' WHERE batch_id = '$new_batch_id'";
            if ($conn->query($updateNewStudentsSql) !== TRUE) {
                header('Location: add-supervisor.php?error=Error+updating+students+supervisor%3A+' . $conn->error);
                exit();
            }
        }

        header('Location: add-supervisor.php?success=Supervisor+updated+successfully');
    } else {
        header('Location: add-supervisor.php?error=Error+updating+supervisor%3A+' . $conn->error);
    }
} else {
    header('Location: add-supervisor.php?error=Invalid+request.');
}

$conn->close();
?>
