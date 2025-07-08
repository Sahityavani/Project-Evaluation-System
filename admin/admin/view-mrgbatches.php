<?php
include('auth.php');
$title = "Admin | View MRG Batches";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$mrg_batchesQuery = "
    SELECT mb.batch_id, mb.mrg_id, b.batch_name, m.mrg_name
    FROM mrg_batches mb
    JOIN batches b ON mb.batch_id = b.batch_id
    JOIN mrg m ON mb.mrg_id = m.mrg_id
";
$mrg_batchesResult = $conn->query($mrg_batchesQuery);

// Fetch members for a specific MRG
$members = [];
if (isset($_GET['mrg_id'])) {
    $mrgId = intval($_GET['mrg_id']);
    $membersQuery = "
        SELECT s.rollno, s.name AS student_name, s.email, s.mobile
        FROM students s
        JOIN batches b ON s.batch_id = b.batch_id
        JOIN mrg_batches mb ON b.batch_id = mb.batch_id
        WHERE mb.mrg_id = $mrgId
    ";
    $membersResult = $conn->query($membersQuery);
    while ($row = $membersResult->fetch_assoc()) {
        $members[] = $row;
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
                                <h4 class="mb-sm-0">MRG Batches</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">View MRG Batches</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MRG Batches Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">MRG Batches</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">MRG ID</th>
                                            <th scope="col">MRG Name</th>
                                            <th scope="col">Batch ID</th>
                                            <th scope="col">Batch Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($mrg_batch = $mrg_batchesResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($mrg_batch['mrg_id']); ?></td>
                                                <td><?php echo htmlspecialchars($mrg_batch['mrg_name']); ?></td>
                                                <td><?php echo htmlspecialchars($mrg_batch['batch_id']); ?></td>
                                                <td><?php echo htmlspecialchars($mrg_batch['batch_name']); ?></td>
                                               <td>
                                                    <button type="button" class="btn btn-primary view-details-btn" data-id="<?php echo $mrg_batch['mrg_id']; ?>">Show Details</button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Details Container -->
                    <div id="details-container" class="mt-4" style="display: none;">
                        <button id="hide-details-btn" class="btn btn-secondary mb-3">Hide Details</button>
                        <!-- Details table will be dynamically inserted here -->
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
                    var mrgId = $(this).data('id');
                    
                    $.ajax({
                        url: 'get_mrgbatch_details.php',
                        type: 'POST',
                        data: { mrg_id: mrgId },
                        success: function(response) {
                            $('#details-container').html(response + '<button id="hide-details-btn" class="btn btn-secondary mb-3">Hide Details</button>').show();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                        }
                    });
                });

                $(document).on('click', '#hide-details-btn', function() {
                    $('#details-container').hide();
                });
            });
            </script>

            <style>
            /* Custom Styles for Details Table */
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
