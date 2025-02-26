<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Correct the path to db.php
require_once 'db.php';

// Fetch courses from the database
try {
    $stmt = $pdo->query("SELECT id, title, description, youtube_link, image_url FROM courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $courses = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self Elevate - Courses</title>
    <link rel="stylesheet" href="/users/styles.css?v=<?php echo time(); ?>">
    <script>
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.profile-pic')) {
                let dropdown = document.getElementById("profileDropdown");
                if (dropdown.classList.contains("show")) {
                    dropdown.classList.remove("show");
                }
            }
        }
    </script>
</head>
<body>

    <div class="wrapper">
        <!-- HEADER SECTION -->
        <header class="header">
            <div class="header-left">
                <a href="myCourses.php" class="enrolled-courses-button">View Enrolled Courses</a>
            </div>
            <div class="logo">Self Elevate</div>
            <div class="nav-container">
                <!-- Add Course Button -->
                <a href="/createCourse.php" class="add-course-button">Add a Course</a>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <img src="/path/to/profile-pic.jpg" alt="Profile" class="profile-pic" onclick="toggleDropdown()">
                    <div id="profileDropdown" class="dropdown-content">
                        <a href="/profile.php">Profile</a>
                        <a href="/users/profileEdit.php">Edit Profile</a>
                    </div>
                </div>

                <!-- Logout Button -->
                <form action="/logout.php" method="post">
                    <button type="submit" class="logout-button">Log Out</button>
                </form>
            </div>
        </header>

        <!-- SEARCH BAR -->
        <div class="search-container">
            <input type="text" placeholder="Search Courses...">
            <button class="search-button">Search</button>
        </div>

        <!-- MAIN CONTENT -->
        <main class="container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['uname']); ?>!</h1>

            <p style="text-align: center; font-size: 1.2em; margin-top: 20px; font-weight: bold;">Explore our courses below:</p>

            <div class="courses-container">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <a href="enroll.php?course_id=<?php echo htmlspecialchars($course['id']); ?>" class="course-link">
                            <div class="course">
                                <?php if (!empty($course['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image" class="course-image">
                                <?php endif; ?>

                                <div class="course-content">
                                    <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                                    <p><?php echo htmlspecialchars($course['description']); ?></p>

                                 
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No courses available. Create your first course!</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- FOOTER (ALWAYS AT BOTTOM) -->
    <footer>
        <p>&copy; 2025 eSlf Elevate. All rights reserved.</p>
    </footer>

</body>
</html>
