<?php
include('auth.php');
$title = "Admin | Add/Edit MRG-Batches";
include('../head.php');
include('../db.php');

// Get user details
$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

// Initialize success and error messages
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch existing batches
$batches = [];
$q_batches = "SELECT * FROM batches";
$res_batches = $conn->query($q_batches);
if ($res_batches) {
    while ($r = $res_batches->fetch_assoc()) {
        $batches[] = $r;
    }
}

// Fetch existing MRGs
$mrgs = [];
$q_mrgs = "SELECT * FROM mrg";
$res_mrgs = $conn->query($q_mrgs);
if ($res_mrgs) {
    while ($r2 = $res_mrgs->fetch_assoc()) {
        $mrgs[] = $r2;
    }
}

// Get the next MRG-Batch ID
$nextmrgbt = 0;
$q_mrgsbt = "SELECT id FROM mrg_batches ORDER BY id DESC LIMIT 1";
$res_mrgsbt = $conn->query($q_mrgsbt);
if ($res_mrgsbt && $res_mrgsbt->num_rows > 0) {
    $nextb = $res_mrgsbt->fetch_assoc();
    $nextmrgbt = $nextb['id'] + 1;
}

if ($nextmrgbt == 0){
    $nextmrgbt = 1;
}

// Fetch existing MRG-Batch mappings
$mrg_batches = [];
$q_mrg_batches = "SELECT mb.id, mb.batch_id, mb.mrg_id, b.batch_name, m.mrg_name
                  FROM mrg_batches mb
                  JOIN batches b ON mb.batch_id = b.batch_id
                  JOIN mrg m ON mb.mrg_id = m.mrg_id";
$res_mrg_batches = $conn->query($q_mrg_batches);
if ($res_mrg_batches) {
    while ($rbnew = $res_mrg_batches->fetch_assoc()) {
        $mrg_batches[] = $rbnew;
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
                                <h4 class="mb-sm-0">MRG-Batch Mappings</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add MRG-Batches</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add MRG-Batch Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add MRG-Batch</h4>
                        </div>
                        <div class="card-body">
                            <form id="addMrgBatchForm" method="post" action="add_mrg_batch.php">
                                <div class="mb-3">
                                    <label for="id" class="form-label">MRG MAP ID</label>
                                    <input type="text" id="id" name="id" class="form-control" value="<?php echo htmlspecialchars($nextmrgbt); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="batch_id" class="form-label">Select Batch</label>
                                    <select id="batch_id" name="batch_id" class="form-control" required>
                                        <option value="">Select Batch</option>
                                        <?php foreach ($batches as $batch): ?>
                                            <option value="<?php echo htmlspecialchars($batch['batch_id']); ?>"><?php echo htmlspecialchars($batch['batch_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="mrg_id" class="form-label">Select MRG</label>
                                    <select id="mrg_id" name="mrg_id" class="form-control" required>
                                        <option value="">Select MRG</option>
                                        <?php foreach ($mrgs as $mrg): ?>
                                            <option value="<?php echo htmlspecialchars($mrg['mrg_id']); ?>"><?php echo htmlspecialchars($mrg['mrg_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Add MRG-Batch</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Existing MRG-Batch Mappings -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing MRG-Batch Mappings</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="MrgBatchTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Mapping ID</th>
                                            <th scope="col">Batch Name</th>
                                            <th scope="col">MRG Name</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mrg_batches as $mrg_batch): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($mrg_batch['id']); ?></td>
                                                <td><?php echo htmlspecialchars($mrg_batch['batch_name']); ?></td>
                                                <td><?php echo htmlspecialchars($mrg_batch['mrg_name']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $mrg_batch['id']; ?>">Delete</button>
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

            <script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to handle delete button click
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const mappingId = this.getAttribute('data-id');
            
            if (confirm('Are you sure you want to delete this MRG-Batch mapping?')) {
                fetch('delete_mrg_batch.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'id': mappingId
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
                        text: 'An error occurred while deleting the MRG-Batch mapping.'
                    });
                });
            }
        });
    });
});
</script>
</body>
</html>
