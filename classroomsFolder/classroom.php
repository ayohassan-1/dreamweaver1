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

require_once '../db.php';

// Fetch the course details
try {
    $stmt = $pdo->prepare("SELECT title, videos FROM courses WHERE id = ?");
    $stmt->execute([$_GET['course_id']]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        die("Course not found.");
    }

    // Convert YouTube link to embeddable format
    function getYouTubeEmbedUrl($url) {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\\w-]+)/', $url, $matches);
        return isset($matches[1]) ? "https://www.youtube.com/embed/" . $matches[1] : null;
    }

    // Decode the JSON string into an array of video details
    $videos = json_decode($course['videos'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decoding JSON for course videos: " . json_last_error_msg());
        $videos = []; // Ensure it's an empty array if decoding fails
    }

    // Ensure it's an array
    if (!is_array($videos)) {
        $videos = [];
    }

    // Organize videos by section
    $sections = [];
    foreach ($videos as $videoData) {
        $sectionName = $videoData['sectionName'];
        $sections[$sectionName][] = $videoData['video'];
    }

    // Get the selected section
    $selectedSection = isset($_GET['section']) ? $_GET['section'] : null;

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
    <link rel="stylesheet" href="style.css">
 <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #2c2c2c;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
    }
    .header {
        width: 100%;
        background: #222222;
        padding: 15px 0;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .back-button {
        display: inline-block;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        transition: 0.3s ease-in-out;
        border: 2px solid transparent;
    }
    .back-button:hover {
        background: gray;
    }
    .classroom-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        max-width: 900px;
        width: 100%;
        margin: 30px auto;
        padding: 20px;
        background: #222222;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        border-radius: 8px;
    }
    .course-title {
        font-size: 2em;
        color: #ffffff;
        margin-bottom: 20px;
        text-align: center;
    }
    .section-link {
        display: inline-block;
        padding: 10px 20px;
        margin: 10px;
        background: #333333;
        color: #ffffff;
        text-decoration: none;
        border-radius: 5px;
        min-width: 150px; /* Set a minimum width to match the current size of section 2 */
        text-align: center; /* Center the text within the button */
    }
    .section-link:hover {
        background: #444444;
    }
    .video-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 100%;
        width: 100%;
        margin-bottom: 30px;
        background: #333333;
        padding: 15px;
        border-radius: 8px;
    }
    iframe {
        width: 100%;
        height: 350px;
        max-width: 800px;
        border-radius: 25px;
    }
    .video-title {
        font-size: 1.5em;
        margin-bottom: 10px;
        color: #ffffff;
        text-align: center;
    }
    .video-description {
        font-size: 1em;
        color: #bbbbbb;
        margin-top: 10px;
        text-align: center;
    }
    @media (max-width: 768px) {
        iframe {
            width: 100%;
            height: 200px;
        }
    }
</style>
</head>
<body>
    <header class="header">
        <a href="/users/landing.php" class="back-button">Back to Courses</a>
    </header>

    <main class="classroom-container">
        <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>

        <?php if (empty($sections)): ?>
            <p>No videos available for this course.</p>
        <?php else: ?>
            <?php foreach ($sections as $sectionTitle => $sectionVideos): ?>
                <a href="?course_id=<?php echo $_GET['course_id']; ?>&section=<?php echo urlencode($sectionTitle); ?>" class="section-link">
                    <?php echo htmlspecialchars($sectionTitle); ?>
                </a>
            <?php endforeach; ?>

            <?php if ($selectedSection && isset($sections[$selectedSection])): ?>
                <?php $video = $sections[$selectedSection][0]; // Display the first video in the selected section ?>
                <?php $embedUrl = getYouTubeEmbedUrl($video['link']); ?>
                <?php if ($embedUrl): ?>
                    <div class="video-wrapper">
                        <h2 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h2>
                        <iframe src="<?php echo $embedUrl; ?>" frameborder="0" allowfullscreen></iframe>
                        <p class="video-description"><?php echo htmlspecialchars($video['description']); ?></p>
                    </div>
                <?php else: ?>
                    <p>Invalid YouTube link: <?php echo htmlspecialchars($video['link']); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>