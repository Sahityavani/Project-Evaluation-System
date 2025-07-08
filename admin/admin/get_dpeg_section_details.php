<?php
include('../db.php');

// Get ID from GET request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $response = [];

    // Fetch DPEG details using the provided ID
    $fetchDPEGQuery = "
        SELECT d.dpeg_id, d.dpeg_name
        FROM dpeg_batches db
        JOIN dpeg d ON db.dpeg_id = d.dpeg_id
        WHERE db.id = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($fetchDPEGQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['dpeg'] = $result->fetch_assoc();
        
        // Fetch associated sections using batch_id from students table
        $fetchSectionsQuery = "
            SELECT DISTINCT s.section
            FROM dpeg_batches db
            JOIN students s ON db.batch_id = s.batch_id
            WHERE db.id = ?
        ";
        $stmtSections = $conn->prepare($fetchSectionsQuery);
        $stmtSections->bind_param("i", $id);
        $stmtSections->execute();
        $sectionsResult = $stmtSections->get_result();
        $response['sections'] = $sectionsResult->fetch_all(MYSQLI_ASSOC);

        // Fetch current batches associated with the DPEG-Section mapping
        $fetchCurrentBatchesQuery = "
            SELECT b.batch_id, b.batch_name
            FROM dpeg_batches db
            JOIN batches b ON db.batch_id = b.batch_id
            WHERE db.id = ?
        ";
        $stmtBatches = $conn->prepare($fetchCurrentBatchesQuery);
        $stmtBatches->bind_param("i", $id);
        $stmtBatches->execute();
        $batchesResult = $stmtBatches->get_result();
        $response['current_batches'] = $batchesResult->fetch_all(MYSQLI_ASSOC);

        // Fetch all available batches
        $fetchAvailableBatchesQuery = "SELECT batch_id, batch_name FROM batches WHERE dpeg_id IS NULL";
        $stmtAvailableBatches = $conn->prepare($fetchAvailableBatchesQuery);
        $stmtAvailableBatches->execute();
        $availableBatchesResult = $stmtAvailableBatches->get_result();
        $response['available_batches'] = $availableBatchesResult->fetch_all(MYSQLI_ASSOC);
    } else {
        $response['error'] = 'DPEG-Section mapping not found.';
    }

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Invalid ID.']);
}
exit;
?>
