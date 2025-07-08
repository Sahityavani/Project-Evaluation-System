<?php
include('auth.php');
$title = "Admin | Add/Edit Supervisors";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$supervisors = [];
$nextBatchId = 0;
$q = "SELECT * FROM supervisors";
$res = $conn->query($q);
if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $supervisors[] = $r;
        $nextBatchId = $r['id'];
    }
}
$nextBatchId += 1;

// Get the list of available batches (where supervisor is NULL)
$availableBatches = [];
$batchQuery = "SELECT * FROM batches WHERE supervisor IS NULL";
$batchResult = $conn->query($batchQuery);
if ($batchResult->num_rows > 0) {
    while ($batchRow = $batchResult->fetch_assoc()) {
        $availableBatches[] = $batchRow;
    }
}
?>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php include('header.php'); ?>
        <?php include('navbar.php'); ?>
        <div class="vertical-overlay"></div>

        <!-- Start right Content here -->
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
                                        <li class="breadcrumb-item active">Add Supervisors</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Supervisor Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add Supervisor</h4>
                        </div>
                        <div class="card-body">
                            <form id="addSupervisorForm" method="post" action="add_supervisor.php">
                                <div class="mb-3">
                                    <label for="id" class="form-label">Supervisor ID</label>
                                    <input type="text" id="id" name="id" class="form-control" placeholder="Supervisor ID" value="<?php echo htmlspecialchars($nextBatchId); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Supervisor Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Supervisor Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Supervisor Username</label>
                                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter Supervisor Username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Supervisor Password</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter Supervisor Password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="batch_id" class="form-label">Supervisor Batch</label>
                                    <select id="batch_id" name="batch_id" class="form-control">
                                        <option value="" selected>Select a Batch</option>
                                        <?php foreach ($availableBatches as $batch): ?>
                                            <option value="<?php echo htmlspecialchars($batch['batch_id']); ?>"><?php echo htmlspecialchars($batch['batch_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Create Supervisor</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Supervisor List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing Supervisors</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="SupervisorTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Supervisor ID</th>
                                            <th scope="col">Supervisor Name</th>
                                            <th scope="col">Supervisor Username</th>
                                            <th scope="col">Supervisor Batch</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($supervisors as $supervisor): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($supervisor['id']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['name']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['username']); ?></td>
                                                <td><?php echo htmlspecialchars(($supervisor['batch_id'] == 0 ? 'Not Alloted':$supervisor['batch_id'])); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success edit-btn" data-id="<?php echo $supervisor['id']; ?>" data-bs-toggle="modal" data-bs-target="#editSupervisorModal">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $supervisor['id']; ?>">Delete</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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

            <!-- Edit Supervisor Modal -->
            <div class="modal fade" id="editSupervisorModal" tabindex="-1" aria-labelledby="editSupervisorModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editSupervisorModalLabel">Edit Supervisor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editSupervisorForm" method="post" action="edit_supervisor.php">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editSupervisorId" class="form-label">Supervisor ID</label>
                                    <input type="text" id="editSupervisorId" name="id" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="editSupervisorName" class="form-label">Supervisor Name</label>
                                    <input type="text" id="editSupervisorName" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSupervisorUsername" class="form-label">Supervisor Username</label>
                                    <input type="text" id="editSupervisorUsername" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSupervisorPassword" class="form-label">Supervisor Password</label>
                                    <input type="password" id="editSupervisorPassword" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSupervisorBatch" class="form-label">Supervisor Batch</label>
                                    <input type="text" id="editSupervisorBatch" name="batch_id" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to handle edit button click
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const supervisorId = this.getAttribute('data-id');
            
            fetch('get_supervisor_details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id': supervisorId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Populate the edit form
                document.getElementById('editSupervisorId').value = data.id;
                document.getElementById('editSupervisorName').value = data.name;
                document.getElementById('editSupervisorUsername').value = data.username;
                document.getElementById('editSupervisorPassword').value = data.password;
                document.getElementById('editSupervisorBatch').value = data.batch_id;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching supervisor details.');
            });
        });
    });

    // Function to handle delete button click
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const supervisorId = this.getAttribute('data-id');
            
            if (confirm('Are you sure you want to delete this supervisor?')) {
                fetch('delete_supervisor.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'id': supervisorId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Display success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.success
                        }).then(() => {
                            // Reload the page to reflect changes
                            window.location.href = window.location.pathname;
                        });
                    } else {
                        // Display error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting the supervisor.'
                    });
                });
            }
        });
    });
});

</script>
</body>
</html>
