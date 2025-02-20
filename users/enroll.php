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

// Fetch course details
$stmt = $pdo->prepare("SELECT name, description FROM courses WHERE course_id = :course_id");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found.");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $reason = $_POST['reason'];

    // Insert enrollment data into the database
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, user_id, name, email, reason, enrollment_date) 
                           VALUES (:course_id, :user_id, :name, :email, :reason, NOW())");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':reason', $reason);
    $stmt->execute();

    // Redirect to course1.php with course ID
    header("Location: /users/course1.php?course_id=" . $course_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in <?php echo htmlspecialchars($course['name']); ?></title>
    <link rel="stylesheet" href="/enrollStyle/style.css">
</head>
<body>
    <div class="container">
        <div class="enroll-container">
            <h1>Enroll in <?php echo htmlspecialchars($course['name']); ?></h1>
            <p><?php echo htmlspecialchars($course['description']); ?></p>

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
