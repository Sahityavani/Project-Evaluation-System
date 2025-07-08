<?php 
include('auth.php');
include('../db.php');
$title = "Evaluator Dashboard";
include('../head.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM evaluators WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$type = $_SESSION['type'];

// Fetch counts for Supervisors, Evaluators, Batches, and Students
$supervisorsCount = $conn->query("SELECT COUNT(*) as count FROM supervisors")->fetch_assoc()['count'];
$projectManagersCount = $conn->query("SELECT COUNT(*) as count FROM evaluators")->fetch_assoc()['count'];
$batchesCount = $conn->query("SELECT COUNT(*) as count FROM batches")->fetch_assoc()['count'];
$studentsCount = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];

// Fetch lists for Supervisors and Evaluators
$supervisorsResult = $conn->query("SELECT * FROM supervisors");
$projectManagersResult = $conn->query("SELECT * FROM evaluators");
$sql2 = "
        SELECT b.*
        FROM batches b
        INNER JOIN mrg_batches mb ON b.batch_id = mb.batch_id
        INNER JOIN mrg_evaluators me ON mb.mrg_id = me.mrg_id
        INNER JOIN evaluators e ON me.evaluator_id = e.evaluator_id
        WHERE e.username = '$username'
    ";
$batchsearch = $conn->query($sql2);


$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
<?php include('header.php');?>
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
                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Supervisors</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?php echo $supervisorsCount; ?>"><?php echo $supervisorsCount; ?></span></h4>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Evaluators</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?php echo $projectManagersCount; ?>"><?php echo $projectManagersCount; ?></span></h4>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Batches</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?php echo $batchesCount; ?>"><?php echo $batchesCount; ?></span></h4>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->

                                    <div class="col-xl-3 col-md-6">
                                        <!-- card -->
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Students</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?php echo $studentsCount; ?>"><?php echo $studentsCount; ?></span></h4>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div> <!-- end row-->

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="listjs-table" id="supervisorList">
                                                    <div class="card-header">
                                                        <h4 class="card-title mb-0"><img width="40" height="40" src="https://img.icons8.com/pulsar-gradient/48/bursts.png" alt="external-supervisor-character-icons-flaticons-flat-flat-icons" /> Your Batches For Evaluation</h4>
                                                    </div><!-- end card header -->

                                                    <div class="table-responsive table-card mt-3 mb-1">
                                                        <table class="table align-middle table-nowrap" id="supervisorTable">
                                                            <thead class="table-light">
                                                                <tr>
                                                                <th class="sort" data-sort="batch-id">Batch ID</th>
                                                                    <th class="sort" data-sort="batch-name">Batch Students</th>
                                                                    <th class="sort" data-sort="batch-title">Project Title</th>
                                                                    <th class="sort" data-sort="batch-members">No of Members</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list form-check-all">
                                                                <?php while ($batches = $batchsearch->fetch_assoc()) : ?>
                                                                    <tr>
                                                                        <td class="batch-id"><b><?php echo htmlspecialchars($batches['batch_id']); ?></b></td>
                                                                        <td class="batch-name"><b><?php echo htmlspecialchars($batches['batch_name']); ?></b></td>
                                                                        <td class="batch-title"><b><?php echo htmlspecialchars($batches['batch_title']); ?></b></td>
                                                                        <td class="batch-members"><b><?php echo htmlspecialchars($batches['batch_members']); ?></b></td>
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

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="listjs-table" id="supervisorList">
                                                    <div class="card-header">
                                                        <h4 class="card-title mb-0"><img width="40" height="40" src="https://img.icons8.com/external-flaticons-flat-flat-icons/64/external-supervisor-character-icons-flaticons-flat-flat-icons.png" alt="external-supervisor-character-icons-flaticons-flat-flat-icons" /> Supervisors List</h4>
                                                    </div><!-- end card header -->

                                                    <div class="table-responsive table-card mt-3 mb-1">
                                                        <table class="table align-middle table-nowrap" id="supervisorTable">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="sort" data-sort="customer_name">Supervisor Name</th>
                                                                    <th class="sort" data-sort="email">Email</th>
                                                                    <th class="sort" data-sort="phone">Phone</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list form-check-all">
                                                                <?php while ($supervisor = $supervisorsResult->fetch_assoc()) : ?>
                                                                    <tr>
                                                                        <td class="customer_name"><b><?php echo htmlspecialchars($supervisor['name']); ?></b></td>
                                                                        <td class="email"><b><?php echo htmlspecialchars($supervisor['email']); ?></b></td>
                                                                        <td class="phone"><b><?php echo htmlspecialchars($supervisor['mobile']); ?></b></td>
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
                                    <div class="col-12 mt-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="listjs-table" id="managerList">
                                                    <div class="card-header">
                                                        <h4 class="card-title mb-0"> <img width="40" style="margin-right: 5px;" height="40" src="https://img.icons8.com/color/48/admin-settings-male.png" alt="admin-settings-male" /> Evaluators List</h4>
                                                    </div><!-- end card header -->

                                                    <div class="table-responsive table-card mt-3 mb-1">
                                                        <table class="table align-middle table-nowrap" id="managerTable">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="sort" data-sort="customer_name">Evaluator Name</th>
                                                                    <th class="sort" data-sort="email">Email</th>
                                                                    <th class="sort" data-sort="phone">Phone</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list form-check-all">
                                                                <?php while ($projectManager = $projectManagersResult->fetch_assoc()) : ?>
                                                                    <tr>
                                                                        <td class="customer_name"><b><?php echo htmlspecialchars($projectManager['name']); ?></b></td>
                                                                        <td class="email"><b><?php echo htmlspecialchars($projectManager['email']); ?></b></td>
                                                                        <td class="phone"><b><?php echo htmlspecialchars($projectManager['mobile']); ?></b></td>
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
                            </div>

                        </div> <!-- end col -->
                    </div>

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php include('../footer.php');?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <?php if ($success) : ?>
        <script>
        Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $success;?>'
            });
            </script>
    <?php endif; ?>
</body>

</html>