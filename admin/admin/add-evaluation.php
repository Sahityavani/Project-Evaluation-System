<?php include('auth.php');
 $title = "Admin | Add Evaluation Types";
 include('../head.php');
 include('../db.php');
 $username = $conn->real_escape_string($_SESSION['user']);
 $sql = "SELECT * FROM users WHERE username = '$username'"; 
 $result = $conn->query($sql); $row = $result->fetch_assoc(); 
 $name = $row['name'];  
 $success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
 $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : ''; 
 $evals = [];
$nexteval = 0;
$q = "SELECT * FROM evaluation_type";
$res = $conn->query($q);
if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $nexteval = $r['id'];
    }
}

$nexteval = $nexteval + 1;
 
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
                                <h4 class="mb-sm-0">Evaluation Types</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add Evaluation Types</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add/Edit Evaluation Type Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add New Evaluation Type</h4>
                        </div>
                        <div class="card-body">
                        <form id="evaluationTypeForm" method="post" action="add_evaluation_type.php">
    <div class="mb-3">
        <label for="evaluationTypeid" class="form-label">Evaluation ID</label>
        <input type="text" id="evaluationTypeid" name="evaluation_type_id" class="form-control" value="<?php echo $nexteval;?>" readonly>
    </div>

    <div class="mb-3">
        <label for="evaluation_type" class="form-label">Evaluation Type</label>
        <select id="evaluation_type" name="evaluation_type" class="form-control" required onchange="updateLabels()">
            <option value="MRG">Major Research Group (MRG)</option>
            <option value="DPEG">Department (DPEG)</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="evaluationTypeName" class="form-label">Evaluation Name</label>
        <input type="text" id="evaluationTypeName" name="evaluation_type_name" class="form-control" placeholder="Enter Evaluation Type Name" required>
    </div>
    
    <div class="mb-3">
        <label for="maxMarksMRG" id="maxMarksLabel" class="form-label">Max Marks by MRG</label>
        <input type="number" id="maxMarksMRG" name="max_marks_mrg" class="form-control" placeholder="Enter Max Marks by MRG" required>
    </div>

    <div class="mb-3">
        <label for="maxMarksSupervisor" class="form-label">Max Marks by Supervisor</label>
        <input type="number" id="maxMarksSupervisor" name="max_marks_supervisor" class="form-control" placeholder="Enter Max Marks by Supervisor" required>
    </div>
    
    <br>
    <h4 class="card-title mb-0" id="subPartsLabel">MRG Sub Parts</h4> 
    <br>
    <div id="mrgSubParts">
        <div class="sub-part-field mb-3">
            <input type="text" id="mrgSubPartName" name="mrg_sub_part_name[]" class="form-control" placeholder="Enter Sub Part Name" required>
            <input type="number" id="mrgSubPartMarks" name="mrg_sub_part_marks[]" class="form-control" placeholder="Enter Sub Part Marks" required>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
        </div>
    </div>
    <button type="button" class="btn btn-secondary" id="addMRGSubPartBtn">Add MRG Sub Part</button>
    
    <br><br>
    <h4 class="card-title mb-0">Supervisor Sub Parts</h4> 
    <br>
    <div id="supervisorSubParts">
        <div class="sub-part-field mb-3">
            <input type="text" id="supervisorSubPartName" name="supervisor_sub_part_name[]" class="form-control" placeholder="Enter Sub Part Name" required>
            <input type="number" id="supervisorSubPartMarks" name="supervisor_sub_part_marks[]" class="form-control" placeholder="Enter Sub Part Marks" required>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
        </div>
    </div>
    <button type="button" class="btn btn-secondary" id="addSupervisorSubPartBtn">Add Supervisor Sub Part</button>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">Add Evaluation Type</button>
    </div>
</form>
                        </div>
                    </div>
                    <!-- Evaluation Type List -->
<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title mb-0">Existing Evaluation Types</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-nowrap" id="evaluationTypeTable">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Evaluation Type Name</th>
                        <th scope="col">Max Marks by Supervisor</th>
                        <th scope="col">Max Marks by MRG/DPEG</th>
                        <th scope="col">Sub Parts (MRG/DPEG)</th>
                        <th scope="col">Sub Parts (Supervisor)</th>
                        <th scope="col">Evaluation Type</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM evaluation_type";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                            <td>' . $row['name'] . '</td>
                            <td>' . $row['max_marks_supervisor'] . '</td>
                            <td>' . $row['max_marks_mrg'] . '</td>
                            <td>';
                        $mrgSubParts = json_decode($row['sub_parts'], true);
                        foreach ($mrgSubParts as $subPart => $marks) {
                            echo $subPart . ' (' . $marks . ')<br>';
                        }
                        echo '</td>
                            <td>';
                        $supervisorSubParts = json_decode($row['sub_parts_supervisor'], true);
                        foreach ($supervisorSubParts as $subPart => $marks) {
                            echo $subPart . ' (' . $marks . ')<br>';
                        }
                        echo '</td>
                            <td>' . $row['type'] . '</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success edit-btn" data-id="' . $row['id'] . '">Edit</button>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '">Delete</button>
                            </td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editEvaluationTypeModal" style="display:none;" tabindex="-1" role="dialog" aria-labelledby="editEvaluationTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEvaluationTypeModalLabel">Edit Evaluation Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editEvaluationTypeModalBody">
                <!-- Edit form will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
            </div>
        </div>
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
          document.getElementById('addMRGSubPartBtn').addEventListener('click', function() {
    var subPartField = document.createElement('div');
    subPartField.classList.add('sub-part-field', 'mb-3');
    subPartField.innerHTML = `
        <input type="text" name="mrg_sub_part_name[]" class="form-control" placeholder="Enter Sub Part Name" required>
        <input type="number" name="mrg_sub_part_marks[]" class="form-control" placeholder="Enter Sub Part Marks" required>
        <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
    `;
    document.getElementById('mrgSubParts').appendChild(subPartField);
});

