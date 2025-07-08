<?php
include('auth.php');
$title = "Student | View Marks";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM students WHERE rollno = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$type = $_SESSION['type'];
$batch_id = $row['batch_id'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

if (empty($batch_id)) {
    echo '<p class="alert alert-warning">You are not associated with any batch. Marks cannot be viewed.</p>';
    exit();
}

$batchQuery = "
    SELECT b.batch_id, b.batch_name, b.batch_title, su.name AS supervisor_name, b.mrg_id
    FROM batches b
    LEFT JOIN supervisors su ON b.supervisor = su.username
    WHERE b.batch_id = '$batch_id'
";
$batchResult = $conn->query($batchQuery);
$batchRow = $batchResult->fetch_assoc();
$batch_name = $batchRow['batch_name'];
$batch_title = $batchRow['batch_title'];
$supervisor_name = $batchRow['supervisor_name'];
$mrg_id = $batchRow['mrg_id'];



// Fetch evaluation types from mrg_evaluations
$evaluationTypes = [];
$evaluationTypeNames = [];
$mrgEvaluationsQuery = "
    SELECT DISTINCT e.evaluation_type, t.name as evaluation_type_name
    FROM mrg_evaluations e
    JOIN evaluation_type t ON e.evaluation_type = t.id
    WHERE e.batch_id = '$batch_id'
";
$mrgEvaluationsResult = $conn->query($mrgEvaluationsQuery);
while ($row = $mrgEvaluationsResult->fetch_assoc()) {
    $evaluationTypes[] = $row['evaluation_type'];
    $evaluationTypeNames[$row['evaluation_type']] = $row['evaluation_type_name'];
}


$mrgEvaluationsQuery = "
    SELECT DISTINCT e.evaluation_type, t.name as evaluation_type_name
    FROM dpeg_evaluations e
    JOIN evaluation_type t ON e.evaluation_type = t.id
    WHERE e.batch_id = '$batch_id'
";
$mrgEvaluationsResult = $conn->query($mrgEvaluationsQuery);
while ($row = $mrgEvaluationsResult->fetch_assoc()) {
    $evaluationTypes[] = $row['evaluation_type'];
    $evaluationTypeNames[$row['evaluation_type']] = $row['evaluation_type_name'];
}

// Fetch students in the batch
$studentsQuery = "
    SELECT rollno, name
    FROM students
    WHERE batch_id = '$batch_id'
";
$studentsResult = $conn->query($studentsQuery);
$students = [];
while ($row = $studentsResult->fetch_assoc()) {
    $students[] = $row;
}

// Function to fetch evaluations
function fetchEvaluations($conn, $batch_id, $evaluationType, $students) {
    $allEvaluations = [];
    foreach ($students as $student) {
        $studentEvaluations = [];
        // Fetch mrg_evaluations
        $mrgEvaluationsQuery = "
            SELECT ev.marks, ev.remarks, e.name AS evaluator_name
            FROM mrg_evaluations ev
            LEFT JOIN evaluators e ON ev.evaluator_id = e.evaluator_id
            WHERE ev.batch_id = ? AND ev.evaluation_type = ? AND ev.student_rollno = ?
        ";
        $mrgEvaluationsStmt = $conn->prepare($mrgEvaluationsQuery);
        $mrgEvaluationsStmt->bind_param('iis', $batch_id, $evaluationType, $student['rollno']);
        $mrgEvaluationsStmt->execute();
        $mrgEvaluationsResult = $mrgEvaluationsStmt->get_result();
        $mrgEvaluations = [];
        $totalEvaluatorMarks = 0;
        $numEvaluators = 0;
        while ($row = $mrgEvaluationsResult->fetch_assoc()) {
            $mrgEvaluations[] = $row;
            $totalEvaluatorMarks += $row['marks'];
            $numEvaluators++;
        }

        // Calculate average evaluator marks
        $averageEvaluatorMarks = $numEvaluators ? $totalEvaluatorMarks / $numEvaluators : 0;

        // Fetch supervisor_evaluations
        $supervisorEvaluationsQuery = "
            SELECT ev.marks, ev.remarks, s.name AS supervisor_name
            FROM supervisor_evaluations ev
            LEFT JOIN supervisors s ON ev.supervisor_id = s.id
            WHERE ev.batch_id = ? AND ev.evaluation_type = ? AND ev.student_rollno = ?
        ";
        $supervisorEvaluationsStmt = $conn->prepare($supervisorEvaluationsQuery);
        $supervisorEvaluationsStmt->bind_param('iis', $batch_id, $evaluationType, $student['rollno']);
        $supervisorEvaluationsStmt->execute();
        $supervisorEvaluationsResult = $supervisorEvaluationsStmt->get_result();
        $supervisorEvaluations = [];
        $totalSupervisorMarks = 0;
        while ($row = $supervisorEvaluationsResult->fetch_assoc()) {
            $supervisorEvaluations[] = $row;
            $totalSupervisorMarks += $row['marks'];
        }

        // Calculate total marks
        $totalMarks = $averageEvaluatorMarks + $totalSupervisorMarks;

        $studentEvaluations[$evaluationType] = [
            'type_name' => $evaluationTypeNames[$evaluationType],
            'mrg' => $mrgEvaluations,
            'supervisor' => $supervisorEvaluations,
            'average_evaluator_marks' => $averageEvaluatorMarks,
            'total_marks' => $totalMarks
        ];

        $allEvaluations[$student['rollno']] = [
            'student_name' => $student['name'],
            'evaluations' => $studentEvaluations
        ];
    }
    return $allEvaluations;
}
?>

<body>
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
                            <h4 class="mb-sm-0">Marks</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active">View Marks</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evaluation Types View -->
                <div class="mt-4">
                    <h5>Batch Details</h5>
                    <p>Batch ID: <?php echo htmlspecialchars($batch_id); ?></p>
                    <p>Batch Name: <?php echo htmlspecialchars($batch_name); ?></p>
                    <p>Batch Title: <?php echo htmlspecialchars($batch_title); ?></p>
                    <p>Supervisor: <?php echo htmlspecialchars($supervisor_name); ?></p>

                    <h5>Evaluation Types</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Evaluation Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evaluationTypes as $evaluationType): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($evaluationTypeNames[$evaluationType]); ?></td>
                                    <td>
                                        <button class="btn btn-primary view-evaluations" data-type="<?php echo htmlspecialchars($evaluationType); ?>">
                                            View Evaluations
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Detailed Evaluations View -->
                <div id="details-container" class="mt-4" style="display:none;">
                    <h5>Evaluation Details</h5>
                    <div id="evaluation-details"></div>
                </div>

                <!-- Print Button -->
                <div class="mt-4">
                    <button id="print-btn" class="btn btn-primary">Print Report</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>




