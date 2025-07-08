<?php
include('../db.php'); // Include your database connection file
include('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure required parameters are present
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $dpeg_id = isset($_POST['dpeg_id']) ? intval($_POST['dpeg_id']) : null;
    $new_batches = isset($_POST['batches']) ? $_POST['batches'] : [];

    if ($id === null || $dpeg_id === null) {
        echo json_encode(['error' => 'Missing required parameters.']);
        exit;
    }

    // Fetch current batches for the given dpeg_id
    $sql = "SELECT batch_id FROM dpeg_batches WHERE dpeg_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $dpeg_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $current_batches = [];
    while ($row = $result->fetch_assoc()) {
        $current_batches[] = $row['batch_id'];
    }

    // Determine which batches need to be removed and which need to be added
    $batches_to_remove = array_diff($current_batches, $new_batches);
    $batches_to_add = array_diff($new_batches, $current_batches);

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Remove batches no longer associated
        if (!empty($batches_to_remove)) {
            $placeholders = implode(',', array_fill(0, count($batches_to_remove), '?'));

            // Remove from dpeg_batches
            $sql = "DELETE FROM dpeg_batches WHERE dpeg_id = ? AND batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $param_types = str_repeat('i', count($batches_to_remove) + 1);
            $params = array_merge([$dpeg_id], $batches_to_remove);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();

            // Update batches table
            $sql = "UPDATE batches SET dpeg_id = NULL WHERE batch_id IN ($placeholders) AND dpeg_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();

            // Update students table
            $sql = "UPDATE students SET dpeg_id = NULL WHERE dpeg_id = ? AND batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();
        }

        // Add new batches
        if (!empty($batches_to_add)) {
            $placeholders = implode(',', array_fill(0, count($batches_to_add), '(?, ?, ?)'));

            // Add entries to dpeg_batches
            $sql = "INSERT INTO dpeg_batches (id, dpeg_id, batch_id) VALUES " . $placeholders;
            $stmt = $conn->prepare($sql);
            $params = [];
            foreach ($batches_to_add as $batch_id) {
                $params[] = $id;
                $params[] = $dpeg_id;
                $params[] = $batch_id;
            }
            $types = str_repeat('iii', count($batches_to_add));
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            // Update batches table
            $placeholders = implode(',', array_fill(0, count($batches_to_add), '?'));
            $sql = "UPDATE batches SET dpeg_id = ? WHERE batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i' . str_repeat('i', count($batches_to_add)), $dpeg_id, ...$batches_to_add);
            $stmt->execute();

            // Update students table
            $sql = "UPDATE students SET dpeg_id = ? WHERE batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i' . str_repeat('i', count($batches_to_add)), $dpeg_id, ...$batches_to_add);
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
<?php
include('../db.php'); // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure required parameters are present
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $dpeg_id = isset($_POST['dpeg_id']) ? intval($_POST['dpeg_id']) : null;
    $new_batches = isset($_POST['batches']) ? $_POST['batches'] : [];

    if ($id === null || $dpeg_id === null) {
        header("Location: add-dpegbatches.php?error=" . urlencode('Missing required parameters.'));
        exit;
    }

    // Fetch current batches for the given dpeg_id
    $sql = "SELECT batch_id FROM dpeg_batches WHERE dpeg_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $dpeg_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $current_batches = [];
    while ($row = $result->fetch_assoc()) {
        $current_batches[] = $row['batch_id'];
    }

    // Determine which batches need to be removed and which need to be added
    $batches_to_remove = array_diff($current_batches, $new_batches);
    $batches_to_add = array_diff($new_batches, $current_batches);

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Remove batches no longer associated
        if (!empty($batches_to_remove)) {
            $placeholders = implode(',', array_fill(0, count($batches_to_remove), '?'));

            // Remove from dpeg_batches
            $sql = "DELETE FROM dpeg_batches WHERE dpeg_id = ? AND batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $param_types = str_repeat('i', count($batches_to_remove) + 1);
            $params = array_merge([$dpeg_id], $batches_to_remove);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();

            // Update batches table
            $sql = "UPDATE batches SET dpeg_id = NULL WHERE batch_id IN ($placeholders) AND dpeg_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();

            // Update students table
            $sql = "UPDATE students SET dpeg_id = NULL WHERE dpeg_id = ? AND batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();
        }

        // Add new batches
        if (!empty($batches_to_add)) {
            $placeholders = implode(',', array_fill(0, count($batches_to_add), '(?, ?, ?)'));

            // Add entries to dpeg_batches
            $sql = "INSERT INTO dpeg_batches (id, dpeg_id, batch_id) VALUES " . $placeholders;
            $stmt = $conn->prepare($sql);
            $params = [];
            foreach ($batches_to_add as $batch_id) {
                $params[] = $id;
                $params[] = $dpeg_id;
                $params[] = $batch_id;
            }
            $types = str_repeat('iii', count($batches_to_add));
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            // Update batches table
            $placeholders = implode(',', array_fill(0, count($batches_to_add), '?'));
            $sql = "UPDATE batches SET dpeg_id = ? WHERE batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i' . str_repeat('i', count($batches_to_add)), $dpeg_id, ...$batches_to_add);
            $stmt->execute();

            // Update students table
            $sql = "UPDATE students SET dpeg_id = ? WHERE batch_id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i' . str_repeat('i', count($batches_to_add)), $dpeg_id, ...$batches_to_add);
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();
        header("Location: add-dpegbatches.php?success=" . urlencode('Changes successfully saved'));
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header("Location: add-dpegbatches.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: add-dpegbatches.php?error=" . urlencode('Invalid request method'));
    exit;
}
?>
