<?php
// View Result Page
// Path: student/view_result.php

require_once '../config/database.php';
require_login();
require_student();

$page_title = "Quiz Result";

// Get result ID
if (!isset($_GET['result_id'])) {
    $_SESSION['error_message'] = "Result ID is required.";
    header("Location: dashboard.php");
    exit();
}

$result_id = intval($_GET['result_id']);
$student_id = $_SESSION['user_id'];

// Fetch result details
$result_query = "SELECT r.*, q.title as quiz_title, q.description as quiz_description 
                 FROM results r 
                 JOIN quizzes q ON r.quiz_id = q.id 
                 WHERE r.id = $result_id AND r.user_id = $student_id";
$result_result = mysqli_query($conn, $result_query);

if (mysqli_num_rows($result_result) === 0) {
    $_SESSION['error_message'] = "Result not found or access denied.";
    header("Location: dashboard.php");
    exit();
}

$result = mysqli_fetch_assoc($result_result);

// Fetch detailed answers
$answers_query = "SELECT sa.*, q.question, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option
                  FROM student_answers sa
                  JOIN questions q ON sa.question_id = q.id
                  WHERE sa.result_id = $result_id
                  ORDER BY q.id ASC";
$answers_result = mysqli_query($conn, $answers_query);

// Determine pass/fail
$passed = $result['percentage'] >= 40;

include '../includes/header.php';
?>

<!-- Result Header -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card bg-<?php echo $passed ? 'success' : 'danger'; ?> text-white">
            <div class="card-body text-center py-4">
                <h2 class="mb-3">
                    <i class="fas fa-<?php echo $passed ? 'check-circle' : 'times-circle'; ?>"></i>
                    Quiz <?php echo $passed ? 'Passed' : 'Failed'; ?>!
                </h2>
                <h4><?php echo htmlspecialchars($result['quiz_title']); ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Score Summary -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie"></i> Score Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="result-box">
                            <h6 class="text-muted">Your Score</h6>
                            <div class="score-display">
                                <?php echo $result['score']; ?>
                            </div>
                            <p class="text-muted">out of <?php echo $result['total_questions']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="result-box">
                            <h6 class="text-muted">Percentage</h6>
                            <div class="score-display">
                                <?php echo $result['percentage']; ?>%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="result-box">
                            <h6 class="text-muted">Correct Answers</h6>
                            <div class="score-display text-success">
                                <?php echo $result['score']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="result-box">
                            <h6 class="text-muted">Wrong Answers</h6>
                            <div class="score-display text-danger">
                                <?php echo $result['total_questions'] - $result['score']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <span class="result-badge badge bg-<?php 
                        echo $result['percentage'] >= 80 ? 'success' : 
                            ($result['percentage'] >= 60 ? 'primary' : 
                            ($result['percentage'] >= 40 ? 'warning' : 'danger')); 
                    ?>">
                        <?php 
                        if ($result['percentage'] >= 80) {
                            echo "Excellent!";
                        } elseif ($result['percentage'] >= 60) {
                            echo "Good Job!";
                        } elseif ($result['percentage'] >= 40) {
                            echo "Passed";
                        } else {
                            echo "Need Improvement";
                        }
                        ?>
                    </span>
                    <br>
                    <small class="text-muted">
                        Attempted on: <?php echo format_date($result['attempt_date']); ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Answers -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list-alt"></i> Detailed Answer Review
                </h5>
            </div>
            <div class="card-body">
                <?php 
                $question_num = 1;
                while ($answer = mysqli_fetch_assoc($answers_result)): 
                ?>
                    <div class="card mb-3 border-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?>">
                        <div class="card-header bg-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?> text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>Question <?php echo $question_num; ?></strong>
                                </span>
                                <span>
                                    <i class="fas fa-<?php echo $answer['is_correct'] ? 'check' : 'times'; ?>-circle"></i>
                                    <?php echo $answer['is_correct'] ? 'Correct' : 'Wrong'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><?php echo htmlspecialchars($answer['question']); ?></h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Options:</strong></p>
                                    <ul class="list-unstyled">
                                        <li class="<?php echo $answer['correct_option'] === 'A' ? 'text-success fw-bold' : ''; ?>">
                                            A. <?php echo htmlspecialchars($answer['option_a']); ?>
                                            <?php if ($answer['correct_option'] === 'A'): ?>
                                                <i class="fas fa-check text-success"></i>
                                            <?php endif; ?>
                                        </li>
                                        <li class="<?php echo $answer['correct_option'] === 'B' ? 'text-success fw-bold' : ''; ?>">
                                            B. <?php echo htmlspecialchars($answer['option_b']); ?>
                                            <?php if ($answer['correct_option'] === 'B'): ?>
                                                <i class="fas fa-check text-success"></i>
                                            <?php endif; ?>
                                        </li>
                                        <li class="<?php echo $answer['correct_option'] === 'C' ? 'text-success fw-bold' : ''; ?>">
                                            C. <?php echo htmlspecialchars($answer['option_c']); ?>
                                            <?php if ($answer['correct_option'] === 'C'): ?>
                                                <i class="fas fa-check text-success"></i>
                                            <?php endif; ?>
                                        </li>
                                        <li class="<?php echo $answer['correct_option'] === 'D' ? 'text-success fw-bold' : ''; ?>">
                                            D. <?php echo htmlspecialchars($answer['option_d']); ?>
                                            <?php if ($answer['correct_option'] === 'D'): ?>
                                                <i class="fas fa-check text-success"></i>
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Your Answer:</strong> 
                                        <span class="badge bg-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?>">
                                            <?php echo $answer['selected_option']; ?>
                                        </span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Correct Answer:</strong> 
                                        <span class="badge bg-success"><?php echo $answer['correct_option']; ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                $question_num++;
                endwhile; 
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-lg-12 text-center">
        <a href="dashboard.php" class="btn btn-primary btn-lg">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
        <a href="result_history.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-history"></i> View All Results
        </a>
        <button onclick="window.print()" class="btn btn-info btn-lg">
            <i class="fas fa-print"></i> Print Result
        </button>
    </div>
</div>

<?php
include '../includes/footer.php';
?>