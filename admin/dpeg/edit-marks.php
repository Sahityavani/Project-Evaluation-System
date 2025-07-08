<?php
include('auth.php');
include('../db.php');

$title = "Evaluator | Edit Marks";
include('../head.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM dept_evaluators WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'];
$type = 'DPEG Evaluator';



// Fetch evaluator ID
$sql = "SELECT evaluator_id FROM dept_evaluators WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $evaluatorId = $row['evaluator_id'];

    // Get Batch Details directly from DPEG Evaluations
    $sql = "
        SELECT b.*, m.dpeg_name, GROUP_CONCAT(DISTINCT e.name SEPARATOR ', ') AS evaluators 
        FROM dpeg_evaluations me
        JOIN batches b ON me.batch_id = b.batch_id
        LEFT JOIN dpeg m ON b.dpeg_id = m.dpeg_id
        LEFT JOIN dpeg_evaluators mre ON m.dpeg_id = mre.dpeg_id
        LEFT JOIN dept_evaluators e ON mre.evaluator_id = e.evaluator_id
        WHERE me.evaluator_id = ?
        GROUP BY b.batch_id, m.dpeg_name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $evaluatorId); 
    $stmt->execute();
    $result = $stmt->get_result();
    
    $batches = [];
    while ($batch = $result->fetch_assoc()) {
        $batches[] = $batch;
    }
} else {
    echo "No evaluator found.";
    exit();
}

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php include('header.php'); ?>
        <?php include('navbar.php'); ?>
        <div class="vertical-overlay"></div>

        <!-- Start right Content here -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0">Edit Marks</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Edit Marks</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch and Evaluation Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Batches and Evaluations</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Batch ID</th>
                                            <th scope="col">Batch Name</th>
                                            <th scope="col">Evaluation Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($batches as $batch): ?>
                                            <?php
                                            // Fetch evaluation types for each batch
                                            $sql = "
                                                SELECT DISTINCT et.id, et.name
                                                FROM dpeg_evaluations me
                                                JOIN evaluation_type et ON me.evaluation_type = et.id
                                                WHERE me.batch_id = ?";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param('i', $batch['batch_id']);
                                            $stmt->execute();
                                            $eval_result = $stmt->get_result();
                                            while ($row = $eval_result->fetch_assoc()):
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($batch['batch_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($batch['batch_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary edit-marks-btn" data-batch-id="<?php echo htmlspecialchars($batch['batch_id']); ?>" data-eval-type="<?php echo htmlspecialchars($row['id']); ?>">Edit Marks</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Marks Details Modal -->
                    <div id="marksModal" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Marks Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="marksModalBody">
                                    <!-- Marks details will be loaded here -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('../footer.php'); ?>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
            $(document).ready(function() {
                $('.edit-marks-btn').click(function() {
                    var batchId = $(this).data('batch-id');
                    var evalType = $(this).data('eval-type');

                    $.ajax({
                        url: 'getedit_marks_details.php',
                        type: 'POST',
                        data: { batch_id: batchId, evaluation_type: evalType },
                        success: function(response) {
                            $('#marksModalBody').html(response);
                            var myModal = new bootstrap.Modal(document.getElementById('marksModal'));
                            myModal.show();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                        }
                    });
                });
            });
            </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to calculate total marks for a student
    function updateTotal(studentId) {
        let totalMarks = 0;
        // Get all select fields for the student
        document.querySelectorAll(`select[data-student-id="${studentId}"]`).forEach(select => {
            totalMarks += parseInt(select.value) || 0;
        });
        // Update the total marks input field
        document.getElementById(`total_${studentId}`).value = totalMarks;
    }

    // Event listener for all mark inputs
    document.querySelectorAll('.mark-input').forEach(select => {
        select.addEventListener('change', function() {
            const studentId = this.dataset.studentId;
            updateTotal(studentId);
        });
    });

    // Initial calculation for all students
    document.querySelectorAll('.mark-input').forEach(select => {
        const studentId = select.dataset.studentId;
        updateTotal(studentId);
    });
});
</script>


            
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
                    title: 'Done',
                    text: '<?php echo $success; ?>'
                });
                </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
