<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="/users/style.css">
</head>
<body>
    <div class="register-container">
        <h1>Sign Up</h1>
        <form action="../signuphandler.php" method="post">
            <!-- Username field -->
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>

            <!-- Email field -->
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <!-- Password field -->
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <!-- Submit button -->
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>


H


