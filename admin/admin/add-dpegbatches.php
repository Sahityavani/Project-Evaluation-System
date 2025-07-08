<?php
include('auth.php');
$title = "Admin | Add/Edit DPEG-Batches";
include('../head.php');
include('../db.php');

// Get user details
$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$name = $row['name'];

// Initialize success and error messages
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch existing sections
$sections = [];
$q_sections = "SELECT DISTINCT section FROM students";
$res_sections = $conn->query($q_sections);
if ($res_sections) {
    while ($r = $res_sections->fetch_assoc()) {
        $sections[] = $r;
    }
}

// Fetch existing DPEGs
$dpegs = [];
$q_dpegs = "SELECT * FROM dpeg";
$res_dpegs = $conn->query($q_dpegs);
if ($res_dpegs) {
    while ($r2 = $res_dpegs->fetch_assoc()) {
        $dpegs[] = $r2;
    }
}

// Get the next DPEG-Batch ID
$nextdpegbt = 1; // Default value
$q_dpegsbt = "SELECT id FROM dpeg_batches ORDER BY id DESC LIMIT 1";
$res_dpegsbt = $conn->query($q_dpegsbt);
if ($res_dpegsbt && $res_dpegsbt->num_rows > 0) {
    $nextb = $res_dpegsbt->fetch_assoc();
    $nextdpegbt = $nextb['id'] + 1;
}

// Fetch existing DPEG-Section mappings
$dpeg_sections = [];
$q_dpeg_sections = "
    SELECT mb.id, s.section, m.dpeg_name, COUNT(DISTINCT b.batch_id) as batch_count
    FROM dpeg_batches mb
    JOIN batches b ON mb.batch_id = b.batch_id
    JOIN dpeg m ON mb.dpeg_id = m.dpeg_id
    JOIN students s ON b.batch_id = s.batch_id
    GROUP BY mb.id, s.section, m.dpeg_name
