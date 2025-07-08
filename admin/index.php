<?php
session_start(); // Ensure session is started

$user = $_SESSION['user'] ?? ''; // Use null coalescing operator for safety

switch ($user) {
    case 'Admin':
        header('Location: ../admin/index.php');
        break;
    case 'Evaluator':
        header('Location: ../evaluator/index.php');
        break;
    case 'Supervisor':
        header('Location: ../supervisor/index.php');
        break;
    case 'Student':
        header('Location: ../student/index.php');
        break;
    case 'DPEGEvaluator':
        header('Location: ../student/index.php');
        break;
    default:
        // Handle unknown or unauthenticated user case
        header('Location: ../login.php'); // Redirect to login or error page
        break;
}

exit; // Ensure the script terminates after redirection
?>
