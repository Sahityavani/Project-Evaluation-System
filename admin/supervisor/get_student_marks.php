<?php
session_start();
include '../db.php'; // Include the database connection file

// Initialize response array with default error status
$response = [
    'status' => 'error', 
    'message' => '', 
    'students' => []
];

// Retrieve POST parameters
$batch = $_POST['batch'] ?? null;
$evaluation_type = $_POST['evaluation_type'] ?? null;

// Check if batch and evaluation_type are provided
if ($batch === null || $evaluation_type === null) {
    $response['message'] = 'Missing batch or evaluation type parameter.';
    echo json_encode($response);
    exit;
}

// Retrieve current supervisor's ID
if (!isset($_SESSION['user'])) {
    $response['message'] = 'User session not set.';
    echo json_encode($response);
    exit;
}

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT id FROM supervisors WHERE username = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $response['message'] = 'Failed to prepare SQL statement: ' . htmlspecialchars($conn->error);
    echo json_encode($response);
    exit;
}

$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    $response['message'] = 'Failed to execute SQL statement: ' . htmlspecialchars($stmt->error);
    echo json_encode($response);
    exit;
}

$supervisor = $result->fetch_assoc();
if (!$supervisor) {
    $response['message'] = 'Supervisor not found for username: ' . htmlspecialchars($username);
    echo json_encode($response);
    exit;
}

$supervisor_id = $supervisor['id'];

// Fetch sub_parts for current and previous evaluation types
function getSubParts($conn, $evaluation_type) {
    $sql = "SELECT sub_parts FROM evaluation_type WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) return null;
    
    $stmt->bind_param('i', $evaluation_type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return json_decode($row['sub_parts'], true) ?: [];
    }
    return null;
}

$sub_parts_current = getSubParts($conn, $evaluation_type);
$previous_evaluation_type = max(1, $evaluation_type - 1);
$sub_parts_previous = getSubParts($conn, $previous_evaluation_type);

if ($sub_parts_current === null || $sub_parts_previous === null) {
    $response['message'] = 'Failed to retrieve sub_parts configuration.';
    echo json_encode($response);
    exit;
}

// Prepare SQL query to fetch students and their previous marks from supervisor_evaluations
$sql = "SELECT DISTINCT s.id, s.name, s.rollno, 
               IFNULL(m.sub_parts, '{}') AS prev_marks
        FROM students s
        LEFT JOIN supervisor_evaluations m ON s.id = m.student_id 
                                            AND m.evaluation_type = ?
                                            AND m.supervisor_id = ?
        WHERE s.batch_id = ?
        GROUP BY s.id";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $response['message'] = 'Failed to prepare SQL statement for fetching students: ' . htmlspecialchars($conn->error);
    echo json_encode($response);
    exit;
}

// Bind parameters and execute the SQL statement
$stmt->bind_param('iii', $previous_evaluation_type, $supervisor_id, $batch);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    $response['message'] = 'Failed to execute SQL statement for fetching students: ' . htmlspecialchars($stmt->error);
    echo json_encode($response);
    exit;
}

// Fetch students and their associated data
$students = [];
while ($row = $result->fetch_assoc()) {
    $prev_marks = json_decode($row['prev_marks'], true) ?? [];
    $formatted_prev_marks = [];
    foreach ($sub_parts_previous as $part => $value) {
        $formatted_prev_marks[$part] = $prev_marks[$part] ?? 0;
    }

    $student = [
        'id' => $row['id'],
        'name' => $row['name'],
        'rollno' => $row['rollno'],
        'sub_parts' => $sub_parts_current,
        'prev_marks' => $formatted_prev_marks
    ];

    $students[] = $student;
}

// Set response status to success and include students data
$response['status'] = 'success';
$response['students'] = $students;

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;