-- Student Assessment / Quiz System Database
-- Save this as: database.sql

CREATE DATABASE IF NOT EXISTS quiz_system;
USE quiz_system;

-- Users table (Admin & Students)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quizzes table
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    time_limit INT NOT NULL COMMENT 'Time in minutes',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Questions table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Results table
CREATE TABLE results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    attempt_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Student Answers table (to track individual answers)
CREATE TABLE student_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    result_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Insert default admin account
-- Password: admin123 (hashed)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@quiz.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample data for testing
INSERT INTO quizzes (title, description, time_limit, status, created_by) VALUES 
('PHP Basics Quiz', 'Test your knowledge of PHP fundamentals', 20, 'active', 1),
('JavaScript Essentials', 'Basic JavaScript concepts and syntax', 15, 'active', 1);

INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES 
(1, 'What does PHP stand for?', 'Personal Home Page', 'Hypertext Preprocessor', 'Private Home Page', 'Public Hypertext Processor', 'B'),
(1, 'Which symbol is used to access a property of an object in PHP?', '.', '->', '::', '&', 'B'),
(1, 'What is the correct way to end a PHP statement?', '.', ';', ':', ',', 'B'),
(1, 'Which function is used to connect to MySQL database in PHP?', 'mysql_connect()', 'mysqli_connect()', 'db_connect()', 'connect_mysql()', 'B'),
(1, 'What is the default file extension for PHP files?', '.html', '.php', '.xml', '.txt', 'B');

INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES 
(2, 'Which keyword is used to declare a variable in JavaScript?', 'var', 'int', 'string', 'variable', 'A'),
(2, 'What is the correct syntax for a JavaScript function?', 'function myFunction[]', 'function:myFunction()', 'function myFunction()', 'def myFunction()', 'C'),
(2, 'How do you write a comment in JavaScript?', '<!-- Comment -->', '/* Comment */', '// Comment', 'Both B and C', 'D');