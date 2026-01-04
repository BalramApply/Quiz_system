<?php
// Add Questions to Quiz
// Path: admin/add_questions.php

require_once '../config/database.php';
require_login();
require_admin();

$page_title = "Add Questions";

// Get quiz_id from URL
if (!isset($_GET['quiz_id'])) {
    $_SESSION['error_message'] = "Quiz ID is required.";
    header("Location: manage_quizzes.php");
    exit();
}

$quiz_id = intval($_GET['quiz_id']);

// Get quiz details
$quiz_query = "SELECT * FROM quizzes WHERE id = $quiz_id";
$quiz_result = mysqli_query($conn, $quiz_query);

if (mysqli_num_rows($quiz_result) === 0) {
    $_SESSION['error_message'] = "Quiz not found.";
    header("Location: manage_quizzes.php");
    exit();
}

$quiz = mysqli_fetch_assoc($quiz_result);

// Get existing questions for this quiz
$questions_query = "SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC";
$questions_result = mysqli_query($conn, $questions_query);
$question_count = mysqli_num_rows($questions_result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = clean_input($_POST['question']);
    $option_a = clean_input($_POST['option_a']);
    $option_b = clean_input($_POST['option_b']);
    $option_c = clean_input($_POST['option_c']);
    $option_d = clean_input($_POST['option_d']);
    $correct_option = clean_input($_POST['correct_option']);
    
    // Validation
    if (empty($question) || empty($option_a) || empty($option_b) || 
        empty($option_c) || empty($option_d) || empty($correct_option)) {
        $_SESSION['error_message'] = "All fields are required.";
    } else {
        $insert_query = "INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) 
                        VALUES ($quiz_id, '$question', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_option')";
        
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['success_message'] = "Question added successfully!";
            header("Location: add_questions.php?quiz_id=" . $quiz_id);
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to add question. Error: " . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<div class="row">
    <!-- Add Question Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-question-circle"></i> Add Question to: <?php echo htmlspecialchars($quiz['title']); ?>
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <!-- Question Text -->
                    <div class="mb-3">
                        <label for="question" class="form-label">
                            <i class="fas fa-question"></i> Question <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="question" name="question" 
                                  rows="3" placeholder="Enter your question" required></textarea>
                    </div>

                    <!-- Option A -->
                    <div class="mb-3">
                        <label for="option_a" class="form-label">
                            <i class="fas fa-check-circle"></i> Option A <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="option_a" name="option_a" 
                               placeholder="Enter option A" required>
                    </div>

                    <!-- Option B -->
                    <div class="mb-3">
                        <label for="option_b" class="form-label">
                            <i class="fas fa-check-circle"></i> Option B <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="option_b" name="option_b" 
                               placeholder="Enter option B" required>
                    </div>

                    <!-- Option C -->
                    <div class="mb-3">
                        <label for="option_c" class="form-label">
                            <i class="fas fa-check-circle"></i> Option C <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="option_c" name="option_c" 
                               placeholder="Enter option C" required>
                    </div>

                    <!-- Option D -->
                    <div class="mb-3">
                        <label for="option_d" class="form-label">
                            <i class="fas fa-check-circle"></i> Option D <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="option_d" name="option_d" 
                               placeholder="Enter option D" required>
                    </div>

                    <!-- Correct Option -->
                    <div class="mb-3">
                        <label for="correct_option" class="form-label">
                            <i class="fas fa-star"></i> Correct Answer <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="correct_option" name="correct_option" required>
                            <option value="">-- Select Correct Option --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                        <a href="manage_quizzes.php" class="btn btn-success">
                            <i class="fas fa-check"></i> Finish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Existing Questions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Questions Added (<?php echo $question_count; ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if ($question_count > 0): ?>
                    <div class="list-group">
                        <?php 
                        $counter = 1;
                        while ($q = mysqli_fetch_assoc($questions_result)): 
                        ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Q<?php echo $counter; ?>:</strong>
                                        <p class="mb-1"><?php echo htmlspecialchars(substr($q['question'], 0, 50)) . '...'; ?></p>
                                        <small class="text-success">
                                            <i class="fas fa-check"></i> Correct: <?php echo $q['correct_option']; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        $counter++;
                        endwhile; 
                        ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> No questions added yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quiz Info -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-info-circle text-primary"></i> Quiz Info</h6>
                <p class="mb-1"><strong>Time Limit:</strong> <?php echo $quiz['time_limit']; ?> minutes</p>
                <p class="mb-0"><strong>Status:</strong> 
                    <span class="badge bg-<?php echo $quiz['status'] === 'active' ? 'success' : 'secondary'; ?>">
                        <?php echo ucfirst($quiz['status']); ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>