<?php 
include('auth.php');
include('../db.php');
$title = "Student Dashboard";
include('../head.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM students WHERE rollno = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$type = $_SESSION['type'];
$name = $row['name'];
$batch_id = $row['batch_id'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';

// Fetch batch details using batch_id
$batchDetailsQuery = $conn->query("SELECT * FROM batches WHERE batch_id = '$batch_id'");
$batchDetails = $batchDetailsQuery->fetch_assoc();

// Fetch students in the same batch if batchDetails is not null
$studentsInBatch = $batchDetails ? $conn->query("SELECT * FROM students WHERE batch_id = '$batch_id'") : null;

?>

<body>

    <!-- Begin page --> 
    <div id="layout-wrapper">
        <?php include('header.php'); ?>
        <?php include('navbar.php');?>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="h-100">
                                <div class="row mb-3 pb-1">
                                    <div class="col-12">
                                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                            <div class="flex-grow-1">
                                                <h4 class="fs-16 mb-1">Welcome, <?php echo $name; ?>!</h4>
                                                <p class="text-muted mb-0">Elevating Innovation and Success.</p>
                                            </div>
                                        </div><!-- end card header -->
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                                <div class="row">
                                    <div class="col-xl-12 col-md-12">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Batch Details</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <?php if ($batchDetails): ?>
                                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">Batch ID: <?php echo htmlspecialchars($batchDetails['batch_id']); ?></h4>
                                                            <p>Batch Name: <?php echo htmlspecialchars($batchDetails['batch_name']); ?></p>
                                                            <p>Supervisor: <?php echo htmlspecialchars($batchDetails['supervisor']); ?></p>
                                                        <?php else: ?>
                                                            <p>You are not added in any batch.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div> <!-- end row-->

                                <?php if ($batchDetails): ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="listjs-table" id="batchList">
                                                        <div class="card-header">
                                                            <h4 class="card-title mb-0"><img width="40" height="40" src="https://img.icons8.com/3d-fluency/94/layers.png" alt="batch-icon" /> Students in Your Batch</h4>
                                                        </div><!-- end card header -->

                                                        <div class="table-responsive table-card mt-3 mb-1">
                                                            <table class="table align-middle table-nowrap" id="batchTable">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="sort" data-sort="customer_roll">Student Rollno</th>
                                                                        <th class="sort" data-sort="customer_name">Student Name</th>
                                                                        <th class="sort" data-sort="email">Email</th>
                                                                        <th class="sort" data-sort="phone">Phone</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="list form-check-all">
                                                                    <?php while ($student = $studentsInBatch->fetch_assoc()): ?>
                                                                        <tr>
                                                                            <td class="customer_roll"><b><?php echo htmlspecialchars($student['rollno']); ?></b></td>
                                                                            <td class="customer_name"><b><?php echo htmlspecialchars($student['name']); ?></b></td>
                                                                            <td class="email"><b><?php echo htmlspecialchars($student['email']); ?></b></td>
                                                                            <td class="phone"><b><?php echo htmlspecialchars($student['mobile']); ?></b></td>
                                                                        </tr>
                                                                    <?php endwhile; ?>
                                                                </tbody>
                                                            </table>
                                                            <div class="noresult" style="display: none">
                                                                <div class="text-center">
                                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                                                    <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders for you search.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- end card -->
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <!-- end row -->
                            </div>
                        </div>
                    </div>
                </div><!-- container-fluid -->
            </div><!-- page-content -->
            <?php include('../footer.php'); ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <?php if ($success): ?>
                <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?php echo $success; ?>'
                });
                </script>
            <?php endif; ?>
</body>

</html>
