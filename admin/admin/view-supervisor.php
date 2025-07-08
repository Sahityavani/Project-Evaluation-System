<?php
include('auth.php');
$title = "Admin | View Supervisors";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$supervisorsQuery = "SELECT * FROM supervisors";
$supervisorsResult = $conn->query($supervisorsQuery);

// Fetch batch members for a specific batch
$batchMembers = [];
if (isset($_GET['batch_id'])) {
    $batchId = intval($_GET['batch_id']);
    $membersQuery = "
        SELECT s.rollno, s.name AS student_name, s.email, s.mobile
        FROM students s
        WHERE s.batch_id = $batchId
    ";
    $membersResult = $conn->query($membersQuery);
    while ($row = $membersResult->fetch_assoc()) {
        $batchMembers[] = $row;
    }
}
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
                                <h4 class="mb-sm-0">Supervisors</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">View Supervisors</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supervisors Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Supervisors</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Supervisor ID</th>
                                            <th scope="col">Supervisor Name</th>
                                            <th scope="col">Supervisor Username</th>
                                            <th scope="col">Batch ID</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($supervisor = $supervisorsResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($supervisor['id']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['name']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['username']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['batch_id'] ?: 'Not Assigned'); ?></td>
                                                <td>
                                                    <?php if ($supervisor['batch_id']): ?>
                                                        <button type="button" class="btn btn-primary view-members-btn" data-id="<?php echo $supervisor['batch_id']; ?>">Show Batch Members</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Batch Members Container -->
                    <div id="members-container" class="mt-4" style="display: none;">
                        <button id="hide-members-btn" class="btn btn-secondary mb-3">Hide Details</button>
                        <!-- Batch members table will be dynamically inserted here -->
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
            <script>
            $(document).ready(function() {
                $('.view-members-btn').click(function() {
                    var batchId = $(this).data('id');
                    
                    $.ajax({
                        url: 'get_batch_members.php',
                        type: 'POST',
                        data: { batch_id: batchId },
                        success: function(response) {
                            $('#members-container').html(response + '<button id="hide-members-btn" class="btn btn-secondary mb-3">Hide Details</button>').show();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                        }
                    });
                });

                $(document).on('click', '#hide-members-btn', function() {
                    $('#members-container').hide();
                });
            });
            </script>




            <style>
            /* Custom Styles for Batch Members Table */
            #members-container table {
                background-color: #f8f9fa; /* Light gray background for table */
                border: 1px solid #dee2e6; /* Border around table */
                border-radius: 0.375rem; /* Rounded corners */
            }
            #members-container th {
                background-color: #e9ecef; /* Slightly darker background for header */
                color: #495057; /* Darker text color */
            }
            #members-container td {
                color: #212529; /* Darker text color */
            }
            </style>

        </div>
    </body>
</html>
