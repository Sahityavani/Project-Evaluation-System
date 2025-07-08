<?php
include('auth.php');
$title = "Admin | View DPEGs";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Query to fetch DPEG details
$dpegQuery = "
    SELECT m.dpeg_id, m.dpeg_name, m.dpeg_title
    FROM dpeg m
";
$dpegResult = $conn->query($dpegQuery);

// Fetch evaluators for a specific DPEG
$evaluators = [];
if (isset($_GET['dpeg_id'])) {
    $dpegId = intval($_GET['dpeg_id']);
    $evaluatorsQuery = "
        SELECT e.evaluator_id, u.name AS evaluator_name
        FROM dpeg_evaluators me
        JOIN dept_evaluators e ON me.evaluator_id = e.evaluator_id
        JOIN users u ON e.evaluator_id = u.id
        WHERE me.dpeg_id = $dpegId
    ";
    $evaluatorsResult = $conn->query($evaluatorsQuery);
    while ($row = $evaluatorsResult->fetch_assoc()) {
        $evaluators[] = $row;
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
                                <h4 class="mb-sm-0">Department Evaluation Groups (DPEGs)</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">View DPEGs</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DPEGs Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">DPEGs</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">DPEG ID</th>
                                            <th scope="col">DPEG Name</th>
                                            <th scope="col">DPEG Title</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($dpeg = $dpegResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($dpeg['dpeg_id']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg['dpeg_name']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg['dpeg_title']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary view-evaluators-btn" data-id="<?php echo $dpeg['dpeg_id']; ?>">View Evaluators</button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluators Container -->
                    <div id="evaluators-container" class="mt-4" style="display: none;">
                        <button id="hide-evaluators-btn" class="btn btn-secondary mb-3">Hide Details</button>
                        <!-- Evaluators table will be dynamically inserted here -->
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
                $('.view-evaluators-btn').click(function() {
                    var dpegId = $(this).data('id');
                    
                    $.ajax({
                        url: 'get_dpeg_evaluators.php',
                        type: 'POST',
                        data: { dpeg_id: dpegId },
                        success: function(response) {
                            $('#evaluators-container').html(response + '<button id="hide-evaluators-btn" class="btn btn-secondary mb-3">Hide Details</button>').show();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                        }
                    });
                });

                $(document).on('click', '#hide-evaluators-btn', function() {
                    $('#evaluators-container').hide();
                });
            });
            </script>

            <style>
            /* Custom Styles for Evaluators Table */
            #evaluators-container table {
                background-color: #f8f9fa; /* Light gray background for table */
                border: 1px solid #dee2e6; /* Border around table */
                border-radius: 0.375rem; /* Rounded corners */
            }
            #evaluators-container th {
                background-color: #e9ecef; /* Slightly darker background for header */
                color: #495057; /* Darker text color */
            }
            #evaluators-container td {
                color: #212529; /* Darker text color */
            }
            </style>

        </div>
    </body>
</html>
