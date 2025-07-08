<?php
include('../db.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "SELECT * FROM evaluation_type WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $mrgSubParts = json_decode($row['sub_parts'], true);
        $supervisorSubParts = json_decode($row['sub_parts_supervisor'], true);

        echo '<form id="editEvaluationTypeForm" method="post" action="update_evaluation_type.php">
            <div class="mb-3">
                <input type="hidden" id="evaluationTypeId" name="id" class="form-control" value="' . $id . '">
                <label for="evaluationTypeName" class="form-label">Evaluation Type Name</label>
                <input type="text" id="evaluationTypeName" name="evaluation_type_name" class="form-control" value="' . $row['name'] . '" required>
            </div>
            <div class="mb-3">
                <label for="maxMarksMRG" class="form-label">Max Marks by MRG</label>
                <input type="number" id="maxMarksMRG" name="max_marks_mrg" class="form-control" value="' . $row['max_marks_mrg'] . '" required>
            </div>
            <div class="mb-3">
                <label for="maxMarksSupervisor" class="form-label">Max Marks by Supervisor</label>
                <input type="number" id="maxMarksSupervisor" name="max_marks_supervisor" class="form-control" value="' . $row['max_marks_supervisor'] . '" required>
            </div>
            <h4>MRG Sub Parts</h4>
            <div id="mrgSubParts">';
        foreach ($mrgSubParts as $subPart => $marks) {
            echo '<div class="sub-part-field mb-3">
                <input type="text" name="mrg_sub_part_name[]" class="form-control" value="' . $subPart . '" required>
                <input type="number" name="mrg_sub_part_marks[]" class="form-control" value="' . $marks . '" required>
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
            </div>';
        }
        echo '</div>
            <button type="button" class="btn btn-secondary" id="addMRGSubPartBtn">Add '.$row['type'].' Sub Part</button>
            <br> <br>
            <h4>Supervisor Sub Parts</h4>
            <div id="supervisorSubParts">';
        foreach ($supervisorSubParts as $subPart => $marks) {
            echo '<div class="sub-part-field mb-3">
                <input type="text" name="supervisor_sub_part_name[]" class="form-control" value="' . $subPart . '" required>
                <input type="number" name="supervisor_sub_part_marks[]" class="form-control" value="' . $marks . '" required>
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-sub-part-btn">Remove</button>
            </div>';
        }
        echo '</div>
             <br>
            <button type="button" class="btn btn-secondary" id="addSupervisorSubPartBtn">Add Supervisor Sub Part</button>
            <br> <br>
            <input type="hidden" name="id" value="' . $id . '">
            <div class="mt-3">
                <button type="submit" class="btn btn-success">Update Evaluation Type</button>
            </div>
        </form>';
    } else {
        echo 'Evaluation type not found';
    }
}
?>