<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$dbname = 'student_review_portal'; // Replace with your actual database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Response array
$response = [
    'success' => false,
    'message' => ''
];

// Validate input function
function validateInput($input, $type = 'string') {
    $input = trim($input);
    
    switch($type) {
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL);
        case 'string':
            return strlen($input) > 0;
        default:
            return !empty($input);
    }
}

try {
    // Create PDO connection with error handling
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );

    // Validate and sanitize input
    $firstName = $_POST['first-name'] ?? '';
    $lastName = $_POST['last-name'] ?? '';
    $email = $_POST['email'] ?? '';
    $department = $_POST['department'] ?? '';

    // Comprehensive input validation
    $errors = [];

    if (!validateInput($firstName)) {
        $errors[] = "First name is required";
    }

    if (!validateInput($lastName)) {
        $errors[] = "Last name is required";
    }

    if (!validateInput($email, 'email')) {
        $errors[] = "Invalid email address";
    }

    if (!validateInput($department)) {
        $errors[] = "Department is required";
    }

    // Check for existing email
    $checkEmailStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE email = :email");
    $checkEmailStmt->execute(['email' => $email]);
    
    if ($checkEmailStmt->fetchColumn() > 0) {
        $errors[] = "Email already exists";
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response['message'] = implode(", ", $errors);
        echo json_encode($response);
        exit;
    }

    // Prepare SQL statement
    $stmt = $pdo->prepare("
        INSERT INTO students (
            first_name, 
            last_name, 
            email, 
            department, 
            created_at
        ) VALUES (
            :first_name, 
            :last_name, 
            :email, 
            :department, 
            NOW()
        )
    ");

    // Execute the statement
    $result = $stmt->execute([
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':email' => $email,
        ':department' => $department
    ]);

    // Check if insertion was successful
    if ($result) {
        $response['success'] = true;
        $response['message'] = "Student registered successfully";
        $response['student_id'] = $pdo->lastInsertId();
    } else {
        $response['message'] = "Failed to register student";
    }

} catch(PDOException $e) {
    // Detailed error logging
    error_log("Database Error: " . $e->getMessage());
    
    $response['message'] = match($e->getCode()) {
        '23000' => "Duplicate email or database constraint violation",
        default => "Database error occurred: " . $e->getMessage()
    };
} catch(Exception $e) {
    // Catch any other unexpected errors
    error_log("Unexpected Error: " . $e->getMessage());
    $response['message'] = "An unexpected error occurred: " . $e->getMessage();
}

// Set proper JSON header
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
