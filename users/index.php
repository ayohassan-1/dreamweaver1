<?php
session_start();

// Check if the user is logged in by verifying if `user_id` is set
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="users/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <p>You are logged in.</p>

        <!-- User Information Section -->
        <div class="user-info">
            <h2>Your Information</h2>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['uname']); ?></p>
            <p><strong>Registration Date:</strong> <?php echo htmls

	// Hasan Haidar 11/09/08