<?php
// Edit Quiz Page
// Path: admin/edit_quiz.php

require_once '../config/database.php';
require_login();
require_admin();

$page_title = "Edit Quiz";

// Get quiz ID
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Quiz ID is required.";
    header("Location: manage_quizzes.php");
    exit();
}

$quiz_id = intval($_GET['id']);

// Fetch quiz details
$quiz_query = "SELECT * FROM quizzes WHERE id = $quiz_id";
$quiz_result = mysqli_query($conn, $quiz_query);

if (mysqli_num_rows($quiz_result) === 0) {
    $_SESSION['error_message'] = "Quiz not found.";
    header("Location: manage_quizzes.php");
    exit();
}

$quiz = mysqli_fetch_assoc($quiz_result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $time_limit = intval($_POST['time_limit']);
    $status = clean_input($_POST['status']);
    
    // Validation
    if (empty($title) || $time_limit <= 0) {
        $_SESSION['error_message'] = "Title and valid time limit are required.";
    } else {
        $update_query = "UPDATE quizzes 
                        SET title = '$title', 
                            description = '$description', 
                            time_limit = $time_limit, 
                            status = '$status' 
                        WHERE id = $quiz_id";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success_message'] = "Quiz updated successfully!";
            header("Location: manage_quizzes.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to update quiz. Error: " . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-edit"></i> Edit Quiz
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
                               value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
                    </div>

                    <!-- Quiz Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i> Description
                        </label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                    </div>

                    <!-- Time Limit -->
                    <div class="mb-3">
                        <label for="time_limit" class="form-label">
                            <i class="fas fa-clock"></i> Time Limit (Minutes) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" 
                               min="1" max="180" value="<?php echo $quiz['time_limit']; ?>" required>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">
                            <i class="fas fa-toggle-on"></i> Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?php echo $quiz['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $quiz['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Quiz
                        </button>
                        <a href="manage_quizzes.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>