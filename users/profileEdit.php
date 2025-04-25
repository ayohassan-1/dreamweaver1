<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current user information
try {
    $query = "SELECT uName, profile_pic FROM users WHERE uid = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_username = $user['uName'];
    $current_profile_pic = $user['profile_pic'];
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $current_username = "";
    $current_profile_pic = "";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['username'])) {
        try {
            $new_username = $_POST['username'];
            $updateQuery = "UPDATE users SET uName = :username WHERE uid = :user_id";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $_SESSION['success_message'] = "Username updated successfully!";
        } catch (PDOException $e) {
            error_log("Error updating username: " . $e->getMessage());
            $_SESSION['error_message'] = "Failed to update username. Please try again.";
        }
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $upload_dir = '../uploads/profile_pics/';
        $file_name = basename($_FILES['profile_pic']['name']);
        $file_path = $upload_dir . $file_name;
        $image_file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $file_path)) {
                try {
                    $updatePicQuery = "UPDATE users SET profile_pic = :profile_pic WHERE uid = :user_id";
                    $stmt = $pdo->prepare($updatePicQuery);
                    $stmt->bindParam(':profile_pic', $file_path);
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->execute();
                    $_SESSION['success_message'] = "Profile picture updated successfully!";
                } catch (PDOException $e) {
                    error_log("Error updating profile picture: " . $e->getMessage());
                    $_SESSION['error_message'] = "Failed to update profile picture. Please try again.";
                }
            } else {
                $_SESSION['error_message'] = "Error uploading the image. Please try again.";
            }
        } else {
            $_SESSION['error_message'] = "Only image files are allowed (jpg, jpeg, png, gif).";
        }
    }
    
    header("Location: /profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/users/profileeditStyle/style.css">
</head>
	
	    <header class="header" id="header">
        <button class="logout-button-left" id="logout-button" onclick="location.href='/logout.php'">Log Out</button>
        <h1 class="header-title" id="header-title">Profile Edit</h1>
    </header>
	
<body class="profile-edit-body">

    <div class="profile-edit-container" id="profile-edit-container">
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="error-message" id="error-message">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="success-message" id="success-message">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <form action="profileEdit.php" method="POST" enctype="multipart/form-data" class="profile-edit-form" id="profile-edit-form">
            <div class="form-group" id="username-group">
                <label for="username">Change Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($current_username); ?>">
            </div>

            <div class="form-group" id="profile-pic-group">
                <label for="profile_pic">Change Profile Picture:</label>
                <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
            </div>

            <div class="button-container" id="button-container">
                <button type="submit" class="save-button" id="save-button">Save Changes</button>
                <button type="button" class="cancel-button" id="cancel-button" onclick="location.href='/profile.php'">Cancel</button>
            </div>
        </form>
    </div>

</body>
</html>
