<?php
include('auth.php');
$title = "Supervisor | View Batches";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM supervisors WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$type = $_SESSION['type'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch batches with students and their MRG details for the specific supervisor
$batchesQuery = "
    SELECT b.batch_id, b.batch_name, b.batch_title, COUNT(s.rollno) AS num_members, IFNULL(su.name, 'Not Allocated') AS supervisor_name, b.mrg_id
    FROM batches b
    LEFT JOIN students s ON b.batch_id = s.batch_id
    LEFT JOIN supervisors su ON b.supervisor = su.username
    WHERE su.username = '$username'
    GROUP BY b.batch_id
";
$batchesResult = $conn->query($batchesQuery);

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

// Fetch all students for each batch
$studentsQuery = "
    SELECT s.batch_id, s.rollno, s.name AS student_name, s.email AS student_email, s.mobile AS student_mobile
    FROM students s
    JOIN batches b ON s.batch_id = b.batch_id
    WHERE b.supervisor = '$username'
";
$studentsResult = $conn->query($studentsQuery);
$studentsDetails = [];
while ($studentRow = $studentsResult->fetch_assoc()) {
    $studentsDetails[$studentRow['batch_id']][] = $studentRow;
}
?>

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
                            <h4 class="mb-sm-0">Batches</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active">View Batches</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed View -->
                <div id="details-container" class="mt-4">
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
                            <?php foreach ($mrgDetails as $mrg_id => $details): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($mrg_id); ?></td>
                                    <td><?php echo htmlspecialchars($details['mrg']['name']); ?></td>
                                    <td><?php echo htmlspecialchars($details['mrg']['title']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h5>Evaluators</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Evaluator ID</th>
                                <th>Evaluator Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mrgDetails as $details): ?>
                                <?php foreach ($details['evaluators'] as $evaluator): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($evaluator['id']); ?></td>
                                        <td><?php echo htmlspecialchars($evaluator['name']); ?></td>
                                        <td><?php echo htmlspecialchars($evaluator['email']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h5>Batch Details</h5>
                    <?php foreach ($studentsDetails as $batch_id => $students): ?>
                        <h6>Batch ID: <?php echo htmlspecialchars($batch_id); ?></h6>
                        <?php
                        // Fetch supervisor name for this batch
                        $batchQuery = "SELECT b.supervisor, b.mrg_id FROM batches b WHERE b.batch_id = '$batch_id'";
                        $batchResult = $conn->query($batchQuery);
                        $batchRow = $batchResult->fetch_assoc();
                        $supervisor = $batchRow['supervisor'];
                        $mrg_id = $batchRow['mrg_id'];
                        $supervisorName = 'Not Allocated';
                        if ($supervisor) {
                            $supervisorQuery = "SELECT name FROM supervisors WHERE username = '$supervisor'";
                            $supervisorResult = $conn->query($supervisorQuery);
                            $supervisorRow = $supervisorResult->fetch_assoc();
                            $supervisorName = $supervisorRow['name'];
                        }
                        ?>
                        <p>Supervisor: <?php echo htmlspecialchars($supervisorName); ?></p>
                        <p>MRG: <?php echo htmlspecialchars($mrgDetails[$mrg_id]['mrg']['name']); ?></p>
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
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                                        <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['student_email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['student_mobile']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
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
            filename:     'batch_and_mrg_details.pdf',
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
