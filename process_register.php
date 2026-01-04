<?php
// Registration Processing Script
// Path: process_register.php

require_once 'config/database.php';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit();
}

// Get and sanitize form data
$name = clean_input($_POST['name']);
$email = clean_input($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validation
$errors = [];

// Check if all fields are filled
if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
    $errors[] = "All fields are required.";
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

// Check password length
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters long.";
}

// Check if passwords match
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

// Check if email already exists
if (empty($errors)) {
    $check_email = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);
    
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Email already registered. Please use a different email or login.";
    }
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(" ", $errors);
    header("Location: register.php");
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user into database
$insert_query = "INSERT INTO users (name, email, password, role) 
                 VALUES ('$name', '$email', '$hashed_password', 'student')";

if (mysqli_query($conn, $insert_query)) {
    $_SESSION['success_message'] = "Registration successful! Please login to continue.";
    header("Location: login.php");
    exit();
} else {
    $_SESSION['error_message'] = "Registration failed. Please try again. Error: " . mysqli_error($conn);
    header("Location: register.php");
    exit();
}
?>