<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dpegId = isset($_POST['dpeg_id']) ? intval($_POST['dpeg_id']) : 0;

    // Query to get DPEG details and evaluators
    $query = "
        SELECT m.dpeg_name, m.dpeg_title, e.evaluator_id, e.name AS evaluator_name
        FROM dpeg m
        LEFT JOIN dpeg_evaluators me ON m.dpeg_id = me.dpeg_id
        LEFT JOIN dept_evaluators e ON me.evaluator_id = e.evaluator_id
        WHERE m.dpeg_id = $dpegId
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch DPEG details
        $dpegDetails = $result->fetch_assoc();
        
        // Output HTML for DPEG details and evaluators
        echo '<div id="printable-area">';
        echo '  <div class="card mb-3">';
        echo '    <div class="card-header">';
        echo '      <h5 class="card-title">DPEG Details</h5>';
        echo '    </div>';
        echo '    <div class="card-body">';
        echo '      <p><strong>DPEG Name:</strong> ' . htmlspecialchars($dpegDetails['dpeg_name']) . '</p>';
        echo '      <p><strong>DPEG Title:</strong> ' . htmlspecialchars($dpegDetails['dpeg_title']) . '</p>';
        
        // Reset pointer to fetch evaluators
        $result->data_seek(0);

        // Output evaluators table
        echo '      <div class="mt-4">';
        echo '        <h6>Evaluators</h6>';
        echo '        <table class="table table-bordered">';
        echo '          <thead class="thead-light">';
        echo '            <tr><th>Evaluator ID</th><th>Evaluator Name</th></tr>';
        echo '          </thead>';
        echo '          <tbody>';

        $hasEvaluators = false;
        while ($row = $result->fetch_assoc()) {
            if ($row['evaluator_id']) {
                $hasEvaluators = true;
                echo '            <tr>';
                echo '              <td>' . htmlspecialchars($row['evaluator_id']) . '</td>';
                echo '              <td>' . htmlspecialchars($row['evaluator_name']) . '</td>';
                echo '            </tr>';
            }
        }

        if (!$hasEvaluators) {
            echo '            <tr><td colspan="2">No evaluators found for this DPEG.</td></tr>';
        }

        echo '          </tbody>';
        echo '        </table>';
        echo '      </div>';
        echo '      <button id="print-btn" class="btn btn-primary mt-3">Print</button>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';

        // Include PrintJS library
        echo '<script src="https://cdn.jsdelivr.net/npm/print-js@1.6.0/dist/print.min.js"></script>';
        echo '<script>';
        echo '  document.addEventListener("DOMContentLoaded", function() {';
        echo '    document.getElementById("print-btn").addEventListener("click", function() {';
        echo '      console.log("Print button clicked");'; // Debugging line
        echo '      printJS({';
        echo '        printable: "printable-area",';
        echo '        type: "html",';
        echo '        targetStyles: ["*"],';
        echo '        header: "DPEG Details",';
        echo '        style: `';
        echo '          @media print {';
        echo '            .card { border: none; }';
        echo '            table { width: 100%; border-collapse: collapse; }';
        echo '            th, td { border: 1px solid black; padding: 8px; }';
        echo '            th { background-color: #f2f2f2; }';
        echo '          }';
        echo '        `';
        echo '      });';
        echo '    });';
        echo '  });';
        echo '</script>';
    } else {
        echo '<p>No DPEG found for this ID.</p>';
    }
}
?>
