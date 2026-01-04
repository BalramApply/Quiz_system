<?php
// Student Dashboard
// Path: student/dashboard.php

require_once '../config/database.php';
require_login();
require_student();

$page_title = "Student Dashboard";
$student_id = $_SESSION['user_id'];

// Get student statistics
// Total Attempts
$total_attempts_query = "SELECT COUNT(*) as total FROM results WHERE user_id = $student_id";
$total_attempts_result = mysqli_query($conn, $total_attempts_query);
$total_attempts = mysqli_fetch_assoc($total_attempts_result)['total'];

// Average Score
$avg_score_query = "SELECT AVG(percentage) as avg_score FROM results WHERE user_id = $student_id";
$avg_score_result = mysqli_query($conn, $avg_score_query);
$avg_score = mysqli_fetch_assoc($avg_score_result)['avg_score'];
$avg_score = $avg_score ? round($avg_score, 2) : 0;

// Passed Quizzes
$passed_query = "SELECT COUNT(*) as total FROM results WHERE user_id = $student_id AND percentage >= 40";
$passed_result = mysqli_query($conn, $passed_query);
$passed_quizzes = mysqli_fetch_assoc($passed_result)['total'];

// Available Active Quizzes
$available_query = "SELECT COUNT(*) as total FROM quizzes WHERE status = 'active'";
$available_result = mysqli_query($conn, $available_query);
$available_quizzes = mysqli_fetch_assoc($available_result)['total'];

// Fetch active quizzes
$quizzes_query = "SELECT q.*, COUNT(qs.id) as question_count
                  FROM quizzes q
                  LEFT JOIN questions qs ON q.id = qs.quiz_id
                  WHERE q.status = 'active'
                  GROUP BY q.id
                  ORDER BY q.created_at DESC";
$quizzes_result = mysqli_query($conn, $quizzes_query);

// Recent attempts
$recent_query = "SELECT r.*, q.title as quiz_title 
                 FROM results r 
                 JOIN quizzes q ON r.quiz_id = q.id 
                 WHERE r.user_id = $student_id 
                 ORDER BY r.attempt_date DESC 
                 LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);

include '../includes/header.php';
?>

<!-- Dashboard Header -->
<div class="row mb-4">
    <div class="col-lg-12">
        <h2>
            <i class="fas fa-tachometer-alt"></i> Student Dashboard
        </h2>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Attempts -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Attempts</h6>
                        <h2 class="mb-0 text-primary"><?php echo $total_attempts; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-clipboard-check fa-3x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Score -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Average Score</h6>
                        <h2 class="mb-0 text-success"><?php echo $avg_score; ?>%</h2>
                    </div>
                    <div>
                        <i class="fas fa-chart-line fa-3x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Passed Quizzes -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Passed Quizzes</h6>
                        <h2 class="mb-0 text-info"><?php echo $passed_quizzes; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-trophy fa-3x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Quizzes -->
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Available Quizzes</h6>
                        <h2 class="mb-0 text-warning"><?php echo $available_quizzes; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-file-alt fa-3x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Quizzes -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list-alt"></i> Available Quizzes
                </h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($quizzes_result) > 0): ?>
                    <div class="row">
                        <?php while ($quiz = mysqli_fetch_assoc($quizzes_result)): ?>
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
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-question-circle"></i> 
                                                <?php echo $quiz['question_count']; ?> Questions
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> 
                                                <?php echo $quiz['time_limit']; ?> Minutes
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="attempt_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" 
                                           class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-play"></i> Start Quiz
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

<!-- Recent Attempts -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history"></i> Recent Attempts
                </h5>
                <a href="result_history.php" class="btn btn-sm btn-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($recent_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($attempt = mysqli_fetch_assoc($recent_result)): ?>
                                    <tr>
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
                                        <td>
                                            <?php if ($attempt['percentage'] >= 40): ?>
                                                <span class="badge bg-success">Passed</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo format_date($attempt['attempt_date']); ?></td>
                                        <td>
                                            <a href="view_result.php?result_id=<?php echo $attempt['id']; ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center mb-0">
                        <i class="fas fa-info-circle"></i> You haven't attempted any quiz yet. Start your first quiz now!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>