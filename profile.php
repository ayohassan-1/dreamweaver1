<?php
session_start();
include '../config.php'; // Adjust path as needed

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT u.uName, u.email, p.profile_pic FROM users u LEFT JOIN profile p ON u.uid = p.user_id WHERE u.uid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="profilestyle/style.css">
</head>
<body>
    <div class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['uName']); ?></h1>
        <img src="<?php echo $user['profile_pic'] ? '../uploads/' . $user['profile_pic'] : '../images/default.png'; ?>" alt="Profile Picture" class="profile-pic">
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <a href="profileEdit.php">Edit Profile</a>
    </div>
</body>
</html>