<?php
include('auth.php');
$title = "Admin | Add/Edit MRGs";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch existing MRGs
$mrgList = [];
$nextmrgid =0;
$q = "SELECT * FROM mrg";
$res = $conn->query($q);
if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $mrgList[] = $r;
        $nextmrgid =$r['mrg_id'];
    }
}

$mrgId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$mrgName = '';
$mrgTitle = '';
$evaluators = [];

// Fetch details for editing
if ($mrgId > 0) {
    $mrgQuery = "SELECT * FROM mrg WHERE mrg_id = $mrgId";
    $mrgResult = $conn->query($mrgQuery);
    if ($mrgResult->num_rows > 0) {
        $mrgData = $mrgResult->fetch_assoc();
        $mrgName = $mrgData['mrg_name'];
        $mrgTitle = $mrgData['mrg_title'];

        // Fetch evaluator IDs for this MRG
        $evaluatorQuery = "SELECT evaluator_id FROM mrg_evaluators WHERE mrg_id = $mrgId";
        $evaluatorResult = $conn->query($evaluatorQuery);
        while ($evaluator = $evaluatorResult->fetch_assoc()) {
            $evaluators[] = $evaluator['evaluator_id'];
        }
    }
}

$nextmrgid = $nextmrgid +1;

$availableEvaluators = [];
$evalQuery = "SELECT evaluator_id, name FROM evaluators WHERE evaluator_id NOT IN (SELECT evaluator_id FROM mrg_evaluators)";
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
                                <h4 class="mb-sm-0">MRGs</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add/Edit MRGs</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add/Edit MRG Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?php echo $mrgId > 0 ? 'Edit MRG' : 'Add New MRG'; ?></h4>
                        </div>
                        <div class="card-body">
                            <form id="mrgForm" method="post" action="add_mrg.php">
                                
                                <div class="mb-3">
                                    <label for="mrgID" class="form-label">MRG ID</label>
                                    <input type="text" id="mrg_id" name="mrg_id" class="form-control" value="<?php echo htmlspecialchars($nextmrgid); ?>" readonly>
                                </div>
                               
                                <div class="mb-3">
                                    <label for="mrgName" class="form-label">MRG Name</label>
                                    <input type="text" id="mrgName" name="mrg_name" class="form-control" placeholder="Enter MRG Name" value="<?php echo htmlspecialchars($mrgName); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="mrgTitle" class="form-label">MRG Title</label>
                                    <input type="text" id="mrgTitle" name="mrg_title" class="form-control" placeholder="Enter MRG Title" value="<?php echo htmlspecialchars($mrgTitle); ?>" required>
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
                                    <button type="submit" class="btn btn-success"><?php echo $mrgId > 0 ? 'Save Changes' : 'Add MRG'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- MRG List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing MRGs</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="mrgTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">MRG ID</th>
                                            <th scope="col">MRG Name</th>
                                            <th scope="col">MRG Title</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mrgList as $mrg): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($mrg['mrg_id']); ?></td>
                                                <td><?php echo htmlspecialchars($mrg['mrg_name']); ?></td>
                                                <td><?php echo htmlspecialchars($mrg['mrg_title']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success edit-btn" data-id="<?php echo $mrg['mrg_id']; ?>" data-bs-toggle="modal" data-bs-target="#editMrgModal">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $mrg['mrg_id']; ?>">Delete</button>
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

            <!-- Edit MRG Modal -->
            <div class="modal fade" id="editMrgModal" tabindex="-1" aria-labelledby="editMrgModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editMrgModalLabel">Edit Major Research Group (MRG)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editMrgForm" method="post" action="mrg_edit.php">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_mrg_id" class="form-label">MRG ID</label>
                                    <input type="text" id="edit_mrg_id" name="mrg_id" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_mrg_name" class="form-label">MRG Name</label>
                                    <input type="text" id="edit_mrg_name" name="mrg_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_mrg_title" class="form-label">MRG Title</label>
                                    <input type="text" id="edit_mrg_title" name="mrg_title" class="form-control" required>
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
        var mrgId = this.getAttribute('data-id');
        fetch('get_mrg_details.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(mrgId)
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_mrg_id').value = data.id; // Changed data.mrg_id to data.id
                document.getElementById('edit_mrg_name').value = data.mrg_name;
                document.getElementById('edit_mrg_title').value = data.mrg_title;

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
            .catch(error => console.error('Error fetching MRG details:', error));
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
        var mrgId = this.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this MRG?')) {
            fetch('mrg_delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'mrg_id=' + encodeURIComponent(mrgId)
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
            .catch(error => console.error('Error deleting MRG:', error));
        }
    });
});

</script>

        </div>
    </div>
</body>
</html>


