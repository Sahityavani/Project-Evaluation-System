<?php
include('auth.php');
$title = "Evaluator | View Batches";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM evaluators WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$type = $_SESSION['type'];

$sql = "SELECT evaluator_id FROM evaluators WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Fetch batches with students and their MRG details for the current evaluator
$batchesQuery = "
    SELECT 
        b.batch_id, 
        b.batch_name, 
        b.batch_title, 
        IFNULL(su.name, 'Not Allocated') AS supervisor_name,
        m.mrg_name, 
        GROUP_CONCAT(DISTINCT e.name SEPARATOR ', ') AS evaluators
    FROM 
        batches b
    LEFT JOIN 
        supervisors su ON b.supervisor = su.username
    LEFT JOIN 
        mrg m ON b.mrg_id = m.mrg_id
    LEFT JOIN 
        mrg_evaluators me ON m.mrg_id = me.mrg_id
    LEFT JOIN 
        evaluators e ON me.evaluator_id = e.evaluator_id
    WHERE 
        e.username = ?
    GROUP BY 
        b.batch_id, m.mrg_name
";
$stmt = $conn->prepare($batchesQuery);
$stmt->bind_param("s", $username); // "s" indicates string parameter type
$stmt->execute();
$batchesResult = $stmt->get_result();
?>

<body>

<!-- Add these in your head or before closing body tag if not already present -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

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
                            <h4 class="mb-sm-0">Batches</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active">View Batches</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed View -->
                <div id="details-container" class="mt-4">
                    <h5>Batch Details</h5>
                    <div class="row">
                        <?php while ($batchRow = $batchesResult->fetch_assoc()): ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($batchRow['batch_name']); ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($batchRow['batch_title']); ?></h6>
                                        <p class="card-text">
                                            <strong>Batch ID:</strong> <?php echo htmlspecialchars($batchRow['batch_id']); ?><br>
                                            <strong>Supervisor:</strong> <?php echo htmlspecialchars($batchRow['supervisor_name']); ?><br>
                                        </p>
                                        <button type="button" class="btn btn-info view-batch-details" data-batch-id="<?php echo htmlspecialchars($batchRow['batch_id']); ?>">Show Details</button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Batch Details Modal -->
                <div class="modal fade show" id="batchDetailsModal"  tabindex="-1" role="dialog" aria-labelledby="batchDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content" id="print">
                            <div class="modal-header">
                                <h5 class="modal-title" id="batchDetailsModalLabel">Batch Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="batch-details-container"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="print-report-btn">Print Report</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>

<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.6.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.6.3/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        var bid = '';
        $('.view-batch-details').on('click', function (event) {
            event.preventDefault();
            const batchId = $(this).data('batch-id');
            bid = batchId;

            $.ajax({
                url: 'get_batch_details_print.php',
                type: 'POST',
                data: { id: batchId },
                success: function (data) {
                    $('#batch-details-container').html(data);
                    $('#batchDetailsModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching batch details:', error);
                }
            });
        });

        $('#print-report-btn').on('click', function() {
            const element = document.getElementById('print');
            
        // Configure the PDF options
        const opt = {
            margin:       1,
            filename: `batchid_${bid}_details.pdf`,
            image:        { type: 'jpeg', quality: 2 },
            html2canvas:  { scale: 10 },
            jsPDF:        { unit: 'in', format: 'A4', orientation: 'portrait' }
        };

        html2pdf().from(element).set(opt).save();
        });
    });
</script>

</body>
</html>
