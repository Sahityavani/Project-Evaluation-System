<?php
include('../db.php');

$batchId = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($batchId)) {
    echo "Invalid batch ID.";
    exit;
}

// Fetch batch details
$batchQuery = "
    SELECT b.batch_name, b.batch_title, IFNULL(su.name, 'Not Allocated') AS supervisor_name
    FROM batches b
    LEFT JOIN supervisors su ON b.supervisor = su.username
    WHERE b.batch_id = ?
";
$stmt = $conn->prepare($batchQuery);
$stmt->bind_param("s", $batchId);
$stmt->execute();
$batchResult = $stmt->get_result();
$batchRow = $batchResult->fetch_assoc();

if (!$batchRow) {
    echo "Batch not found.";
    exit;
}

// Fetch students in the batch
$studentsQuery = "
    SELECT rollno, name AS student_name, email AS student_email, mobile AS student_mobile
    FROM students
    WHERE batch_id = ?
";
$stmt = $conn->prepare($studentsQuery);
$stmt->bind_param("s", $batchId);
$stmt->execute();
$studentsResult = $stmt->get_result();

?>

<h5>Batch Details</h5>
<p><strong>Batch Name:</strong> <?php echo htmlspecialchars($batchRow['batch_name']); ?></p>
<p><strong>Batch Title:</strong> <?php echo htmlspecialchars($batchRow['batch_title']); ?></p>
<p><strong>Supervisor:</strong> <?php echo htmlspecialchars($batchRow['supervisor_name']); ?></p>

<h6>Batch Members</h6>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Roll No</th>
            <th>Student Name</th>
            <th>Email</th>
            <th>Mobile</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($studentRow = $studentsResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($studentRow['rollno']); ?></td>
                <td><?php echo htmlspecialchars($studentRow['student_name']); ?></td>
                <td><?php echo htmlspecialchars($studentRow['student_email']); ?></td>
                <td><?php echo htmlspecialchars($studentRow['student_mobile']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
