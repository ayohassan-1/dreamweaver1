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

// Function to add a new user with hashed password
function addUser($pdo, $username, $password, $email, $role) {
    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (uName, pWord, email, role, regDate) 
                           VALUES (:uName, :pWord, :email, :role, NOW())");
    $stmt->bindParam(':uName', $username);
    $stmt->bindParam(':pWord', $hashedPassword); // Store the hashed password
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
