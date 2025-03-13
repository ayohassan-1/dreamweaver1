<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}

// Include the database connection file
require_once 'users/db.php';

// Fetch enrolled courses initially
$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT c.id, c.title, c.description, c.youtube_link, c.image_url 
                            FROM courses c
                            INNER JOIN enrollments e ON c.id = e.course_id
                            WHERE e.user_id = ?");
    $stmt->execute([$user_id]);
    $enrolledCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $enrolledCourses = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link rel="stylesheet" href="/users/style.css">
   <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("searchInput").addEventListener("input", searchCourses);
    });

    function searchCourses() {
        let query = document.getElementById("searchInput").value.trim();
        let xhr = new XMLHttpRequest();
        
        xhr.open("GET", "fetch_courses.php?search=" + encodeURIComponent(query), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById("coursesContainer").innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
</script>

</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <div class="back-button">
            <a href="/users/landing.php" class="back-link">Back to Landing Page</a>
        </div>

        <!-- Header -->
        <h1>My Enrolled Courses</h1>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search Enrolled Courses...">
            <button class="search-button" onclick="searchCourses()">Search</button>
        </div>

        <!-- Enrolled Courses Section -->
        <div class="courses" id="coursesContainer">
            <?php if (!empty($enrolledCourses)): ?>
                <?php foreach ($enrolledCourses as $course): ?>
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
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You are not enrolled in any courses yet.</p>
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
