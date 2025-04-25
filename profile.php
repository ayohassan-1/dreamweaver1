<?php
session_start();

// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require_once 'users/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
try {
    $stmt = $pdo->prepare("SELECT uName, email, regDate, profile_pic FROM users WHERE uid = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Error: User not found.");
    }
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage()); // Show actual error
}

$profilePic = $user['profile_pic']; // Store profile picture URL
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="/">
    <style>
        /* Header styling */
        .header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20px 40px;
		background-color: #1B1B1B;
		position: relative;
		gap: 20px; /* Ensures spacing between elements */
		}
        .header h1 {
	   	font-size: 38px;
		color: white;
		font-weight: bold;
		text-align: center;
		margin: 0 auto; /* Ensures it's centered */
		flex: 1;
        }
        .logout-button-left {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .profile-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-img-small {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-container {
		text-align: center;
		margin: 50px auto; /* Centers the box */
		padding: 20px;
		width: 50%; /* Adjust width as needed */
		background-color: #282828; /* Slightly lighter than the background */
		border: 2px solid #262626; /* Dark border for contrast */
		border-radius: 10px; /* Rounded corners */
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
		}

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
		
		/*
        .btn {
		display: inline-block;
		margin: 10px;
		padding: 10px 15px;
		text-decoration: none;
		color: white;
		background-color: #353535;
		border: 2px solid grey;
		border-radius: 5px;
		transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease-in-out;
		}

		.btn:hover {
			background-color: grey;
			color: black; 
			transform: scale(1.05); 
		}
		
		This is for view enrolled courses, removed
		*/
		
		.edit {
		display: inline-block;
		margin: 10px;
		padding: 10px 15px;
		text-decoration: none;
		color: white;
		background-color: #232323; /* Test color */
		border-radius: 5px;
		transition: background-color 0.3s ease, transform 0.3s ease-in-out;
		}

		.edit:hover {
			background-color: #161616; /* Test hover color */
			transform: scale(1.05); /* Slightly enlarges the button */
		}


		body {
		background-color: #1e1e1e; /* Dark Gray Background */
		color: white; /* Ensure text is visible */
		min-height: 100vh;
		margin: 0;
		}

    </style>
	
	    <div class="header">
        <button class="logout-button-left" onclick="location.href='/logout.php'">Log Out</button>
        <h1>Self Elevate</h1>
        <div class="profile-section">
<!--            <button class="btn" onclick="location.href='/users/courses.php'">View Enrolled Courses</button>   This was from view enrolled courses. -->
            <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-img-small">
        </div>
    </div>
	
</head>
<body>

    <!-- Profile Section -->
    <div class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['uName']); ?></h1>
        
        <!-- Display profile picture -->
        <?php if ($profilePic): ?>
            <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-img">
        <?php else: ?>
            <img src="/path/to/default/pic.jpg" alt="Default Profile Picture" class="profile-img">
        <?php endif; ?>
        
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Registration Date: <?php echo htmlspecialchars($user['regDate']); ?></p>

        <a href="/users/profileEdit.php" class="edit">Edit Profile</a>
        <a href="/users/landing.php" class="edit">Back to Home</a>
    </div>

</body>
</html>
