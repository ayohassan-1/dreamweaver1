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

// Function to add a new user (uses hashed password)
function addUser($pdo, $username, $password, $email) {
    $stmt = $pdo->prepare("INSERT INTO users (uName, pWord, email, regDate) VALUES (:uName, :pWord, :email, NOW())");
    $stmt->bindParam(':uName', $username);
    $stmt->bindParam(':pWord', password_hash($password, PASSWORD_DEFAULT)); // Hashing the password
    $stmt->bindParam(':email', $email);
    return $stmt->execute();
}
?>
