<?php
include('auth.php');
$title = "Admin | View DPEG Evaluators";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$evaluatorsQuery = "
    SELECT e.evaluator_id, e.name, e.username, m.dpeg_id
    FROM dept_evaluators e
    LEFT JOIN dpeg_evaluators me ON e.evaluator_id = me.evaluator_id
    LEFT JOIN dpeg m ON me.dpeg_id = m.dpeg_id
";
$evaluatorsResult = $conn->query($evaluatorsQuery);

// Fetch DPEG details for a specific DPEG
$dpegDetails = [];
if (isset($_GET['dpeg_id'])) {
    $dpegId = intval($_GET['dpeg_id']);
    $dpegQuery = "
        SELECT m.dpeg_name, m.dpeg_title
        FROM dpeg m
        WHERE m.dpeg_id = $dpegId
    ";
    $dpegResult = $conn->query($dpegQuery);
    if ($dpegResult->num_rows > 0) {
        $dpegDetails = $dpegResult->fetch_assoc();
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
                                <h4 class="mb-sm-0">DPEG Evaluators</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">View DPEG Evaluators</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DPEG Details (if applicable) -->
                    <?php if ($dpegDetails): ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title mb-0">DPEG Details</h4>
                            </div>
                            <div class="card-body">
                                <p><strong>DPEG Name:</strong> <?php echo htmlspecialchars($dpegDetails['dpeg_name']); ?></p>
                                <p><strong>DPEG Title:</strong> <?php echo htmlspecialchars($dpegDetails['dpeg_title']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Evaluators Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Evaluators</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Evaluator ID</th>
                                            <th scope="col">Evaluator Name</th>
                                            <th scope="col">Evaluator Username</th>
                                            <th scope="col">DPEG ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($evaluator = $evaluatorsResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($evaluator['evaluator_id']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluator['name']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluator['username']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluator['dpeg_id'] ?: 'Not Assigned'); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluator Details Container -->
                    <div id="details-container" class="mt-4" style="display: none;">
                        <button id="hide-details-btn" class="btn btn-secondary mb-3">Hide Details</button>
                        <!-- Evaluator details table will be dynamically inserted here -->
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
            /* Custom Styles for Evaluator Details Table */
            #details-container table {
                background-color: #f8f9fa; /* Light gray background for table */
                border: 1px solid #dee2e6; /* Border around table */
                border-radius: 0.375rem; /* Rounded corners */
            }
            #details-container th {
                background-color: #e9ecef; /* Slightly darker background for header */
                color: #495057; /* Darker text color */
            }
            #details-container td {
                color: #212529; /* Darker text color */
            }
            </style>

        </div>
    </body>
</html>
