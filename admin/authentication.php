<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    $type = $_POST['type']; // Get user type from POST request

    // Function to check user credentials based on type
    function authenticateUser($conn, $username, $password, $type) {
        // Determine table and fields based on type
        switch ($type) {
            case 'Admin':
                $table = 'users';
                $user_field = 'username';
                break;
            case 'Supervisor':
                $table = 'supervisors';
                $user_field = 'username';
                break;
            case 'Evaluator':
                $table = 'evaluators';
                $user_field = 'username';
                break;
            case 'Student':
                $table = 'students';
                $user_field = 'rollno'; // For students, use rollno instead of username
                break;
            case 'DPEGEvaluator':
                $table = 'dept_evaluators';
                $user_field = 'username';
                break;
            default:
                return false; // Unknown user type
        }

        // Fetch password from the appropriate table
        $query = "SELECT $user_field, password FROM $table WHERE $user_field = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($db_username, $db_password);
                $stmt->fetch();
                if (password_verify($password, $db_password)) {
                    return ['username' => $db_username, 'type' => $type];
                }
            }
            $stmt->close();
        }

        // No match found
        return false;
    }

    // Set session cookie parameters before starting the session
    $lifetime = $remember ? 3 * 24 * 60 * 60 : 5 * 60 * 60; // 3 days or 5 hours
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'domain' => '', // Adjust domain as needed
        'secure' => true, // Set to true if using HTTPS
        'httponly' => true,
    ]);

    // Start the session
    session_start();

    // Authenticate user
    $user = authenticateUser($conn, $username, $password, $type);

    if ($user) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['type'] = $user['type'];

        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        // Redirect based on user type
        switch ($user['type']) {
            case 'Admin':
                header("Location: admin/index.php?success=Successfully+Logged+In");
                break;
            case 'Supervisor':
                header("Location: supervisor/index.php?success=Successfully+Logged+In");
                break;
            case 'Evaluator':
                header("Location: mrg/index.php?success=Successfully+Logged+In");
                break;
            case 'Student':
                header("Location: student/index.php?success=Successfully+Logged+In");
                break;
            case 'DPEGEvaluator':
                header("Location: dpeg/index.php?success=Successfully+Logged+In");
                break;
            default:
                header("Location: login.php?error=" . urlencode("Unknown user type."));
                break;
        }
        exit();
    } else {
        $error = "Invalid username or password.";
        header("Location: login.php?error=" . urlencode($error)); // Redirect with error message
        exit();
    }

    $conn->close();
}
?>
