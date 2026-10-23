// Main JavaScript for LMS

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        });
    }, 5000);
    
    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Progress tracking
function updateLessonProgress(lessonId, watchedSeconds) {
    fetch('api/update-progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lesson_id: lessonId,
            watched_seconds: watchedSeconds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update progress bar
            const progressBar = document.querySelector('.course-progress-bar');
            if (progressBar) {
                progressBar.style.width = data.progress + '%';
                progressBar.setAttribute('aria-valuenow', data.progress);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Video player tracking
function initializeVideoTracking(videoElement, lessonId) {
    let lastUpdate = 0;
    
    videoElement.addEventListener('timeupdate', function() {
        const currentTime = Math.floor(this.currentTime);
        
        // Update every 30 seconds
        if (currentTime - lastUpdate >= 30) {
            lastUpdate = currentTime;
            updateLessonProgress(lessonId, currentTime);
        }
    });
    
    videoElement.addEventListener('ended', function() {
        updateLessonProgress(lessonId, this.duration);
        
        // Mark lesson as completed
        fetch('api/complete-lesson.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                lesson_id: lessonId,
                completed: true
            })
        });
    });
}

// Quiz functionality
class QuizManager {
    constructor(quizData) {
        this.quizData = quizData;
        this.currentQuestion = 0;
        this.answers = {};
        this.score = 0;
    }
    
    start() {
        this.showQuestion(0);
    }
    
    showQuestion(index) {
        const question = this.quizData.questions[index];
        const container = document.getElementById('quiz-container');
        
        let html = `
            <div class="quiz-question">
                <h4>Question ${index + 1} of ${this.quizData.questions.length}</h4>
                <p class="mb-4">${question.question}</p>
                <div class="quiz-options">
        `;
        
        ['A', 'B', 'C', 'D'].forEach(letter => {
            const optionKey = `option_${letter.toLowerCase()}`;
            html += `
                <div class="quiz-option" data-option="${letter}" onclick="quiz.selectOption(${index}, '${letter}')">
                    <span class="badge bg-secondary me-2">${letter}</span>
                    ${question[optionKey]}
                </div>
            `;
        });
        
        html += `
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    <button class="btn btn-outline-primary" onclick="quiz.previousQuestion()" ${index === 0 ? 'disabled' : ''}>
                        Previous
                    </button>
                    <button class="btn btn-primary" onclick="quiz.nextQuestion()">
                        ${index === this.quizData.questions.length - 1 ? 'Finish' : 'Next'}
                    </button>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Highlight selected option if any
        if (this.answers[index]) {
            const selectedOption = document.querySelector(`.quiz-option[data-option="${this.answers[index]}"]`);
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
        }
    }
    
    selectOption(questionIndex, option) {
        this.answers[questionIndex] = option;
        
        // Remove selected class from all options
        document.querySelectorAll('.quiz-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Add selected class to chosen option
        event.currentTarget.classList.add('selected');
    }
    
    previousQuestion() {
        if (this.currentQuestion > 0) {
            this.currentQuestion--;
            this.showQuestion(this.currentQuestion);
        }
    }
    
    nextQuestion() {
        if (this.currentQuestion < this.quizData.questions.length - 1) {
            this.currentQuestion++;
            this.showQuestion(this.currentQuestion);
        } else {
            this.submitQuiz();
        }
    }
    
    submitQuiz() {
        // Calculate score
        let correctAnswers = 0;
        this.quizData.questions.forEach((question, index) => {
            if (this.answers[index] === question.correct_answer) {
                correctAnswers++;
            }
        });
        
        const percentage = (correctAnswers / this.quizData.questions.length) * 100;
        
        // Submit to server
        fetch('api/submit-quiz.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                quiz_id: this.quizData.id,
                answers: this.answers,
                score: correctAnswers,
                percentage: percentage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showResults(correctAnswers, percentage, data.passed);
            }
        });
    }
    
    showResults(correct, percentage, passed) {
        const container = document.getElementById('quiz-container');
        container.innerHTML = `
            <div class="quiz-results text-center">
                <div class="mb-4">
                    <i class="bi bi-${passed ? 'check-circle-fill text-success' : 'x-circle-fill text-danger'}" style="font-size: 4rem;"></i>
                </div>
                <h3>${passed ? 'Congratulations!' : 'Better luck next time!'}</h3>
                <p class="lead">You scored ${correct} out of ${this.quizData.questions.length}</p>
                <p class="display-4 mb-4">${percentage.toFixed(1)}%</p>
                ${passed ? 
                    '<p>You have successfully passed the quiz!</p>' : 
                    `<p>You need ${this.quizData.passing_score}% to pass. You can try again.</p>
                     <button class="btn btn-primary" onclick="location.reload()">Try Again</button>`
                }
                <div class="mt-4">
                    <a href="course.php?id=${this.quizData.course_id}" class="btn btn-outline-primary">Back to Course</a>
                </div>
            </div>
        `;
    }
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchCourses');
    const courseCards = document.querySelectorAll('.course-card');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            courseCards.forEach(card => {
                const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const description = card.querySelector('p')?.textContent.toLowerCase() || '';
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

// Category filtering
function initializeCategoryFilter() {
    const filterButtons = document.querySelectorAll('.category-filter');
    const courseCards = document.querySelectorAll('.course-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter courses
            courseCards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeCategoryFilter();
});