<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// Get profile picture
try {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE uid = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_pic = $user['profile_pic'] ?? '';
} catch (PDOException $e) {
    error_log("Error fetching profile picture: " . $e->getMessage());
    $profile_pic = '';
}

// Get available courses
try {
    $stmt = $pdo->prepare("SELECT c.id, c.title, c.description, c.youtube_link, c.image_url FROM courses c WHERE c.id NOT IN (SELECT course_id FROM enrollments WHERE user_id = :user_id)");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
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
    <title>Self Elevate - Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/users/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/users/buttonStyles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.closest('.profile-section')) {
                document.getElementById("profileDropdown").classList.remove("show");
            }
        }

        function searchCourses() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const courses = document.querySelectorAll('.course-link');
            courses.forEach(course => {
                const title = course.querySelector('h2').textContent.toLowerCase();
                course.style.display = title.includes(input) ? 'block' : 'none';
            });
        }
    </script>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <button class="logout-button-left" onclick="location.href='/logout.php'">Log Out</button>
        </div>

        <div class="logo">Self Elevate</div>

        <div class="header-right">
            <a href="myCourses.php" class="view-courses">View Enrolled Courses</a>
            <div class="profile-section">
                <button class="profile-button" onclick="toggleDropdown()">
                    <?php if ($profile_pic): ?>
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic">
                    <?php else: ?>
                        <span>Profile</span>
                    <?php endif; ?>
                </button>
                <div id="profileDropdown" class="dropdown-content">
                    <a href="/profile.php">Profile</a>
                    <a href="/users/profileEdit.php">Edit Profile</a>
                </div>
            </div>
        </div>
    </header>
	
	

    <div class="search-container">
        <i class="fa fa-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Search for anything" onkeyup="searchCourses()">
    </div>

    <main class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['uname']); ?>!</h1>
        <p>Explore our courses below or <a href="/createCourse.php" class="create-course">create your own course</a></p>
    </main>

    <div class="courses-container" id="coursesContainer">
        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <a href="/EnrollFile/enroll.php?course_id=<?php echo htmlspecialchars($course['id']); ?>" class="course-link">
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
</body>
</html>
