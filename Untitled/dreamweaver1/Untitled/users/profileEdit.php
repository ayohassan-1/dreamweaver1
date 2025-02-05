<?php
include 'db.php'; // Include your db.php file
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['newUsername'];
    $userId = $_SESSION['user_id']; // Assuming 'user_id' is set correctly during login

    // Basic input validation
    if (empty($newUsername)) {
        $error = "Username cannot be empty.";
    } else {
        try {
            // Use prepared statement with $pdo from db.php and correct column name
            $stmt = $pdo->prepare("UPDATE users SET uname = ? WHERE uID = ?");
            $stmt->execute([$newUsername, $userId]);

            $_SESSION['uname'] = $newUsername; // Update session variable
            header("Location: /profile.php"); // Redirect back to profile
            exit();
        } catch (PDOException $e) {
            $error = "Error updating username: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="newUsername">New Username:</label>
                <input type="text" id="newUsername" name="newUsername" value="<?php echo htmlspecialchars($_SESSION['uname']); ?>">
            </div>
            <button type="submit">Save Changes</button>
        </form>
        <div class="navigation">
            <a href="/profile.php" class="button">Back to Profile</a>
        </div>
    </div>
</body>
</html>