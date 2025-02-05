<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Ensure no output is sent before the header function
    header("Location: /login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="users/style.css">
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        <p>Here is your information:</p>

        <div class="user-info">
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['uname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Registration Date:</strong> <?php echo htmlspecialchars($_SESSION['regDate']); ?></p>
        </div>

        <div class="navigation">
            <a href="/users/landing.php" class="button">Back to Home</a>
            <a href="users/profileEdit.php" class="button">Edit Profile</a> </div>

        <div class="logout-section">
            <form action="/users/logout.php" method="post">
                <button type="submit" class="logout-button">Log Out</button>
            </form>
        </div>
    </div>
</body>

