<?php
$host = 'srv1536.hstgr.io';
$dbname = 'u237055794_Elevate';
$username = 'u237055794_Self';
$password = 'B5Z[2s*b]';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    exit("Database connection failed."); // Stop further execution if connection fails
}

// Function to retrieve all user emails
function getAllUserEmails($pdo) {
    $stmt = $pdo->prepare("SELECT email FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to add a new user with a hashed password
function addUser($pdo, $username, $password, $email, $role, $profile_pic) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (uName, pWord, email, role, profile_pic, regDate) 
                           VALUES (:uName, :pWord, :email, :role, :profile_pic, NOW())");
    $stmt->bindParam(':uName', $username);
    $stmt->bindParam(':pWord', $hashedPassword);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':profile_pic', $profile_pic);
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

// Function to check if a user is already enrolled in a specific course
function isUserEnrolled($pdo, $course_id, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = :course_id AND email = :email");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// Function to enroll a user in a course
function enrollUserInCourse($pdo, $course_id, $email, $reason) {
    if (isUserEnrolled($pdo, $course_id, $email)) {
        return false;
    }
    
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, email, enrollment_date, reason) VALUES (?, ?, NOW(), ?)");
    return $stmt->execute([$course_id, $email, $reason]);
}

// Function to count enrolled users in a course
function countEnrolledUsers($pdo, $course_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM enrollments WHERE course_id = :course_id");
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Function to get all enrolled users with their profile pictures
function getEnrolledUsers($pdo, $course_id) {
    $stmt = $pdo->prepare("
        SELECT u.uName AS name, u.email, u.profile_pic, e.enrollment_date, e.reason 
        FROM enrollments e
        JOIN users u ON e.email = u.email
        WHERE e.course_id = :course_id
        ORDER BY e.enrollment_date DESC
    ");
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
