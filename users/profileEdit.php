<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES['profile_pic']['name']);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file);
        
        $updateQuery = "UPDATE profile SET profile_pic = ? WHERE user_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $file_name, $user_id);
        $stmt->execute();
    }
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/profileeditStyle/style.css">
</head>
<body>
    <div class="profile-edit-container">
        <h1>Edit Profile</h1>
        <form action="profileEdit.php" method="POST" enctype="multipart/form-data">
            <label for="profile_pic">Upload New Profile Picture:</label>
            <input type="file" name="profile_pic" id="profile_pic">
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>