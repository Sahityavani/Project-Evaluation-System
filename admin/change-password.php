<?php
include('auth.php');
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['user']; // Assuming the username is stored in the session
    $old_password = $_POST['old'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Determine the page to redirect back to
    $redirect_back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../login.php';

    // Determine the user type and corresponding table
    if (strpos($redirect_back, 'admin/profile-setting.php') !== false) {
        $table = 'users';
        $where = 'username';
    } elseif (strpos($redirect_back, 'supervisor/profile-setting.php') !== false) {
        $table = 'supervisors';
        $where = 'username';
    } elseif (strpos($redirect_back, 'evaluator/profile-setting.php') !== false) {
        $table = 'evaluators';
        $where = 'username';
    } elseif (strpos($redirect_back, 'student/profile-setting.php') !== false) {
        $table = 'students';
        $where = 'rollno';
    } elseif (strpos($redirect_back, 'dpeg/profile-setting.php') !== false) {
        $table = 'dept_evaluators';
        $where = 'username';
    } else {
        // Invalid redirect_back or unknown user type
        header("Location: ../login.php?error=Invalid+user+type+or+redirect+page");
        exit();
    }

    // Fetch the old password from the appropriate table
    if($table === 'students'){
    $query = "SELECT password FROM $table WHERE rollno = ?";
    }
    else{
        $query = "SELECT password FROM $table WHERE username = ?";
        }
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the old password
        if (password_verify($old_password, $hashed_password)) {
            // Check if new password and confirm password match
            if ($new_password === $confirm_password) {
                // Hash the new password using bcrypt
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the new password in the database
                $update_query = "UPDATE $table SET password = ? WHERE $where = ?";
                if ($update_stmt = $conn->prepare($update_query)) {
                    $update_stmt->bind_param('ss', $new_hashed_password, $username);
                    if ($update_stmt->execute()) {
                        // Remove session variables
                        unset($_SESSION['user']);
                        unset($_SESSION['type']);
                        // Redirect with success message
                        header("Location: ../login.php?success=Your+password+is+changed");
                        exit();
                    } else {
                        // Error updating password
                        header("Location: " . $redirect_back . "?error=Error+updating+password.+Please+try+again.");
                        exit();
                    }
                    $update_stmt->close();
                } else {
                    // Error preparing the update statement
                    header("Location: " . $redirect_back . "?error=Error+preparing+update+statement.");
                    exit();
                }
            } else {
                // New password and confirm password do not match
                header("Location: " . $redirect_back . "?error=New+password+and+confirm+password+do+not+match");
                exit();
            }
        } else {
            // Old password is incorrect
            header("Location: " . $redirect_back . "?error=Old+password+is+incorrect");
            exit();
        }

        $stmt->close();
    } else {
        // Error preparing the select statement
        header("Location: " . $redirect_back . "?error=Error+fetching+user+data.+Please+try+again");
        exit();
    }
    $conn->close();
}
?>
