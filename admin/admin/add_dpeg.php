<?php
include('auth.php');
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and escape input data
    $dpegId = isset($_POST['dpeg_id']) ? intval($_POST['dpeg_id']) : null;
    $dpegName = $conn->real_escape_string($_POST['dpeg_name']);
    $dpegTitle = $conn->real_escape_string($_POST['dpeg_title']);
    $evaluators = isset($_POST['evaluators']) ? array_map('intval', $_POST['evaluators']) : [];

    // Check if DPEG ID is provided
    if (!$dpegId) {
        header("Location: add-dpeg.php?error=DPEG ID is required");
        exit();
    }

    // Check for duplicate DPEG IDs in the input
    $dpegIds = array_map('intval', $_POST['dpeg_ids'] ?? []);
    if (count($dpegIds) !== count(array_unique($dpegIds))) {
        header("Location: add-dpeg.php?error=Duplicate DPEG IDs detected");
        exit();
    }

    // Check if DPEG ID already exists
    $dpegQuery = "SELECT dpeg_id FROM dpeg WHERE dpeg_id = $dpegId";
    $dpegResult = $conn->query($dpegQuery);

    if ($dpegResult->num_rows > 0) {
        // DPEG ID already exists
        header("Location: add-dpeg.php?error=DPEG ID already exists");
        exit();
    }

    // Check if all evaluators exist in the evaluators table
    $evaluatorsExist = true;
    foreach ($evaluators as $evaluatorId) {
        $evaluatorId = intval($evaluatorId);
        $evaluatorQuery = "SELECT evaluator_id FROM dept_evaluators WHERE evaluator_id = $evaluatorId";
        $evaluatorResult = $conn->query($evaluatorQuery);

        if ($evaluatorResult->num_rows === 0) {
            // Evaluator does not exist
            $evaluatorsExist = false;
            break;
        }
    }

    if (!$evaluatorsExist) {
        // Redirect if any evaluator does not exist
        header("Location: add-dpeg.php?error=One or more evaluators do not exist");
        exit();
    }

    // Check for duplicate evaluators
    $uniqueEvaluators = array_unique($evaluators);
    if (count($uniqueEvaluators) < count($evaluators)) {
        header("Location: add-dpeg.php?error=Duplicate evaluator IDs detected");
        exit();
    }

    // Check if evaluators are already associated with another DPEG
    $evaluatorCheckPassed = true;
    foreach ($evaluators as $evaluatorId) {
        $evaluatorId = intval($evaluatorId);

        // Check if evaluator is already associated with another DPEG
        $evaluatorQuery = "SELECT dpeg_id FROM dpeg_evaluators WHERE evaluator_id = $evaluatorId";
        $evaluatorResult = $conn->query($evaluatorQuery);

        if ($evaluatorResult->num_rows > 0) {
            // If the evaluator is already in another DPEG, set flag to false
            $evaluatorCheckPassed = false;
            break;
        }
    }

    if (!$evaluatorCheckPassed) {
        // Redirect if any evaluator is already in another DPEG
        header("Location: add-dpeg.php?error=Some evaluators are already associated with another DPEG");
        exit();
    }

    // Insert new DPEG with provided dpeg_id
    $sql = "INSERT INTO dpeg (dpeg_id, dpeg_name, dpeg_title) VALUES ($dpegId, '$dpegName', '$dpegTitle')";
    if ($conn->query($sql) === TRUE) {
        // Insert evaluators into dpeg_evaluators table
        foreach ($uniqueEvaluators as $evaluatorId) {
            $evaluatorId = intval($evaluatorId);
            $conn->query("INSERT INTO dpeg_evaluators (dpeg_id, evaluator_id) VALUES ($dpegId, $evaluatorId)");
        }

        // Redirect or show success message
        header("Location: add-dpeg.php?success=DPEG added successfully");
    } else {
        // Show error if DPEG insertion failed
        header("Location: add-dpeg.php?error=" . $conn->error);
    }
}
?>
