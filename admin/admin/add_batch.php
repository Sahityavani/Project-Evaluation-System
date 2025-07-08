<?php
include('../db.php');
include('auth.php');

// Function to sanitize user inputs
function sanitize_input($data, $conn) {
    return $conn->real_escape_string(trim($data));
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $batchId = isset($_POST['batch_id']) ? intval($_POST['batch_id']) : 0;
    $batchName = sanitize_input($_POST['batch_name'], $conn);
    $batchTitle = sanitize_input($_POST['batch_title'], $conn);
    $members = isset($_POST['members']) ? array_map('sanitize_input', $_POST['members'], array_fill(0, count($_POST['members']), $conn)) : [];

    // Remove duplicate members
    $uniqueMembers = array_unique($members);

    // Check for duplicates
    if (count($uniqueMembers) < count($members)) {
        $duplicateMembers = array_diff_assoc($members, $uniqueMembers);
        $duplicateMembersList = implode(', ', array_unique($duplicateMembers));
        header("Location: add-batch.php?error=" . urlencode("Duplicate members found: $duplicateMembersList"));
        exit();
    }

    // Validate members
    $validMembers = [];
    $invalidMembers = [];

    foreach ($uniqueMembers as $member) {
        $checkMemberQuery = "SELECT rollno FROM students WHERE rollno = ? AND batch_id IS NULL";
        $stmt = $conn->prepare($checkMemberQuery);
        $stmt->bind_param('s', $member);
        $stmt->execute();
        $checkMemberResult = $stmt->get_result();

        if ($checkMemberResult->num_rows > 0) {
            $validMembers[] = $member;
        } else {
            $invalidMembers[] = $member;
        }
    }

    // Handle validation errors
    if (!empty($invalidMembers)) {
        $invalidMembersList = implode(', ', $invalidMembers);
        header("Location: add-batch.php?error=" . urlencode("The following members are invalid or already assigned to a batch: $invalidMembersList"));
        exit();
    }

    // Insert new batch with the count of valid members
    $numMembers = count($validMembers);
    $insertBatchQuery = "INSERT INTO batches (batch_id, batch_name, batch_title, batch_members) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertBatchQuery);
    $stmt->bind_param('issi', $batchId, $batchName, $batchTitle, $numMembers);

    if ($stmt->execute()) {
        // Update students with the new batch_id
        foreach ($validMembers as $member) {
            $updateStudentQuery = "UPDATE students SET batch_id = ? WHERE rollno = ?";
            $stmt = $conn->prepare($updateStudentQuery);
            $stmt->bind_param('is', $batchId, $member);
            $stmt->execute();
        }

        // Redirect with success message
        header('Location: add-batch.php?success=' . urlencode('Batch added successfully'));
        exit();
    } else {
        // Redirect with error message
        header("Location: add-batch.php?error=" . urlencode("Error adding batch: " . $conn->error));
        exit();
    }
}
?>
