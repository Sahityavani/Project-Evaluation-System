<?php
include('../db.php');

if (!isset($_POST['mrg_id']) || empty($_POST['mrg_id'])) {
    echo 'No MRG ID provided.';
    exit;
}

$mrgId = intval($_POST['mrg_id']);

// Fetch MRG details
$sql_mrg = "
    SELECT m.mrg_id, m.mrg_name
    FROM mrg m
    WHERE m.mrg_id = $mrgId
";
$result_mrg = $conn->query($sql_mrg);
$mrg = $result_mrg->fetch_assoc();

// Fetch evaluators for the MRG
$sql_evaluators = "
    SELECT e.evaluator_id, e.name, e.email
    FROM mrg_evaluators me
    JOIN evaluators e ON me.evaluator_id = e.evaluator_id
    WHERE me.mrg_id = $mrgId
";
$result_evaluators = $conn->query($sql_evaluators);
$evaluators = [];
while ($row = $result_evaluators->fetch_assoc()) {
    $evaluators[] = $row;
}

// Fetch batch details and their supervisors
$sql_batches = "
    SELECT DISTINCT b.batch_id, 
                    COALESCE(sup.name, 'Not Allocated') AS supervisor_name
    FROM batches b
    LEFT JOIN supervisors sup ON b.supervisor = sup.username
    JOIN mrg_batches mb ON b.batch_id = mb.batch_id
    WHERE mb.mrg_id = ?
";

$stmt = $conn->prepare($sql_batches);
$stmt->bind_param('i', $mrgId);
$stmt->execute();
$result_batches = $stmt->get_result();
$batches = [];
while ($row = $result_batches->fetch_assoc()) {
    $batches[] = $row;
}

// Fetch batch members with batch IDs
$sql_members = "
    SELECT s.rollno, 
           s.name AS student_name, 
           s.email, 
           s.mobile, 
           b.batch_id
    FROM students s
    JOIN batches b ON s.batch_id = b.batch_id
    JOIN mrg_batches mb ON b.batch_id = mb.batch_id
    WHERE mb.mrg_id = ?
";

$stmt = $conn->prepare($sql_members);
$stmt->bind_param('i', $mrgId);
$stmt->execute();
$result_members = $stmt->get_result();
$members = [];
while ($row = $result_members->fetch_assoc()) {
    $members[] = $row;
}

?>

<h5>MRG Details</h5>
<?php if ($mrg): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>MRG ID</th>
                <th>MRG Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($mrg['mrg_id']); ?></td>
                <td><?php echo htmlspecialchars($mrg['mrg_name']); ?></td>
                <td>Description (Placeholder)</td> <!-- Update this placeholder as needed -->
            </tr>
        </tbody>
    </table>
<?php else: ?>
    <p>No MRG details found.</p>
<?php endif; ?>

<h5>Evaluators</h5>
<?php if ($evaluators): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Evaluator ID</th>
                <th>Evaluator Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($evaluators as $evaluator): ?>
                <tr>
                    <td><?php echo htmlspecialchars($evaluator['evaluator_id']); ?></td>
                    <td><?php echo htmlspecialchars($evaluator['name']); ?></td>
                    <td><?php echo htmlspecialchars($evaluator['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No evaluators found for this MRG.</p>
<?php endif; ?>

<h5>Batch Details</h5>
<?php if ($batches): ?>
    <?php foreach ($batches as $batch): ?>
        <h6>Batch ID: <?php echo htmlspecialchars($batch['batch_id']); ?></h6>
        <p>Supervisor: <?php echo htmlspecialchars($batch['supervisor_name']); ?></p>
        <h6>Batch Members</h6>
        <?php
        // Filter members for the current batch
        $batch_members = array_filter($members, function($member) use ($batch) {
            return $member['batch_id'] === $batch['batch_id'];
        });
        ?>
        <?php if ($batch_members): ?>
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
                    <?php foreach ($batch_members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['rollno']); ?></td>
                            <td><?php echo htmlspecialchars($member['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td><?php echo htmlspecialchars($member['mobile']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No members found for this batch.</p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>No batch details found for this MRG.</p>
<?php endif; ?>
