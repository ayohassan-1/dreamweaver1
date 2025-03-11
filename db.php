<?php
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

// Function to retrieve all enrolled users
function getEnrolledUsers($pdo) {
    $stmt = $pdo->prepare("SELECT e.user_id, e.name, e.email, e.enrollment_date 
                           FROM enrollments e 
                           INNER JOIN users u ON e.user_id = u.uid");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to add a new user with plain-text password
function addUser($pdo, $username, $password, $email, $role) {
    $stmt = $pdo->prepare("INSERT INTO users (uName, pWord, email, role, regDate) 
                           VALUES (:uName, :pWord, :email, :role, NOW())");
    $stmt->bindParam(':uName', $username);
    $stmt->bindParam(':pWord', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    return $stmt->execute();
}

// Function to add a course to the database
function addCourse($pdo, $title, $description, $youtubeLink, $imageUrl) {
    $stmt = $pdo->prepare("INSERT INTO courses (title, description, youtube_link, image_url, created_at)
                           VALUES (:title, :description, :youtubeLink, :imageUrl, NOW())");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':youtubeLink', $youtubeLink);
    $stmt->bindParam(':imageUrl', $imageUrl);
    return $stmt->execute();
}

// Function to fetch all courses from the database
function getAllCourses($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM courses ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>