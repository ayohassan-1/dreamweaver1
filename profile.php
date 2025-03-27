<?php
session_start();

// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require_once 'users/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
try {
    $stmt = $pdo->prepare("SELECT uName, email, regDate, profile_pic FROM users WHERE uid = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Error: User not found.");
    }
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage()); // Show actual error
}

$profilePic = $user['profile_pic']; // Store profile picture URL
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="/users/profilestyle/style.css">
    <style>
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%; /* Make the image circular */
            object-fit: cover; /* Ensure the image covers the area without distortion */
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['uName']); ?></h1>
        
        <!-- Display profile picture if it exists -->
        <?php if ($profilePic): ?>
            <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-img">
        <?php else: ?>
            <img src="/path/to/default/pic.jpg" alt="Default Profile Picture" class="profile-img">
        <?php endif; ?>
        
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Registration Date: <?php echo htmlspecialchars($user['regDate']); ?></p>

        <a href="/users/profileEdit.php">Edit Profile</a>
        <a href="/users/landing.php" class="back-home-button">Back to Home</a>
    </div>
</body>
</html>
