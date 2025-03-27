<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /users/login.php");
    exit();
}

require_once 'db.php';

try {
    $stmt = $pdo->prepare("
        SELECT c.id AS course_id, c.title, c.description, c.youtube_link, c.image_url
        FROM courses c
        INNER JOIN enrollments e ON c.id = e.course_id
        WHERE e.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
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
    <link rel="stylesheet" href="/users/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/users/buttonStyles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
            color: #fff;
        }
        .logo {
            font-size: 2.5em;
            font-weight: bold;
        }
        .header-buttons {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logout-button-left, .back-button {
            background-color: #ff4d4d;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .back-button {
            background-color: #575757;
        }
        .profile-section {
            position: relative;
        }
        .profile-button {
            background: none;
            border: none;
            cursor: pointer;
        }
        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 150px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: #333;
            padding: 10px 12px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #ddd;
        }
        .dropdown-content.show {
            display: block;
        }
        .container {
            margin: 20px;
            text-align: center;
        }
        .page-title {
            font-size: 2.5em;
            margin: 20px 0;
            font-weight: bold;
        }
        .courses {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .course {
            border: 1px solid #ccc;
            padding: 15px;
            width: 300px;
            text-align: center;
            transition: box-shadow 0.3s ease;
        }
        .course:hover {
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .course img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
    <script>
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }
        window.onclick = function(event) {
            if (!event.target.matches('.profile-button') && !event.target.matches('.profile-button *')) {
                document.getElementById("profileDropdown").classList.remove("show");
            }
        }
    </script>
</head>
<body>
    <header class="header">
        <button class="logout-button-left" onclick="location.href='/logout.php'">Log Out</button>
        <div class="logo">Self Elevate</div> <!-- This was moved my christiano, and commented the profile button, if you have questions answer here or in person.-->
        <div class="header-buttons">
            <button class="back-button" onclick="location.href='/users/login.php'">Back to Home Page</button>
<!--
            <div class="profile-section">
                <button class="profile-button" onclick="toggleDropdown()">
                    <?php if (isset($profile_pic) && $profile_pic): ?>
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic">
                    <?php else: ?>
                        <span>Profile</span>
                    <?php endif; ?>
                </button>
                <div id="profileDropdown" class="dropdown-content">
                    <a href="/profile.php">Profile</a>
                    <a href="/users/profileEdit.php">Edit Profile</a>
                    <a href="myCourses.php">View Enrolled Courses</a>
                </div>
            </div>
-->
        </div>
    </header>

    <div class="container">
        <div class="page-title">My Enrolled Courses</div>
        <div class="courses">
            <?php if (count($enrolledCourses) > 0): ?>
                <?php foreach ($enrolledCourses as $course): ?>
                    <a href="/users/course1.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>" class="course-link">
                        <div class="course">
                            <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                            <?php if (!empty($course['youtube_link'])): ?>
                                <p><small>Watch on YouTube</small></p>
                            <?php endif; ?>
                            <?php if (!empty($course['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image">
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You are not enrolled in any courses yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
