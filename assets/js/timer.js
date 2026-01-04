// Quiz Timer JavaScript
// Path: assets/js/timer.js

// This file is currently embedded in attempt_quiz.php
// But can be extracted here for better organization

/**
 * Quiz Timer Functions
 * Handles countdown timer for quiz attempts
 */

class QuizTimer {
    constructor(timeLimit) {
        this.timeLimit = timeLimit; // in seconds
        this.timeRemaining = timeLimit;
        this.timerInterval = null;
        this.timerElement = document.getElementById('timer');
        this.timerBox = document.getElementById('timerBox');
        this.quizForm = document.getElementById('quizForm');
    }

    /**
     * Start the countdown timer
     */
    start() {
        this.timerInterval = setInterval(() => {
            this.tick();
        }, 1000);
    }

    /**
     * Timer tick - called every second
     */
    tick() {
        // Calculate minutes and seconds
        let minutes = Math.floor(this.timeRemaining / 60);
        let seconds = this.timeRemaining % 60;

        // Add leading zero if needed
        seconds = seconds < 10 ? '0' + seconds : seconds;

        // Update timer display
        this.timerElement.textContent = minutes + ':' + seconds;

        // Warning when 2 minutes remaining
        if (this.timeRemaining <= 120 && !this.timerBox.classList.contains('timer-warning')) {
            this.timerBox.classList.add('timer-warning');
            this.showWarning('Only 2 minutes remaining!');
        }

        // Warning when 1 minute remaining
        if (this.timeRemaining === 60) {
            this.showWarning('Only 1 minute remaining!');
        }

        // Time expired - auto submit
        if (this.timeRemaining <= 0) {
            this.stop();
            this.handleTimeUp();
        }

        this.timeRemaining--;
    }

    /**
     * Stop the timer
     */
    stop() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    /**
     * Handle time up event
     */
    handleTimeUp() {
        alert('Time is up! Your quiz will be submitted automatically.');
        
        // Disable the beforeunload warning
        window.onbeforeunload = null;
        
        // Submit the form
        if (this.quizForm) {
            this.quizForm.submit();
        }
    }

    /**
     * Show warning message
     */
    showWarning(message) {
        // Create toast notification (optional enhancement)
        console.log(message);
    }

    /**
     * Pause timer (for future use)
     */
    pause() {
        this.stop();
    }

    /**
     * Resume timer (for future use)
     */
    resume() {
        this.start();
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the quiz attempt page
    const timerElement = document.getElementById('timer');
    const quizForm = document.getElementById('quizForm');
    
    if (timerElement && quizForm) {
        // Get time limit from form data
        const timeLimitInput = document.querySelector('input[name="time_limit"]');
        if (timeLimitInput) {
            const timeLimit = parseInt(timeLimitInput.value) * 60; // Convert to seconds
            
            // Initialize and start timer
            const timer = new QuizTimer(timeLimit);
            timer.start();
            
            // Prevent form resubmission
            quizForm.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                }
                
                // Stop timer on submit
                timer.stop();
                
                // Remove beforeunload warning
                window.onbeforeunload = null;
            });
            
            // Warn before leaving page
            window.onbeforeunload = function(e) {
                e.preventDefault();
                e.returnValue = 'Are you sure you want to leave? Your quiz progress will be lost.';
                return e.returnValue;
            };
        }
    }
});