<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
session_start();
require_once '../db.php';

// Check if course_id is provided
if (!isset($_GET['course_id'])) {
    die("Course ID is required");
}

$course_id = $_GET['course_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $reason = $_POST['reason'];

    // Insert enrollment data into the database (create a new table for enrollments)
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, user_id, name, email, reason, enrollment_date) 
                           VALUES (:course_id, :user_id, :name, :email, :reason, NOW())");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':reason', $reason);
    $stmt->execute();

    // Redirect to course1.php with form data in the URL
    header("Location: /users/course1.php?course_id=" . $course_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Course</title>
    <style>
        /* General reset for consistent styling across browsers */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* Container styling for the form */
        .enroll-container {
            text-align: center;
            padding: 2rem;
            max-width: 400px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Heading styling */
        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
        }

        /* Label and input styling */
        label {
            display: block;
            font-size: 1rem;
            color: #666;
            margin: 0.5rem 0 0.25rem;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        textarea {
            height: 100px;
        }

        /* Button styling */
        button {
            background-color: #007bff;
            color: #fff;
            font-size: 1rem;
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Error message styling */
        .error {
            color: red;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="enroll-container">
        <h1>Enroll in Course</h1>
        <form method="post" action="/users/enroll.php?course_id=<?php echo htmlspecialchars($course_id); ?>">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required><br><br>
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="reason">Reason for enrolling:</label>
            <textarea id="reason" name="reason" required></textarea><br><br>
            <button type="submit">Enroll Now</button>
        </form>
    </div>
</body>
</html>


ssss