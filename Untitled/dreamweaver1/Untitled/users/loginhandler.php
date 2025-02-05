<?php
// Start the session to manage user sessions
session_start();

// Include the database connection file
include 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);  // Display errors to help with debugging
ini_set('display_startup_errors', 1);  // Display errors on startup
error_reporting(E_ALL);  // Report all types of errors

// Check if the request method is POST to ensure form data is being sent
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve the username and password from the form input
    $username = $_POST['username'];  // Username entered in the form
    $password = $_POST['password'];  // Password entered in the form

    // Prepare the SQL query to select user data by username
    $stmt = $pdo->prepare("SELECT uid, email, uName, pWord, regDate FROM users WHERE uName = :username");
    $stmt->bindParam(':username', $username);  // Bind the username parameter to the query

    try {
        // Execute the query to fetch the user's data
        $stmt->execute();
    } catch (PDOException $e) {
        // Display error message if query execution fails and exit the script
        echo "Error executing query: " . $e->getMessage();
        exit();
    }

    // Fetch the user data as an associative array
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password and check if the user data exists
    if ($user && $password === $user['pWord']) {  // Replace this with password_verify() if hashing is used

        // Store user information in session variables for use on other pages
        $_SESSION['user_id'] = $user['uid'];      // Store user's unique ID
        $_SESSION['email'] = $user['email'];      // Store user's email
        $_SESSION['uname'] = $user['uName'];      // Store user's username
        $_SESSION['regDate'] = $user['regDate'];  // Store user's registration date

        // Redirect the user to the landing page upon successful login
        header("Location: /users/landing.php");  // Redirect to the root-level landing.php
        exit();  // Stop script execution after redirection
    } else {
        // Display error message if login details are incorrect
        echo "<div class='error'>Invalid username or password. <a href='login.php'>Try again</a></div>";
    }
}
?>
