<?php
include('auth.php');
$title = "Admin | View Evaluation Types";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Query to fetch evaluation types
$evaluationTypesQuery = "SELECT * FROM evaluation_type";
$evaluationTypesResult = $conn->query($evaluationTypesQuery);

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
                                <h4 class="mb-sm-0">Evaluation Types</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">View Evaluation Types</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation Types Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Evaluation Types</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Evaluation Type ID</th>
                                            <th scope="col">Evaluation Type Name</th>
                                            <th scope="col">Max Marks by MRG/DPEG</th>
                                            <th scope="col">Max Marks by Supervisor</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Sub Parts (MRG/DPEG)</th>
                                            <th scope="col">Sub Parts (Supervisor)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($evaluationType = $evaluationTypesResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($evaluationType['id']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluationType['name']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluationType['max_marks_mrg']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluationType['max_marks_supervisor']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluationType['type']); ?></td>
                                                <td>
                                                    <?php
                                                    $subPartsMRG = json_decode($evaluationType['sub_parts'], true);
                                                    foreach ($subPartsMRG as $subPart => $marks) {
                                                        echo $subPart . ' (' . $marks . ')<br>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $subPartsSupervisor = json_decode($evaluationType['sub_parts_supervisor'], true);
                                                    foreach ($subPartsSupervisor as $subPart => $marks) {
                                                        echo $subPart . ' (' . $marks . ')<br>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Evaluation Type Modal -->
                    <div class="modal fade" id="editEvaluationTypeModal" style="display:none;" tabindex="-1" role="dialog" aria-labelledby="editEvaluationTypeModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editEvaluationTypeModalLabel">Edit Evaluation Type</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="editEvaluationTypeModalBody">
                                    <!-- Edit form will be displayed here -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
                                </div>
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
            

            <style>
            /* Custom Styles for Evaluation Types Table */
            table {
                background-color: #f8f9fa; /* Light gray background for table */
                border: 1px solid #dee2e6; /* Border around table */
                border-radius: 0.375rem; /* Rounded corners */
            }
            th {
                background-color: #e9ecef; /* Slightly darker background for header */
                color: #495057; /* Darker text color */
            }
            td {
                color: #212529; /* Darker text color */
            }
            </style>
        </div>
    </body>
</html>