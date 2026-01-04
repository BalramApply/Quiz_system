<?php
// Submit Quiz Processing Script
// Path: student/submit_quiz.php

require_once '../config/database.php';
require_login();
require_student();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

$quiz_id = intval($_POST['quiz_id']);
$student_id = $_SESSION['user_id'];
$answers = isset($_POST['answer']) ? $_POST['answer'] : [];

// Validate quiz exists
$quiz_query = "SELECT * FROM quizzes WHERE id = $quiz_id";
$quiz_result = mysqli_query($conn, $quiz_query);

if (mysqli_num_rows($quiz_result) === 0) {
    $_SESSION['error_message'] = "Invalid quiz.";
    header("Location: dashboard.php");
    exit();
}

// Get all questions for this quiz
$questions_query = "SELECT * FROM questions WHERE quiz_id = $quiz_id";
$questions_result = mysqli_query($conn, $questions_query);
$total_questions = mysqli_num_rows($questions_result);

if ($total_questions === 0) {
    $_SESSION['error_message'] = "This quiz has no questions.";
    header("Location: dashboard.php");
    exit();
}

// Calculate score
$score = 0;
$question_details = [];

// Reset pointer
mysqli_data_seek($questions_result, 0);

while ($question = mysqli_fetch_assoc($questions_result)) {
    $question_id = $question['id'];
    $correct_answer = $question['correct_option'];
    $student_answer = isset($answers[$question_id]) ? $answers[$question_id] : null;
    
    $is_correct = false;
    if ($student_answer === $correct_answer) {
        $score++;
        $is_correct = true;
    }
    
    // Store question details for later
    $question_details[] = [
        'question_id' => $question_id,
        'student_answer' => $student_answer,
        'is_correct' => $is_correct
    ];
}

// Calculate percentage
$percentage = calculate_percentage($score, $total_questions);

// Insert result into database
$insert_result = "INSERT INTO results (user_id, quiz_id, score, total_questions, percentage) 
                  VALUES ($student_id, $quiz_id, $score, $total_questions, $percentage)";

if (mysqli_query($conn, $insert_result)) {
    $result_id = mysqli_insert_id($conn);
    
    // Insert individual answers
    foreach ($question_details as $detail) {
        if ($detail['student_answer']) {
            $q_id = $detail['question_id'];
            $s_answer = $detail['student_answer'];
            $is_correct = $detail['is_correct'] ? 1 : 0;
            
            $insert_answer = "INSERT INTO student_answers (result_id, question_id, selected_option, is_correct) 
                             VALUES ($result_id, $q_id, '$s_answer', $is_correct)";
            mysqli_query($conn, $insert_answer);
        }
    }
    
    // Redirect to result page
    $_SESSION['success_message'] = "Quiz submitted successfully!";
    header("Location: view_result.php?result_id=" . $result_id);
    exit();
} else {
    $_SESSION['error_message'] = "Failed to submit quiz. Error: " . mysqli_error($conn);
    header("Location: dashboard.php");
    exit();
}
?>