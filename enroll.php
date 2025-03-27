<?php
session_start();

// Include the database connection
require_once 'db.php';

// Check if the course_id is set and is valid
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("Course ID is required.");
}

$course_id = $_GET['course_id'];
$email = $_POST['email'] ?? ''; // Get email from POST request

// Check if the user is already enrolled
if (isUserEnrolled($pdo, $course_id, $email)) {
    // If the user is already enrolled, redirect back to course1.php with an error message
    header("Location: /users/course1.php?course_id={$course_id}&error=already_enrolled");
    exit();
}

// If the user is not enrolled, proceed to enroll them (Insert into the enrollments table)
try {
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, email, enroll_date) VALUES (?, ?, NOW())");
    $stmt->execute([$course_id, $email]);

    // Redirect to course1.php after successful enrollment
    header("Location: /users/course1.php?course_id={$course_id}");
    exit();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Error enrolling the user.");
}
?>
