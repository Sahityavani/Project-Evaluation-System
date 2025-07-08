<?php
include('../db.php');
include('auth.php');

if (isset($_POST['evaluation_type_name']) && isset($_POST['max_marks_mrg']) && isset($_POST['max_marks_supervisor']) && isset($_POST['mrg_sub_part_name']) && isset($_POST['mrg_sub_part_marks']) && isset($_POST['supervisor_sub_part_name']) && isset($_POST['supervisor_sub_part_marks']) && isset($_POST['id'])) {
    $evaluation_type_name = $_POST['evaluation_type_name'];
    $max_marks_mrg = $_POST['max_marks_mrg'];
    $max_marks_supervisor = $_POST['max_marks_supervisor'];
    $mrg_sub_part_name = $_POST['mrg_sub_part_name'];
    $mrg_sub_part_marks = $_POST['mrg_sub_part_marks'];
    $supervisor_sub_part_name = $_POST['supervisor_sub_part_name'];
    $supervisor_sub_part_marks = $_POST['supervisor_sub_part_marks'];
    $id = $_POST['id'];

    // Check if any evaluations exist for the given ID in mrg_evaluations and supervisor_evaluations
    $sql = "SELECT * FROM mrg_evaluations WHERE evaluation_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header('Location: add-evaluation.php?error=Evaluations already exist for this evaluation type');
        exit;
    }

    $sql = "SELECT * FROM supervisor_evaluations WHERE evaluation_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header('Location: add-evaluation.php?error=Evaluations already exist for this evaluation type');
        exit;
    }

    // Check if total marks of sub parts equal to marks for both MRG and Supervisor
    $mrg_total_marks = array_sum($mrg_sub_part_marks);
    $supervisor_total_marks = array_sum($supervisor_sub_part_marks);

    if ($mrg_total_marks != $max_marks_mrg || $supervisor_total_marks != $max_marks_supervisor) {
        header('Location: add-evaluation.php?id=&error=Total marks of sub parts do not equal to marks for both MRG and Supervisor');
        exit;
    }

    // Update evaluation type
    $mrg_sub_parts = array();
    for ($i = 0; $i < count($mrg_sub_part_name); $i++) {
        $mrg_sub_parts[$mrg_sub_part_name[$i]] = $mrg_sub_part_marks[$i];
    }

    $supervisor_sub_parts = array();
    for ($i = 0; $i < count($supervisor_sub_part_name); $i++) {
        $supervisor_sub_parts[$supervisor_sub_part_name[$i]] = $supervisor_sub_part_marks[$i];
    }

    $sql = "UPDATE evaluation_type SET name = ?, max_marks_mrg = ?, max_marks_supervisor = ?, sub_parts = ?, sub_parts_supervisor = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siissi", $evaluation_type_name, $max_marks_mrg, $max_marks_supervisor, json_encode($mrg_sub_parts), json_encode($supervisor_sub_parts), $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: add-evaluation.php?success=Evaluation type updated successfully');
    } else {
        header('Location: add-evaluation.php?error=Failed to update evaluation type');
    }
} else {
    header('Location: add-evaluation.php?error=Invalid request');
}
?>