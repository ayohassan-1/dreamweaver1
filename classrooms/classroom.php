<?php
// Start the session and include the database connection
session_start();
require_once '../db.php';

// Debugging mode (set to true to see detailed errors)
$debug = true;

try {
    // Check if course_id is provided in the URL
    if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
        throw new Exception("Error: Course ID is missing from the URL.");
    }

    $course_id = intval($_GET['course_id']);

    // Fetch course details using the provided course ID
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :course_id");
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        throw new Exception("Error: No course found with ID {$course_id}.");
    }

    // Fetch classrooms related to the course
    $stmtClassrooms = $pdo->prepare("SELECT * FROM classrooms WHERE course_id = :course_id");
    $stmtClassrooms->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmtClassrooms->execute();
    $classrooms = $stmtClassrooms->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    if ($debug) {
        // Display the error details in debug mode
        echo "<h3>Debug Error:</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>";
        print_r($e->getTrace());
        echo "</pre>";
    } else {
        // Display a generic error in production
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
    <title><?php echo htmlspecialchars($course['title']); ?> - Classroom</title>
    <link rel="stylesheet" href="/users/styles.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($course['title']); ?> - Classroom</h1>

        <?php if (!empty($classrooms)): ?>
            <h2>Classrooms:</h2>
            <div class="classrooms">
                <?php foreach ($classrooms as $classroom): ?>
                    <div class="classroom">
                        <img src="<?php echo htmlspecialchars($classroom['image']); ?>" alt="<?php echo htmlspecialchars($classroom['title']); ?>" style="max-width: 100%; height: auto;">
                        <p><?php echo htmlspecialchars($classroom['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No classrooms found for this course.</p>
        <?php endif; ?>
    </div>
</body>
</html>
