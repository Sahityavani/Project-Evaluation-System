<?php

include('auth.php');
$title = "Supervisor | Add/Edit Marks";
include('../head.php');
include('../db.php');

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';

// Fetch user details
$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM supervisors WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'];
$type = $_SESSION['type'];

// Fetch batches assigned to the supervisor from `batches` table
$batches = [];
$sql = "SELECT * FROM batches WHERE supervisor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
while ($batch = $result->fetch_assoc()) {
    $batches[] = $batch;
}

// Fetch evaluation types
$evaluation_types = [];
$sql = "SELECT * FROM evaluation_type";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $evaluation_types[] = $row;
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
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Add/Edit Marks</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add/Edit Marks</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Select Term and Batch Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Select Batch and Evaluation Type</h4>
                        </div>
                        <div class="card-body">
                            <form id="selectBatchForm" method="post">
                                <div class="mb-3">
                                    <label for="batch" class="form-label">Select Batch</label>
                                    <select id="batch" name="batch" class="form-select" required>
                                        <option value="">Choose...</option>
                                        <?php foreach ($batches as $batch): ?>
                                            <option value="<?php echo htmlspecialchars($batch['batch_id']); ?>"><?php echo htmlspecialchars($batch['batch_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="evaluation_type" class="form-label">Select Evaluation Type</label>
                                    <select id="evaluation_type" name="evaluation_type" class="form-select" required>
                                        <option value="">Choose...</option>
                                        <?php foreach ($evaluation_types as $type): ?>
                                            <option value="<?php echo htmlspecialchars($type['id']); ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <button type="button" id="fetchStudents" class="btn btn-success">Fetch Students</button>
                                </div>
                            </form>
                        </div>
                    </div>

                     <!-- Students List and Marks Form -->
                    <div class="card mt-4" id="studentsContainer" style="display: none;">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Award Marks</h4>
                        </div>
                        <div class="card-body">
                            <form id="awardMarksForm" method="post" action="award_marks.php">
                                <input type="hidden" id="selectedBatch" name="batch">
                                <input type="hidden" id="selectedEvaluationType" name="evaluation_type">
                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap" id="StudentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">Student RollNo</th>
                                                <th scope="col">Student Name</th>
                                                <th scope="col">Sub Parts</th>
                                                <th scope="col">Remarks</th>
                                                <th scope="col">Previous Marks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsList">
                                            <!-- Student rows will be inserted here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Award Marks</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('../footer.php'); ?>

            <!-- SweetAlert2 for notifications -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <!-- JavaScript for handling form actions -->
            <script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('fetchStudents').addEventListener('click', function() {
        const batch = document.getElementById('batch').value;
        const evaluation_type = document.getElementById('evaluation_type').value;

        if (batch && evaluation_type) {
            fetch('get_student_marks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'batch': batch,
                    'evaluation_type': evaluation_type
                })
            })
            .then(response => response.json()) // Get response as JSON
            .then(data => {
                 // Log the raw response for debugging

                if (data.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message
                    });
                } else {
                    if (!data.students || data.students.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Students',
                            text: 'No students found for the selected batch and evaluation type.'
                        });
                    } else {
                        document.getElementById('selectedBatch').value = batch;
                        document.getElementById('selectedEvaluationType').value = evaluation_type;

                        const studentsList = document.getElementById('studentsList');
                        studentsList.innerHTML = '';

                        data.students.forEach(student => {
                            const row = document.createElement('tr');

                            // Student ID
                            const idCell = document.createElement('td');
                            idCell.textContent = student.rollno;
                            row.appendChild(idCell);

                            // Student Name
                            const nameCell = document.createElement('td');
                            nameCell.textContent = student.name;
                            row.appendChild(nameCell);

                            // Sub Parts
                            const subPartsCell = document.createElement('td');
                            const subParts = student.sub_parts ? student.sub_parts : {};
                            const subPartsList = document.createElement('ul');
                            subPartsList.className = 'list-group';

                            Object.keys(subParts).forEach(subPart => {
                                const subPartItem = document.createElement('li');
                                subPartItem.className = 'list-group-item';
                                const subPartLabel = document.createElement('label');
                                subPartLabel.textContent = subPart;
                                subPartItem.appendChild(subPartLabel);

                                const subPartSelect = document.createElement('select');
                                subPartSelect.name = `sub_parts[${student.id}][${subPart}]`;
                                subPartSelect.className = 'form-select';

                                for (let i = 0; i <= parseInt(subParts[subPart], 10); i++) {
                                    const option = document.createElement('option');
                                    option.value = i;
                                    option.textContent = i;
                                    subPartSelect.appendChild(option);
                                }

                                subPartItem.appendChild(subPartSelect);
                                subPartsList.appendChild(subPartItem);
                            });

                            subPartsCell.appendChild(subPartsList);
                            row.appendChild(subPartsCell);

                            // Remarks
                            const remarksCell = document.createElement('td');
                            const remarksInput = document.createElement('input');
                            remarksInput.type = 'text';
                            remarksInput.name = `remarks[${student.id}]`;
                            remarksInput.className = 'form-control';
                            remarksInput.placeholder = 'Enter remarks';
                            remarksCell.appendChild(remarksInput);
                            row.appendChild(remarksCell);

                            // Previous Marks
                            const prevMarksCell = document.createElement('td');
                            const prevMarksList = document.createElement('ul');
                            prevMarksList.className = 'list-group';

                            const prevMarks = student.prev_marks ? student.prev_marks : {};
                            Object.keys(prevMarks).forEach(part => {
                                const prevMarksItem = document.createElement('li');
                                prevMarksItem.className = 'list-group-item';
                                prevMarksItem.textContent = `${part}: ${prevMarks[part]}`;
                                prevMarksList.appendChild(prevMarksItem);
                            });

                            prevMarksCell.appendChild(prevMarksList);
                            row.appendChild(prevMarksCell);

                            studentsList.appendChild(row);
                        });

                        document.getElementById('studentsContainer').style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while fetching the student details. Please check the console for more information.'
                });
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Input',
                text: 'Please select batch and evaluation type.'
            });
        }
    });
});
</script>

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
            title: 'Success!',
            text: '<?php echo $success; ?>'
        });
    </script>
<?php endif; ?>
</body>
</html>
