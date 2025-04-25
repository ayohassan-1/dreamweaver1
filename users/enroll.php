<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

if (!isset($_GET['course_id'])) {
    die("Course ID is required");
}

$course_id = $_GET['course_id'];

try {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE uid = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_pic = $user['profile_pic'];
} catch (PDOException $e) {
    error_log("Error fetching user profile picture: " . $e->getMessage());
    $profile_pic = '';
}

try {
    $stmt = $pdo->prepare("SELECT title, introduction_video_url, introduction_description, introduction_image_url, image_url, description FROM courses WHERE id = :course_id");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    $title = $course['title'] ?? '';
    $video_url = $course['introduction_video_url'] ?? '';
    $description = $course['introduction_description'] ?? '';
    $image_url = $course['introduction_image_url'] ?? '';
    $course_image_url = $course['image_url'] ?? '';
    $course_description = $course['description'] ?? '';

    $youtube_id = '';
    if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/[^\/]+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $video_url, $matches)) {
        $youtube_id = $matches[1];
    }

    $youtube_thumbnail = $youtube_id ? "http://img.youtube.com/vi/{$youtube_id}/0.jpg" : '';
} catch (PDOException $e) {
    error_log("Error fetching course introduction: " . $e->getMessage());
    die("An error occurred while fetching course details. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $reason = htmlspecialchars(trim($_POST['reason']));

    if (empty($name) || empty($email) || empty($reason)) {
        die("Please fill all fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, user_id, name, email, reason, enrollment_date) VALUES (:course_id, :user_id, :name, :email, :reason, NOW())");
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':reason', $reason);
        $stmt->execute();
        header("Location: /users/course1.php?course_id=" . $course_id);
        exit();
    } catch (PDOException $e) {
        error_log("Error enrolling in course: " . $e->getMessage());
        die("An error occurred while processing your enrollment. Please try again later.");
    }
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(user_id) as member_count FROM enrollments WHERE course_id = :course_id");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $memberCount = $stmt->fetch(PDO::FETCH_ASSOC)['member_count'];
} catch (PDOException $e) {
    error_log("Error fetching member count: " . $e->getMessage());
    $memberCount = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1.0">
    <title>Enroll in Course</title>
    <link rel="stylesheet" href="/users/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }
        window.onclick = function(event) {
            if (!event.target.matches('.profile-button') && !event.target.matches('.profile-button *')) {
                document.getElementById("profileDropdown").classList.remove("show");
            }
        }
        function showImage() {
            document.getElementById('video-container').style.display = 'none';
            document.getElementById('image-container').style.display = 'block';
        }
        function showVideo() {
            document.getElementById('image-container').style.display = 'none';
            document.getElementById('video-container').style.display = 'block';
        }
        function showEnrollPopup() {
            document.getElementById('enroll-popup').style.display = 'block';
        }
        function closeEnrollPopup() {
            document.getElementById('enroll-popup').style.display = 'none';
        }
    </script>
    <style>
        body { font-family: sans-serif; background-color: #121212; color: white; margin: 0; padding: 0; }
        .container { display: flex; justify-content: space-around; padding: 30px; }
        .enroll-container, .media-container { background-color: #1e1e1e; padding: 20px; border-radius: 15px; width: 45%; margin: 10px; border: 2px solid #555; }
        h1 { color: grey; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, textarea { width: 100%; padding: 10px; border: none; border-radius: 5px; background-color: #252525; color: white; }
        textarea { resize: vertical; height: 100px; }
        button { background-color: grey; color: black; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .self-elevate { color: white; text-decoration: none; font-size: 2.5em; font-weight: bold; }
        .video-container, .image-container { width: 100%; max-width: 100%; margin: 0 10px; border-radius: 10px; overflow: hidden; position: relative; }
        .video-container iframe { width: 100%; height: 315px; }
        .image-container img { width: 100%; display: block; }
        .course-title { color: white; font-size: 1.5em; margin-bottom: 10px; position: absolute; top: 10px; left: 10px; }
        .media-switch { display: flex; justify-content: left; margin-top: 10px; }
        .media-switch img { width: 50px; height: 40px; border-radius: 5px; cursor: pointer; margin: 0 5px; }
        .course-description { color: white; margin-top: 20px; text-align: left; font-size: 1.2em; }
        .header { background-color: #1e1e1e; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #555; }
        .logout-button-left { background-color: #333; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .profile-section { position: relative; }
        .profile-button { background: none; border: none; cursor: pointer; display: flex; align-items: center; }
        .profile-pic { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
        .dropdown-content { display: none; position: absolute; background-color: #333; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 5px; }
        .dropdown-content a { color: white; padding: 12px 16px; text-decoration: none; display: block; }
        .dropdown-content a:hover { background-color: #555; }
        .show { display: block; }
        .enroll-container form { display: none; flex-direction: column; justify-content: space-around; height: 100%; }
        .enroll-container form label, .enroll-container form input, .enroll-container form textarea, .enroll-container form button { margin-bottom: 20px; }
        #enroll-popup { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #1e1e1e; border-radius: 15px; border: 2px solid #555; z-index: 1000; width: 500px; text-align: center; font-family: sans-serif; display: flex; flex-direction: column; }
        #enroll-popup > div:first-child { background-color: #2a2a2a; padding: 20px; border-top-left-radius: 15px; border-top-right-radius: 15px; }
        #enroll-popup > div:last-child { background-color: #3a3a3a; padding: 20px; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; }
        #enroll-popup img { width: 140px; height: 140px; border-radius: 5px; margin-bottom: 10px; }
        #enroll-popup form { display: flex; flex-direction: column; }
        #enroll-popup form input, #enroll-popup form textarea { background-color: #252525; color: white; padding: 10px; margin-bottom: 15px; border: none; border-radius: 5px; font-size: 16px; }
        #enroll-popup button { background-color: grey; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; width: 100%; }
        .header-course-info {
            display: flex;
            align-items: center;
        }
        .header-course-info img {
            width: 60px; /* Increased image size */
            height: 60px; /* Increased image size */
            margin-right: 10px;
            border-radius: 5px;
        }
        .header-course-info span {
            color: white;
            font-size: 1em;
            margin-right: 20px;
        }
        .header-course-arrows {
            display: flex;
            flex-direction: column;
            margin-left: 10px; /* Moved arrows to the right of the image */
        }
        .header-course-arrows i {
            color: lightgrey;
            font-size: 1em;
        }
        .profile-section {
            position: absolute; /* Position profile picture center-right */
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        .enroll-container {
            border: 1px solid #555; /* Thin border */
            width: 30%; /* Adjust width as needed */
            margin-top: 20px; /* Add margin for spacing */
            margin-left: auto; /* Position to the right */
        }
        .enroll-container-content {
            padding: 20px;
        }
        .enroll-container-members {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            color: white;
        }
        .enroll-container-members img {
            width: 20px; /* Adjust size as needed */
            height: 20px; /* Adjust size as needed */
            margin-right: 5px;
        }
        .media-switch {
            display: flex;
            justify-content: left;
            margin-top: 10px;
        }
        .media-switch img {
            width: 50px;
            height: 40px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
        }
        .media-container {
            position: relative;
        }
        .media-switch {
            position: absolute;
            bottom: -50px; /* Position below the main image/video */
            left: 0;
            transform: translateX(0);
        }
        .powered-by {
            text-align: center;
            color: lightgrey;
            margin-top: 10px;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }
        .logo-container img {
            width: 20px; /* Adjust logo size */
            height: 20px; /* Adjust logo size */
            margin: 0 5px;
        }
        .enroll-now-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
        }
        .enroll-now-section .media-container {
            width: 60%; /* Adjust as needed */
        }
        .enroll-now-section .enroll-container {
            width: 35%; /* Adjust as needed */
            margin-left: 20px; /* Spacing between borders */
        }
        .price-tag-logo {
            width: 20px; /* Adjust as needed */
            height: 20px; /* Adjust as needed */
            margin-right: 5px;
        }
        .members-logo {
            width: 20px; /* Adjust as needed */
            height: 20px; /* Adjust as needed */
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-course-info">
            <img src="<?php echo htmlspecialchars($course_image_url); ?>" alt="Course Image">
            <span><?php echo htmlspecialchars($title); ?></span>
            <div class="header-course-arrows">
                <i class="fas fa-chevron-up"></i>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
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
    </header>
    <div class="container">
        <div class="enroll-now-section">
            <div class="media-container">
                <h1 class="course-title"><?php echo htmlspecialchars($title); ?></h1>
                <div id="video-container" class="video-container">
                    <?php if ($youtube_id): ?>
                        <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($youtube_id); ?>" frameborder="0" allowfullscreen></iframe>
                    <?php else: ?>
                        <p>Video not available</p>
                    <?php endif; ?>
                </div>
                <div id="image-container" class="image-container" style="display: none;">
                    <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Course Image">
                </div>
                <div class="media-switch">
                    <?php if ($youtube_thumbnail): ?>
                        <img src="<?php echo htmlspecialchars($youtube_thumbnail); ?>" alt="Video Thumbnail" onclick="showVideo()">
                    <?php endif; ?>
                    <?php if ($image_url): ?>
                        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Course Image" onclick="showImage()">
                    <?php endif; ?>
                </div>
                <div class="course-description">
                    <h3>Introduction</h3>
                    <p><?php echo nl2br(htmlspecialchars($description)); ?></p>
                </div>
                <div class="logo-container">
                    <img src="members_logo.png" alt="Members Logo" class="members-logo">
                    <span><?php echo htmlspecialchars($memberCount); ?> Members</span>
                    <img src="price_tag_logo.png" alt="Price Tag Logo" class="price-tag-logo">
                    <span>Free</span>
                </div>
            </div>
            <div class="enroll-container">
                <div class="enroll-container-content">
                    <img src="<?php echo htmlspecialchars($course_image_url); ?>" alt="Course Image" style="width: 100%; max-width: 100%; border-radius: 10px; margin-bottom: 10px;">
                    <h2 style="color: white; text-align: center; margin-bottom: 5px;"><?php echo htmlspecialchars($title); ?></h2>
                    <p style="color: white; text-align: center; margin-bottom: 20px;"><?php echo htmlspecialchars($course_description); ?></p>
                    <button onclick="showEnrollPopup()" style="background-color: grey; color: black; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 100%;">Enroll Now</button>
                </div>
                <div class="logo-container">
                    <img src="members_logo.png" alt="Members Logo" class="members-logo">
                    <span><?php echo htmlspecialchars($memberCount); ?> Members</span>
                </div>
            </div>
        </div>
    </div>
    <div class="powered-by">
        Powered by Self Elevate
    </div>
    <div id="enroll-popup">
        <div>
            <img src="<?php echo htmlspecialchars($course_image_url); ?>" alt="Course Image">
            <h2 style="color: white; text-align: center; margin-bottom: 10px;"><?php echo htmlspecialchars($title); ?></h2>
        </div>
        <div>
            <form method="post" action="/users/enroll.php?course_id=<?php echo htmlspecialchars($course_id); ?>">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <textarea name="reason" placeholder="Reason for enrolling" required></textarea>
                <button type="submit">Join Now</button>
            </form>
            <button onclick="closeEnrollPopup()" style="background-color: #333; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; margin-top: 10px;">Close</button>
        </div>
    </div>
</body>
</html>