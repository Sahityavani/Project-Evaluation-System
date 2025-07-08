<?php
include('auth.php');
$title = "Admin | Add/Edit DPEGs";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch existing DPEGs
$dpegList = [];
$nextdpegid =0;
$q = "SELECT * FROM dpeg";
$res = $conn->query($q);
if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $dpegList[] = $r;
        $nextdpegid =$r['dpeg_id'];
    }
}

$dpegId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$dpegName = '';
$dpegTitle = '';
$evaluators = [];

// Fetch details for editing
if ($dpegId > 0) {
    $dpegQuery = "SELECT * FROM dpeg WHERE dpeg_id = $dpegId";
    $dpegResult = $conn->query($dpegQuery);
    if ($dpegResult->num_rows > 0) {
        $dpegData = $dpegResult->fetch_assoc();
        $dpegName = $dpegData['dpeg_name'];
        $dpegTitle = $dpegData['dpeg_title'];

        // Fetch evaluator IDs for this DPEG
        $evaluatorQuery = "SELECT evaluator_id FROM dpeg_evaluators WHERE dpeg_id = $dpegId";
        $evaluatorResult = $conn->query($evaluatorQuery);
        while ($evaluator = $evaluatorResult->fetch_assoc()) {
            $evaluators[] = $evaluator['evaluator_id'];
        }
    }
}

$nextdpegid = $nextdpegid +1;

$availableEvaluators = [];
$evalQuery = "SELECT evaluator_id, name FROM dept_evaluators WHERE evaluator_id NOT IN (SELECT evaluator_id FROM dpeg_evaluators)";
$evalResult = $conn->query($evalQuery);
if ($evalResult->num_rows > 0) {
    while ($eval = $evalResult->fetch_assoc()) {
        $availableEvaluators[] = $eval;
    }
}
?>

