<?php

include('../db.php');

$batch_id = isset($_POST['batch_id']) ? $conn->real_escape_string($_POST['batch_id']) : '';

if (empty($batch_id)) {
    echo "<p>Batch ID is required.</p>";
    exit;
}

// Fetch students for the given batch
$students_query = "
    SELECT s.rollno, s.name 
    FROM students s
    WHERE s.batch_id = '$batch_id'
";
$students_result = $conn->query($students_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks for Batch <?php echo htmlspecialchars($batch_id); ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Assuming you have a styles.css file -->
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>View Marks for Batch <?php echo htmlspecialchars($batch_id); ?></h1>

    <?php
    if ($students_result->num_rows > 0) {
        while ($student = $students_result->fetch_assoc()) {
            $roll_no = $student['rollno'];
            $name = $student['name'];

            echo "<h2>Student: $name ($roll_no)</h2>";

            // Fetch evaluator IDs associated with the batch
            $mrg_evaluators_query = "
                SELECT evaluator_id 
                FROM mrg_evaluators 
                WHERE batch_id = '$batch_id'
            ";
            $mrg_evaluators_result = $conn->query($mrg_evaluators_query);

            $evaluator_ids = [];
            while ($row = $mrg_evaluators_result->fetch_assoc()) {
                $evaluator_ids[] = $row['evaluator_id'];
            }

            if (!empty($evaluator_ids)) {
                $evaluator_ids_list = implode(',', $evaluator_ids);

                // Fetch evaluator marks for this student
                $evaluator_marks_query = "
                    SELECT e.name AS evaluator_name, m.marks, m.remarks 
                    FROM marks m
                    JOIN evaluators e ON m.evaluator_id = e.evaluator_id
                    WHERE m.student_rollno = '$roll_no' AND m.evaluator_id IN ($evaluator_ids_list)
                ";
                $evaluator_marks_result = $conn->query($evaluator_marks_query);
            } else {
                $evaluator_marks_result = [];
            }

            // Fetch supervisor marks for this student
            $supervisor_marks_query = "
                SELECT marks 
                FROM supervisor_evaluations
                WHERE student_rollno = '$roll_no'
            ";
            $supervisor_marks_result = $conn->query($supervisor_marks_query);

            // Calculate average evaluator marks
            $total_evaluator_marks = 0;
            $evaluator_count = 0;
            if ($evaluator_marks_result && $evaluator_marks_result->num_rows > 0) {
                echo "<h3>Evaluator Marks:</h3>";
                echo "<table>
                    <tr>
                        <th>Evaluator</th>
                        <th>Marks</th>
                        <th>Remarks</th>
                    </tr>";
                while ($row = $evaluator_marks_result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['evaluator_name']}</td>
                        <td>{$row['marks']}</td>
                        <td>{$row['remarks']}</td>
                    </tr>";
                    $total_evaluator_marks += $row['marks'];
                    $evaluator_count++;
                }
                echo "</table>";

                $average_evaluator_marks = $evaluator_count > 0 ? $total_evaluator_marks / $evaluator_count : 0;
            } else {
                echo "<p>No evaluator marks found for this student.</p>";
                $average_evaluator_marks = 0;
            }

            // Calculate total supervisor marks
            $total_supervisor_marks = 0;
            if ($supervisor_marks_result->num_rows > 0) {
                while ($row = $supervisor_marks_result->fetch_assoc()) {
                    $total_supervisor_marks += $row['marks'];
                }
            } else {
                echo "<p>No supervisor marks found for this student.</p>";
            }

            // Total marks calculation
            $total_marks = $total_supervisor_marks + $total_evaluator_marks;

            echo "<div class='summary'>
                <h3>Summary:</h3>
                <p>Supervisor Marks: $total_supervisor_marks</p>
                <p>Average Evaluator Marks: $average_evaluator_marks</p>
                <p>Total Marks: $total_marks</p>
            </div>";
        }
    } else {
        echo "<p>No students found for the given batch.</p>";
    }

    $conn->close();
    ?>

</body>
</html>
