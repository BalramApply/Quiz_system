<?php
// Admin Dashboard
// Path: admin/dashboard.php

require_once '../config/database.php';
require_login();
require_admin();

$page_title = "Admin Dashboard";

// Get statistics
// Total Quizzes
$total_quizzes_query = "SELECT COUNT(*) as total FROM quizzes";
$total_quizzes_result = mysqli_query($conn, $total_quizzes_query);
$total_quizzes = mysqli_fetch_assoc($total_quizzes_result)['total'];

// Active Quizzes
$active_quizzes_query = "SELECT COUNT(*) as total FROM quizzes WHERE status = 'active'";
$active_quizzes_result = mysqli_query($conn, $active_quizzes_query);
$active_quizzes = mysqli_fetch_assoc($active_quizzes_result)['total'];

// Total Students
$total_students_query = "SELECT COUNT(*) as total FROM users WHERE role = 'student'";
$total_students_result = mysqli_query($conn, $total_students_query);
$total_students = mysqli_fetch_assoc($total_students_result)['total'];

// Total Attempts
$total_attempts_query = "SELECT COUNT(*) as total FROM results";
$total_attempts_result = mysqli_query($conn, $total_attempts_query);
$total_attempts = mysqli_fetch_assoc($total_attempts_result)['total'];

// Recent quiz attempts
$recent_attempts_query = "SELECT r.*, u.name as student_name, q.title as quiz_title 
                          FROM results r 
                          JOIN users u ON r.user_id = u.id 
                          JOIN quizzes q ON r.quiz_id = q.id 
                          ORDER BY r.attempt_date DESC 
                          LIMIT 10";
$recent_attempts_result = mysqli_query($conn, $recent_attempts_query);

include '../includes/header.php';
?>

<!-- Dashboard Header -->
<div class="row mb-4">
    <div class="col-lg-12">
        <h2>
            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
        </h2>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Quizzes -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Quizzes</h6>
                        <h2 class="mb-0 text-primary"><?php echo $total_quizzes; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-file-alt fa-3x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Quizzes -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Active Quizzes</h6>
                        <h2 class="mb-0 text-success"><?php echo $active_quizzes; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Students -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Students</h6>
                        <h2 class="mb-0 text-info"><?php echo $total_students; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-users fa-3x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Attempts -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Attempts</h6>
                        <h2 class="mb-0 text-warning"><?php echo $total_attempts; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-clipboard-check fa-3x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="create_quiz.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Quiz
                    </a>
                    <a href="manage_quizzes.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Manage Quizzes
                    </a>
                    <a href="view_results.php" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> View All Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Quiz Attempts -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Quiz Attempts</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($recent_attempts_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($attempt = mysqli_fetch_assoc($recent_attempts_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($attempt['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($attempt['quiz_title']); ?></td>
                                        <td><?php echo $attempt['score'] . '/' . $attempt['total_questions']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $attempt['percentage'] >= 80 ? 'success' : 
                                                    ($attempt['percentage'] >= 60 ? 'primary' : 
                                                    ($attempt['percentage'] >= 40 ? 'warning' : 'danger')); 
                                            ?>">
                                                <?php echo $attempt['percentage']; ?>%
                                            </span>
                                        </td>
                                        <td><?php echo format_date($attempt['attempt_date']); ?></td>
                                        <td>
                                            <?php if ($attempt['percentage'] >= 40): ?>
                                                <span class="badge bg-success">Passed</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> No quiz attempts yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>