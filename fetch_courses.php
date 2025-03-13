<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

// Include database connection
require_once 'users/db.php';

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepare the SQL query with wildcard search
try {
    $stmt = $pdo->prepare("SELECT c.id, c.title, c.description, c.youtube_link, c.image_url 
                            FROM courses c
                            INNER JOIN enrollments e ON c.id = e.course_id
                            WHERE e.user_id = ? 
                            AND (c.title LIKE ? OR c.description LIKE ?)");
    $stmt->execute([$user_id, "%$search%", "%$search%"]);
    $filteredCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "Error fetching courses.";
    exit();
}

// Return results as HTML
if (!empty($filteredCourses)): 
    foreach ($filteredCourses as $course): ?>
        <a href="enroll.php?course_id=<?php echo htmlspecialchars($course['id']); ?>" class="course-link">
            <div class="course">
                <?php if (!empty($course['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image" class="course-image">
                <?php endif; ?>

                <div class="course-content">
                    <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                </div>
            </div>
        </a>
    <?php endforeach;
else: 
    echo "<p>No courses found.</p>";
endif;
?>
