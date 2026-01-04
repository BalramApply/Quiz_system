<?php
// Login Page
// Path: login.php

require_once 'config/database.php';

// If user is already logged in, redirect to dashboard
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit();
}

$page_title = "Login";
include 'includes/header.php';
?>

<div class="auth-container">
    <div class="card auth-card">
        <div class="card-header text-center">
            <h3 class="mb-0">
                <i class="fas fa-sign-in-alt"></i> Login
            </h3>
        </div>
        <div class="card-body p-4">
            <form action="process_login.php" method="POST">
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your email" required autofocus>
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter your password" required>
                </div>

                <!-- Remember Me (Optional) -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>

                <!-- Registration Link -->
                <div class="text-center">
                    <p class="mb-0">
                        Don't have an account? 
                        <a href="register.php">Register here</a>
                    </p>
                </div>
            </form>

            <!-- Admin Login Info -->
            <hr class="my-4">
            <div class="alert alert-info mb-0">
                <strong><i class="fas fa-info-circle"></i> Default Admin Login:</strong><br>
                <small>
                    Email: <code>admin@quiz.com</code><br>
                    Password: <code>admin123</code>
                </small>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>