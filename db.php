<?php
// db.php
$host = 'srv1536.hstgr.io';
$dbname = 'u237055794_Elevate';
$username = 'u237055794_Self';
$password = 'B5Z[2s*b]';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    exit("Database connection failed.");
}

function getEnrolledUsers($pdo) {
    $stmt = $pdo->prepare("SELECT e.user_id, e.name, e.email, e.enrollment_date
                                FROM enrollments e
                                INNER JOIN users u ON e.user_id = u.uid");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addUser($pdo, $username, $password, $email, $role) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (uName, pWord, email, role, regDate)
                                VALUES (:uName, :pWord, :email, :role, NOW())");
    $stmt->bindParam(':uName', $username);
    $stmt->bindParam(':pWord', $hashedPassword);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    return $stmt->execute();
}

function addCourse($pdo, $title, $description, $imageUrl, $about, $videosJson, $introductionVideoUrl = null, $introductionDescription = null, $introductionImageUrl = null) {
    $stmt = $pdo->prepare("INSERT INTO courses (title, description, image_url, about, videos, introduction_video_url, introduction_description, introduction_image_url)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $imageUrl, $about, $videosJson, $introductionVideoUrl, $introductionDescription, $introductionImageUrl]);
    return $pdo->lastInsertId();
}

function getAllCourses($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM courses ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCourseById($pdo, $courseId) {
    $stmt = $pdo->prepare("SELECT id, title, description, image_url, about, videos, introduction_video_url, introduction_description, introduction_image_url
                            FROM courses WHERE id = ?");
    $stmt->execute([$courseId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function decodeVideos($videosJson) {
    $videos = json_decode($videosJson, true);
    return is_array($videos) ? $videos : [];
}

function enrollUserInCourse($pdo, $courseId, $userId, $name, $email, $reason) {
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, user_id, name, email, reason, enrollment_date)
                                    VALUES (:course_id, :user_id, :name, :email, :reason, NOW())");
    $stmt->bindParam(':course_id', $courseId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':reason', $reason);
    return $stmt->execute();
}

function getUserProfilePic($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE uid = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user['profile_pic'] ?? '';
}

function getCourseIntroduction($pdo, $courseId) {
    $stmt = $pdo->prepare("SELECT title, introduction_video_url, introduction_description, introduction_image_url FROM courses WHERE id = :course_id");
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>