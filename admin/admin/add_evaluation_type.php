<?php
include('../db.php');
include('auth.php');

if (
    isset($_POST['evaluation_type']) && 
    isset($_POST['evaluation_type_name']) && 
    isset($_POST['max_marks_mrg']) && 
    isset($_POST['max_marks_supervisor']) && 
    isset($_POST['mrg_sub_part_name']) && 
    isset($_POST['mrg_sub_part_marks']) && 
    isset($_POST['supervisor_sub_part_name']) && 
    isset($_POST['supervisor_sub_part_marks'])
) {
    $evaluation_type = $_POST['evaluation_type'];
    $evaluation_type_name = $_POST['evaluation_type_name'];
    $evaluation_type_id = $_POST['evaluation_type_id'];
    $max_marks_mrg = $_POST['max_marks_mrg'];
    $max_marks_supervisor = $_POST['max_marks_supervisor'];
    $mrg_sub_part_name = $_POST['mrg_sub_part_name'];
    $mrg_sub_part_marks = $_POST['mrg_sub_part_marks'];
    $supervisor_sub_part_name = $_POST['supervisor_sub_part_name'];
    $supervisor_sub_part_marks = $_POST['supervisor_sub_part_marks'];

    // Check if total marks of sub parts equal to marks for both MRG and Supervisor
    $mrg_total_marks = array_sum($mrg_sub_part_marks);
    $supervisor_total_marks = array_sum($supervisor_sub_part_marks);

    if ($mrg_total_marks != $max_marks_mrg || $supervisor_total_marks != $max_marks_supervisor) {
        header('Location: add-evaluation.php?error=Total marks of sub parts do not equal to marks for both MRG and Supervisor');
        exit;
    }

    // Check if there is already an evaluation type with the same name and type
    $sql = "SELECT * FROM evaluation_type WHERE name = ? AND type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $evaluation_type_name, $evaluation_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header('Location: add-evaluation.php?error=Evaluation type with the same name and type already exists');
        exit;
    }

    // Insert new evaluation type or update if the name exists with a different type
    $mrg_sub_parts = array();
    for ($i = 0; $i < count($mrg_sub_part_name); $i++) {
        $mrg_sub_parts[$mrg_sub_part_name[$i]] = $mrg_sub_part_marks[$i];
    }

    $supervisor_sub_parts = array();
    for ($i = 0; $i < count($supervisor_sub_part_name); $i++) {
        $supervisor_sub_parts[$supervisor_sub_part_name[$i]] = $supervisor_sub_part_marks[$i];
    }

    $sql = "INSERT INTO evaluation_type (id, name, type, max_marks_mrg, max_marks_supervisor, sub_parts, sub_parts_supervisor)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            type = VALUES(type),
            max_marks_mrg = VALUES(max_marks_mrg),
            max_marks_supervisor = VALUES(max_marks_supervisor),
            sub_parts = VALUES(sub_parts),
            sub_parts_supervisor = VALUES(sub_parts_supervisor)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ississs", 
        $evaluation_type_id, 
        $evaluation_type_name, 
        $evaluation_type, 
        $max_marks_mrg, 
        $max_marks_supervisor, 
        json_encode($mrg_sub_parts), 
        json_encode($supervisor_sub_parts)
    );
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: add-evaluation.php?success=Evaluation type added or updated successfully');
    } else {
        header('Location: add-evaluation.php?error=Failed to add or update evaluation type');
    }
} else {
    header('Location: add-evaluation.php?error=Invalid request');
}
?>
