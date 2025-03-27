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
    $youtube_link = htmlspecialchars($_POST['youtube_link']);
    $imageUrl = filter_var($_POST['imageUrl'], FILTER_VALIDATE_URL);
    $about = htmlspecialchars($_POST['about']);

    if ($imageUrl) {
        try {
            $courseAdded = addCourse($pdo, $title, $description, $youtube_link, $imageUrl, $about);

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
        echo "Invalid image URL.";
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
    <div class="stars-container"></div>

    <div class="container">
        <h1>Create a New Course</h1>
        <form action="createCourse.php" method="post">
            <label for="title">Course Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Course Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="youtube_link">YouTube Link:</label>
            <input type="url" id="youtube_link" name="youtube_link" placeholder="https://www.youtube.com/watch?v=..." required>

            <label for="imageUrl">Course Image URL:</label>
            <input type="url" id="imageUrl" name="imageUrl" required>

            <label for="about">About Course:</label>
            <textarea id="about" name="about" rows="4" required></textarea>

            <button type="submit" class="submit-button">Create Course</button>
        </form>
    </div>

    <script src="/createcorusestyle/script.js"></script>
</body>
</html>
