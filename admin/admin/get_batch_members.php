<?php
include('../db.php');

// Check if batch_id is set
if (isset($_POST['batch_id'])) {
    $batchId = intval($_POST['batch_id']);
    
    // Fetch batch details including supervisor
    $batchQuery = "
        SELECT b.batch_name, b.batch_title, b.supervisor, s.name AS supervisor_name
        FROM batches b
        LEFT JOIN supervisors s ON b.supervisor = s.username
        WHERE b.batch_id = $batchId
    ";
    $batchResult = $conn->query($batchQuery);

    if ($batchResult->num_rows > 0) {
        $batch = $batchResult->fetch_assoc();
        $supervisorName = $batch['supervisor_name'] ?: 'Not Allocated';

        // Fetch members for the batch
        $membersQuery = "
            SELECT rollno, name, email, mobile
            FROM students
            WHERE batch_id = $batchId
        ";
        $membersResult = $conn->query($membersQuery);

        echo '<div id="batch-details">';
        echo '<div class="card">';
        echo '<div class="card-header">';
        echo '<h4 class="card-title mb-0">Batch Details</h4>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<h5>Batch Name: ' . htmlspecialchars($batch['batch_name']) . '</h5>';
        echo '<h6>Batch Title: ' . htmlspecialchars($batch['batch_title']) . '</h6>';
        echo '<h6>Supervisor: ' . htmlspecialchars($supervisorName) . '</h6>';
        echo '<div class="table-responsive">';
        echo '<table id="batch-table" class="table align-middle table-nowrap">';
        echo '<thead class="table-light">';
        echo '<tr>';
        echo '<th scope="col">Roll Number</th>';
        echo '<th scope="col">Name</th>';
        echo '<th scope="col">Email</th>';
        echo '<th scope="col">Mobile</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        if ($membersResult->num_rows > 0) {
            while ($member = $membersResult->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($member['rollno']) . '</td>';
                echo '<td>' . htmlspecialchars($member['name']) . '</td>';
                echo '<td>' . htmlspecialchars($member['email']) . '</td>';
                echo '<td>' . htmlspecialchars($member['mobile']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4">No members found for this batch.</td></tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '<button id="print-btn" class="btn btn-primary mt-3">Generate PDF</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="card">';
        echo '<div class="card-header">';
        echo '<h4 class="card-title mb-0">Batch Details</h4>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<p>Batch not found.</p>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo 'Batch ID not provided.';
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const printButton = document.getElementById('print-btn');
    
    if (printButton) {
        printButton.addEventListener('click', function() {
            // Hide the print button before generating the PDF
            printButton.style.display = 'none';
            
            html2canvas(document.getElementById('batch-details'), {
                scale: 2, // Increase scale for better quality
                useCORS: true // Enable CORS to handle cross-origin images
            }).then(canvas => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const imgWidth = 190; // A4 width in mm minus margins
                const pageHeight = 295; // A4 height in mm
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;

                const imgData = canvas.toDataURL('image/jpeg', 0.95); // Use JPEG with 95% quality

                let position = 0;

                doc.addImage(imgData, 'JPEG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position -= pageHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'JPEG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                doc.save('batch-details.pdf');

                // Show the print button again after generating the PDF
                printButton.style.display = 'block';
            }).catch(error => {
                console.error('Error generating PDF:', error);
                // Show the print button again if there's an error
                printButton.style.display = 'block';
            });
        });
    }
});
</script>
