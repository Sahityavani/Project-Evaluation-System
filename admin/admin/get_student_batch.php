<?php
include('auth.php');
include('../db.php');

if (isset($_GET['batch_id']) && isset($_GET['evaluation_type'])) {
    $batch_id = $conn->real_escape_string($_GET['batch_id']);
    $evaluation_type = $conn->real_escape_string($_GET['evaluation_type']);

    // Check if evaluations are completed for the selected batch and type
    $checkQuery = "
        SELECT COUNT(*) as count 
        FROM mrg_evaluations 
        WHERE batch_id = '$batch_id' AND evaluation_type = '$evaluation_type'
    ";
    $checkResult = $conn->query($checkQuery);
    $checkRow = $checkResult->fetch_assoc();
    
    if ($checkRow['count'] == 0) {
        // No evaluations completed
        echo '';
        exit();
    }

    // Fetch student marks for the selected batch and type
    $marksQuery = "
        SELECT s.rollno, s.name, e.marks 
        FROM students s
        JOIN mrg_evaluations e ON s.rollno = e.rollno
        WHERE s.batch_id = '$batch_id' AND e.evaluation_type = '$evaluation_type'
    ";
    $marksResult = $conn->query($marksQuery);

    if ($marksResult->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table align-middle table-nowrap">';
        echo '<thead class="table-light">';
        echo '<tr>';
        echo '<th scope="col">Roll No</th>';
        echo '<th scope="col">Name</th>';
        echo '<th scope="col">Marks</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($row = $marksResult->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['rollno']) . '</td>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['marks']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo 'No evaluations completed for the selected batch and type.';
    }
} else {
    echo 'Invalid request.';
}
?>
