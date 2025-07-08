<?php
include('auth.php');
$title = "Student | View Batch";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM students WHERE rollno= '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$batch_id = $row['batch_id'];
$type = $_SESSION['type'];

if (empty($batch_id)) {
    echo '<p class="alert alert-warning">You are not associated with any batch. My Batch cannot be viewed.</p>';
    exit();
}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch batch details for the specific student
$batchQuery = "
    SELECT b.batch_id, b.batch_name, b.batch_title, COUNT(s.rollno) AS num_members, IFNULL(su.name, 'Not Allocated') AS supervisor_name, b.mrg_id,
    b.dpeg_id FROM batches b
    LEFT JOIN students s ON b.batch_id = s.batch_id
    LEFT JOIN supervisors su ON b.supervisor = su.username
    WHERE b.batch_id = '$batch_id'
    GROUP BY b.batch_id
";
$batchResult = $conn->query($batchQuery);
$batchDetails = $batchResult->fetch_assoc();

// Fetch MRG details and evaluators
$mrgQuery = "
    SELECT m.mrg_id, m.mrg_name, m.mrg_title, e.evaluator_id, e.name AS evaluator_name, e.email AS evaluator_email
    FROM mrg m
    LEFT JOIN mrg_evaluators me ON m.mrg_id = me.mrg_id
    LEFT JOIN evaluators e ON me.evaluator_id = e.evaluator_id
";
$mrgResult = $conn->query($mrgQuery);
$mrgDetails = [];
while ($mrgRow = $mrgResult->fetch_assoc()) {
    $mrgDetails[$mrgRow['mrg_id']]['mrg'] = [
        'name' => $mrgRow['mrg_name'],
        'title' => $mrgRow['mrg_title']
    ];
    $mrgDetails[$mrgRow['mrg_id']]['evaluators'][] = [
        'id' => $mrgRow['evaluator_id'],
        'name' => $mrgRow['evaluator_name'],
        'email' => $mrgRow['evaluator_email']
    ];
}

$dpegQuery = "
    SELECT m.dpeg_id, m.dpeg_name, m.dpeg_title, e.evaluator_id, e.name AS evaluator_name, e.email AS evaluator_email
    FROM dpeg m
    LEFT JOIN dpeg_evaluators me ON m.dpeg_id = me.dpeg_id
    LEFT JOIN dept_evaluators e ON me.evaluator_id = e.evaluator_id
";
$dpegResult = $conn->query($dpegQuery);
$dpegDetails = [];
while ($dpegRow = $dpegResult->fetch_assoc()) {
    $dpegDetails[$dpegRow['dpeg_id']]['dpeg'] = [
        'name' => $dpegRow['dpeg_name'],
        'title' => $dpegRow['dpeg_title']
    ];
    $dpegDetails[$dpegRow['dpeg_id']]['evaluators'][] = [
        'id' => $dpegRow['evaluator_id'],
        'name' => $dpegRow['evaluator_name'],
        'email' => $dpegRow['evaluator_email']
    ];
}

// Fetch all students for the specific batch
$studentsQuery = "
    SELECT s.batch_id, s.rollno, s.name AS student_name, s.email AS student_email, s.mobile AS student_mobile
    FROM students s
    WHERE s.batch_id = '$batch_id'
";
$studentsResult = $conn->query($studentsQuery);
$studentsDetails = [];
while ($studentRow = $studentsResult->fetch_assoc()) {
    $studentsDetails[] = $studentRow;
}
?>

<style>
.table {
    width: 100%;
    margin-bottom: 1rem;
    background-color: transparent;
    border: solid black;
}

.table-bordered {
    border: 2px solid black;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}

.table-bordered thead{
    border: 1px solid black;
}

