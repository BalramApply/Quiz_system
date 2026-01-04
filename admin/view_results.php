<?php
// View All Results Page
// Path: admin/view_results.php

require_once '../config/database.php';
require_login();
require_admin();

$page_title = "View Results";

// Fetch all results with filters
$where_clause = "1=1";

// Filter by quiz
if (isset($_GET['quiz_id']) && !empty($_GET['quiz_id'])) {
    $quiz_id = intval($_GET['quiz_id']);
    $where_clause .= " AND r.quiz_id = $quiz_id";
}

// Filter by student
if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);
    $where_clause .= " AND r.user_id = $student_id";
}

// Fetch results
$results_query = "SELECT r.*, u.name as student_name, u.email as student_email, q.title as quiz_title 
                  FROM results r 
                  JOIN users u ON r.user_id = u.id 
                  JOIN quizzes q ON r.quiz_id = q.id 
                  WHERE $where_clause
                  ORDER BY r.attempt_date DESC";
$results_result = mysqli_query($conn, $results_query);

// Get all quizzes for filter dropdown
$quizzes_query = "SELECT id, title FROM quizzes ORDER BY title ASC";
$quizzes_result = mysqli_query($conn, $quizzes_query);

// Get all students for filter dropdown
$students_query = "SELECT id, name FROM users WHERE role = 'student' ORDER BY name ASC";
$students_result = mysqli_query($conn, $students_query);

include '../includes/header.php';
?>

<div class="row mb-3">
    <div class="col-lg-12">
        <h2><i class="fas fa-chart-bar"></i> Quiz Results</h2>
    </div>
</div>

<!-- Filters -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter"></i> Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-4">
                        <label for="quiz_id" class="form-label">Filter by Quiz</label>
                        <select class="form-select" id="quiz_id" name="quiz_id">
                            <option value="">All Quizzes</option>
                            <?php while ($q = mysqli_fetch_assoc($quizzes_result)): ?>
                                <option value="<?php echo $q['id']; ?>" 
                                    <?php echo (isset($_GET['quiz_id']) && $_GET['quiz_id'] == $q['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($q['title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="student_id" class="form-label">Filter by Student</label>
                        <select class="form-select" id="student_id" name="student_id">
                            <option value="">All Students</option>
                            <?php while ($s = mysqli_fetch_assoc($students_result)): ?>
                                <option value="<?php echo $s['id']; ?>"
                                    <?php echo (isset($_GET['student_id']) && $_GET['student_id'] == $s['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filter
                        </button>
                        <a href="view_results.php" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <?php if (mysqli_num_rows($results_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($result = mysqli_fetch_assoc($results_result)): ?>
                                    <tr>
                                        <td><?php echo $result['id']; ?></td>
                                        <td><?php echo htmlspecialchars($result['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($result['student_email']); ?></td>
                                        <td><?php echo htmlspecialchars($result['quiz_title']); ?></td>
                                        <td>
                                            <strong><?php echo $result['score']; ?></strong> / <?php echo $result['total_questions']; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $result['percentage'] >= 80 ? 'success' : 
                                                    ($result['percentage'] >= 60 ? 'primary' : 
                                                    ($result['percentage'] >= 40 ? 'warning' : 'danger')); 
                                            ?>">
                                                <?php echo $result['percentage']; ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($result['percentage'] >= 40): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Passed
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> Failed
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo format_date($result['attempt_date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No results found.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>