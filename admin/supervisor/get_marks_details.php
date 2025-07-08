<?php
include('../db.php');

header('Content-Type: text/html');

// Get POST data
$evaluation_type = isset($_POST['evaluation_type']) ? (int)$_POST['evaluation_type'] : 0;

if (!$evaluation_type) {
    echo 'Invalid evaluation type.';
    exit();
}

// Fetch marks details
$sql = "SELECT s.id, s.rollno, s.name, e.marks, e.remarks 
        FROM supervisor_evaluations e
        JOIN students s ON e.student_id = s.id
        WHERE e.evaluation_type = $evaluation_type";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<table class="table table-bordered">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Marks</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $student_id = isset($row['id']) ? htmlspecialchars($row['id']) : '';
        echo '<tr>
                <td>' . htmlspecialchars($row['rollno']) . '</td>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . htmlspecialchars($row['marks']) . '</td>
                <td>' . htmlspecialchars($row['remarks']) . '</td>
                <td>
                    <button type="button" class="btn btn-warning edit-btn" data-student-id="' . $student_id . '">Edit</button>
                </td>
              </tr>';
    }
    
    echo '</tbody></table>';
} else {
    echo 'No marks found for this evaluation type.';
}

$conn->close();
?>