<body>
    <div id="layout-wrapper">
        <?php include('header.php'); ?>
        <?php include('navbar.php'); ?>
        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0">DPEGs</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add/Edit DPEGs</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add/Edit DPEG Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?php echo $dpegId > 0 ? 'Edit DPEG' : 'Add New DPEG'; ?></h4>
                        </div>
                        <div class="card-body">
                            <form id="dpegForm" method="post" action="add_dpeg.php">
                                
                                <div class="mb-3">
                                    <label for="dpegID" class="form-label">DPEG ID</label>
                                    <input type="text" id="dpeg_id" name="dpeg_id" class="form-control" value="<?php echo htmlspecialchars($nextdpegid); ?>" readonly>
                                </div>
                               
                                <div class="mb-3">
                                    <label for="dpegName" class="form-label">DPEG Name</label>
                                    <input type="text" id="dpegName" name="dpeg_name" class="form-control" placeholder="Enter DPEG Name" value="<?php echo htmlspecialchars($dpegName); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dpegTitle" class="form-label">DPEG Title</label>
                                    <input type="text" id="dpegTitle" name="dpeg_title" class="form-control" placeholder="Enter DPEG Title" value="<?php echo htmlspecialchars($dpegTitle); ?>" required>
                                </div>
                                <div id="evaluators">
                                    <?php foreach ($evaluators as $evaluatorId): ?>
                                        <div class="evaluator-field mb-3">
                                            <label class="form-label">Evaluator</label>
                                            <select name="evaluators[]" class="form-select" required>
                                                <?php foreach ($availableEvaluators as $evaluator): ?>
                                                    <option value="<?php echo htmlspecialchars($evaluator['evaluator_id']); ?>" <?php echo $evaluatorId == $evaluator['evaluator_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($evaluator['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-evaluator-btn">Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-secondary" id="addEvaluatorBtn">Add Evaluator</button>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success"><?php echo $dpegId > 0 ? 'Save Changes' : 'Add DPEG'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- DPEG List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing DPEGs</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="dpegTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">DPEG ID</th>
                                            <th scope="col">DPEG Name</th>
                                            <th scope="col">DPEG Title</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dpegList as $dpeg): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($dpeg['dpeg_id']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg['dpeg_name']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg['dpeg_title']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success edit-btn" data-id="<?php echo $dpeg['dpeg_id']; ?>" data-bs-toggle="modal" data-bs-target="#editDpegModal">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $dpeg['dpeg_id']; ?>">Delete</button>
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

            <!-- Edit DPEG Modal -->
            <div class="modal fade" id="editDpegModal" tabindex="-1" aria-labelledby="editDpegModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editDpegModalLabel">Edit Major Research Group (DPEG)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editDpegForm" method="post" action="dpeg_edit.php">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_dpeg_id" class="form-label">DPEG ID</label>
                                    <input type="text" id="edit_dpeg_id" name="dpeg_id" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_dpeg_name" class="form-label">DPEG Name</label>
                                    <input type="text" id="edit_dpeg_name" name="dpeg_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_dpeg_title" class="form-label">DPEG Title</label>
                                    <input type="text" id="edit_dpeg_title" name="dpeg_title" class="form-control" required>
                                </div>
                                <div id="editEvaluators">
                                    <!-- Evaluators will be dynamically added here -->
                                </div>
                                <button type="button" class="btn btn-secondary" id="editAddEvaluatorBtn">Add Evaluator</button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include('../footer.php'); ?>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <?php if ($error): ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
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
     document.getElementById('addEvaluatorBtn').addEventListener('click', function () {
            var evaluatorField = document.createElement('div');
            evaluatorField.classList.add('evaluator-field', 'mb-3');
            evaluatorField.innerHTML = `
                <label class="form-label">Evaluator</label>
                <select name="evaluators[]" class="form-select" required>
                    <?php foreach ($availableEvaluators as $evaluator): ?>
                        <option value="<?php echo htmlspecialchars($evaluator['evaluator_id']); ?>">
                            <?php echo htmlspecialchars($evaluator['evaluator_id'] .' '.$evaluator['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-evaluator-btn">Remove</button>
            `;
            document.getElementById('evaluators').appendChild(evaluatorField);
        });

        // Remove Evaluator
        document.getElementById('evaluators').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-evaluator-btn')) {
                e.target.closest('.evaluator-field').remove();
            }
        });

        // Handle Edit Button Click
        document.querySelectorAll('.edit-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        var dpegId = this.getAttribute('data-id');
        fetch('get_dpeg_details.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(dpegId)
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_dpeg_id').value = data.id; // Changed data.dpeg_id to data.id
                document.getElementById('edit_dpeg_name').value = data.dpeg_name;
                document.getElementById('edit_dpeg_title').value = data.dpeg_title;

                var editEvaluators = document.getElementById('editEvaluators');
                editEvaluators.innerHTML = '';
                data.evaluators.forEach(function (evaluator) {
                    var evaluatorField = document.createElement('div');
                    evaluatorField.className = 'evaluator-field mb-3';
                    evaluatorField.innerHTML = `
                        <label class="form-label">Evaluator ID</label>
                        <input type="text" name="evaluators[]" class="form-control" value="${evaluator.evaluator_id}" required readonly>
                        <label class="form-label">Evaluator Name</label>
                        <input type="text" name="evaluator_names[]" class="form-control" value="${evaluator.evaluator_name}" readonly>
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-evaluator-btn">Remove</button>
                    `;
                    editEvaluators.appendChild(evaluatorField);
                });
            })
            .catch(error => console.error('Error fetching DPEG details:', error));
    });
});


        // Remove Evaluator in Edit Modal
        document.getElementById('editEvaluators').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-evaluator-btn')) {
                e.target.parentElement.remove();
            }
        });

        // Add Evaluator in Edit Modal
        document.getElementById('editAddEvaluatorBtn').addEventListener('click', function () {
        const newEvaluatorDiv = document.createElement('div');
        newEvaluatorDiv.classList.add('mb-3');
        newEvaluatorDiv.innerHTML = `
            <label class="form-label">Add New Evaluator</label>
            <select name="evaluators[]" class="form-select" required>
                <option value="">Select Evaluator</option>
                <?php foreach ($availableEvaluators as $evaluator): ?>
                    <option value="<?php echo $evaluator['evaluator_id']; ?>">
                        <?php echo $evaluator['evaluator_id'].' '.$evaluator['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-evaluator-btn">Remove</button>
        `;
        document.getElementById('editEvaluators').appendChild(newEvaluatorDiv);
    });

    // Remove Evaluator
    document.getElementById('editEvaluators').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-evaluator-btn')) {
            e.target.closest('.mb-3').remove();
        }
    });

        // Handle Delete Button Click
        document.querySelectorAll('.delete-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        var dpegId = this.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this DPEG?')) {
            fetch('dpeg_delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'dpeg_id=' + encodeURIComponent(dpegId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    }).then(() => {
                        window.location.href = window.location.pathname; // Reload the page to reflect the changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => console.error('Error deleting DPEG:', error));
        }
    });
});

</script>

        </div>
    </div>
</body>
</html>


