<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}

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
    <link rel="stylesheet" href="/users/styles.css?v=<?php echo time(); ?>">
    <script>
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.profile-pic')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
    
      
 
</head>
<body>
    <div class="header">
        <form action="/logout.php" method="post">
            <button type="submit" class="logout-button">Log Out</button>
        </form>
        <div class="logo">Self Elevate</div>
        <a href="myCourses.php" class="enrolled-courses-button">View Enrolled Courses</a>
    </div>

    <div class="search-container">
        <input type="text" placeholder="Search Courses...">
        <button class="search-button">Search</button>
        
        <div class="dropdown">
            <img src="/path/to/profile-pic.jpg" alt="Profile" class="profile-pic" onclick="toggleDropdown()">
            <div id="profileDropdown" class="dropdown-content">
                <a href="/profile.php">Profile</a>
                <a href="/users/profileEdit.php">Edit Profile</a>
            </div>
        </div>
        
        <a href="/createCourse.php" class="add-course-button">Add a Course</a>
    </div>

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['uname']); ?>!</h1>
        <p>Explore our courses below:</p>

        <div class="courses-container">
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course">
                        <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                        <p><?php echo htmlspecialchars($course['description']); ?></p>

                        <?php if (!empty($course['youtube_link'])): ?>
                            <a href="<?php echo htmlspecialchars($course['youtube_link']); ?>" target="_blank">Watch on YouTube</a>
                        <?php endif; ?>

                        <?php if (!empty($course['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image" class="course-image">
                        <?php endif; ?>

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
    </div>
</body>
</html>

