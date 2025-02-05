<?php
require_once '../classrooms/db.php';

if (!isset($_GET['classroom_id'])) {
    echo "Invalid classroom ID.";
    exit();
}

$classroomId = $_GET['classroom_id'];

// Fetch classroom details from the database
$query = $pdo->prepare("SELECT * FROM classrooms WHERE id = :id");
$query->execute(['id' => $classroomId]);
$classroom = $query->fetch();

if (!$classroom) {
    echo "Classroom not found.";
    exit();
}

// Fetch course parts (video links or other content) related to the classroom
$queryParts = $pdo->prepare("SELECT * FROM classroom_parts WHERE classroom_id = :classroom_id");
$queryParts->execute(['classroom_id' => $classroomId]);
$parts = $queryParts->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($classroom['title']); ?></title>
    <link rel="stylesheet" href="/users/style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($classroom['title']); ?></h1>
        <img src="<?php echo htmlspecialchars($classroom['image_path']); ?>" alt="Classroom Image">
        <p><?php echo htmlspecialchars($classroom['description']); ?></p>

        <div class="parts-navigation">
            <h2>Parts:</h2>
            <?php foreach ($parts as $part): ?>
                <div class="part">
                    <h3><a href=""><?php echo htmlspecialchars($part['title']); ?></a></h3>
                    <p><?php echo htmlspecialchars($part['description']); ?></p>
                    <a href="<?php echo htmlspecialchars($part['video_link']); ?>">Watch Video</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
