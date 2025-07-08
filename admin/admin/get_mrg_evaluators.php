<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mrgId = isset($_POST['mrg_id']) ? intval($_POST['mrg_id']) : 0;

    // Query to get MRG details and evaluators
    $query = "
        SELECT m.mrg_name, m.mrg_title, e.evaluator_id, e.name AS evaluator_name
        FROM mrg m
        LEFT JOIN mrg_evaluators me ON m.mrg_id = me.mrg_id
        LEFT JOIN evaluators e ON me.evaluator_id = e.evaluator_id
        WHERE m.mrg_id = $mrgId
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch MRG details
        $mrgDetails = $result->fetch_assoc();
        
        // Output HTML for MRG details and evaluators
        echo '<div id="printable-area">';
        echo '  <div class="card mb-3">';
        echo '    <div class="card-header">';
        echo '      <h5 class="card-title">MRG Details</h5>';
        echo '    </div>';
        echo '    <div class="card-body">';
        echo '      <p><strong>MRG Name:</strong> ' . htmlspecialchars($mrgDetails['mrg_name']) . '</p>';
        echo '      <p><strong>MRG Title:</strong> ' . htmlspecialchars($mrgDetails['mrg_title']) . '</p>';
        
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
            echo '            <tr><td colspan="2">No evaluators found for this MRG.</td></tr>';
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
        echo '        header: "MRG Details",';
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
        echo '<p>No MRG found for this ID.</p>';
    }
}
?>
