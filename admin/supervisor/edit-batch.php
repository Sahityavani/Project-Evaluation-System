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

$batchesQuery = "
    SELECT b.batch_id, b.batch_name, b.batch_title, COUNT(s.rollno) AS num_members, IFNULL(su.name, 'Not Allocated') AS supervisor_name
    FROM batches b
    LEFT JOIN students s ON b.batch_id = s.batch_id
    LEFT JOIN supervisors su ON b.supervisor = su.username
    WHERE b.supervisor = '$username'
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
    
    // Handle form submission
    
    // Handle delete student button click
    $(document).on('click', '.delete-student-btn', function() {
        var rollNo = $(this).data('rollno');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_student.php',
                    type: 'POST',
                    data: { rollno: rollNo },
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire(
                                'Deleted!',
                                'Student has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload(); // Reload to reflect changes
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete student.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            }
        });
    });

    $(document).on('click', '.edit-student-btn', function() {
        var rollNo = $(this).data('rollno');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var mobile = $(this).data('mobile');

        $('#edit-student-modal').find('input[name="rollno"]').val(rollNo);
        $('#edit-student-modal').find('input[name="name"]').val(name);
        $('#edit-student-modal').find('input[name="email"]').val(email);
        $('#edit-student-modal').find('input[name="mobile"]').val(mobile);

        var editStudentModal = new bootstrap.Modal(document.getElementById('edit-student-modal'));
        editStudentModal.show();
    });

    // Handle form submission
    $(document).on('submit', '#edit-student-form', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'update_student.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire(
                        'Updated!',
                        'Student details have been updated.',
                        'success'
                    ).then(() => {
                        var editStudentModal = bootstrap.Modal.getInstance(document.getElementById('edit-student-modal'));
                        editStudentModal.hide();
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        response.message || 'Failed to update student details.',
                        'error'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });

    // Handle edit batch button click
    $(document).on('click', '.edit-batch-btn', function() {
        var batchId = $(this).data('id');
        var batchName = $(this).data('name');
        var batchTitle = $(this).data('title');
        
        $('#edit-batch-modal').find('input[name="batch_id"]').val(batchId);
        $('#edit-batch-modal').find('input[name="batch_name"]').val(batchName);
        $('#edit-batch-modal').find('input[name="batch_title"]').val(batchTitle);
        
        var editBatchModal = new bootstrap.Modal(document.getElementById('edit-batch-modal'));
        editBatchModal.show();
    });

    // Handle batch form submission
    $(document).on('submit', '#edit-batch-form', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'update_batch.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire(
                        'Updated!',
                        'Batch details have been updated.',
                        'success'
                    ).then(() => {
                        $('#edit-batch-modal').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        response.message || 'Failed to update batch details.',
                        'error'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });
});

        </script>
    </div>
</body>

</html>