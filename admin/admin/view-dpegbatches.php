<?php
include('auth.php');
$title = "Admin | View DPEG Batches";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Query to get DPEG batches, number of batches, and sections
$dpeg_batchesQuery = "
    SELECT mb.dpeg_id, m.dpeg_name, COUNT(mb.batch_id) AS num_batches, 
           (SELECT DISTINCT s.section
            FROM dpeg_batches db
            JOIN students s ON db.batch_id = s.batch_id
            WHERE db.dpeg_id = mb.dpeg_id LIMIT 1) AS section
    FROM dpeg_batches mb
    JOIN dpeg m ON mb.dpeg_id = m.dpeg_id
    GROUP BY mb.dpeg_id, m.dpeg_name
";
$dpeg_batchesResult = $conn->query($dpeg_batchesQuery);
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
                                <h4 class="mb-sm-0">DPEG Batches</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">View DPEG Batches</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DPEG Batches Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">DPEG Batches</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">DPEG ID</th>
                                            <th scope="col">DPEG Name</th>
                                            <th scope="col">Number of Batches</th>
                                            <th scope="col">Section</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($dpeg_batch = $dpeg_batchesResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($dpeg_batch['dpeg_id']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg_batch['dpeg_name']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg_batch['num_batches']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg_batch['section']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary view-details-btn" data-id="<?php echo $dpeg_batch['dpeg_id']; ?>">Show Details</button>
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
                        <button id="print-details-btn" class="btn btn-primary mb-3">Print Details</button>
                        <!-- Details section will be dynamically inserted here -->
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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('.view-details-btn').click(function() {
                        var dpegId = $(this).data('id');
                        
                        $.ajax({
                            url: 'get_dpegbatch_details.php',
                            type: 'POST',
                            data: { dpeg_id: dpegId },
                            success: function(response) {
                                $('#details-container').html(response + 
                                    '<button id="hide-details-btn" class="btn btn-secondary mb-3">Hide Details</button>' +
                                    '<button id="print-details-btn" class="btn btn-primary mb-3">Print Details</button>'
                                ).show();
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error);
                            }
                        });
                    });

                    $(document).on('click', '#hide-details-btn', function() {
                        $('#details-container').hide();
                    });

                    $(document).on('click', '#print-details-btn', function() {
                        const element = document.querySelector('#details-container');
                        
                        // Hide the buttons before printing
                        $('#hide-details-btn').hide();
                        $('#print-details-btn').hide();
                        
                        // Set options for html2pdf
                        const options = {
                            margin: [0.5, 0.5],
                            filename: 'DPEG_Batch_Details.pdf',
                            image: { type: 'jpeg', quality: 1 },
                            html2canvas: { scale: 2 },
                            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                        };

                        html2pdf().set(options).from(element).save().finally(function() {
                            // Show the buttons again after printing
                            $('#hide-details-btn').show();
                            $('#print-details-btn').show();
                        });
                    });
                });
            </script>
            
            <style>
            /* Custom Styles for Details Section */
            #details-container {
                background-color: #f8f9fa; /* Light gray background */
                border: 1px solid #dee2e6; /* Border around container */
                border-radius: 0.375rem; /* Rounded corners */
                padding: 1rem; /* Padding inside container */
            }
            #details-container table {
                width: 100%; /* Full width table */
                margin-top: 1rem; /* Margin at the top */
            }
            #details-container th {
                background-color: #e9ecef; /* Header background */
                color: #495057; /* Header text color */
            }
            #details-container td {
                color: #212529; /* Text color */
            }
            </style>
        </div>
    </div>
</body>
</html>
