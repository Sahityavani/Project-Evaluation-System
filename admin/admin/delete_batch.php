<?php
include('../db.php');

$batchId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($batchId > 0) {
    // Delete batch members
    $sql = "update students set batch_id =NULL where batch_id=".$batchId;
    $conn->query($sql);

    // Delete batch
    $sql = "DELETE FROM batches WHERE batch_id = $batchId";
    $conn->query($sql);

    $sql = "DELETE FROM mrg_evaluations WHERE batch_id = $batchId";
    $conn->query($sql);

    $sql = "DELETE FROM supervisor_evaluations WHERE batch_id = $batchId";
    $conn->query($sql);
}

header('Location: manage_batches.php?success=1');
?>