<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-evaluations');
    const detailsContainer = document.getElementById('details-container');
    const evaluationDetails = document.getElementById('evaluation-details');
    const printReportBtn = document.getElementById('print-report-btn');

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const evaluationType = this.getAttribute('data-type');
            const batchId = '<?php echo $batch_id; ?>'; // Ensure this PHP variable is set correctly

            // Fetch evaluations for the selected type
            fetch(`fetch_evaluations.php?batch_id=${batchId}&evaluation_type=${evaluationType}`)
                .then(response => response.json())
                .then(data => {
                    // Populate evaluation details
                    let detailsHtml = '';
                    for (const [rollno, studentData] of Object.entries(data)) {
                        detailsHtml += `<h5>Student: ${studentData.student_name} (Roll No: ${rollno})</h5>`;
                        for (const [type, evaluationData] of Object.entries(studentData.evaluations)) {
                            detailsHtml += `<h6>${evaluationData.type_name} Evaluations</h6>`;
                            if(evaluationData.mrg){
                                detailsHtml += `<h6>MRG Evaluations</h6>`;
                            }
                            else if(evaluationData.dpeg){
                                detailsHtml += `<h6>DPEG Evaluations</h6>`;
                            }
                            detailsHtml += `<table class="table table-bordered"><thead><tr><th>Evaluator Name</th><th>Marks</th><th>Remarks</th><th>Marks Evaluated</th></tr></thead><tbody>`;
                            if (evaluationData.mrg && evaluationData.mrg.length > 0) {
                                for (const evaluation of evaluationData.mrg) {
                                    detailsHtml += `<tr><td>${evaluation.evaluator_name || 'N/A'}</td><td>${evaluation.marks || 'N/A'}</td><td>${evaluation.remarks || 'N/A'}</td><td>${evaluation.sub_parts ? Object.entries(evaluation.sub_parts).map(([key, value]) => `${key}: ${value}`).join('<br>') : 'N/A'}</td></tr>`;
                                }
                            }
                            else if (evaluationData.dpeg && evaluationData.dpeg.length > 0) {
                                for (const evaluation of evaluationData.dpeg) {
                                    detailsHtml += `<tr><td>${evaluation.evaluator_name || 'N/A'}</td><td>${evaluation.marks || 'N/A'}</td><td>${evaluation.remarks || 'N/A'}</td><td>${evaluation.sub_parts ? Object.entries(evaluation.sub_parts).map(([key, value]) => `${key}: ${value}`).join('<br>') : 'N/A'}</td></tr>`;
                                }
                            } else {
                                detailsHtml += `<tr><td colspan="4">No MRG evaluations available</td></tr>`;
                            }
                            detailsHtml += `</tbody></table>`;

                            detailsHtml += `<h6>Supervisor Evaluations</h6>`;
                            detailsHtml += `<table class="table table-bordered"><thead><tr><th>Supervisor Name</th><th>Marks</th><th>Remarks</th><th>Marks Evaluted</th></tr></thead><tbody>`;
                            if (evaluationData.supervisor && evaluationData.supervisor.length > 0) {
                                for (const evaluation of evaluationData.supervisor) {
                                    detailsHtml += `<tr><td>${evaluation.supervisor_name || 'N/A'}</td><td>${evaluation.marks || 'N/A'}</td><td>${evaluation.remarks || 'N/A'}</td><td>${evaluation.sub_parts ? Object.entries(evaluation.sub_parts).map(([key, value]) => `${key}: ${value}`).join('<br>') : 'N/A'}</td></tr>`;
                                }
                            } else {
                                detailsHtml += `<tr><td colspan="4">No Supervisor evaluations available</td></tr>`;
                            }
                            detailsHtml += `</tbody></table>`;

                            detailsHtml += `<p><b>Average Evaluator Marks: ${evaluationData.average_evaluator_marks.toFixed(2)}</b></p>`;
                            detailsHtml += `<p><b>Total Marks: ${evaluationData.total_marks.toFixed(2)}</b></p>`;
                        }
                    }
                    evaluationDetails.innerHTML = detailsHtml;

                    // Show details container and print button
                    detailsContainer.style.display = 'block';
                    printReportBtn.style.display = 'inline-block';
                });
        });
    });

    document.getElementById('print-btn').addEventListener('click', function() {
        const element = document.getElementById('details-container');

        // Configure the PDF options
        const opt = {
            margin:       1,
            filename:     'marks_details.pdf',
            image:        { type: 'jpeg', quality: 2 },
            html2canvas:  { scale: 5 },
            jsPDF:        { unit: 'in', format: 'A4', orientation: 'portrait' }
        };

        // Generate the PDF
        html2pdf().from(element).set(opt).save();
    });
});
</script>


</body>
</html>
