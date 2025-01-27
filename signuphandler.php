<?php
// Start a new or resume an existing session
session_start();

// Include the database connection file
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if the form is submitted via POST
    // Retrieve and trim form data to remove extra spaces
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation to ensure no fields are empty
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required."); // Exit with an error message if any field is empty
    }

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format."); // Exit with an error message if the email format is invalid
    }

    try {
        // Check if the email already exists in the database
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            die("Email is already in use."); // Exit with an error message
        }

        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (uName, pWord, email, regDate) 
                               VALUES (:uName, :pWord, :email, NOW())");
        $stmt->bindParam(':uName', $username);
        $stmt->bindParam(':pWord', $password); // Note: Store hashed password for security
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Redirect to the login page after successful registration
        header("Location: /users/login.php"); // Change to the login page
        exit();

    } catch (PDOException $e) {
        error_log("Error during signup: " . $e->getMessage()); // Log errors
        die("An error occurred. Please try again later."); // Exit with a generic error
    }
} else {
    // Redirect to the signup page if accessed directly
    header("Location: signup.php");
    exit();
}
?>
