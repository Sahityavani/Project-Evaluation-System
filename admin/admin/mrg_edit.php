<?php
include('../db.php');

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required fields are provided
    if (isset($_POST['mrg_id']) && !empty($_POST['mrg_id']) &&
        isset($_POST['mrg_name']) && !empty($_POST['mrg_name']) &&
        isset($_POST['mrg_title']) && !empty($_POST['mrg_title'])) {
        
        // Sanitize input
        $mrgId = intval($_POST['mrg_id']);
        $mrgName = $conn->real_escape_string($_POST['mrg_name']);
        $mrgTitle = $conn->real_escape_string($_POST['mrg_title']);
        $newEvaluators = isset($_POST['evaluators']) ? array_map('intval', $_POST['evaluators']) : [];

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update MRG details
            $updateMrgQuery = "UPDATE mrg SET mrg_name = ?, mrg_title = ? WHERE mrg_id = ?";
            if ($stmt = $conn->prepare($updateMrgQuery)) {
                $stmt->bind_param('ssi', $mrgName, $mrgTitle, $mrgId);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception('Error preparing MRG update query');
            }

            // Fetch existing evaluators
            $existingEvaluators = [];
            $fetchEvaluatorsQuery = "SELECT evaluator_id FROM mrg_evaluators WHERE mrg_id = ?";
            if ($stmt = $conn->prepare($fetchEvaluatorsQuery)) {
                $stmt->bind_param('i', $mrgId);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $existingEvaluators[] = $row['evaluator_id'];
                }
                $stmt->close();
            } else {
                throw new Exception('Error preparing fetch evaluators query');
            }

            // Find evaluators to remove
            $evaluatorsToRemove = array_diff($existingEvaluators, $newEvaluators);

            // Find evaluators to add
            $evaluatorsToAdd = array_diff($newEvaluators, $existingEvaluators);

            // Check for duplicates in evaluators to add
            $duplicates = array_diff_assoc($newEvaluators, array_unique($newEvaluators));
            if (!empty($duplicates)) {
                throw new Exception('One or more evaluators were added multiple times.');
            }

            // Remove evaluators no longer assigned
            if (!empty($evaluatorsToRemove)) {
                $removeEvaluatorsQuery = "DELETE FROM mrg_evaluators WHERE mrg_id = ? AND evaluator_id IN (" . implode(',', $evaluatorsToRemove) . ")";
                if ($stmt = $conn->prepare($removeEvaluatorsQuery)) {
                    $stmt->bind_param('i', $mrgId);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    throw new Exception('Error preparing remove evaluators query');
                }
            }

            // Add new evaluators
            if (!empty($evaluatorsToAdd)) {
                $addEvaluatorsQuery = "INSERT INTO mrg_evaluators (mrg_id, evaluator_id) VALUES ";
                $values = [];
                foreach ($evaluatorsToAdd as $evaluatorId) {
                    $values[] = "($mrgId, $evaluatorId)";
                }
                $addEvaluatorsQuery .= implode(',', $values);
                if (!$conn->query($addEvaluatorsQuery)) {
                    throw new Exception('Error inserting evaluators');
                }
            }

            // Commit transaction
            $conn->commit();

            // Success response
            header('Location: add-mrg.php?success=MRG updated successfully');
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();

            // Error response
            header('Location: add-mrg.php?error=' . urlencode($e->getMessage()));
        }
    } else {
        // Invalid request
        header('Location: add-mrg.php?error=Invalid request');
    }
} else {
    // Method not allowed
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
?>
