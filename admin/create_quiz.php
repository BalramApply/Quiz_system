<?php
// Create Quiz Page
// Path: admin/create_quiz.php

require_once '../config/database.php';
require_login();
require_admin();

$page_title = "Create Quiz";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $time_limit = intval($_POST['time_limit']);
    $status = clean_input($_POST['status']);
    $created_by = $_SESSION['user_id'];
    
    // Validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Quiz title is required.";
    }
    
    if ($time_limit <= 0) {
        $errors[] = "Time limit must be greater than 0.";
    }
    
    if (empty($errors)) {
        $insert_query = "INSERT INTO quizzes (title, description, time_limit, status, created_by) 
                        VALUES ('$title', '$description', $time_limit, '$status', $created_by)";
        
        if (mysqli_query($conn, $insert_query)) {
            $quiz_id = mysqli_insert_id($conn);
            $_SESSION['success_message'] = "Quiz created successfully! Now add questions to it.";
            header("Location: add_questions.php?quiz_id=" . $quiz_id);
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to create quiz. Error: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = implode(" ", $errors);
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle"></i> Create New Quiz
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <!-- Quiz Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading"></i> Quiz Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="Enter quiz title" required>
                    </div>

                    <!-- Quiz Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i> Description
                        </label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Enter quiz description (optional)"></textarea>
                    </div>

                    <!-- Time Limit -->
                    <div class="mb-3">
                        <label for="time_limit" class="form-label">
                            <i class="fas fa-clock"></i> Time Limit (Minutes) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" 
                               min="1" max="180" value="20" required>
                        <small class="text-muted">Enter time limit in minutes (1-180)</small>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">
                            <i class="fas fa-toggle-on"></i> Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <small class="text-muted">Only active quizzes are visible to students</small>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Create Quiz
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-info-circle text-info"></i> Note
                </h6>
                <p class="card-text mb-0">
                    After creating the quiz, you will be redirected to add questions. 
                    You can add multiple questions with different options and mark the correct answer.
                </p>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>