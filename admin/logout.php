<?php
// Start the session
session_start();

// Unset session variables
unset($_SESSION['user']);
unset($_SESSION['type']);

// Destroy the session
session_destroy();

// Redirect to login page or homepage
header("Location: ../login.php?success=You+Logged+Out+Successfully");
exit();
?>
