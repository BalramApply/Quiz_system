<?php
// Login Processing Script
// Path: process_login.php

require_once 'config/database.php';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// Get and sanitize form data
$email = clean_input($_POST['email']);
$password = $_POST['password'];

// Validation
if (empty($email) || empty($password)) {
    $_SESSION['error_message'] = "Email and password are required.";
    header("Location: login.php");
    exit();
}

// Check if email exists and get user details
$query = "SELECT id, name, email, password, role FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

// Check if user exists
if (mysqli_num_rows($result) === 0) {
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: login.php");
    exit();
}

// Fetch user data
$user = mysqli_fetch_assoc($result);

// Verify password
if (!password_verify($password, $user['password'])) {
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: login.php");
    exit();
}

// Password is correct - Create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['name'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

// Set success message
$_SESSION['success_message'] = "Welcome back, " . htmlspecialchars($user['name']) . "!";

// Redirect based on role
if ($user['role'] === 'admin') {
    header("Location: admin/dashboard.php");
} else {
    header("Location: student/dashboard.php");
}
exit();
?>