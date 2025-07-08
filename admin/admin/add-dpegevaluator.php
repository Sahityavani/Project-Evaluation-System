<?php
include('auth.php');
$title = "Admin | Add/Edit DPEG Evaluators";
include('../head.php');
include('../db.php');

// Fetch user details
$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

// Handle success and error messages
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch existing evaluators
$evaluators = [];
$nextEvaluatorId = 0;
$q = "SELECT * FROM dept_evaluators";
$res = $conn->query($q);
if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $evaluators[] = $r;
        $nextEvaluatorId = $r['evaluator_id'];
    }
}
$nextEvaluatorId += 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include necessary meta tags and stylesheets -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Bootstrap CSS and other dependencies -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div id="layout-wrapper">
        <?php include('header.php'); ?>
        <?php include('navbar.php'); ?>
        <div class="vertical-overlay"></div>

        <!-- Start main content -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">DPEG Evaluators</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add DPEG Evaluators</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add DPEG Evaluator Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add DPEG Evaluator</h4>
                        </div>
                        <div class="card-body">
                            <form id="addEvaluatorForm" method="post" action="add_dpegevaluator.php">
                                <div class="mb-3">
                                    <label for="id" class="form-label">Evaluator ID</label>
                                    <input type="text" id="id" name="id" class="form-control" placeholder="Evaluator ID" value="<?php echo htmlspecialchars($nextEvaluatorId); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Evaluator Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Evaluator Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Evaluator Username</label>
                                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter Evaluator Username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Evaluator Password</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter Evaluator Password" required>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Create Evaluator</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Evaluator List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing DPEG Evaluators</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="EvaluatorTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Evaluator ID</th>
                                            <th scope="col">Evaluator Name</th>
                                            <th scope="col">Evaluator Username</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($evaluators as $evaluator): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($evaluator['evaluator_id']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluator['name']); ?></td>
                                                <td><?php echo htmlspecialchars($evaluator['username']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success edit-btn" data-id="<?php echo $evaluator['evaluator_id']; ?>" data-bs-toggle="modal" data-bs-target="#editEvaluatorModal">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $evaluator['evaluator_id']; ?>">Delete</button>
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

            <!-- SweetAlert2 for notifications -->
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

            <!-- Edit Evaluator Modal -->
            <div class="modal fade" id="editEvaluatorModal" tabindex="-1" aria-labelledby="editEvaluatorModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="editEvaluatorModalLabel">Edit Evaluator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEvaluatorForm" method="post" action="edit_dpegevaluator.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editEvaluatorId" class="form-label">Evaluator ID</label>
                        <input type="text" id="editEvaluatorId" name="id" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editEvaluatorName" class="form-label">Evaluator Name</label>
                        <input type="text" id="editEvaluatorName" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEvaluatorUsername" class="form-label">Evaluator Username</label>
                        <input type="text" id="editEvaluatorUsername" name="username" class="form-control" required>
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


            <!-- JavaScript for handling edit and delete actions -->
            <script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle Password Visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('editEvaluatorPassword');

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle the icon
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    }

    // Handle Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const evaluatorId = this.getAttribute('data-id');

            fetch('get_dpegevaluator_details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id': evaluatorId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error
                    });
                    return;
                }

                // Populate the edit form
                document.getElementById('editEvaluatorId').value = data.evaluator_id || '';
                document.getElementById('editEvaluatorName').value = data.name || '';
                document.getElementById('editEvaluatorUsername').value = data.username || '';
            })
            .catch(error => {
                console.error('Error fetching evaluator details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching evaluator details.'
                });
            });
        });
    });

    // Handle Delete Button Click
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const evaluatorId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this evaluator?')) {
                fetch('delete_dpegevaluator.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'id': evaluatorId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Display success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: data.success
                        }).then(() => {
                            // Reload the page to reflect changes
                            window.location.href = window.location.pathname;
                        });
                    } else if (data.error) {
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
                        text: 'An error occurred while deleting the evaluator.'
                    });
                });
            }
        });
    });
});
</script>

        </div>
    </div>
</body>
</html>
