<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batchId = $_POST['id'];
    
    // Fetch batch details
    $batchDetailsQuery = "
        SELECT 
            b.batch_id, 
            b.batch_name, 
            b.batch_title, 
            COUNT(DISTINCT s.rollno) AS num_members, 
            IFNULL(su.name, 'Not Allocated') AS supervisor_name,
            m.mrg_name, 
            GROUP_CONCAT(DISTINCT e.name SEPARATOR ', ') AS evaluators
        FROM 
            batches b
        LEFT JOIN 
            students s ON b.batch_id = s.batch_id
        LEFT JOIN 
            supervisors su ON b.supervisor = su.username
        LEFT JOIN 
            mrg m ON b.mrg_id = m.mrg_id
        LEFT JOIN 
            mrg_evaluators me ON m.mrg_id = me.mrg_id
        LEFT JOIN 
            evaluators e ON me.evaluator_id = e.evaluator_id
        WHERE 
            b.batch_id = ?
        GROUP BY 
            b.batch_id, m.mrg_name
    ";
    $stmt = $conn->prepare($batchDetailsQuery);
    $stmt->bind_param("s", $batchId);
    $stmt->execute();
    $batchDetailsResult = $stmt->get_result();
    $batchDetails = $batchDetailsResult->fetch_assoc();

    if ($batchDetails) {
        echo "
            <div class='batch-details'>
                <h4 class='batch-title'>{$batchDetails['batch_name']} ({$batchDetails['batch_title']})</h4>
                <p><strong>Batch ID:</strong> {$batchDetails['batch_id']}</p>
                <p><strong>Number of Members:</strong> {$batchDetails['num_members']}</p>
                <p><strong>Supervisor:</strong> {$batchDetails['supervisor_name']}</p>
                <p><strong>MRG:</strong> {$batchDetails['mrg_name']}</p>
                <p><strong>Evaluators:</strong> {$batchDetails['evaluators']}</p>
                <h5>Students:</h5>
                <table class='table table-striped table-bordered'>
                    <thead class='thead-dark'>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>";

        // Fetch student details
        $studentDetailsQuery = "
            SELECT rollno, name 
            FROM students 
            WHERE batch_id = ?
        ";
        $stmt = $conn->prepare($studentDetailsQuery);
        $stmt->bind_param("s", $batchId);
        $stmt->execute();
        $studentResult = $stmt->get_result();

        while ($student = $studentResult->fetch_assoc()) {
            echo "<tr>
                    <td>{$student['rollno']}</td>
                    <td>{$student['name']}</td>
                  </tr>";
        }

        echo "      </tbody>
                  </table>
              </div>";
    } else {
        echo "<p>No details found for the selected batch.</p>";
    }
}
?>

<!-- Add this custom CSS to improve the print styling -->
<style>
    .batch-details {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .batch-title {
        color: #007bff;
        font-weight: bold;
    }
    .table {
        margin-top: 15px;
    }
    .table thead {
        background-color: #343a40;
        color: #ffffff;
    }
</style>