";
$res_dpeg_sections = $conn->query($q_dpeg_sections);
if ($res_dpeg_sections) {
    while ($rs = $res_dpeg_sections->fetch_assoc()) {
        $dpeg_sections[] = $rs;
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
                                <h4 class="mb-sm-0">DPEG-Section Mappings</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                        <li class="breadcrumb-item active">Add DPEG-Batches</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add DPEG-Batch Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Add DPEG-Section Batches</h4>
                        </div>
                        <div class="card-body">
                            <form id="addDpegBatchForm" method="post" action="add_dpeg_batch.php">
                                <div class="mb-3">
                                    <label for="id" class="form-label">DPEG MAP ID</label>
                                    <input type="text" id="id" name="id" class="form-control" value="<?php echo htmlspecialchars($nextdpegbt); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="section" class="form-label">Select Section</label>
                                    <select id="section" name="section" class="form-control" required>
                                        <option value="">Select Section</option>
                                        <?php foreach ($sections as $section): ?>
                                            <option value="<?php echo htmlspecialchars($section['section']); ?>"><?php echo htmlspecialchars($section['section']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="dpeg_id" class="form-label">Select DPEG</label>
                                    <select id="dpeg_id" name="dpeg_id" class="form-control" required>
                                        <option value="">Select DPEG</option>
                                        <?php foreach ($dpegs as $dpeg): ?>
                                            <option value="<?php echo htmlspecialchars($dpeg['dpeg_id']); ?>"><?php echo htmlspecialchars($dpeg['dpeg_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Add DPEG-Section</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Existing DPEG-Section Mappings -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Existing DPEG-Section Mappings</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap" id="DpegSectionTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Mapping ID</th>
                                            <th scope="col">Section</th>
                                            <th scope="col">DPEG Name</th>
                                            <th scope="col">No of Batches</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dpeg_sections as $dpeg_section): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($dpeg_section['id']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg_section['section']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg_section['dpeg_name']); ?></td>
                                                <td><?php echo htmlspecialchars($dpeg_section['batch_count']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="<?php echo htmlspecialchars($dpeg_section['id']); ?>">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-section-btn" data-id="<?php echo htmlspecialchars($dpeg_section['id']); ?>">Delete</button>
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

           <!-- Edit Modal -->
<div class="modal fade" id="editDpegSectionModal" tabindex="-1" aria-labelledby="editDpegSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDpegSectionModalLabel">Edit DPEG-Section Mapping</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDpegSectionForm" method="post" action="edit_dpeg_section.php">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit_section" class="form-label">Section</label>
                        <input type="text" id="edit_section" name="section" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_dpeg_id" class="form-label">DPEG ID</label>
                        <input type="text" id="edit_dpeg_id" name="dpeg_id" class="form-control" readonly>
                    </div>
                    
                    <label for="batches" class="form-label">Associated Batches</label>
                    <div id="batches_list">
                    
                        <!-- Existing batches will be populated here -->
                    </div>

                    <div class="mb-3 mt-3">
                        <button type="button" id="add_batch_btn" class="btn btn-primary">Add Batch</button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="available_batches" class="form-label">Available Batches</label>
                        <select id="available_batches" class="form-control" multiple>
                            <!-- Options will be populated with JavaScript -->
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


        </div>
        <!-- End main content -->

        <?php include('../footer.php'); ?>
    </div>
    <!-- End page -->

    <!-- JavaScript -->
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>

    <script>
       document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.edit-btn');

    // Handle edit button clicks
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            // Fetch DPEG section details using AJAX
            fetch(`get_dpeg_section_details.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                        return;
                    }
                    
                    // Populate modal with DPEG data
                    document.getElementById('edit_id').value = data.dpeg.dpeg_id;
                    document.getElementById('edit_section').value = data.sections[0].section;
                    document.getElementById('edit_dpeg_id').value = data.dpeg.dpeg_id;

                    // Populate existing batches
                    const batchesList = document.getElementById('batches_list');
                    batchesList.innerHTML = '';
                    data.current_batches.forEach(batch => {
                        const batchDiv = document.createElement('div');
                        batchDiv.className = 'batch-item mb-2';
                        batchDiv.dataset.batchId = batch.batch_id;
                        batchDiv.innerHTML = `
                            <input type="text" name="batches[]" value="${batch.batch_id}" class="form-control" readonly>
                            <button type="button" class="btn btn-danger btn-sm mt-1 remove-batch-btn" data-batch-id="${batch.batch_id}">Remove</button>
                        `;
                        batchesList.appendChild(batchDiv);
                    });

                    // Populate available batches in a select dropdown
                    const availableBatchesSelect = document.getElementById('available_batches');
                    availableBatchesSelect.innerHTML = '';
                    data.available_batches.forEach(batch => {
                        const option = document.createElement('option');
                        option.value = batch.batch_id;
                        option.textContent = `${batch.batch_id} - ${batch.batch_name}`;
                        availableBatchesSelect.appendChild(option);
                    });

                    // Show the edit modal
                    $('#editDpegSectionModal').modal('show');
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Handle adding new batches to the DPEG-Section
    document.getElementById('add_batch_btn').addEventListener('click', function() {
        const availableBatchesSelect = document.getElementById('available_batches');
        const selectedOptions = [...availableBatchesSelect.selectedOptions];

        selectedOptions.forEach(option => {
            const batchDiv = document.createElement('div');
            batchDiv.className = 'batch-item mb-2';
            batchDiv.dataset.batchId = option.value;
            batchDiv.innerHTML = `
                <input type="text" name="batches[]" value="${option.value}" class="form-control" readonly>
                <button type="button" class="btn btn-danger btn-sm mt-1 remove-batch-btn" data-batch-id="${option.value}">Remove</button>
            `;
            document.getElementById('batches_list').appendChild(batchDiv);

            // Remove the selected batch from the available batches dropdown
            option.remove();
        });
    });

    // Handle removing batches from the DPEG-Section
    document.getElementById('batches_list').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-batch-btn')) {
            const batchItem = event.target.closest('.batch-item');
            const batchId = batchItem.dataset.batchId;

            // Remove the batch item from the list
            batchItem.remove();

            // Add the removed batch back to the available batches dropdown
            const availableBatchesSelect = document.getElementById('available_batches');
            const option = document.createElement('option');
            option.value = batchId;
            option.textContent = batchId;
            availableBatchesSelect.appendChild(option);
        }
    });

    const deleteBtns = document.querySelectorAll('.delete-section-btn');

    // Handle delete button clicks
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');

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
                    // Send delete request
                    fetch(`delete_dpeg_section.php?id=${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            'id': id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Deleted!',
                                'The DPEG-Section mapping has been deleted.',
                                'success'
                            ).then(() => {
                                // Reload the page or remove the row
                                window.location.href = window.location.pathname;
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                data.error,
                                'error'
                            );
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    });
});

    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

</body>
</html>