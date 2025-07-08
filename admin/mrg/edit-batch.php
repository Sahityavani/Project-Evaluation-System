<?php
include('auth.php');
$title = "Evaluator | Edit Batches";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM evaluators WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$type = $_SESSION['type'];

$sql = "SELECT evaluator_id FROM evaluators WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$evaluatorId = $row['evaluator_id'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch batches
$batchesQuery = "
    SELECT b.batch_id, b.batch_name, b.batch_title, COUNT(s.rollno) AS num_members, IFNULL(su.name, 'Not Allocated') AS supervisor_name
    FROM batches b
    LEFT JOIN students s ON b.batch_id = s.batch_id
    LEFT JOIN supervisors su ON b.supervisor = su.username
    INNER JOIN mrg_batches mb ON b.batch_id = mb.batch_id
    INNER JOIN mrg_evaluators me ON mb.mrg_id = me.mrg_id
    WHERE me.evaluator_id = $evaluatorId
    GROUP BY b.batch_id
";
$batchesResult = $conn->query($batchesQuery);
?>

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

                <!-- Batches Cards -->
                <div class="row mt-4">
                    <?php while ($batch = $batchesResult->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Batch ID: <?php echo htmlspecialchars($batch['batch_id']); ?></h6>
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($batch['batch_name']); ?></h5>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($batch['batch_title']); ?></p>
                                    <p class="text-muted mb-0">Supervisor: <?php echo htmlspecialchars($batch['supervisor_name']); ?></p>
                                    <p class="text-muted mb-0">Number of Members: <?php echo htmlspecialchars($batch['num_members']); ?></p>
                                </div>
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-primary view-students-btn" data-id="<?php echo $batch['batch_id']; ?>">View Students</button>
                                    <button type="button" class="btn btn-warning edit-batch-btn" data-id="<?php echo $batch['batch_id']; ?>" data-name="<?php echo htmlspecialchars($batch['batch_name']); ?>" data-title="<?php echo htmlspecialchars($batch['batch_title']); ?>">Edit Batch</button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Students Container -->
                <div id="students-container" class="mt-4" style="display: none;">
                    <button id="hide-students-btn" class="btn btn-secondary mb-3">Hide Students</button>
                    <!-- Students cards will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <?php include('../footer.php'); ?>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="edit-student-modal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-student-form" action="update_student.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="student-rollno">Roll No</label>
                            <input type="text" class="form-control" name="rollno" id="student-rollno" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="student-name">Name</label>
                            <input type="text" class="form-control" name="name" id="student-name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="student-email">Email</label>
                            <input type="email" class="form-control" name="email" id="student-email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="student-mobile">Mobile</label>
                            <input type="text" class="form-control" name="mobile" id="student-mobile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Batch Modal -->
    <div class="modal fade" id="edit-batch-modal" tabindex="-1" role="dialog" aria-labelledby="editBatchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBatchModalLabel">Edit Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-batch-form" action="update_batch.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="batch-id">Batch ID</label>
                            <input type="text" class="form-control" name="batch_id" id="batch-id" readonly>
                        </div>
                        <div class="form-group">
                            <label for="batch-name">Name</label>
                            <input type="text" class="form-control" name="batch_name" id="batch-name" required>
                        </div>
                        <div class="form-group">
                            <label for="batch-title">Title</label>
                            <input type="text" class="form-control" name="batch_title" id="batch-title" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Handle view students button click
    $('.view-students-btn').click(function() {
        var batchId = $(this).data('id');
        
        $.ajax({
            url: 'get_students.php',
            type: 'POST',
            data: { batch_id: batchId },
            success: function(response) {
                $('#students-container').html(response + '<button id="hide-students-btn" class="btn btn-secondary mb-3">Hide Students</button>').show();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });
    
    // Handle hide students button click
    $(document).on('click', '#hide-students-btn', function() {
        $('#students-container').hide();
    });

    // Handle edit student button click
    $(document).on('click', '.edit-student-btn', function() {
        var rollno = $(this).data('rollno');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var mobile = $(this).data('mobile');

        $('#student-rollno').val(rollno);
        $('#student-name').val(name);
        $('#student-email').val(email);
        $('#student-mobile').val(mobile);

        $('#edit-student-modal').modal('show');
    });

    // Handle edit batch button click
    $('.edit-batch-btn').click(function() {
        var batchId = $(this).data('id');
        var batchName = $(this).data('name');
        var batchTitle = $(this).data('title');

        $('#batch-id').val(batchId);
        $('#batch-name').val(batchName);
        $('#batch-title').val(batchTitle);

        $('#edit-batch-modal').modal('show');
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
</body>
</html>
