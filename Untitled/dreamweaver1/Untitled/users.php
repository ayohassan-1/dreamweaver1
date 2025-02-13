<?php
include 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to retrieve all user details
function getAllUserDetails($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve all user details
$users = getAllUserDetails($pdo);

if ($users) {
    echo "List of users:<br>";
    foreach ($users as $user) {
        echo "UID: " . htmlspecialchars($user['uid']) . "<br>";
        echo "Username: " . htmlspecialchars($user['uName']) . "<br>";
        echo "Password (hashed): " . htmlspecialchars($user['pWord']) . "<br>";
        echo "Email: " . htmlspecialchars($user['email']) . "<br>";
        echo "Registration Date: " . htmlspecialchars($user['regDate']) . "<br><br>";
    }
} else {
    echo "No users found in the database.<br>";
}
?>
