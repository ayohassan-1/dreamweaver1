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

die("Course ID is required for enrollment.");

}



$course_id = $_GET['course_id'];



try {

$userStatement = $pdo->prepare("SELECT profile_pic FROM users WHERE uid = :userId");

$userStatement->bindParam(':userId', $_SESSION['user_id']);

$userStatement->execute();

$userResult = $userStatement->fetch(PDO::FETCH_ASSOC);

$profilePicture = $userResult['profile_pic'];

} catch (PDOException $userException) {

error_log("Failed to fetch user profile picture: " . $userException->getMessage());

$profilePicture = '';

}



try {

$courseStatement = $pdo->prepare("SELECT title, introduction_video_url, introduction_description, introduction_image_url, image_url, description FROM courses WHERE id = :courseId");

$courseStatement->bindParam(':courseId', $course_id);

$courseStatement->execute();

$courseData = $courseStatement->fetch(PDO::FETCH_ASSOC);



$courseTitle = $courseData['title'] ?? '';

$videoUrl = $courseData['introduction_video_url'] ?? '';

$courseDescriptionIntro = $courseData['introduction_description'] ?? '';

$imageUrlIntro = $courseData['introduction_image_url'] ?? '';

$courseImageUrl = $courseData['image_url'] ?? '';

$courseFullDescription = $courseData['description'] ?? '';



$youtubeVideoId = '';

if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/[^\/]+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $videoUrl, $matches)) {

$youtubeVideoId = $matches[1];

}



$youtubeThumbnailUrl = $youtubeVideoId ? "http://img.youtube.com/vi/{$youtubeVideoId}/0.jpg" : '';

} catch (PDOException $courseException) {

error_log("Failed to fetch course introduction: " . $courseException->getMessage());

die("Error retrieving course details. Please try again later.");

}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$enrollmentName = htmlspecialchars(trim($_POST['name']));

$enrollmentEmail = htmlspecialchars(trim($_POST['email']));

$enrollmentReason = htmlspecialchars(trim($_POST['reason']));



if (empty($enrollmentName) || empty($enrollmentEmail) || empty($enrollmentReason)) {

die("Please complete all required fields.");

}



try {

$enrollmentStatement = $pdo->prepare("INSERT INTO enrollments (course_id, user_id, name, email, reason, enrollment_date) VALUES (:courseId, :userId, :name, :email, :reason, NOW())");

$enrollmentStatement->bindParam(':courseId', $course_id);

$enrollmentStatement->bindParam(':userId', $_SESSION['user_id']);

$enrollmentStatement->bindParam(':name', $enrollmentName);

$enrollmentStatement->bindParam(':email', $enrollmentEmail);

$enrollmentStatement->bindParam(':reason', $enrollmentReason);

$enrollmentStatement->execute();

header("Location: /users/course1.php?course_id=" . $course_id);

exit();

} catch (PDOException $enrollmentException) {

error_log("Failed to process course enrollment: " . $enrollmentException->getMessage());

die("Error during enrollment. Please try again.");

}

}



try {

$memberCountStatement = $pdo->prepare("SELECT COUNT(user_id) as member_count FROM enrollments WHERE course_id = :courseId");

$memberCountStatement->bindParam(':courseId', $course_id);

$memberCountStatement->execute();

$memberCountResult = $memberCountStatement->fetch(PDO::FETCH_ASSOC);

$totalMembers = $memberCountResult['member_count'];

} catch (PDOException $memberCountException) {

error_log("Failed to fetch member count: " . $memberCountException->getMessage());

$totalMembers = 0;

}

?>



<!DOCTYPE html>

<html lang="en">

