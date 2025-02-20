<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $youtubeLink = htmlspecialchars($_POST['youtubeLink']);
    $imageUrl = filter_var($_POST['imageUrl'], FILTER_VALIDATE_URL);
    $classroomImageUrl = filter_var($_POST['classroomImageUrl'], FILTER_VALIDATE_URL);
    $classroomDescription = htmlspecialchars($_POST['classroomDescription']);

    if ($imageUrl && $classroomImageUrl) {
        try {
            $courseAdded = addCourse($pdo, $title, $description, $youtubeLink, $imageUrl, $classroomImageUrl, $classroomDescription);

            if ($courseAdded) {
                header("Location: users/course1.php?course_id=" . $courseAdded);
                exit();
            } else {
                echo "Error adding course. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Failed to add course: " . $e->getMessage());
            echo "Error adding course. Please try again.";
        }
    } else {
        echo "Invalid image URLs.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
    <link rel="stylesheet" href="/createcorusestyle/style.css">
</head>
<body>
    <div class="container">
        <h1>Create a New Course</h1>
        <form action="createCourse.php" method="post">
            <label for="title">Course Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Course Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="youtubeLink">YouTube Link:</label>
            <input type="url" id="youtubeLink" name="youtubeLink" placeholder="https://www.youtube.com/watch?v=..." required>

            <label for="imageUrl">Course Image URL:</label>
            <input type="url" id="imageUrl" name="imageUrl" required>

            <label for="classroomImageUrl">Classroom Image URL:</label>
            <input type="url" id="classroomImageUrl" name="classroomImageUrl" required>

            <label for="classroomDescription">Classroom Description:</label>
            <textarea id="classroomDescription" name="classroomDescription" rows="4" required></textarea>

            <button type="submit" class="submit-button">Create Course</button>
        </form>
    </div>
</body>
</html>

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
