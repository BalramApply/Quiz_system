<?php
// Result History Page
// Path: student/result_history.php

require_once '../config/database.php';
require_login();
require_student();

$page_title = "Result History";
$student_id = $_SESSION['user_id'];

// Fetch all results for this student
$results_query = "SELECT r.*, q.title as quiz_title 
                  FROM results r 
                  JOIN quizzes q ON r.quiz_id = q.id 
                  WHERE r.user_id = $student_id 
                  ORDER BY r.attempt_date DESC";
$results_result = mysqli_query($conn, $results_query);

// Calculate statistics
$total_attempts = mysqli_num_rows($results_result);
$total_passed = 0;
$total_failed = 0;
$total_score = 0;

$results_array = [];
while ($row = mysqli_fetch_assoc($results_result)) {
    $results_array[] = $row;
    if ($row['percentage'] >= 40) {
        $total_passed++;
    } else {
        $total_failed++;
    }
    $total_score += $row['percentage'];
}

$avg_percentage = $total_attempts > 0 ? round($total_score / $total_attempts, 2) : 0;

include '../includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-lg-12">
        <h2>
            <i class="fas fa-history"></i> My Result History
        </h2>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Attempts</h6>
                        <h2 class="mb-0 text-primary"><?php echo $total_attempts; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-clipboard-list fa-3x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Passed</h6>
                        <h2 class="mb-0 text-success"><?php echo $total_passed; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Failed</h6>
                        <h2 class="mb-0 text-danger"><?php echo $total_failed; ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-times-circle fa-3x text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Average Score</h6>
                        <h2 class="mb-0 text-info"><?php echo $avg_percentage; ?>%</h2>
                    </div>
                    <div>
                        <i class="fas fa-chart-line fa-3x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> All Quiz Attempts
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($results_array) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Quiz Title</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $serial = 1;
                                foreach ($results_array as $result): 
                                ?>
                                    <tr>
                                        <td><?php echo $serial; ?></td>
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
                                            <?php 
                                            if ($result['percentage'] >= 80) {
                                                echo '<span class="badge bg-success">A+</span>';
                                            } elseif ($result['percentage'] >= 70) {
                                                echo '<span class="badge bg-success">A</span>';
                                            } elseif ($result['percentage'] >= 60) {
                                                echo '<span class="badge bg-primary">B</span>';
                                            } elseif ($result['percentage'] >= 50) {
                                                echo '<span class="badge bg-info">C</span>';
                                            } elseif ($result['percentage'] >= 40) {
                                                echo '<span class="badge bg-warning">D</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">F</span>';
                                            }
                                            ?>
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
                                        <td>
                                            <a href="view_result.php?result_id=<?php echo $result['id']; ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                $serial++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> You haven't attempted any quiz yet.
                        <br><br>
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-play"></i> Start Your First Quiz
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart Info -->
<?php if (count($results_array) > 0): ?>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Performance Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h6>Pass Rate</h6>
                        <h3 class="text-success">
                            <?php echo $total_attempts > 0 ? round(($total_passed / $total_attempts) * 100, 2) : 0; ?>%
                        </h3>
                    </div>
                    <div class="col-md-4">
                        <h6>Fail Rate</h6>
                        <h3 class="text-danger">
                            <?php echo $total_attempts > 0 ? round(($total_failed / $total_attempts) * 100, 2) : 0; ?>%
                        </h3>
                    </div>
                    <div class="col-md-4">
                        <h6>Overall Average</h6>
                        <h3 class="text-primary"><?php echo $avg_percentage; ?>%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
include '../includes/footer.php';
?>