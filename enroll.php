<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
session_start();
require_once 'db.php';

// Check if course_id is provided
if (!isset($_GET['course_id'])) {
    die("Course ID is required");
}

$course_id = $_GET['course_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $reason = $_POST['reason'];

    // Insert enrollment data into the database (create a new table for enrollments)
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, user_id, name, email, reason, enrollment_date) 
                           VALUES (:course_id, :user_id, :name, :email, :reason, NOW())");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':reason', $reason);
    $stmt->execute();

    // Redirect to course1.php with form data in the URL
    header("Location: /users/course1.php?course_id=" . $course_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Course</title>
    <link rel="stylesheet" href="/users/styles.css"> <!-- Updated path to style.css -->
</head>
<body>
    <h1>Enroll in Course</h1>
    <form method="post" action="/users/enroll.php?course_id=<?php echo htmlspecialchars($course_id); ?>"> <!-- Corrected form action -->
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="reason">Reason for enrolling:</label>
        <textarea id="reason" name="reason" required></textarea><br><br>
        <button type="submit">Enroll Now</button>
    </form>
</body>
</html>

Khris