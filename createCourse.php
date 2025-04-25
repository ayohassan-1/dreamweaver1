<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: users/login.php");
    exit();
}

// Fetch current user's profile picture
try {
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE uid = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_pic = $user['profile_pic'];
} catch (PDOException $e) {
    error_log("Error fetching user profile picture: " . $e->getMessage());
    $profile_pic = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Course Introduction Data
    $introductionVideoUrl = filter_var($_POST['introduction_video_url'] ?? '', FILTER_VALIDATE_URL);
    $introductionDescription = htmlspecialchars($_POST['introduction_description'] ?? '');
    $introductionImageUrl = filter_var($_POST['introduction_image_url'] ?? '', FILTER_VALIDATE_URL);
	
    // Course Details
    $title = htmlspecialchars($_POST['title'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');
    $youtubeLink = filter_var($_POST['youtube_link'] ?? '', FILTER_VALIDATE_URL);
    $imageUrl = filter_var($_POST['image_url'] ?? '', FILTER_VALIDATE_URL);
    $about = htmlspecialchars($_POST['about'] ?? '');

    // Video Count
    $videoCount = isset($_POST['video_count']) ? intval($_POST['video_count']) : 0;
    $videos = [];
    for ($i = 1; $i <= $videoCount; $i++) {
        $sectionName = htmlspecialchars($_POST["section_name_$i"] ?? '');
        $youtubeVideoLink = filter_var($_POST["youtube_link_$i"] ?? '', FILTER_VALIDATE_URL);
        $videoTitle = htmlspecialchars($_POST["video_title_$i"] ?? '');
        $videoDescription = htmlspecialchars($_POST["video_description_$i"] ?? '');
        if (!empty($sectionName) && !empty($youtubeVideoLink) && !empty($videoTitle)) {
            $videos[] = [
                'sectionName' => $sectionName,
                'video' => [
                    'link' => $youtubeVideoLink,
                    'title' => $videoTitle,
                    'description' => $videoDescription
                ]
            ];
        }
    }

    // Ensure required fields are filled
    if (!$title || !$description || !$imageUrl || !$about) {
        exit("<div style='color: red;'>Invalid input. Make sure all required fields are filled.</div>");
    }

    try {
        $videosJson = json_encode($videos);
        $stmt = $pdo->prepare("INSERT INTO courses (title, description, youtube_link, image_url, about, videos, introduction_video_url, introduction_description, introduction_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $youtubeLink, $imageUrl, $about, $videosJson, $introductionVideoUrl, $introductionDescription, $introductionImageUrl]);
        $courseId = $pdo->lastInsertId();
        header("Location: users/course1.php?course_id=" . $courseId);
        exit();
    } catch (PDOException $e) {
        error_log("Failed to add course: " . $e->getMessage());
        echo "Error adding course. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course</title>
    <link rel="stylesheet" href="/createcorusestyle/style.css?v=1.0">
    <link rel="stylesheet" href="/users/styles.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        function toggleDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }
        window.onclick = function(event) {
            if (!event.target.matches('.profile-button') && !event.target.matches('.profile-button *')) {
                document.getElementById("profileDropdown").classList.remove("show");
            }
        }
    </script>
    <style>
        /* Header Style */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            background-color: #1e1e1e;
            width: 100%;
            z-index: 100;
        }
        .logout-button-left {
            margin-left: 10px;
        }
        .self-elevate {
            color: white;
            text-decoration: none;
            font-size: 2.5em;
            font-weight: bold;
            flex-grow: 1;
            text-align: center;
        }
        .profile-section {
            position: relative;
            margin-right: 10px;
        }
        .profile-button {
            background: none;
            border: none;
            cursor: pointer;
        }
        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0;
        }
        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #1D1616;
        }
        .show {
            display: block;
        }
        
        /* Main container */
        .main-container {
            display: flex;
            justify-content: space-between;
            margin-top: 70px;
        }
        .form-container {
            width: 48%;
            border: 1px solid #ddd;
            padding: 20px;
            box-sizing: border-box;
            margin: 10px;
            overflow-y: auto;
            max-height: 80vh;
        }
        .form-container label {
            display: block;
            margin-bottom: 15px;
        }
        .form-container input[type="url"],
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container textarea {
            width: 100%;
            padding: 5px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        .form-container textarea {
            resize: vertical;
        }

    </style>
</head>
<body>
    <header class="header">
        <button class="logout-button-left" onclick="location.href='/logout.php'">Log Out</button>
        <a href="/users/landing.php" class="self-elevate">Self Elevate</a>
        <div class="profile-section">
            <button class="profile-button" onclick="toggleDropdown()">
                <?php if ($profile_pic): ?>
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic">
                <?php else: ?>
                    <span>Profile</span>
                <?php endif; ?>
            </button>
            <div id="profileDropdown" class="dropdown-content">
                <a href="/profile.php">Profile</a>
                <a href="/users/profileEdit.php">Edit Profile</a>
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="form-container">
            <h2>Create Introduction</h2>
            <form action="createCourse.php" method="POST">
                <label for="introduction_video_url">Introduction Video URL:</label>
                <input type="url" name="introduction_video_url" id="introduction_video_url" required>
                <label for="introduction_description">Introduction Description:</label>
                <textarea name="introduction_description" id="introduction_description" required></textarea>
                <label for="introduction_image_url">Introduction Image URL:</label>
                <input type="url" name="introduction_image_url" id="introduction_image_url" required>
        </div>
        <div class="form-container">
            <h1>Create a New Course</h1>
            <form action="createCourse.php" method="POST">
                <label for="title">Course Title:</label>
                <input type="text" name="title" id="title" required>
                <label for="description">Course Description:</label>
                <textarea name="description" id="description" required></textarea>
                <label for="youtube_link">Course YouTube Link (Optional):</label>
                <input type="url" name="youtube_link" id="youtube_link">
                <label for="image_url">Course Image URL:</label>
                <input type="url" name="image_url" id="image_url" required>
                <label for="about">About the Course:</label>
                <textarea name="about" id="about" required></textarea>
                <label for="video_count">Number of Videos:</label>
                <input type="number" name="video_count" id="video_count" min="1" required>
                <div id="video-links" class="scroll-container"></div>
                <button type="submit">Create Course</button>
            </form>
        </div>
    </div>
    <script src="/createcorusestyle/script.js" defer></script>
    <script>
        document.getElementById('video_count').addEventListener('input', function() {
            var videoLinksContainer = document.getElementById('video-links');
            videoLinksContainer.innerHTML = '';
            var count = parseInt(this.value);
            for (var i = 1; i <= count; i++) {
                var div = document.createElement('div');
                div.innerHTML = `
                    <label for="section_name_${i}">Section Name ${i}:</label>
                    <input type="text" name="section_name_${i}" id="section_name_${i}" required>
                    <label for="youtube_link_${i}">YouTube Link ${i}:</label>
                    <input type="url" name="youtube_link_${i}" id="youtube_link_${i}" required>
                    <label for="video_title_${i}">Title of Video ${i}:</label>
                    <input type="text" name="video_title_${i}" id="video_title_${i}" required>
                    <label for="video_description_${i}">Video Description ${i}:</label>
                    <textarea name="video_description_${i}" id="video_description_${i}" required></textarea>
                `;
                videoLinksContainer.appendChild(div);
            }
        });
    </script>
</body>
</html>
