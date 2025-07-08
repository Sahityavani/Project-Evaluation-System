<?php
include('../db.php');

if (isset($_POST['student_id'])) {
    $studentId = intval($_POST['student_id']);

    // Query to fetch student details
    $studentQuery = "
        SELECT s.rollno, s.name, s.batch_id, s.mrg_id, b.batch_name, m.mrg_name
        FROM students s
        LEFT JOIN batches b ON s.batch_id = b.batch_id
        LEFT JOIN mrg m ON s.mrg_id = m.mrg_id
        WHERE s.id = $studentId
    ";
    $studentResult = $conn->query($studentQuery);

    if ($studentResult->num_rows > 0) {
        $student = $studentResult->fetch_assoc();

        // Prepare the HTML output
        $output = '
        <table class="table align-middle table-nowrap">
            <thead class="table-light">
                <tr>
                    <th scope="col">Roll No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Batch</th>
                    <th scope="col">MRG</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>' . htmlspecialchars($student['rollno']) . '</td>
                    <td>' . htmlspecialchars($student['name']) . '</td>
                    <td>' . htmlspecialchars($student['batch_name'] ? $student['batch_name'] : 'Not Assigned') . '</td>
                    <td>' . htmlspecialchars($student['mrg_name'] ? $student['mrg_name'] : 'Not Assigned') . '</td>
                </tr>
            </tbody>
        </table>
        ';

        echo $output;
    } else {
        echo 'No details found.';
    }
} else {
    echo 'Invalid request.';
}

$conn->close();
?>
