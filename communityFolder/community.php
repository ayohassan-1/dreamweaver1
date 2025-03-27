<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../users/db.php';

// Ensure course_id is provided
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("Course ID is required.");
}

$course_id = intval($_GET['course_id']); // Ensure it's an integer

// Get total number of enrolled members
try {
    $total_members = countEnrolledUsers($pdo, $course_id);
    $members = getEnrolledUsers($pdo, $course_id);
} catch (Exception $e) {
    die("Error retrieving members: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community</title>
    <link rel="stylesheet" type="text/css" href="/communityFolder/style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="header">
    <div class="nav-buttons">
        <a href="/users/landing.php">Self-Elevate</a>
    </div>
    <div class="nav-buttons center-button">
        <a href="/classroomsFolder/classroom.php?course_id=<?php echo $course_id; ?>">Classroom</a>
    </div>
    <div class="nav-buttons">
        <a href="/communityFolder/community.php?course_id=<?php echo $course_id; ?>">Community</a>
    </div>
</div>

<div class="community-container">
    <div class="header">
        <button class="members-btn">Members <?php echo htmlspecialchars($total_members); ?></button>
    </div>
    <div class="members-list">
        <?php if ($total_members > 0): ?>
            <?php foreach ($members as $member): ?>
                <div class="member-card">
                    <div class="member-info">
                        <h3>
                            <?php echo htmlspecialchars($member['name']); ?>
                            <?php if (!empty($member['profile_pic'])): ?>
                                <div class="profile-pic-container">
                                    <img src="<?php echo htmlspecialchars($member['profile_pic']); ?>" alt="Profile Picture">
                                </div>
                            <?php endif; ?>
                        </h3>
                        <p>Email: <?php echo htmlspecialchars($member['email']); ?></p>
                        <p>Reason: <?php echo htmlspecialchars($member['reason'] ?? 'N/A'); ?></p>
                        <p class="joined">Joined: <?php echo date('M d, Y', strtotime($member['enrollment_date'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No members enrolled yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>