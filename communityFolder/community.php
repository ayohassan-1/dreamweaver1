<?php
include 'db.php';

// Fetch enrolled users
$enrolledUsers = getEnrolledUsers($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Page</title>
    <link rel="stylesheet" type="text/css" href="communityFolder/style.css">
</head>
<body>
    <div class="container">
        <h1>Community Members</h1>
        <div class="community-list">
            <?php if (!empty($enrolledUsers)) : ?>
                <ul>
                    <?php foreach ($enrolledUsers as $user) : ?>
                        <li>
                            <strong>Username:</strong> <?php echo htmlspecialchars($user['uName']); ?> <br>
                            <strong>UID:</strong> <?php echo htmlspecialchars($user['uid']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No enrolled users found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
