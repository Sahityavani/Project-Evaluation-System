<?php
include('auth.php');
$title = "Supervisor | View Students";
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

// Query to fetch student details
$studentsQuery = "
    SELECT s.id, s.rollno, s.name, s.batch_id, s.mrg_id
    FROM students s
";
$studentsResult = $conn->query($studentsQuery);
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
                                <h4 class="mb-sm-0">Students</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">View Students</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Students</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Roll No</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Batch No</th>
                                            <th scope="col">MRG No</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($student = $studentsResult->fetch_assoc()): ?>
                                            <tr data-id="<?php echo $student['id']; ?>">
                                                <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['batch_id'] ? $student['batch_id'] : 'Not Assigned'); ?></td>
                                                <td><?php echo htmlspecialchars($student['mrg_id'] ? $student['mrg_id'] : 'Not Assigned'); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary view-details-btn" data-id="<?php echo $student['id']; ?>">View Details</button>
                                                </td>
                                            </tr>
                                            <tr class="details-row" id="details-<?php echo $student['id']; ?>" style="display: none;">
                                                <td colspan="5">
                                                    <!-- Details will be loaded here -->
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
                $('.view-details-btn').click(function() {
                    var studentId = $(this).data('id');
                    var $detailsRow = $('#details-' + studentId);

                    if ($detailsRow.is(':visible')) {
                        $detailsRow.hide();
                        return;
                    }

                    $.ajax({
                        url: 'get_student_details.php',
                        type: 'POST',
                        data: { student_id: studentId },
                        success: function(response) {
                            $detailsRow.html('<td colspan="5">' + response + '</td>').show();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                        }
                    });
                });
            });
            </script>

            <style>
            /* Custom Styles for Student Details Table */
            #student-details-container table {
                background-color: #f8f9fa; /* Light gray background for table */
                border: 1px solid #dee2e6; /* Border around table */
                border-radius: 0.375rem; /* Rounded corners */
            }
            #student-details-container th {
                background-color: #e9ecef; /* Slightly darker background for header */
                color: #495057; /* Darker text color */
            }
            #student-details-container td {
                color: #212529; /* Darker text color */
            }
            </style>

        </div>
    </body>
</html>
