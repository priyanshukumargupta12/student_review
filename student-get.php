<?php
// Database configuration
$host = 'localhost';
$dbname = 'student_review_portal'; // Replace with your actual database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Response array
$response = [];

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

    // Prepare and execute query to get all students
    $stmt = $pdo->query("
        SELECT 
            student_id, 
            first_name, 
            last_name, 
            email, 
            department 
        FROM students 
        ORDER BY last_name, first_name
    ");

    // Fetch all students
    $students = $stmt->fetchAll();

    // Return students as JSON
    header('Content-Type: application/json');
    echo json_encode($students);

} catch(PDOException $e) {
    // Error handling
    error_log("Database Error: " . $e->getMessage());
    
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
exit;
?>
