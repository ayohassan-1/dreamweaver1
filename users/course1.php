<?php
session_start();

// Include the database connection
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
    <title><?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="/users/style.css">
    
</head>
<body>
    <!-- Header with buttons -->
    <div class="header">
        <div class="nav-buttons">
            <a href="/users/landing.php">Self-Elevate</a>
        </div>
        <div class="nav-buttons center-button">
            <a href="classroom.php?course_id=<?php echo $course_id; ?>">Classroom</a>
        </div>
        <div class="nav-buttons">
            <a href="#about-section">About</a>
        </div>
    </div>

    <div class="container">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p id="about-section"><?php echo htmlspecialchars($course['description']); ?></p>
        <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image" style="max-width: 100%; height: auto;">

        <!-- Display Classrooms -->
        <h2>Classrooms</h2>
        <div class="classrooms">
            <?php
            $classrooms = json_decode($course['classrooms'], true);
            if (!empty($classrooms)) {
                foreach ($classrooms as $classroom) {
                    echo "<div class='classroom'>";
                    echo "<a href='classroom.php?classroom_id=" . $classroom['id'] . "'>";
                    echo "<img src='" . htmlspecialchars($classroom['image']) . "' alt='" . htmlspecialchars($classroom['title']) . "'>";
                    echo "</a>";
                    echo "<p>" . htmlspecialchars($classroom['description']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No classrooms added yet.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
