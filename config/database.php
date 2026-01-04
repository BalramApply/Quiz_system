<?php
// Database Configuration File
// Path: config/database.php

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quiz_system');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to sanitize input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Check if user is student
function is_student() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit();
    }
}

// Redirect if not admin
function require_admin() {
    if (!is_admin()) {
        header("Location: ../index.php");
        exit();
    }
}

// Redirect if not student
function require_student() {
    if (!is_student()) {
        header("Location: ../index.php");
        exit();
    }
}

// Format date
function format_date($date) {
    return date('d M Y, h:i A', strtotime($date));
}

// Calculate percentage
function calculate_percentage($score, $total) {
    if ($total == 0) return 0;
    return round(($score / $total) * 100, 2);
}
?>