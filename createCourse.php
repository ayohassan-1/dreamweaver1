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
    $image = $_FILES['image'];
    $classroomImage = $_FILES['classroomImage']; // New image for classroom
    $classroomDescription = htmlspecialchars($_POST['classroomDescription']); // New description for classroom

    // Ensure the uploads directory exists and is writable
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);  // Create directory if it doesn't exist
    }

    if ($image['error'] === 0 && $classroomImage['error'] === 0) {
        // Securely handle file upload for course image
        $imagePath = $uploadDir . basename($image['name']);
        // Securely handle file upload for classroom image
        $classroomImagePath = $uploadDir . basename($classroomImage['name']);

        // Check if the course image is a valid image
        if (getimagesize($image['tmp_name']) === false || getimagesize($classroomImage['tmp_name']) === false) {
            echo "Uploaded file is not a valid image.";
            exit();
        }

        // Move files to the upload directory
        if (move_uploaded_file($image['tmp_name'], $imagePath) && move_uploaded_file($classroomImage['tmp_name'], $classroomImagePath)) {
            // Insert course and classroom data into the database using the addCourse function from db.php
            try {
                $courseAdded = addCourse($pdo, $title, $description, $youtubeLink, $imagePath, $classroomImagePath, $classroomDescription);

                if ($courseAdded) {
                    // Redirect to course1.php after success
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
            echo "Failed to upload the image files.";
        }
    } else {
        echo "Error with the uploaded images.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
    <link rel="stylesheet" href="/users/style.css">
</head>
<body>
    <div class="container">
        <h1>Create a New Course</h1>
        <form action="createCourse.php" method="post" enctype="multipart/form-data">
            <label for="title">Course Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Course Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="youtubeLink">YouTube Link:</label>
            <input type="url" id="youtubeLink" name="youtubeLink" placeholder="https://www.youtube.com/watch?v=..." required>

            <label for="image">Course Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <label for="classroomImage">Classroom Image:</label>
            <input type="file" id="classroomImage" name="classroomImage" accept="image/*" required>

            <label for="classroomDescription">Classroom Description:</label>
            <textarea id="classroomDescription" name="classroomDescription" rows="4" required></textarea>

            <button type="submit" class="submit-button">Create Course</button>
        </form>
    </div>
</body>
</html>
