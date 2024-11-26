-- Create the database
CREATE DATABASE IF NOT EXISTS student_review_portal;

-- Use the created database
USE student_review_portal;

-- Table for students
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,         -- Unique ID for each student
    first_name VARCHAR(50) NOT NULL,                  -- First name of the student
    last_name VARCHAR(50) NOT NULL,                   -- Last name of the student
    email VARCHAR(100) UNIQUE NOT NULL,               -- Unique email for each student
    department VARCHAR(100),                          -- Department of the student
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP    -- Timestamp when the student was created
);

-- Table for reviews
CREATE TABLE IF NOT EXISTS student_reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,         -- Unique ID for each review
    student_id INT NOT NULL,                          -- Reference to the student being reviewed
    reviewer_name VARCHAR(100) NOT NULL,             -- Name of the reviewer
    rating ENUM('1', '2', '3', '4', '5') NOT NULL,    -- Rating given by the reviewer
    review_text TEXT NOT NULL,                        -- Review text
    skills_demonstrated SET(
        'Communication',
        'Technical',
        'Teamwork',
        'Problem Solving',
        'Leadership'
    ) DEFAULT NULL,                                   -- Skills demonstrated by the student
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Timestamp for the review
    FOREIGN KEY (student_id) REFERENCES students(student_id) 
    ON DELETE CASCADE                                 -- Delete reviews if the student is deleted
);

-- Create an index for performance
CREATE INDEX idx_student_reviews ON student_reviews(student_id);


-- -- Create database for student reviews
-- CREATE DATABASE student_review_portal;
-- USE student_review_portal;

-- -- Table for students
-- CREATE TABLE students (
--     student_id INT AUTO_INCREMENT PRIMARY KEY,
--     first_name VARCHAR(50) NOT NULL,
--     last_name VARCHAR(50) NOT NULL,
--     email VARCHAR(100) UNIQUE NOT NULL,
--     department VARCHAR(100),
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- -- Table for reviews
-- CREATE TABLE reviews (
--     review_id INT AUTO_INCREMENT PRIMARY KEY,
--     student_id INT NOT NULL,
--     reviewer_name VARCHAR(100) NOT NULL,
--     rating ENUM('1', '2', '3', '4', '5') NOT NULL,
--     review_text TEXT NOT NULL,
--     skills_demonstrated SET('Communication', 'Technical', 'Teamwork', 'Problem Solving', 'Leadership') DEFAULT NULL,
--     submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
-- );

-- -- Create an index for performance
-- CREATE INDEX idx_student_reviews ON reviews(student_id);