.thead-dark th {
    color: black;
    background-color: #343a40;
    border-color: black;
}
</style>
<body>
<div id="layout-wrapper">
    <?php include('header.php'); ?>
    <?php include('navbar.php'); ?>
    <div class="vertical-overlay"></div>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                            <h4 class="mb-sm-0">Batch Details</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active">View Batch</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed View -->
                <div id="details-container" class="mt-4">
                    <h5>Batch Details</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Batch ID</th>
                                <th>Batch Name</th>
                                <th>Batch Title</th>
                                <th>Supervisor</th>
                                <th>MRG</th>
                                <th>DPEG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($batchDetails['batch_id']); ?></td>
                                <td><?php echo htmlspecialchars($batchDetails['batch_name']); ?></td>
                                <td><?php echo htmlspecialchars($batchDetails['batch_title']); ?></td>
                                <td><?php echo htmlspecialchars($batchDetails['supervisor_name']); ?></td>
                                <td><?php echo empty($batchDetails['mrg_id']) ? 'Not Allocated' : htmlspecialchars($mrgDetails[$batchDetails['mrg_id']]['mrg']['name']); ?></td>
                <td><?php echo empty($batchDetails['dpeg_id']) ? 'Not Allocated' : htmlspecialchars($dpegDetails[$batchDetails['dpeg_id']]['dpeg']['name']); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <h5>Batch Members</h5>
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
                            <?php foreach ($studentsDetails as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_mobile']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h5>MRG Details</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>MRG ID</th>
                                <th>MRG Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($batchDetails['mrg_id'])): ?>
                <tr>
                    <td colspan="3">Not Allocated</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mrgDetails as $mrg_id => $details): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($mrg_id); ?></td>
                        <td><?php echo htmlspecialchars($details['mrg']['name']); ?></td>
                        <td><?php echo htmlspecialchars($details['mrg']['title']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>

                        
                    </table>

                    <h5>MRG Evaluators</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Evaluator ID</th>
                                <th>Evaluator Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($batchDetails['mrg_id'])): ?>
                <tr>
                    <td colspan="3">Not Allocated</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mrgDetails[$batchDetails['mrg_id']]['evaluators'] as $evaluator): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($evaluator['id']); ?></td>
                        <td><?php echo htmlspecialchars($evaluator['name']); ?></td>
                        <td><?php echo htmlspecialchars($evaluator['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>
                    </table>

                    <h5>DPEG Details</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>DPEG ID</th>
                                <th>DPEG Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($batchDetails['dpeg_id'])): ?>
                <tr>
                    <td colspan="3">Not Allocated</td>
                </tr>
            <?php else: ?>
                <?php foreach ($dpegDetails as $dpeg_id => $details): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dpeg_id); ?></td>
                        <td><?php echo htmlspecialchars($details['dpeg']['name']); ?></td>
                        <td><?php echo htmlspecialchars($details['dpeg']['title']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>

                        
                    </table>

                    <h5>DPEG Evaluators</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Evaluator ID</th>
                                <th>Evaluator Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($batchDetails['dpeg_id'])): ?>
                <tr>
                    <td colspan="3">Not Allocated</td>
                </tr>
            <?php else: ?>
                <?php foreach ($dpegDetails[$batchDetails['dpeg_id']]['evaluators'] as $evaluator): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($evaluator['id']); ?></td>
                        <td><?php echo htmlspecialchars($evaluator['name']); ?></td>
                        <td><?php echo htmlspecialchars($evaluator['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Print Button -->
                <div class="mt-4">
                    <button id="print-report-btn" class="btn btn-primary">Print Report</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>

<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.6.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.6.3/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
    document.getElementById('print-report-btn').addEventListener('click', function() {
        const element = document.getElementById('details-container');

        // Configure the PDF options
        const opt = {
            margin:       1,
            filename:     'batch_and_details.pdf',
            image:        { type: 'jpeg', quality: 2 },
            html2canvas:  { scale: 5 },
            jsPDF:        { unit: 'in', format: 'A4', orientation: 'portrait' }
        };

        // Generate the PDF
        html2pdf().from(element).set(opt).save();
    });
</script>


</body>
</html>
