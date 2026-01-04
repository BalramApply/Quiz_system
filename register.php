<?php
// Student Registration Page
// Path: register.php

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

$page_title = "Register";
include 'includes/header.php';
?>

<div class="auth-container">
    <div class="card auth-card">
        <div class="card-header text-center">
            <h3 class="mb-0">
                <i class="fas fa-user-plus"></i> Student Registration
            </h3>
        </div>
        <div class="card-body p-4">
            <form action="process_register.php" method="POST" id="registerForm">
                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Enter your full name" required>
                </div>

                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your email" required>
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter password (min 6 characters)" required minlength="6">
                    <small class="text-muted">Password must be at least 6 characters long</small>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <input type="password" class="form-control" id="confirm_password" 
                           name="confirm_password" placeholder="Re-enter password" required>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="mb-0">
                        Already have an account? 
                        <a href="login.php">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for password validation -->
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
});
</script>

<?php
include 'includes/footer.php';
?>