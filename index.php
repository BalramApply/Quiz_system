<?php
// Home Page / Landing Page
// Path: index.php

require_once 'config/database.php';

// If user is already logged in, redirect to their dashboard
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit();
}

$page_title = "Home";
include 'includes/header.php';

// Fetch active quizzes for display
$quiz_query = "SELECT id, title, description, time_limit, created_at 
               FROM quizzes 
               WHERE status = 'active' 
               ORDER BY created_at DESC";
$quiz_result = mysqli_query($conn, $quiz_query);
?>

<!-- Hero Section -->
<div class="row mb-5">
    <div class="col-lg-12">
        <div class="card bg-primary text-white">
            <div class="card-body text-center py-5">
                <h1 class="display-4 mb-3">
                    <i class="fas fa-graduation-cap"></i> Welcome to Quiz System
                </h1>
                <p class="lead mb-4">
                    Test your knowledge with our interactive quizzes. Track your progress and improve your skills!
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="login.php" class="btn btn-light btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-clock fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">Timed Quizzes</h5>
                <p class="card-text">
                    Challenge yourself with time-bound assessments that auto-submit when time expires.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-chart-line fa-3x text-success"></i>
                </div>
                <h5 class="card-title">Instant Results</h5>
                <p class="card-text">
                    Get immediate feedback on your performance with detailed score breakdowns.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-history fa-3x text-info"></i>
                </div>
                <h5 class="card-title">Track Progress</h5>
                <p class="card-text">
                    View your complete quiz history and monitor your improvement over time.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Available Quizzes Section -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-list-alt"></i> Available Quizzes
                </h4>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($quiz_result) > 0): ?>
                    <div class="row">
                        <?php while ($quiz = mysqli_fetch_assoc($quiz_result)): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card quiz-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">
                                            <i class="fas fa-file-alt"></i>
                                            <?php echo htmlspecialchars($quiz['title']); ?>
                                        </h5>
                                        <p class="card-text">
                                            <?php echo htmlspecialchars($quiz['description']); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i>
                                                <?php echo $quiz['time_limit']; ?> minutes
                                            </small>
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="login.php" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-lock"></i> Login to Attempt
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No active quizzes available at the moment.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="row mt-5">
    <div class="col-lg-12">
        <div class="card bg-light">
            <div class="card-body text-center py-4">
                <h3 class="mb-3">Ready to Get Started?</h3>
                <p class="mb-4">Join thousands of students improving their knowledge through our quiz platform.</p>
                <a href="register.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> Start Your Journey
                </a>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>