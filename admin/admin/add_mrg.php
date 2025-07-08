<?php
include('auth.php');
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and escape input data
    $mrgId = isset($_POST['mrg_id']) ? intval($_POST['mrg_id']) : null;
    $mrgName = $conn->real_escape_string($_POST['mrg_name']);
    $mrgTitle = $conn->real_escape_string($_POST['mrg_title']);
    $evaluators = isset($_POST['evaluators']) ? array_map('intval', $_POST['evaluators']) : [];

    // Check if MRG ID is provided
    if (!$mrgId) {
        header("Location: add-mrg.php?error=MRG ID is required");
        exit();
    }

    // Check for duplicate MRG IDs in the input
    $mrgIds = array_map('intval', $_POST['mrg_ids'] ?? []);
    if (count($mrgIds) !== count(array_unique($mrgIds))) {
        header("Location: add-mrg.php?error=Duplicate MRG IDs detected");
        exit();
    }

    // Check if MRG ID already exists
    $mrgQuery = "SELECT mrg_id FROM mrg WHERE mrg_id = $mrgId";
    $mrgResult = $conn->query($mrgQuery);

    if ($mrgResult->num_rows > 0) {
        // MRG ID already exists
        header("Location: add-mrg.php?error=MRG ID already exists");
        exit();
    }

    // Check if all evaluators exist in the evaluators table
    $evaluatorsExist = true;
    foreach ($evaluators as $evaluatorId) {
        $evaluatorId = intval($evaluatorId);
        $evaluatorQuery = "SELECT evaluator_id FROM evaluators WHERE evaluator_id = $evaluatorId";
        $evaluatorResult = $conn->query($evaluatorQuery);

        if ($evaluatorResult->num_rows === 0) {
            // Evaluator does not exist
            $evaluatorsExist = false;
            break;
        }
    }

    if (!$evaluatorsExist) {
        // Redirect if any evaluator does not exist
        header("Location: add-mrg.php?error=One or more evaluators do not exist");
        exit();
    }

    // Check for duplicate evaluators
    $uniqueEvaluators = array_unique($evaluators);
    if (count($uniqueEvaluators) < count($evaluators)) {
        header("Location: add-mrg.php?error=Duplicate evaluator IDs detected");
        exit();
    }

    // Check if evaluators are already associated with another MRG
    $evaluatorCheckPassed = true;
    foreach ($evaluators as $evaluatorId) {
        $evaluatorId = intval($evaluatorId);

        // Check if evaluator is already associated with another MRG
        $evaluatorQuery = "SELECT mrg_id FROM mrg_evaluators WHERE evaluator_id = $evaluatorId";
        $evaluatorResult = $conn->query($evaluatorQuery);

        if ($evaluatorResult->num_rows > 0) {
            // If the evaluator is already in another MRG, set flag to false
            $evaluatorCheckPassed = false;
            break;
        }
    }

    if (!$evaluatorCheckPassed) {
        // Redirect if any evaluator is already in another MRG
        header("Location: add-mrg.php?error=Some evaluators are already associated with another MRG");
        exit();
    }

    // Insert new MRG with provided mrg_id
    $sql = "INSERT INTO mrg (mrg_id, mrg_name, mrg_title) VALUES ($mrgId, '$mrgName', '$mrgTitle')";
    if ($conn->query($sql) === TRUE) {
        // Insert evaluators into mrg_evaluators table
        foreach ($uniqueEvaluators as $evaluatorId) {
            $evaluatorId = intval($evaluatorId);
            $conn->query("INSERT INTO mrg_evaluators (mrg_id, evaluator_id) VALUES ($mrgId, $evaluatorId)");
        }

        // Redirect or show success message
        header("Location: add-mrg.php?success=MRG added successfully");
    } else {
        // Show error if MRG insertion failed
        header("Location: add-mrg.php?error=" . $conn->error);
    }
}
?>
