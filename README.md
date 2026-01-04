# 🎓 Student Assessment Quiz System

A comprehensive web-based quiz application built with **Core PHP** and **MySQL**, designed specifically for educational institutions. Features include real-time timer, automatic submission, detailed analytics, and role-based access control.

---

## 🌟 Key Features

### 👨‍💼 Admin Features
- ✅ Complete dashboard with statistics
- ✅ Create and manage quizzes
- ✅ Add multiple-choice questions (A, B, C, D)
- ✅ Edit quiz details and questions
- ✅ Activate/Deactivate quizzes
- ✅ Delete quizzes with cascade
- ✅ View all student results
- ✅ Filter results by quiz and student

### 👨‍🎓 Student Features
- ✅ User registration and login
- ✅ View available active quizzes
- ✅ Attempt quiz with live countdown timer
- ✅ Auto-submit on time expiry
- ✅ Instant result display
- ✅ Detailed answer review (correct/incorrect)
- ✅ View complete attempt history
- ✅ Performance analytics
- ✅ Print results

### 🔐 Security Features
- ✅ Password hashing (bcrypt)
- ✅ SQL injection prevention
- ✅ XSS prevention
- ✅ Session-based authentication
- ✅ Role-based access control (Admin/Student)

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| **Core PHP** | Backend logic |
| **MySQL** | Database management |
| **HTML5 & CSS3** | Structure and styling |
| **Bootstrap 5** | Responsive design |
| **JavaScript** | Client-side interactivity |
| **Font Awesome** | Icons |
| **Apache** | Web server (XAMPP/WAMP) |

---

## 📋 System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache**: 2.4 or higher
- **Web Browser**: Chrome, Firefox, Edge, Safari
- **XAMPP/WAMP/LAMP**: Recommended

---

## 🚀 Quick Installation

### 1. Clone/Download Project
```bash
# Extract to web server directory
C:\xampp\htdocs\quiz_system     # Windows (XAMPP)
/var/www/html/quiz_system       # Linux
```

### 2. Create Database
```sql
CREATE DATABASE quiz_system;
```

### 3. Import Database
- Open phpMyAdmin
- Select `quiz_system` database
- Import `database.sql` file

### 4. Configure Database
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quiz_system');
```

### 5. Start Server
- Start Apache and MySQL
- Access: `http://localhost/quiz_system/`

---

## 🔑 Default Credentials

**Admin Account:**
- Email: `admin@quiz.com`
- Password: `admin123`

**Student Account:**
- Register new account via registration page

---

## 📊 Database Schema

### Tables Overview

1. **users** - User accounts (Admin & Students)
2. **quizzes** - Quiz information
3. **questions** - Quiz questions with options
4. **results** - Quiz attempt results
5. **student_answers** - Individual answer tracking

### Entity Relationships
```
users (1) -----> (N) results
quizzes (1) ----> (N) questions
quizzes (1) ----> (N) results
results (1) ----> (N) student_answers
questions (1) ---> (N) student_answers
```

---

## 🎯 How It Works

### Complete Flow

1. **Admin Creates Quiz**
   - Login to admin panel
   - Create new quiz with title, description, time limit
   - Add multiple questions with 4 options each
   - Mark correct answer for each question
   - Activate quiz

2. **Student Attempts Quiz**
   - Register/Login to student account
   - View available active quizzes
   - Click "Start Quiz"
   - Timer starts automatically
   - Answer all questions
   - Submit manually or auto-submit on time expiry

3. **Result Generation**
   - System calculates score automatically
   - Compares student answers with correct answers
   - Stores result in database
   - Displays detailed result immediately
   - Shows question-by-question review

4. **Performance Tracking**
   - Students can view all attempt history
   - See performance analytics
   - Track improvement over time
   - Print results for records

---

## 📁 Project Structure

