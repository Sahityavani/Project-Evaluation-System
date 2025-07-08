<?php
include('auth.php');
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Start the session to access the logged-in user

    // Retrieve input data
    $username = $_SESSION['user']; // Assuming the username is stored in the session
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $new_username = isset($_POST['username']) ? $_POST['username'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : null;
    $type = isset($_POST['type']) ? $_POST['type'] : $_SESSION['type']; // Get user type from POST request

    // Determine the page to redirect back to
    $redirect_back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../profile.php';
    $parsed_url = parse_url($redirect_back);
    $redirect_back = $parsed_url['scheme'] . '://' . $parsed_url['host'] . (isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '') . $parsed_url['path'];

    // Validate input data (basic validation for demonstration purposes)
    $errors = [];
    if ($new_username && !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $new_username)) {
        $errors[] = "Invalid username format.";
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($mobile && !preg_match('/^\d{10,15}$/', $mobile)) { // Adjust the length as needed
        $errors[] = "Invalid mobile number format. It should contain only digits and be 10 to 15 digits long.";
    }

    if (empty($errors)) {
        // Prepare SQL query based on provided inputs and user type
        $updates = [];
        $where_clause = '';

        switch ($type) {
            case 'Admin':
                $table = 'users';
                $where_clause = "username = '" . mysqli_real_escape_string($conn, $username) . "'";
                break;
            case 'Supervisor':
                $table = 'supervisors';
                $where_clause = "username = '" . mysqli_real_escape_string($conn, $username) . "'";
                break;
            case 'Evaluator':
                $table = 'evaluators';
                $where_clause = "username = '" . mysqli_real_escape_string($conn, $username) . "'";
                break;
            case 'Student':
                $table = 'students';
                $where_clause = "rollno = '" . mysqli_real_escape_string($conn, $username) . "'";
                break;
            case 'DPEGEvaluator':
                $table = 'dept_evaluators';
                $where_clause = "username = '" . mysqli_real_escape_string($conn, $username) . "'";
                break;
            default:
                header("Location: " . $redirect_back . "?error=" . urlencode("Unknown user type."));
                exit();
        }

        if ($name) {
            $updates[] = "name = '" . mysqli_real_escape_string($conn, $name) . "'";
        }
        if ($new_username) {
            $updates[] = $type === 'Student' ? "rollno = '" . mysqli_real_escape_string($conn, $new_username) . "'" : "username = '" . mysqli_real_escape_string($conn, $new_username) . "'";
            $_SESSION['user'] = $new_username; // Update the session['user'] with the new username
        }
        if ($email) {
            $updates[] = "email = '" . mysqli_real_escape_string($conn, $email) . "'";
        }
        if ($mobile) {
            $updates[] = "mobile = " . (int) $mobile; // Cast to integer
        }

        if (empty($updates)) {
            header("Location: " . $redirect_back . "?status=error&message=" . urlencode("No fields to update"));
            exit();
        }

        $update_query = "UPDATE $table SET " . implode(', ', $updates) . " WHERE " . $where_clause;

        if (mysqli_query($conn, $update_query)) {
            header("Location: " . $redirect_back . "?success=" . urlencode("Your information has been updated"));
            exit();
        } else {
            header("Location: " . $redirect_back . "?error=" . urlencode("Error updating information. Please try again."));
            exit();
        }
    } else {
        $error_message = implode(' ', $errors);
        header("Location: " . $redirect_back . "?error=" . urlencode($error_message));
        exit();
    }

    $conn->close();
}
?>
