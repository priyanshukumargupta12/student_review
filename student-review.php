<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$dbname = 'student_review_portal';
$username = 'root';
$password = '';

// Response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Validate required fields
    $requiredFields = ['student-id', 'reviewer-name', 'rating', 'review-text'];
    $errors = [];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Missing required field: $field";
        }
    }

    // Validate rating
    if (!in_array($_POST['rating'], ['1', '2', '3', '4', '5'])) {
        $errors[] = "Invalid rating";
    }

    // Check for existing student
    $checkStudentStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE student_id = :student_id");
    $checkStudentStmt->execute(['student_id' => $_POST['student-id']]);
    
    if ($checkStudentStmt->fetchColumn() == 0) {
        $errors[] = "Invalid student selected";
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response['message'] = implode(", ", $errors);
        echo json_encode($response);
        exit;
    }

    // Prepare skills array (optional)
    $skills = isset($_POST['skills']) ? implode(', ', $_POST['skills']) : null;

    // Prepare SQL statement
    $stmt = $pdo->prepare("
        INSERT INTO student_reviews 
        (student_id, reviewer_name, rating, skills_demonstrated, review_text, submission_date) 
        VALUES 
        (:student_id, :reviewer_name, :rating, :skills, :review_text, NOW())
    ");

    // Execute the statement
    $result = $stmt->execute([
        ':student_id' => $_POST['student-id'],
        ':reviewer_name' => $_POST['reviewer-name'],
        ':rating' => $_POST['rating'],
        ':skills' => $skills,
        ':review_text' => $_POST['review-text']
    ]);

    // Set success response
    $response['success'] = $result;
    $response['message'] = $result 
        ? 'Review submitted successfully' 
        : 'Failed to submit review';

} catch(PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $response['message'] = "Database error: " . $e->getMessage();
} catch(Exception $e) {
    error_log("Unexpected Error: " . $e->getMessage());
    $response['message'] = "An unexpected error occurred: " . $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>