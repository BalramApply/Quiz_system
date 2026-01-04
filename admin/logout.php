<?php
// Admin Logout Script
// Path: admin/logout.php

session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page with message
session_start();
$_SESSION['success_message'] = "You have been logged out successfully.";
header("Location: ../login.php");
exit();
?>