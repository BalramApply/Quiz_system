<?php
// Attempt Quiz Page
// Path: student/attempt_quiz.php

require_once '../config/database.php';
require_login();
require_student();

$page_title = "Attempt Quiz";
$include_timer = true; // Include timer JavaScript

// Get quiz ID
if (!isset($_GET['quiz_id'])) {
    $_SESSION['error_message'] = "Quiz ID is required.";
    header("Location: dashboard.php");
    exit();
}

$quiz_id = intval($_GET['quiz_id']);
$student_id = $_SESSION['user_id'];

// Fetch quiz details
$quiz_query = "SELECT * FROM quizzes WHERE id = $quiz_id AND status = 'active'";
$quiz_result = mysqli_query($conn, $quiz_query);

if (mysqli_num_rows($quiz_result) === 0) {
    $_SESSION['error_message'] = "Quiz not found or not available.";
    header("Location: dashboard.php");
    exit();
}

$quiz = mysqli_fetch_assoc($quiz_result);

// Fetch all questions for this quiz
$questions_query = "SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC";
$questions_result = mysqli_query($conn, $questions_query);
$total_questions = mysqli_num_rows($questions_result);

if ($total_questions === 0) {
    $_SESSION['error_message'] = "This quiz has no questions yet.";
    header("Location: dashboard.php");
    exit();
}

include '../includes/header.php';
?>

<!-- Timer Box (Fixed Position) -->
<div id="timerBox" class="timer-box">
    <i class="fas fa-clock"></i>
    Time Remaining: <span id="timer"><?php echo $quiz['time_limit']; ?>:00</span>
</div>

<!-- Quiz Header -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h3 class="mb-2">
                    <i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($quiz['title']); ?>
                </h3>
                <p class="mb-0"><?php echo htmlspecialchars($quiz['description']); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Quiz Instructions -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Instructions
                </h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Total Questions: <strong><?php echo $total_questions; ?></strong></li>
                    <li>Time Limit: <strong><?php echo $quiz['time_limit']; ?> minutes</strong></li>
                    <li>Each question has 4 options (A, B, C, D)</li>
                    <li>Select one answer for each question</li>
                    <li>The quiz will auto-submit when time expires</li>
                    <li>Click "Submit Quiz" when you're done</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Quiz Form -->
<form id="quizForm" method="POST" action="submit_quiz.php">
    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
    <input type="hidden" name="time_limit" value="<?php echo $quiz['time_limit']; ?>">
    
    <?php 
    $question_number = 1;
    while ($question = mysqli_fetch_assoc($questions_result)): 
    ?>
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card question-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Question <?php echo $question_number; ?> of <?php echo $total_questions; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3"><?php echo htmlspecialchars($question['question']); ?></h6>
                        
                        <div class="options">
                            <!-- Option A -->
                            <label class="option-label">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" 
                                       value="A" required>
                                <span class="option-text">
                                    <strong>A.</strong> <?php echo htmlspecialchars($question['option_a']); ?>
                                </span>
                            </label>
                            
                            <!-- Option B -->
                            <label class="option-label">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" 
                                       value="B" required>
                                <span class="option-text">
                                    <strong>B.</strong> <?php echo htmlspecialchars($question['option_b']); ?>
                                </span>
                            </label>
                            
                            <!-- Option C -->
                            <label class="option-label">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" 
                                       value="C" required>
                                <span class="option-text">
                                    <strong>C.</strong> <?php echo htmlspecialchars($question['option_c']); ?>
                                </span>
                            </label>
                            
                            <!-- Option D -->
                            <label class="option-label">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" 
                                       value="D" required>
                                <span class="option-text">
                                    <strong>D.</strong> <?php echo htmlspecialchars($question['option_d']); ?>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
    $question_number++;
    endwhile; 
    ?>
    
    <!-- Submit Button -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Submit Quiz
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary btn-lg ms-2" 
                       onclick="return confirm('Are you sure you want to cancel? Your progress will be lost.')">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- JavaScript for Timer -->
<script>
// Set the time limit in seconds
let timeLimit = <?php echo $quiz['time_limit'] * 60; ?>;
let timeRemaining = timeLimit;

// Timer function
function startTimer() {
    const timerElement = document.getElementById('timer');
    const timerBox = document.getElementById('timerBox');
    
    const timerInterval = setInterval(function() {
        let minutes = Math.floor(timeRemaining / 60);
        let seconds = timeRemaining % 60;
        
        // Add leading zero if needed
        seconds = seconds < 10 ? '0' + seconds : seconds;
        
        // Update timer display
        timerElement.textContent = minutes + ':' + seconds;
        
        // Warning when 2 minutes remaining
        if (timeRemaining <= 120) {
            timerBox.classList.add('timer-warning');
        }
        
        // Time expired - auto submit
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! Your quiz will be submitted automatically.');
            document.getElementById('quizForm').submit();
        }
        
        timeRemaining--;
    }, 1000);
}

// Start timer when page loads
window.onload = function() {
    startTimer();
};

// Prevent form resubmission
document.getElementById('quizForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
});

// Warn before leaving page
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>

<?php
include '../includes/footer.php';
?>