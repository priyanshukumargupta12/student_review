document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.dataset.tab;

            // Reset tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Activate current tab
            button.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Student Registration Form
    const studentForm = document.getElementById('student-form');
    const studentSelect = document.getElementById('student-select');

    studentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('student-registration.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student registered successfully!');
                loadStudents(); // Refresh student list
                studentForm.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while registering the student.');
        });
    });

    // Load Students for Review Dropdown
    function loadStudents() {
        fetch('student-get.php')
        .then(response => response.json())
        .then(students => {
            studentSelect.innerHTML = '<option value="">Select a Student</option>';
            students.forEach(student => {
                const option = document.createElement('option');
                option.value = student.student_id;
                option.textContent = `${student.first_name} ${student.last_name} (${student.department})`;
                studentSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading students:', error);
        });
    }

    // Review Form Submission
    const reviewForm = document.getElementById('review-form');
reviewForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validate required fields before submission
    const reviewerName = document.getElementById('reviewer-name').value.trim();
    const studentId = document.getElementById('student-select').value;
    const rating = document.querySelector('input[name="rating"]:checked');
    const reviewText = document.getElementById('review-text').value.trim();

    // Comprehensive validation
    if (!reviewerName) {
        alert('Please enter reviewer name');
        return;
    }

    if (!studentId) {
        alert('Please select a student');
        return;
    }

    if (!rating) {
        alert('Please select a rating');
        return;
    }

    if (!reviewText) {
        alert('Please enter review details');
        return;
    }

    const formData = new FormData(this);

    // Log form data for debugging
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    fetch('student-review.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Review submitted successfully!');
            loadReviews();
            reviewForm.reset();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the review.');
    });
});
    // const reviewForm = document.getElementById('review-form');
    // reviewForm.addEventListener('submit', function(e) {
    //     e.preventDefault();

    //     const formData = new FormData(this);

    //     fetch('student-get.php', {
    //         method: 'POST',
    //         body: formData
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.success) {
    //             alert('Review submitted successfully!');
    //             loadReviews();
    //             reviewForm.reset();
    //         } else {
    //             alert('Error: ' + data.message);
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //         alert('An error occurred while submitting the review.');
    //     });
    // });

    // Load and Display Reviews
    const reviewsList = document.getElementById('reviews-list');
    const searchInput = document.getElementById('search-student');

    function loadReviews(searchTerm = '') {
        fetch(`student-get.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(reviews => {
            reviewsList.innerHTML = '';
            if (reviews.length === 0) {
                reviewsList.innerHTML = '<p>No reviews found.</p>';
                return;
            }

            reviews.forEach(review => {
                const reviewCard = document.createElement('div');
                reviewCard.className = 'review-card';
                reviewCard.innerHTML = `
                    <div class="review-rating">Rating: ${review.rating}/5</div>
                    <h3>${review.first_name} ${review.last_name}</h3>
                    <p><strong>Department:</strong> ${review.department}</p>
                    <p><strong>Reviewer:</strong> ${review.reviewer_name}</p>
                    <p><strong>Skills:</strong> ${review.skills_demonstrated || 'None'}</p>
                    <p>${review.review_text}</p>
                    <small>Submitted on: ${new Date(review.submission_date).toLocaleString()}</small>
                `;
                reviewsList.appendChild(reviewCard);
            });
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            reviewsList.innerHTML = '<p>Error loading reviews.</p>';
        });
    }

    // Search functionality
    searchInput.addEventListener('input', function() {
        loadReviews(this.value);
    });

    // Initial loads
    loadStudents();
    loadReviews();
});
