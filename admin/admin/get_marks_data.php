<?php
include('../db.php');
include('auth.php');

$filterType = $_POST['filterType'];
$evaluationType = $_POST['evaluationType'];
$searchTerm = $_POST['searchTerm'];
$page = $_POST['page'];
$rowsPerPage = $_POST['rowsPerPage'];

$offset = ($page - 1) * $rowsPerPage;

$whereClause = "";
$params = array();

if ($searchTerm) {
    $whereClause .= " AND (s.rollno LIKE ?)";
    $params[] = "%$searchTerm%";
}

if ($filterType === 'student') {
    $sql = "SELECT s.rollno, s.name, s.batch_id, et.name as evaluation_type_name, et.id as evaluation_type_id,
                   (SELECT AVG(me.marks) FROM mrg_evaluations me WHERE me.student_rollno = s.rollno AND me.evaluation_type = et.id) as avg_mrg_marks,
                   (SELECT AVG(de.marks) FROM dpeg_evaluations de WHERE de.student_rollno = s.rollno AND de.evaluation_type = et.id) as avg_dpeg_marks,
                   (SELECT se.marks FROM supervisor_evaluations se WHERE se.student_rollno = s.rollno AND se.evaluation_type = et.id) as supervisor_marks
            FROM students s
            JOIN evaluation_type et ON 1=1 
            LEFT JOIN mrg_evaluations me ON s.rollno = me.student_rollno AND me.evaluation_type = et.id
            LEFT JOIN dpeg_evaluations de ON s.rollno = de.student_rollno AND de.evaluation_type = et.id
            LEFT JOIN supervisor_evaluations se ON s.rollno = se.student_rollno AND se.evaluation_type = et.id
            WHERE 1=1";
            if ($whereClause) {
                $sql .= " $whereClause";
            }

    if ($evaluationType) {
        $sql .= " AND et.id = ?";
        $params[] = $evaluationType;
    }

    $sql .= " $whereClause
              GROUP BY s.rollno, et.id
              ORDER BY s.rollno, et.id
              LIMIT $offset, $rowsPerPage";
} else {
    // Evaluator-wise query (Updated to include DPEG and batch_id)
    $sql = "SELECT u.id as evaluator_id, u.name as evaluator_name, 
                   et.name as evaluation_type, et.id as evaluation_type_id,
                   AVG(e.marks) as avg_marks, 
                   COUNT(DISTINCT e.student_rollno) as total_students,
                   s.batch_id
            FROM users u
            LEFT JOIN (
                SELECT evaluator_id, student_rollno, marks, evaluation_type FROM mrg_evaluations
                UNION ALL
                SELECT evaluator_id, student_rollno, marks, evaluation_type FROM dpeg_evaluations 
            ) e ON u.id = e.evaluator_id AND u.type IN ('mrg', 'dpeg')
            JOIN evaluation_type et ON e.evaluation_type = et.id
            JOIN students s ON e.student_rollno = s.rollno
            WHERE u.type IN ('mrg', 'dpeg') $whereClause
            GROUP BY u.id, et.id, s.batch_id
            ORDER BY u.name, et.name, s.batch_id
            LIMIT $offset, $rowsPerPage";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...array_values($params));
}
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Count total rows for pagination
if ($filterType === 'student') {
    $countSql = "SELECT COUNT(DISTINCT s.rollno) as total 
                 FROM students s
                 JOIN evaluation_type et ON 1=1
                 WHERE 1=1 ";
    
    if ($evaluationType) {
        $countSql .= " AND et.id = ?";
    }
    
    $countSql .= $whereClause;
} else {
    $countSql = "SELECT COUNT(DISTINCT u.id) as total 
                 FROM users u
                 WHERE u.type IN ('mrg', 'dpeg') $whereClause";
}

$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

echo json_encode(array(
    'status' => 'success',
    'data' => $data,
    'totalPages' => $totalPages
));

$stmt->close();
$countStmt->close();
$conn->close();
?>
