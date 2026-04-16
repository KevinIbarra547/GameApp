<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$username = $_SESSION['username'];
$rawData = file_get_contents("php://input");
$gameData = json_decode($rawData, true);

if ($gameData) {
    $score        = $gameData['score'];
    $maxWave      = $gameData['maxWave'];
    $wavesCompleted = $gameData['wavesCompleted'];

    // ✅ FIX: correct path
    // ✅ CORRECT — save_score.php is already inside /account/ so this is right
    $file = 'users.json';
    
    if (file_exists($file)) {
        $users = json_decode(file_get_contents($file), true);

        if (isset($users[$username])) {
            if (!isset($users[$username]['highScore']) || $score > $users[$username]['highScore']) {
                $users[$username]['highScore'] = $score;
            }
            if (!isset($users[$username]['maxWave']) || $maxWave > $users[$username]['maxWave']) {
                $users[$username]['maxWave'] = $maxWave;
            }
            if (!isset($users[$username]['wavesCompleted']) || $wavesCompleted > $users[$username]['wavesCompleted']) {
                $users[$username]['wavesCompleted'] = $wavesCompleted;
            }

            file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
        }
    }

    // ✅ NEW: Also log this session to gamePlay.json
    $gamePlayFile = 'gamePlay.json';
    $sessions = file_exists($gamePlayFile) ? json_decode(file_get_contents($gamePlayFile), true) : [];
    if (!is_array($sessions)) $sessions = [];

    $sessions[] = [
        "playerName" => $username,
        "score"      => $score,
        "maxWave"    => $maxWave,
        "wavesCompleted" => $wavesCompleted,
        "dateTime"   => time()
    ];

    file_put_contents($gamePlayFile, json_encode($sessions, JSON_PRETTY_PRINT));

    echo json_encode(["status" => "success", "message" => "Stats saved!"]);

} else {
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>