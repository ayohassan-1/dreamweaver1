<?php
session_start();

// Include the database connection
require_once '../db.php';

// Debugging mode (set to true for detailed errors)
$debug = true;

try {
    // Check if course_id is provided in the URL
    if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
        throw new Exception("Error: Course ID is missing from the URL.");
    }

    $course_id = intval($_GET['course_id']);

    // Fetch course details including YouTube link
    $stmt = $pdo->prepare("SELECT youtubeLink FROM courses WHERE id = :course_id");
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        throw new Exception("Error: No course found with ID {$course_id}.");
    }

    // Extract the YouTube video ID from the URL
    preg_match("/v=([a-zA-Z0-9_-]+)/", $course['youtubeLink'], $matches);
    $video_id = $matches[1] ?? null;

    if (!$video_id) {
        throw new Exception("Error: Invalid YouTube link.");
    }
} catch (Exception $e) {
    if ($debug) {
        echo "<h3>Debug Error:</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>";
        print_r($e->getTrace());
        echo "</pre>";
    } else {
        echo "<p>Oops, something went wrong! Please try again later.</p>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom</title>
    <link rel="stylesheet" href="/classrooms/style.css">
</head>
<body>
    <div class="container">
        <h1>Classroom Video</h1>
        <div class="video-container">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video_id); ?>" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</body>
</html>
