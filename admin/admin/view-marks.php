<?php
include('auth.php');
$title = "Admin | View Batches";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch batches
$batchesQuery = "
    SELECT b.batch_id, b.batch_name, b.batch_title, COUNT(s.rollno) AS num_members, IFNULL(su.name, 'Not Allocated') AS supervisor_name
    FROM batches b
    LEFT JOIN students s ON b.batch_id = s.batch_id
    LEFT JOIN supervisors su ON b.supervisor = su.username
    GROUP BY b.batch_id
";
$batchesResult = $conn->query($batchesQuery);

// Fetch evaluation types
$evaluationTypesQuery = "SELECT id, name FROM evaluation_type";
$evaluationTypesResult = $conn->query($evaluationTypesQuery);
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

                    <!-- Marks Container -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">View Marks</h4>
                        </div>
                        <div class="card-body">
                            <form id="view-marks-form">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="batch-id" class="form-label">Batch ID</label>
                                        <select id="batch-id" class="form-select">
                                            <option value="">Select Batch ID</option>
                                            <?php
                                            $batchesResult->data_seek(0); // Reset the pointer to the start of the result set
                                            while ($batch = $batchesResult->fetch_assoc()) {
                                                echo "<option value=\"" . htmlspecialchars($batch['batch_id']) . "\">" . htmlspecialchars($batch['batch_id']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="evaluation-type" class="form-label">Evaluation Type</label>
                                        <select id="evaluation-type" class="form-select">
                                            <option value="">Select Evaluation Type</option>
                                            <?php while ($type = $evaluationTypesResult->fetch_assoc()): ?>
                                                <option value="<?php echo htmlspecialchars($type['id']); ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="button" id="get-marks-btn" class="btn btn-primary">Get Marks</button>
                            </form>
                        </div>
                    </div>

                    <div id="marks-container" class="mt-4" style="display: none;">
                        <button id="hide-marks-btn" class="btn btn-secondary mb-3">Hide Details</button>
                        <!-- Marks table will be dynamically inserted here -->
                    </div>
                </div>
            </div>

            <?php include('../footer.php'); ?>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <?php if ($error): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?php echo $error; ?>'
                });
            </script>
            <?php endif; ?>
            <?php if ($success): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?php echo $success; ?>'
                });
            </script>
            <?php endif; ?>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
            <script>
$(document).ready(function() {
    $('#get-marks-btn').click(function() {
        var batchId = $('#batch-id').val();
        var evaluationType = $('#evaluation-type').val();

        if (!batchId || !evaluationType) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select both Batch ID and Evaluation Type.'
            });
            return;
        }

        // Fetch evaluations for the selected type
        $.ajax({
            url: 'fetch_evaluations.php',
            type: 'GET',
            dataType: 'json',
            data: {
                batch_id: batchId,
                evaluation_type: evaluationType
            },
            success: function(data) {
                var evaluations = data;
                var detailsHtml = '';
                for (const [rollno, studentData] of Object.entries(evaluations)) {
                    detailsHtml += `<h5>Student: ${studentData.student_name} (Roll No: ${rollno})</h5>`;
                    for (const [type, evaluationData] of Object.entries(studentData.evaluations)) {
                        detailsHtml += `<h6>${evaluationData.type_name} Evaluations</h6>`;

                        // Handle MRG Evaluations
                        detailsHtml += `<table class="table table-striped table-bordered"><thead><tr><th>Evaluator Name</th><th>Marks</th><th>Remarks</th><th>Marks Evaluated</th></tr></thead><tbody>`;
                        if (evaluationData.mrg && evaluationData.mrg.length > 0) {
                            for (const evaluation of evaluationData.mrg) {
                                detailsHtml += `<tr><td>${evaluation.evaluator_name || 'N/A'}</td><td>${evaluation.marks || 'N/A'}</td><td>${evaluation.remarks || 'N/A'}</td><td>${evaluation.sub_parts ? Object.entries(evaluation.sub_parts).map(([key, value]) => `${key}: ${value}`).join('<br>') : 'N/A'}</td></tr>`;
                            }
                        }else if (evaluationData.dpeg && evaluationData.dpeg.length > 0) {
                                for (const evaluation of evaluationData.dpeg) {
                                    detailsHtml += `<tr><td>${evaluation.evaluator_name || 'N/A'}</td><td>${evaluation.marks || 'N/A'}</td><td>${evaluation.remarks || 'N/A'}</td><td>${evaluation.sub_parts ? Object.entries(evaluation.sub_parts).map(([key, value]) => `${key}: ${value}`).join('<br>') : 'N/A'}</td></tr>`;
                                }
                            } 
                        
                        else {
                            detailsHtml += `<tr><td colspan="4">No MRG evaluations available</td></tr>`;
                        }
                        detailsHtml += `</tbody></table>`;

                        // Handle Supervisor Evaluations
                        detailsHtml += `<h6>Supervisor Evaluations</h6>`;
                        detailsHtml += `<table class="table table-striped table-bordered"><thead><tr><th>Supervisor Name</th><th>Marks</th><th>Remarks</th><th>Marks Evaluated</th></tr></thead><tbody>`;
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
                detailsHtml += `<div class="mt-4"><button id="print-btn" class="btn btn-primary">Print Report</button></div>`;
                $('#marks-container').html(detailsHtml);
                $('#marks-container').show();

                // Attach the click event to the print button after it's added to the DOM
                $('#print-btn').click(function() {
                    // Example of generating a PDF without worrying about missing images
const opt = {
    margin: 1,
    filename: 'marks_details.pdf',
    html2canvas: { scale: 2, useCORS: true },
    jsPDF: { unit: 'in', format: 'A4', orientation: 'portrait' }
};
html2pdf().set(opt).from(element).save();

                });

                $('#hide-marks-btn').click(function() {
                    $('#marks-container').hide();
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch marks. Please try again.'
                });
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.9.2/dist/html2pdf.bundle.min.js"></script>

        </div>
    </div>
</body>
</html>