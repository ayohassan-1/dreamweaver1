<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the POST request
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['video_id']) && isset($data['checked'])) {
        $videoId = $data['video_id'];
        $checked = $data['checked'];

        // Save the checked state in the session
        $_SESSION['checked_videos'][$videoId] = $checked;

        // Return a success response
        echo json_encode(['status' => 'success']);
    } else {
        // Invalid data
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
