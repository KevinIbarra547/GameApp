<?php
session_start();

// 1. Check if a user is actually logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$username = $_SESSION['username'];

// 2. Catch the data sent from the JavaScript game
$rawData = file_get_contents("php://input");
$gameData = json_decode($rawData, true);

if ($gameData) {
    $score = $gameData['score'];
    $maxWave = $gameData['maxWave'];
    $wavesCompleted = $gameData['wavesCompleted'];

    $file = 'users.json';

    if (file_exists($file)) {
        $users = json_decode(file_get_contents($file), true);

        if (isset($users[$username])) {
            // 3. Compare and update stats!
            // Update High Score if it's better
            if (!isset($users[$username]['highScore']) || $score > $users[$username]['highScore']) {
                $users[$username]['highScore'] = $score;
            }

            // Update Highest Wave Reached
            if (!isset($users[$username]['maxWave']) || $maxWave > $users[$username]['maxWave']) {
                $users[$username]['maxWave'] = $maxWave;
            }

            // Update Max Waves Completed in a single run
            if (!isset($users[$username]['wavesCompleted']) || $wavesCompleted > $users[$username]['wavesCompleted']) {
                $users[$username]['wavesCompleted'] = $wavesCompleted;
            }

            // 4. Save everything back to the JSON file
            file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

            echo json_encode(["status" => "success", "message" => "Stats saved successfully!"]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>