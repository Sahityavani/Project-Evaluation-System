<?php
include('auth.php');
include('../db.php');

$title = "Supervisor | Edit Marks";
include('../head.php');

$username = $conn->real_escape_string($_SESSION['user']);

// Fetch batches assigned to the supervisor
$sql = "SELECT batch_id FROM batches WHERE supervisor = '$username'";
$batches_result = $conn->query($sql);

// Collect batch IDs
$batch_ids = [];
while ($row = $batches_result->fetch_assoc()) {
    $batch_ids[] = $row['batch_id'];
}

if (empty($batch_ids)) {
    echo "No batches assigned to you.";
    exit();
}

// Fetch evaluation types with evaluations
$evaluation_types = [];
$sql = "
    SELECT DISTINCT et.id, et.name
    FROM supervisor_evaluations e
    JOIN evaluation_type et ON e.evaluation_type = et.id
    WHERE e.batch_id IN (" . implode(',', array_map('intval', $batch_ids)) . ")
";
$eval_result = $conn->query($sql);
while ($row = $eval_result->fetch_assoc()) {
    $evaluation_types[$row['id']] = $row['name'];
}

$sql = "SELECT * FROM supervisors WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'];
$type = $_SESSION['type'];
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php include('header.php'); ?>
        <?php include('navbar.php'); ?>
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
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

                    <!-- Evaluation Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Evaluation Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Evaluation ID</th>
                                            <th scope="col">Evaluation Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($evaluation_types as $eval_id => $eval_name): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($eval_id); ?></td>
                                                <td><?php echo htmlspecialchars($eval_name); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary edit-marks-btn" data-eval-type="<?php echo htmlspecialchars($eval_id); ?>">Edit Marks</button>
                                                </td>
                                            </tr>
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
                    var evalType = $(this).data('eval-type');

                    $.ajax({
                        url: 'getedit_marks_details.php',
                        type: 'POST',
                        data: { evaluation_type: evalType },
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
            <?php if ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?php echo $error; ?>'
        });
        </script>
    <?php endif; ?>
    <?php if ($success) : ?>
        <script>
        Swal.fire({
                icon: 'success',
                title: 'Done',
                text: '<?php echo $success;?>'
            });
            </script>
    <?php endif; ?>
        </div>
    </body>
</html>
