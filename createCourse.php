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
    <!-- Stars Container -->
    <div class="stars-container"></div>

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

    <!-- External JavaScript -->
    <script src="/createcorusestyle/script.js"></script>
</body>
</html>
