<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $userId = $_SESSION['user_id'];
    $uploadDir = 'uploads/profile_pics/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['profile_pic'];
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $uploadDir . $fileName;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (in_array($file['type'], $allowedTypes) && $file['size'] <= 2 * 1024 * 1024) {
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE uID = ?");
            $stmt->execute([$targetFile, $userId]);
            $_SESSION['profile_pic'] = $targetFile;
            header("Location: /profile.php");
            exit();
        } else {
            $error = "Failed to upload file.";
        }
    } else {
        $error = "Invalid file type or file too large.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Upload Profile Picture</h1>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="uploadProfilePic.php" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_pic" accept="image/*" required>
            <button type="submit">Upload</button>
        </form>
        <a href="/profile.php">Back to Profile</a>
    </div>
</body>
</html>