```
quiz_system/
│
├── config/
│   └── database.php              # Database config & helpers
│
├── admin/
│   ├── dashboard.php             # Admin home
│   ├── create_quiz.php           # Create quiz
│   ├── manage_quizzes.php        # Manage quizzes
│   ├── add_questions.php         # Add questions
│   ├── edit_quiz.php             # Edit quiz
│   ├── view_results.php          # View results
│   └── logout.php                # Logout
│
├── student/
│   ├── dashboard.php             # Student home
│   ├── attempt_quiz.php          # Take quiz
│   ├── submit_quiz.php           # Submit handler
│   ├── view_result.php           # View result
│   ├── result_history.php        # History
│   └── logout.php                # Logout
│
├── assets/
│   ├── css/style.css             # Custom styles
│   └── js/timer.js               # Quiz timer
│
├── includes/
│   ├── header.php                # Common header
│   └── footer.php                # Common footer
│
├── index.php                     # Landing page
├── login.php                     # Login page
├── register.php                  # Registration
├── process_login.php             # Login handler
├── process_register.php          # Register handler
└── database.sql                  # Database schema
```

---

## 🖼️ Screenshots

### Admin Dashboard
- Statistics overview (Total Quizzes, Active Quizzes, Students, Attempts)
- Quick actions
- Recent quiz attempts

### Student Dashboard
- Personal statistics (Attempts, Average Score, Passed Quizzes)
- Available quizzes
- Recent attempts

### Quiz Attempt Page
- Live countdown timer (fixed position)
- All questions displayed
- Option selection with visual feedback
- Submit button

### Result Page
- Pass/Fail banner
- Score summary
- Detailed answer review
- Print functionality

---

## 🔧 Configuration

### Change Passing Percentage
Edit calculation in relevant files (default: 40%)

### Modify Timer Warning
Edit `attempt_quiz.php` timer JavaScript (default: 2 minutes)

### Change Session Timeout
Edit `config/database.php`:
```php
ini_set('session.gc_maxlifetime', 3600);
```

---

## 🐛 Common Issues

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config/database.php`
- Ensure database exists

### Timer Not Working
- Enable JavaScript in browser
- Clear browser cache
- Check console for errors

### Blank Page After Login
- Enable error reporting
- Check PHP error logs
- Verify session configuration

---

## 📈 Future Enhancements

- [ ] Email notifications
- [ ] PDF export for results
- [ ] Question bank management
- [ ] Random question selection
- [ ] Quiz categories
- [ ] Analytics dashboard with charts
- [ ] Mobile app version
- [ ] Multi-language support

---

## 🎓 Interview Tips

**Key Points to Mention:**

1. **Architecture**: MVC-like separation (config, controllers, views)
2. **Security**: Password hashing, SQL injection prevention, XSS protection
3. **Features**: Real-time timer, auto-submit, role-based access
4. **Database**: Normalized design with proper relationships
5. **UX**: Responsive design, intuitive navigation, instant feedback

**Common Questions:**

Q: How does the timer work?  
A: JavaScript countdown that runs every second, with auto-submit when time expires.

Q: How do you prevent SQL injection?  
A: Using mysqli_real_escape_string() and prepared statements where needed.

Q: How is the score calculated?  
A: PHP compares student answers with correct answers from database, calculates percentage.

Q: How do you handle concurrent users?  
A: PHP sessions with unique session IDs, database transactions for data integrity.

---

## 📄 License

This project is created for educational purposes and can be used freely for learning and portfolio projects.

---

## 👨‍💻 Author

**Student Assessment Quiz System**  
Built with ❤️ using Core PHP & MySQL  
Perfect for NIIT interviews and educational projects

---

## 🤝 Contributing

This is an educational project. Feel free to:
- Report bugs
- Suggest features
- Improve code quality
- Add documentation

---

## 📞 Support

For questions or issues:
1. Check INSTALLATION_GUIDE.md
2. Review common issues section
3. Check PHP/MySQL error logs

---

**⭐ Star this project if you find it helpful!**