<head>
	
	<?php
 try {
  $stmtEnrolledCourses = $pdo->prepare("
   SELECT c.id AS course_id, c.title, c.image_url
   FROM courses c
   INNER JOIN enrollments e ON c.id = e.course_id
   WHERE e.user_id = :user_id
  ");
  $stmtEnrolledCourses->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmtEnrolledCourses->execute();
  $enrolledCoursesDropdown = $stmtEnrolledCourses->fetchAll(PDO::FETCH_ASSOC);
 } catch (PDOException $e) {
  error_log("Database error fetching enrolled courses for dropdown: " . $e->getMessage());
  $enrolledCoursesDropdown = [];
 }
 ?>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Course Enrollment</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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

.video-container iframe { width: 100%; height: 515px; }

.image-container img { width: 100%; height: 515px; }

.course-title { color: white; font-size: 1.5em; margin-bottom: 10px; position: absolute; top: 10px; left: 10px; }

.media-switch { display: flex; justify-content: left; margin-top: 10px; }

.media-switch img { width: 50px; height: 40px; border-radius: 5px; cursor: pointer; margin: 0 5px; }

.course-description { color: white; margin-top: 20px; text-align: left; font-size: 1.2em; }

.header { background-color: #1e1e1e; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #555; position: relative;}

.logout-button-left { background-color: #333; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }

.profile-section { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); }

.profile-button { background: none; border: none; cursor: pointer; display: flex; align-items: center; }

.profile-pic { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; background-color: #1e1e1e; }

.dropdown-content { display: none; position: absolute; background-color: #333; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 5px; }

.dropdown-content a { color: white; padding: 12px 16px; text-decoration: none; display: block; }

.dropdown-content a:hover { background-color: #555; }

.show { display: block; }

#enroll-popup { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #1e1e1e; border-radius: 15px; border: 2px solid #555; z-index: 1000; width: 500px; text-align: center; font-family: sans-serif; display: flex; flex-direction: column; }

#enroll-popup > div:first-child { background-color: #2a2a2a; padding: 20px; border-top-left-radius: 15px; border-top-right-radius: 15px; }

#enroll-popup > div:last-child { background-color: #3a3a3a; padding: 20px; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; }

#enroll-popup img { width: 140px; height: 140px; border-radius: 5px; margin-bottom: 10px; }

#enroll-popup form { display: flex; flex-direction: column; }

#enroll-popup form input, #enroll-popup form textarea { background-color: #252525; color: white; padding: 10px; margin-bottom: 15px; border: none; border-radius: 5px; font-size: 16px; }

#enroll-popup button { background-color: grey; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; width: 100%; }

.header-course-info { display: flex; align-items: center; }

.header-course-info img { width: 60px; height: 60px; margin-right: 10px; border-radius: 5px; }

.header-course-info span { color: white; font-size: 1em; margin-right: 20px; }

.header-course-arrows { display: flex; flex-direction: column; margin-left: 10px; }

.header-course-arrows {
  display: flex;
  flex-direction: column;
  align-items: center; /* Center the buttons */
  margin-left: 10px;
  position: relative; /* For positioning the dropdown */
 }

 .arrow-button {
  background: none;
  border: none;
  padding: 5px;
  cursor: pointer;
  color: lightgrey;
  font-size: 1em;
 }

 .arrow-button:hover {
  color: white;
 }

 .course-dropdown-content {
  display: none;
  position: absolute;
  top: 100%; /* Position below the arrows */
  left: 50%;
  transform: translateX(-50%);
  background-color: #1e1e1e;
  border: 1px solid #555;
  border-radius: 5px;
  padding: 10px;
  z-index: 100;
  min-width: 200px; /* Adjust as needed */
 }

 .course-dropdown-content.show {
  display: block;
 }

 .dropdown-section-title {
  color: grey;
  padding-bottom: 5px;
  margin-bottom: 5px;
  border-bottom: 1px solid #333;
 }

 .dropdown-item {
  display: flex;
  align-items: center;
  color: white;
  padding: 8px 10px;
  text-decoration: none;
 }

 .dropdown-item:hover {
  background-color: #333;
 }

 .dropdown-item-image {
  width: 30px;
  height: 30px;
  border-radius: 5px;
  margin-right: 10px;
  object-fit: cover;
 }

 .dropdown-separator {
  border-top: 1px solid #333;
  margin: 8px 0;
 }

 .discover-communities, .create-course {
  /* Add any specific styling if needed, like icons */
 }

 .discover-communities i, .create-course i {
  margin-right: 10px;
 }

.enroll-container { border: 1px solid #555; width: 30%; margin-top: 20px; margin-left: auto; }

.enroll-container-content { padding: 20px; }

.enroll-container-members { display: flex; align-items: center; justify-content: center; margin-top: 10px; color: white; }

.enroll-container-members img { width: 20px; height: 20px; margin-right: 5px; }

.powered-by { text-align: center; color: lightgrey; margin-top: 10px; }

.logo-container { display: flex; justify-content: center; align-items: center; margin-top: 10px; }

.logo-container img { width: 20px; height: 20px; margin: 0 5px; }

.enroll-now-section { display: flex; justify-content: space-between; align-items: flex-start; margin-top: 20px; }

.enroll-now-section .media-container { width: 60%; }

.enroll-now-section .enroll-container { width: 35%; margin-left: 20px; }

.price-tag-logo { width: 20px; height: 20px; margin-right: 5px; }

.members-logo { width: 20px; height: 20px; margin-right: 5px; }

/* Introduction section styling */

.intro-container {

background-color: #1a1a1a;

color: white;

padding: 20px;

border-radius: 8px;

box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);

position: relative;

}

.intro-title {

font-size: 28px;

font-weight: bold;

margin-bottom: 20px;

position: absolute;

top: 20px;

left: 20px;

}

.intro-description {

margin-top: 250px;

line-height: 1.8;

padding: 10px;

}

.intro-video-image {

width: 100%;

height: 250px;

object-fit: cover;

border-radius: 8px;

}

.nav-thumbnails {

display: flex;

justify-content: left;

margin-top: 10px;

}

.nav-thumbnails img {

width: 50px;

height: 40px;

border-radius: 6px;

cursor: pointer;

margin: 0 5px;

}

.logos {

display: flex;

margin-top: 10px;

}

.logos img {

width: 20px;

height: 20px;

margin-right: 5px;

}



</style>

<script>

function toggleCourseDropdown() {
  document.getElementById("courseDropdown").classList.toggle("show");
 }

 window.onclick = function(event) {
  if (!event.target.matches('.profile-button') && !event.target.matches('.profile-button *') && !event.target.matches('.arrow-button') && !event.target.matches('.arrow-button *')) {
   var dropdowns = document.getElementsByClassName("dropdown-content");
   for (var i = 0; i < dropdowns.length; i++) {
    var openDropdown = dropdowns[i];
    if (openDropdown.classList.contains('show')) {
     openDropdown.classList.remove('show');
    }
   }
   var courseDropdown = document.getElementById("courseDropdown");
   if (courseDropdown.classList.contains('show')) {
    courseDropdown.classList.remove('show');
   }
  }
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

</head>

<body>

<header class="header">

<div class="header-course-info">

<img src="<?php echo htmlspecialchars($courseImageUrl); ?>" alt="Course Image">

<span><?php echo htmlspecialchars($courseTitle); ?></span>

<div class="header-course-arrows">
  <button class="arrow-button" onclick="toggleCourseDropdown()">
   <i class="fas fa-chevron-up"></i>
  </button>
  <button class="arrow-button" onclick="toggleCourseDropdown()">
   <i class="fas fa-chevron-down"></i>
  </button>
  <div id="courseDropdown" class="course-dropdown-content">
   <?php if (!empty($enrolledCoursesDropdown)): ?>
    <div class="dropdown-section-title">My Courses</div>
    <?php foreach ($enrolledCoursesDropdown as $enrolledCourse): ?>
     <a href="/users/course1.php?course_id=<?php echo htmlspecialchars($enrolledCourse['course_id']); ?>" class="dropdown-item">
      <img src="<?php echo htmlspecialchars($enrolledCourse['image_url']); ?>" alt="Course Image" class="dropdown-item-image">
      <span><?php echo htmlspecialchars($enrolledCourse['title']); ?></span>
     </a>
    <?php endforeach; ?>
   <?php else: ?>
    <div class="dropdown-item">No enrolled courses</div>
   <?php endif; ?>
   <div class="dropdown-separator"></div>
   <a href="/users/landing.php" class="dropdown-item discover-communities">
    <i class="fas fa-globe"></i>
    <span>Discover Communities</span>
   </a>
   <a href="/createCourse.php" class="dropdown-item create-course">
    <i class="fas fa-plus-circle"></i>
    <span>Create Course</span>
   </a>
  </div>
 </div>

</div>

<div class="profile-section">

<button class="profile-button" onclick="toggleDropdown()">

<?php if ($profilePicture): ?>

<img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-pic">

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

<h1 class="course-title"><?php echo htmlspecialchars($courseTitle); ?></h1>

<div id="video-container" class="video-container">

<?php if ($youtubeVideoId): ?>

<iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($youtubeVideoId); ?>" frameborder="0" allowfullscreen></iframe>

<?php else: ?>

<p>Video not available</p>

<?php endif; ?>

</div>

<div id="image-container" class="image-container" style="display: none;">

<img src="<?php echo htmlspecialchars($imageUrlIntro); ?>" alt="Course Image">

</div>

<div class="nav-thumbnails">

<?php if ($youtubeThumbnailUrl): ?>

<img src="<?php echo htmlspecialchars($youtubeThumbnailUrl); ?>" alt="Video Thumbnail" onclick="showVideo()">

<?php endif; ?>

<?php if ($imageUrlIntro): ?>

<img src="<?php echo htmlspecialchars($imageUrlIntro); ?>" alt="Course Image" onclick="showImage()">

<?php endif; ?>

</div>

<div class="logos">

<img src="images/members.png" alt="Members Logo" class="members-logo">

<span><?php echo htmlspecialchars($totalMembers); ?> Members</span>

<img src="images/price.png" alt="Price Tag Logo" class="price-tag-logo">

<span>Free</span>

</div>

<div class="course-description">

<h3>Introduction</h3>

<p><?php echo nl2br(htmlspecialchars($courseDescriptionIntro)); ?></p>

</div>

</div>

<div class="enroll-container">

<div class="enroll-container-content">

<img src="<?php echo htmlspecialchars($courseImageUrl); ?>" alt="Course Image" style="width: 100%; max-width: 100%; border-radius: 10px; margin-bottom: 10px;">

<h2 style="color: white; text-align: center; margin-bottom: 5px;"><?php echo htmlspecialchars($courseTitle); ?></h2>

<p style="color: white; text-align: center; margin-bottom: 20px;"><?php echo htmlspecialchars($courseFullDescription); ?></p>

<button onclick="showEnrollPopup()" style="background-color: grey; color: black; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 100%;">Enroll Now</button>

</div>

<div class="logo-container">

<img src="images/members.png" alt="Members Logo" class="members-logo">

<span><?php echo htmlspecialchars($totalMembers); ?> Members</span>

</div>

</div>

</div>

</div>

<div class="powered-by">

Powered by Self Elevate

</div>

<div id="enroll-popup">

<div>

<img src="<?php echo htmlspecialchars($courseImageUrl); ?>" alt="Course Image">

<h2 style="color: white; text-align: center; margin-bottom: 10px;"><?php echo htmlspecialchars($courseTitle); ?></h2>

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