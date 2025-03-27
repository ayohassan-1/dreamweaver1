<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../db.php';

// Check if course_id is provided
if (!isset($_GET['course_id'])) {
    die("Course ID is required");
}

$course_id = $_GET['course_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user input and sanitize it
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $reason = htmlspecialchars(trim($_POST['reason']));

    // Simple validation
    if (empty($name) || empty($email) || empty($reason)) {
        die("Please fill all fields");
    }

    // Insert enrollment data into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, user_id, name, email, reason, enrollment_date) 
                               VALUES (:course_id, :user_id, :name, :email, :reason, NOW())");
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':reason', $reason);
        $stmt->execute();
        
        // Redirect to course page after successful enrollment
        header("Location: /users/course1.php?course_id=" . $course_id);
        exit();
    } catch (PDOException $e) {
        error_log("Error enrolling in course: " . $e->getMessage());
        die("An error occurred while processing your enrollment. Please try again later.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Course</title>
    <link rel="stylesheet" href="/enrollStyle/style.css">
</head>
<body>
    <div class="container">
        <div class="enroll-container">
            <h1>Enroll in Course</h1>
            <form method="post" action="/users/enroll.php?course_id=<?php echo htmlspecialchars($course_id); ?>">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason for enrolling:</label>
                    <textarea id="reason" name="reason" required></textarea>
                </div>
                <button type="submit">Enroll Now</button>
            </form>
        </div>
    </div>
</body>
</html>
