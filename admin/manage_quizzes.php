<?php
// Manage Quizzes Page
// Path: admin/manage_quizzes.php

require_once '../config/database.php';
require_login();
require_admin();

$page_title = "Manage Quizzes";

// Handle delete action
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $delete_query = "DELETE FROM quizzes WHERE id = $delete_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['success_message'] = "Quiz deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete quiz.";
    }
    header("Location: manage_quizzes.php");
    exit();
}

// Handle status toggle
if (isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    $toggle_query = "UPDATE quizzes SET status = IF(status = 'active', 'inactive', 'active') WHERE id = $toggle_id";
    
    if (mysqli_query($conn, $toggle_query)) {
        $_SESSION['success_message'] = "Quiz status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update quiz status.";
    }
    header("Location: manage_quizzes.php");
    exit();
}

// Fetch all quizzes with question count
$quizzes_query = "SELECT q.*, COUNT(qs.id) as question_count, u.name as creator_name
                  FROM quizzes q
                  LEFT JOIN questions qs ON q.id = qs.quiz_id
                  LEFT JOIN users u ON q.created_by = u.id
                  GROUP BY q.id
                  ORDER BY q.created_at DESC";
$quizzes_result = mysqli_query($conn, $quizzes_query);

include '../includes/header.php';
?>

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-list"></i> Manage Quizzes</h2>
            <a href="create_quiz.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Quiz
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <?php if (mysqli_num_rows($quizzes_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Questions</th>
                                    <th>Time Limit</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($quiz = mysqli_fetch_assoc($quizzes_result)): ?>
                                    <tr>
                                        <td><?php echo $quiz['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($quiz['title']); ?></strong>
                                            <?php if (!empty($quiz['description'])): ?>
                                                <br><small class="text-muted">
                                                    <?php echo htmlspecialchars(substr($quiz['description'], 0, 50)); ?>
                                                    <?php echo strlen($quiz['description']) > 50 ? '...' : ''; ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $quiz['question_count']; ?> Questions
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-clock"></i> <?php echo $quiz['time_limit']; ?> min
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $quiz['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($quiz['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($quiz['creator_name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($quiz['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="add_questions.php?quiz_id=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-primary" title="Add Questions">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-warning" title="Edit Quiz">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?toggle=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-<?php echo $quiz['status'] === 'active' ? 'secondary' : 'success'; ?>" 
                                                   title="Toggle Status"
                                                   onclick="return confirm('Are you sure you want to change the status?')">
                                                    <i class="fas fa-toggle-<?php echo $quiz['status'] === 'active' ? 'off' : 'on'; ?>"></i>
                                                </a>
                                                <a href="?delete=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-danger" title="Delete Quiz"
                                                   onclick="return confirm('Are you sure? This will delete all questions and results!')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No quizzes created yet.
                        <br>
                        <a href="create_quiz.php" class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Create Your First Quiz
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>