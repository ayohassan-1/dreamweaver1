<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session if it's not already started
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}

// Include the database connection file
require_once 'db.php';

// Fetch courses from the database
try {
    $stmt = $pdo->query("SELECT * FROM courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to fetch courses: " . $e->getMessage());
    $courses = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="/users/style.css"> <!-- Linking to style.css -->
</head>
<body>
    <div class="container">
        <!-- View Enrolled Courses Button -->
        <div class="top-menu">
            <a href="/myCourses.php" class="view-enrolled-button" style="position: absolute; >View Enrolled Courses"</a>
        </div>

        <!-- User Information Section -->
        <div class="user-info">
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['uname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Joined:</strong> <?php echo htmlspecialchars($_SESSION['regDate']); ?></p>
            <a href="/profile.php">Go To Profile</a>
        </div>

        <!-- Add Course Button -->
        <div class="add-course">
            <a href="/createCourse.php" class="add-course-button">Add a Course</a>
        </div>

        <!-- Welcome Section -->
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['uname']); ?>!</h1>
        <p>Explore our courses below:</p>

        <!-- Courses Section -->
        <div class="courses">
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course">
                        <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                        <p><?php echo htmlspecialchars($course['description']); ?></p>

                        <!-- YouTube Link -->
                        <?php if (!empty($course['youtube_link'])): ?>
                            <a href="<?php echo htmlspecialchars($course['youtube_link']); ?>" target="_blank">Watch on YouTube</a>
                        <?php endif; ?>

                        <!-- Course Image -->
                        <?php if (!empty($course['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image" style="max-width: 100%; height: auto;">
                        <?php endif; ?>

                        <!-- Enroll Now Form -->
                        <form action="/enroll.php" method="get">
                            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['id']); ?>">
                            <button type="submit" class="enroll-button">Enroll Now</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courses available. Create your first course!</p>
            <?php endif; ?>
        </div>

        <!-- Logout Button -->
        <div class="logout-section">
            <form action="/logout.php" method="post">
                <button type="submit" class="logout-button">Log Out</button>
            </form>
        </div>
    </div>
</body>
</html>
