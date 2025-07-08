<?php
include('auth.php');
$title = "Admin | Add/Edit Batches";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$batches = [];
$nextBatchId = 0;
$q = "SELECT * FROM batches";
$res = $conn->query($q);
if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $batches[] = $r;
        $nextBatchId = $r['batch_id'];
    }
}

$batchId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$batchName = '';
$batchTitle = '';
$members = [];

// Get the next batch ID for new entries
$nextBatchId = $nextBatchId + 1;

if ($batchId > 0) {
    $batchQuery = "SELECT * FROM batches WHERE id = $batchId";
    $batchResult = $conn->query($batchQuery);
    if ($batchResult->num_rows > 0) {
        $batchData = $batchResult->fetch_assoc();
        $batchName = $batchData['batch_name'];
        $batchTitle = $batchData['batch_title'];
        // Fetch members if necessary
        $membersQuery = "SELECT rollno FROM batch_members WHERE batch_id = $batchId";
        $membersResult = $conn->query($membersQuery);
        while ($member = $membersResult->fetch_assoc()) {
            $members[] = $member['rollno'];
        }
    }
}

// Fetch students who are not added to any batches
$students = [];
$studentsQuery = "SELECT rollno, name FROM students WHERE batch_id IS NULL order by id";
$studentsResult = $conn->query($studentsQuery);
while ($student = $studentsResult->fetch_assoc()) {
    $students[] = $student;
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
                                <h4 class="mb-sm-0">Batches</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add batches</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Batch Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add New Batch</h4>
                        </div>
                        <div class="card-body">
                            <form id="addBatchForm" method="post" action="add_batch.php">
                                <div class="mb-3">
                                    <label for="batchId" class="form-label">Batch ID</label>
                                    <input type="text" id="batchId" name="batch_id" class="form-control" placeholder="Batch ID" value="<?php echo htmlspecialchars($nextBatchId); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="batchName" class="form-label">Batch Name</label>
                                    <input type="text" id="batchName" name="batch_name" class="form-control" placeholder="Enter Batch Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="batchTitle" class="form-label">Batch Title</label>
                                    <input type="text" id="batchTitle" name="batch_title" class="form-control" placeholder="Enter Batch Title" required>
                                </div>
                                <div id="members">
                                    <div class="member-field mb-3">
                                        <label class="form-label">Member Roll Number</label>
                                        <select name="members[]" class="form-select" required>
                                            <option value="">Select Member</option>
                                            <?php foreach ($students as $student): ?>
                                                <option value="<?php echo $student['rollno']; ?>"><?php echo $student['rollno'] . ' - ' . $student['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-member-btn">Remove</button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary" id="addMemberBtn">Add Member</button>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Save Batch</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Batch List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing Batches</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="batchTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Batch ID</th>
                                            <th scope="col">Batch Name</th>
                                            <th scope="col">Batch Title</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($batches as $batch): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($batch['batch_id']); ?></td>
                                                <td><?php echo htmlspecialchars($batch['batch_name']); ?></td>
                                                <td><?php echo htmlspecialchars($batch['batch_title']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success edit-btn" data-id="<?php echo $batch['batch_id']; ?>" data-bs-toggle="modal" data-bs-target="#editBatchModal">Edit</button>
                                                    <!-- Delete button to trigger JavaScript -->
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $batch['batch_id']; ?>">Delete</button>
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
            <?php if ($success) : ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: '<?php echo $success; ?>'
                    });
                </script>
            <?php endif; ?>
        </div>

        <!-- Edit Batch Modal -->
        <div class="modal fade" id="editBatchModal" tabindex="-1" aria-labelledby="editBatchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="editBatchModalLabel">Edit Batch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editBatchForm" method="post" action="edit_batch.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editBatchId" class="form-label">Batch ID</label>
                                <input type="text" id="editBatchId" name="batch_id" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="editBatchName" class="form-label">Batch Name</label>
                                <input type="text" id="editBatchName" name="batch_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="editBatchTitle" class="form-label">Batch Title</label>
                                <input type="text" id="editBatchTitle" name="batch_title" class="form-control" required>
                            </div>
                            <div id="editMembers">
                                <!-- Existing members will be populated here via JavaScript -->
                            </div>
                            <button type="button" class="btn btn-secondary" id="addEditMemberBtn">Add Member</button>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for Adding New Members -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Handle Add Member Button Click
    document.getElementById('addMemberBtn').addEventListener('click', function() {
        const memberDiv = document.createElement('div');
        memberDiv.classList.add('member-field', 'mb-3');
        memberDiv.innerHTML = `
            <label class="form-label">Member Roll Number</label>
            <select name="members[]" class="form-select" required>
                <option value="">Select Member</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['rollno']; ?>"><?php echo $student['rollno'] . ' - ' . $student['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-member-btn">Remove</button>
        `;
        document.getElementById('members').appendChild(memberDiv);
    });

    // Handle Remove Member Button Click
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-member-btn')) {
            event.target.closest('.member-field').remove();
        }
    });

    // Handle Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const batchId = this.getAttribute('data-id');

            fetch('get_batch_details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id': batchId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error
                    });
                    return;
                }

                document.getElementById('editBatchId').value = data.id;
                document.getElementById('editBatchName').value = data.batch_name;
                document.getElementById('editBatchTitle').value = data.batch_title;

                const editMembersDiv = document.getElementById('editMembers');
                editMembersDiv.innerHTML = '';

                data.members.forEach(member => {
                    const memberDiv = document.createElement('div');
                    memberDiv.classList.add('member-field', 'mb-3');
                    memberDiv.innerHTML = `
                        <label class="form-label">Member Roll Number</label>
                        <input type="text" name="members[]" value="${member.rollno}" class="form-control" readonly>
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-member-btn">Remove</button>
                    `;
                    editMembersDiv.appendChild(memberDiv);
                });

                $('#editBatchModal').modal('show');
            })
            .catch(error => {
                console.error('Error fetching batch details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch batch details. Please try again.'
                });
            });
        });
    });

    document.getElementById('addEditMemberBtn').addEventListener('click', function() {
    const newMemberDiv = document.createElement('div');
    newMemberDiv.classList.add('mb-3');
    newMemberDiv.innerHTML = `
        <label class="form-label">Add New Member</label>
        <select name="members[]" class="form-select" required>
            <option value="">Select Member</option>
            <?php foreach ($students as $student): ?>
                <option value="<?php echo $student['rollno']; ?>">
                    <?php echo $student['rollno'] . ' - ' . $student['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="btn btn-danger btn-sm mt-2 remove-editmember-btn">Remove</button>
    `;
    document.getElementById('editMembers').appendChild(newMemberDiv);
});

document.getElementById('editMembers').addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-editmember-btn')) {
        const memberDiv = event.target.closest('div.mb-3');
        if (memberDiv) {
            memberDiv.remove();
        }
    }
});

    // Handle Delete Button Click
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const batchId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this batch?')) {
                // Create a form element to send the POST request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'edit_batch.php';

                // Create a hidden input field for the batch ID
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'batch_id_to_delete';
                inputId.value = batchId;
                form.appendChild(inputId);

                // Create a hidden input field for the action
                const inputAction = document.createElement('input');
                inputAction.type = 'hidden';
                inputAction.name = 'action';
                inputAction.value = 'delete';
                form.appendChild(inputAction);

                // Append the form to the body and submit it
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});

        </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