document.getElementById('addSupervisorSubPartBtn').addEventListener('click', function() {
    var subPartField = document.createElement('div');
    subPartField.classList.add('sub-part-field', 'mb-3');
    subPartField.innerHTML = `
        <input type="text" name="supervisor_sub_part_name[]" class="form-control" placeholder="Enter Sub Part Name" required>
        <input type="number" name="supervisor_sub_part_marks[]" class="form-control" placeholder="Enter Sub Part Marks" required>
        <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
    `;
    document.getElementById('supervisorSubParts').appendChild(subPartField);
});

document.getElementById('mrgSubParts').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-sub-part-btn')) {
        e.target.closest('.sub-part-field').remove();
    }
});


// Handle Edit Button Click
document.querySelectorAll('.edit-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        var evaluationTypeId = this.getAttribute('data-id');
        fetch('edit_evaluation_type.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(evaluationTypeId)
        })
        .then(response => response.text())
        .then(data => {
            var modalBody = document.getElementById('editEvaluationTypeModalBody');
            modalBody.innerHTML = data;
            var modal = document.getElementById('editEvaluationTypeModal');
            modal.style.display = 'block';
            $('#editEvaluationTypeModal').modal('show');

            // Add event listeners to the buttons in the modal
            var addMRGSubPartBtn = modalBody.querySelector('#addMRGSubPartBtn');
            var addSupervisorSubPartBtn = modalBody.querySelector('#addSupervisorSubPartBtn');

            addMRGSubPartBtn.addEventListener('click', function() {
                var subPartField = document.createElement('div');
                subPartField.classList.add('sub-part-field', 'mb-3');
                subPartField.innerHTML = `
                    <input type="text" name="mrg_sub_part_name[]" class="form-control" placeholder="Enter Sub Part Name" required>
                    <input type="number" name="mrg_sub_part_marks[]" class="form-control" placeholder="Enter Sub Part Marks" required>
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
                `;
                modalBody.querySelector('#mrgSubParts').appendChild(subPartField);
            });

            addSupervisorSubPartBtn.addEventListener('click', function() {
                var subPartField = document.createElement('div');
                subPartField.classList.add('sub-part-field', 'mb-3');
                subPartField.innerHTML = `
                    <input type="text" name="supervisor_sub_part_name[]" class="form-control" placeholder="Enter Sub Part Name" required>
                    <input type="number" name="supervisor_sub_part_marks[]" class="form-control" placeholder="Enter Sub Part Marks" required>
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
                `;
                modalBody.querySelector('#supervisorSubParts').appendChild(subPartField);
            });

            // Add event listeners to the remove buttons in the modal
            modalBody.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-sub-part-btn')) {
                    event.target.closest('.sub-part-field').remove();
                }
            });
        })
        .catch(error => console.error('Error fetching evaluation type details:', error));
    });
});

// Close modal window when close button is clicked
document.querySelectorAll('.close').forEach(function(button) {
    button.addEventListener('click', function() {
        $('#editEvaluationTypeModal').modal('hide');
    });
});

// Handle Delete Button Click
document.querySelectorAll('.delete-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        var evaluationTypeId = this.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this evaluation type?')) {
            fetch('delete_evaluation_type.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(evaluationTypeId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                    setTimeout(function() {
                        window.location.href = window.location.pathname;
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => console.error('Error deleting evaluation type:', error));
        }
    });
});

document.getElementById('supervisorSubParts').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-sub-part-btn')) {
        e.target.closest('.sub-part-field').remove();
    }
});

function updateLabels() {
        const evaluationType = document.getElementById('evaluation_type').value;
        const maxMarksLabel = document.getElementById('maxMarksLabel');
        const subPartsLabel = document.getElementById('subPartsLabel');
        const addMRGSubPartBtn = document.getElementById('addMRGSubPartBtn');

        if (evaluationType === 'DPEG') {
            maxMarksLabel.textContent = 'Max Marks by DPEG';
            subPartsLabel.textContent = 'DPEG Sub Parts';
            addMRGSubPartBtn.textContent = 'Add DPEG Sub Part';
        } else {
            maxMarksLabel.textContent = 'Max Marks by MRG';
            subPartsLabel.textContent = 'MRG Sub Parts';
            addMRGSubPartBtn.textContent = 'Add MRG Sub Part';
        }
    }
        </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </div>
</body>
</html>