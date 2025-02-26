<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure course_id is provided
if (!isset($_GET['course_id'])) {
    die("Course not found.");
}

// Correct the path to db.php
require_once 'db.php';

// Fetch the course details
try {
    $stmt = $pdo->prepare("SELECT title, description, youtube_link FROM courses WHERE id = ?");
    $stmt->execute([$_GET['course_id']]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        die("Course not found.");
    }

    // Extract YouTube link
    $youtube_link = $course['youtube_link'];

    // Convert YouTube link to embeddable format if necessary
    function getYouTubeEmbedUrl($url) {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]+)/', $url, $matches);
        return isset($matches[1]) ? "https://www.youtube.com/embed/" . $matches[1] : null;
    }

    $embed_url = getYouTubeEmbedUrl($youtube_link);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Error retrieving course details.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - Classroom</title>
    <link rel="stylesheet" href="/users/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="header">
        <a href="landing.php" class="back-button">Back to Courses</a>
    </header>

    <main class="classroom-container">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p><?php echo htmlspecialchars($course['description']); ?></p>

        <?php if ($embed_url): ?>
            <div class="video-container">
                <iframe width="560" height="315" src="<?php echo htmlspecialchars($embed_url); ?>" frameborder="0" allowfullscreen></iframe>
            </div>
        <?php else: ?>
            <p>No video available for this course.</p>
        <?php endif; ?>
    </main>
</body>
</html>
