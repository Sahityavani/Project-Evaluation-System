<?php
include('auth.php');
$title = "Admin | Add/Edit Students";
include('../head.php');
include('../db.php');

// Fetch user details
$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'];

// Handle success and error messages
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch existing students
$students = [];
$nextStudentId = 0;
$q = "SELECT * FROM students order by id";
$res = $conn->query($q);
if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $students[] = $r;
        $nextStudentId = $r['id'];
    }
}
$nextStudentId += 1;
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
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Manage Students</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add/Edit Students</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Student Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add Student</h4>
                        </div>
                        <div class="card-body">
                            <form id="addStudentForm" method="post" action="add_student.php">
                                <div class="mb-3">
                                    <label for="id" class="form-label">Student ID</label>
                                    <input type="text" id="id" name="id" class="form-control" placeholder="Student ID" value="<?php echo htmlspecialchars($nextStudentId); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Student Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Student Name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="rollno" class="form-label">Student Rollno</label>
                                    <input type="text" id="rollno" name="rollno" class="form-control" placeholder="Enter Student Rollno" required>
                                </div>

                                <div class="mb-3">
                                    <label for="section" class="form-label">Student Section</label>
                                    <select name="section" class="form-select" required>
                                            <option value="">Select Section</option>
                                                <option value="CSE1">CSE-1</option>
                                                <option value="CSE2">CSE-2</option>
                                                <option value="CSE3">CSE-3</option>
                                                <option value="CSM1">CSM-1</option>
                                                <option value="CSM2">CSM-2</option>
                                        </select>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Student Password</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter Student Password" required>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Add Student</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Student List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing Students</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="StudentTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Student ID</th>
                                            <th scope="col">Student Name</th>
                                            <th scope="col">Student Rollno</th>
                                            <th scope="col">Student Section</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['id']); ?></td>
                                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                                                <td><?php echo htmlspecialchars($student['section']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success edit-btn" data-id="<?php echo htmlspecialchars($student['rollno']); ?>" data-bs-toggle="modal" data-bs-target="#editStudentModal">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo htmlspecialchars($student['rollno']); ?>">Delete</button>
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

            <!-- Edit Student Modal -->
            <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editStudentForm" method="post" action="edit_student.php">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editStudentId" class="form-label">Student ID</label>
                                    <input type="text" id="editStudentId" name="id" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="editStudentName" class="form-label">Student Name</label>
                                    <input type="text" id="editStudentName" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editStudentRollno" class="form-label">Student Rollno</label>
                                    <input type="text" id="editStudentRollno" name="rollno" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="section" class="form-label">Student Section</label>
                                    <select name="section" class="form-select" required>
                                            <option id="editStudentSection" value="">Select Section</option>
                                                <option value="CSE1">CSE-1</option>
                                                <option value="CSE2">CSE-2</option>
                                                <option value="CSE3">CSE-3</option>
                                                <option value="CSM1">CSM-1</option>
                                                <option value="CSM2">CSM-2</option>
                                        </select>
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
    // Handle Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function () {
        const rollno = this.getAttribute('data-id');

        fetch('student_details.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'rollno': rollno
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message
                });
            } else {
                // Populate the form fields with the data received
                document.getElementById('editStudentId').value = data.data.id;
                document.getElementById('editStudentName').value = data.data.name;
                document.getElementById('editStudentRollno').value = data.data.rollno;
                
                // Set the section based on the returned data
                const section = data.data.section;
                const sectionOptions = document.querySelectorAll('#editStudentForm select[name="section"] option');
                
                // Loop through the options and match the value
                sectionOptions.forEach(option => {
                    if (option.value.toLowerCase() === section.toLowerCase()) {
                        option.selected = true;
                    } else {
                        option.selected = false; // Reset others to avoid multiple selections
                    }
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'An error occurred while fetching the student details.'
            });
        });
    });
});


    // Handle Delete Button Click
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const rollno = this.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_student.php?rollno=' + encodeURIComponent(rollno))
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: data.message
                                }).then(() => {
                                    // Reload or update the student list
                                    location.reload(); // Or use other methods to update the UI
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting the student.'
                            });
                        });
                }
            });
        });
    });
});
            </script>
        </div>
    </div>
</body>
</html>
