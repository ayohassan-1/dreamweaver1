<?php
session_start();

// Include the database connection
require_once '../db.php';

// Debugging mode (set to true to see detailed errors)
$debug = true;

try {
    // Check if the course_id is passed in the URL and ensure it's valid
    if (!isset($_GET['course_id']) || empty($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
        throw new Exception("Error: Invalid or missing course ID.");
    }

    $course_id = intval($_GET['course_id']); // Cast to integer for safety

    // Query to fetch course details from the database
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :course_id");
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the course exists
    if (!$course) {
        throw new Exception("Error: No course found with ID {$course_id}.");
    }
} catch (Exception $e) {
    // Display error details in debug mode, otherwise show a generic message
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
    <title><?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="/users/styles.css">
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            background-color: #333;
            color: white;
            padding: 10px 20px;
            z-index: 1000;
        }
        .header .nav-buttons {
            display: flex;
            gap: 20px;
        }
        .header .nav-buttons a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #444;
            border-radius: 5px;
        }
        .header .nav-buttons a:hover {
            background-color: #555;
        }
        .header .nav-buttons .center-button {
            margin: 0 auto;
        }

        body {
            margin-top: 60px;
        }

        .container {
            padding: 20px;
        }

        .classrooms {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .classroom {
            width: 300px;
            text-align: center;
        }

        .classroom img {
            width: 100%;
            height: auto;
            cursor: pointer;
        }

        .course-image {
            display: block;
            max-width: 60%;
            height: auto;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
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
            <a href="/communityFolder/community.php?course_id=<?php echo $course_id; ?>&classroom_id=1">Community</a>
        </div>
    </div>

    <div class="container">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p id="about-section"><?php echo htmlspecialchars($course['description']); ?></p>
        <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image" class="course-image">

        <h2>Classrooms</h2>
        <div class="classrooms">
            <?php
            // Check if the 'classrooms' field contains a valid JSON string
            $classrooms = json_decode($course['classrooms'], true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($classrooms)) {
                foreach ($classrooms as $classroom) {
                    echo "<div class='classroom'>";
                    echo "<a href='/classrooms/classroom.php?course_id=" . $course_id . "&classroom_id=" . urlencode($classroom['id']) . "'>";
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
