<?php
session_start();

// If already logged in, redirect to landing.php
if (isset($_SESSION['user_id'])) {
   header("Location: /users/landing.php");
 // Updated path to "landing.php"
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php
        // Display error messages if redirected back with a query parameter
        if (isset($_GET['error'])) {
            echo "<div class='error'>" . htmlspecialchars($_GET['error']) . "</div>";
        }
        ?>
        <form action="loginhandler.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <button type="submit" class="login-button">Log In</button>
            <button type="button" class="signup-button" onclick="window.location.href='signup.php'">Sign Up</button>
        </form>
    </div>
</body>
</html>
