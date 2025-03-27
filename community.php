<?php
require_once 'file:///Macintosh HD/Users/370593/Desktop/DesktopGitHubClassV1/users/db.php';

// Ensure course_id is provided
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("Course ID is required.");
}

$course_id = intval($_GET['course_id']); // Ensure it's an integer

// Get total number of members enrolled in the specific course
$stmt = $pdo->prepare("SELECT COUNT(*) as total_members FROM enrollments WHERE course_id = :course_id");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$total_members = $stmt->fetch(PDO::FETCH_ASSOC)['total_members'];

// Get users enrolled in the specific course
$stmt = $pdo->prepare("SELECT user_id, name, email, reason, enrollment_date FROM enrollments WHERE course_id = :course_id ORDER BY enrollment_date DESC");
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community</title>
    <link rel="stylesheet" href="/communityFolder/style.css">
</head>
<body>
    <div class="community-container">
        <div class="header">
            <button class="members-btn">Members <?php echo $total_members; ?></button>
        </div>
        <div class="members-list">
            <?php if ($total_members > 0): ?>
                <?php foreach ($members as $index => $member): ?>
                    <div class="member-card">
                        <div class="member-rank"> <?php echo $index + 1; ?> </div>
                        <div class="member-info">
                            <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                            <p>Email: <?php echo htmlspecialchars($member['email']); ?></p>
                            <p>Reason: <?php echo htmlspecialchars($member['reason']); ?></p>
                            <p>Joined: <?php echo date('M d, Y', strtotime($member['enrollment_date'])); ?></p>
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