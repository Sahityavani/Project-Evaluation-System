<?php
include('../db.php');
include('auth.php');
header('Content-Type: text/html');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT evaluator_id FROM evaluators WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row_evaluator = $result->fetch_assoc();
    $evaluator_id = $row_evaluator['evaluator_id'];
} else {
    echo 'evaluator not found';
    exit();
}


// Get evaluation type and batch ID from POST data
$evaluation_type = isset($_POST['evaluation_type']) ? (int)$_POST['evaluation_type'] : 0;
$batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : 0;

if (!$evaluation_type || !$batch_id) {
    echo 'Invalid evaluation type or batch ID';
    exit();
}

// Fetch maximum values for sub-parts from evaluation_type
$sql_max = "
    SELECT sub_parts
    FROM evaluation_type
    WHERE id = ?
";
$stmt_max = $conn->prepare($sql_max);
$stmt_max->bind_param('i', $evaluation_type);
$stmt_max->execute();
$result_max = $stmt_max->get_result();

if ($result_max->num_rows > 0) {
    $row_max = $result_max->fetch_assoc();
    $maxSubParts = json_decode($row_max['sub_parts'], true);
} else {
    echo 'No sub-parts configuration available for this evaluation type.';
    exit();
}

// Fetch current marks and sub_parts details from mrg_evaluations
$sql_marks = "
    SELECT e.student_id, e.marks, e.remarks, s.rollno, s.name, e.sub_parts
    FROM mrg_evaluations e
    JOIN students s ON e.student_id = s.id
    WHERE e.evaluation_type = ? AND e.batch_id = ? AND e.evaluator_id = ?
";
$stmt_marks = $conn->prepare($sql_marks);
$stmt_marks->bind_param('iis', $evaluation_type, $batch_id, $evaluator_id);
$stmt_marks->execute();
$result_marks = $stmt_marks->get_result();

if ($result_marks->num_rows > 0) {
    echo '<form method="POST" action="update_marks.php">';
    echo '<input type="hidden" name="evaluation_type" value="' . htmlspecialchars($evaluation_type) . '">';
    echo '<input type="hidden" name="evaluator_id" value="' . htmlspecialchars($evaluator_id) . '">';
    echo '<input type="hidden" name="batch_id" value="' . htmlspecialchars($batch_id) . '">';
    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>Roll Number</th><th>Name</th><th>Marks</th><th>Total</th><th>Remarks</th></tr></thead>';
    echo '<tbody>';

    while ($row = $result_marks->fetch_assoc()) {
        // Decode sub_parts and marks from JSON
        $marks = json_decode($row['marks'], true);
        $subParts = json_decode($row['sub_parts'], true);

        if (!is_array($marks)) {
            $marks = [];
        }

        if (!is_array($subParts)) {
            $subParts = [];
        }

        // Calculate total marks
        $totalMarks = $row['marks'];
        foreach ($subParts as $part => $max) {
            if (isset($marks[$part])) {
                $totalMarks += (int)$marks[$part];
            }
        }

        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['rollno']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';

        // Display select fields for each sub-part
        echo '<td>';
        foreach ($maxSubParts as $part => $max) {
            $markValue = isset($subParts[$part]) ? htmlspecialchars($subParts[$part]) : '';
            echo '<label>' . htmlspecialchars($part) . '</label>';
            echo '<select id="mark_' . htmlspecialchars($row['student_id']) . '_' . htmlspecialchars($part) . '" 
                            name="marks[' . htmlspecialchars($row['student_id']) . '][' . htmlspecialchars($part) . ']" 
                            class="form-control mark-input" 
                            data-student-id="' . htmlspecialchars($row['student_id']) . '" 
                            data-part="' . htmlspecialchars($part) . '" 
                            onchange="updateTotal(' . htmlspecialchars($row['student_id']) . ')">';
            for ($i = 0; $i <= $max; $i++) {
                $selected = ($markValue == $i) ? 'selected' : '';
                echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
            }
            echo '</select>';
            echo '<br>';
        }
        echo '</td>';

        // Display total marks
        echo '<td><input type="text" id="total_' . htmlspecialchars($row['student_id']) . '" 
                            name="total_marks[' . htmlspecialchars($row['student_id']) . ']" 
                            value="' . htmlspecialchars($totalMarks) . '" 
                            class="form-control" 
                            readonly></td>';

        echo '<td><input type="text" name="remarks[' . htmlspecialchars($row['student_id']) . ']" 
                            value="' . htmlspecialchars($row['remarks']) . '" 
                            class="form-control"></td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '<button type="submit" class="btn btn-success">Save Changes</button>';
    echo '</form>';
} else {
    echo 'No marks details available for this evaluation type.';
}

$stmt_max->close();
$stmt_marks->close();
$conn->close();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to calculate total marks for a student
    function updateTotal(studentId) {
        let totalMarks = 0;
        // Get all select fields for the student
        document.querySelectorAll(`select[data-student-id="${studentId}"]`).forEach(select => {
            totalMarks += parseInt(select.value) || 0;
        });
        // Update the total marks input field
        document.getElementById(`total_${studentId}`).value = totalMarks;
    }

    // Event listener for all mark inputs
    document.querySelectorAll('.mark-input').forEach(select => {
        select.addEventListener('change', function() {
            const studentId = this.dataset.studentId;
            updateTotal(studentId);
        });
    });

    // Initial calculation for all students
    document.querySelectorAll('.mark-input').forEach(select => {
        const studentId = select.dataset.studentId;
        updateTotal(studentId);
    });
});
</script>
