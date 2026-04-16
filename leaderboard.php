<?php
session_start(); // ✅ MUST be first, before anything else
require_once 'functions.php';

$jsonData  = file_get_contents('account/users.json');
// ... rest of your code
$usersAssoc = json_decode($jsonData, true) ?? [];

$playerList = [];
foreach ($usersAssoc as $username => $data) {
    $data['playerName'] = $username;
    $playerList[] = $data;
}

usort($playerList, function($a, $b) {
    return $b['highScore'] <=> $a['highScore'];
});

// ✅ NEW: Get the logged-in user's most recent session from gamePlay.json
$recentSession = null;
    if (isset($_SESSION['username'])) {
    $currentUser = $_SESSION['username']; // ✅ session already started above
    $gamePlayFile  = 'account/gamePlay.json';

    if (file_exists($gamePlayFile)) {
        $sessions = json_decode(file_get_contents($gamePlayFile), true) ?? [];
        // Filter only this user's sessions, grab the last one
        $mySessions = array_filter($sessions, fn($s) => $s['playerName'] === $currentUser);
        if (!empty($mySessions)) {
            $recentSession = end($mySessions);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard - Fortress Fall</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="welcome-screen">
        <h1>High Scores</h1>

        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player Name</th>
                    <th>Highest Wave</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($playerList as $index => $player): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($player['playerName']) ?></td>
                    <td><?= $player['maxWave'] ?? '-' ?></td>
                    <td><?= $player['highScore'] ?? 0 ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if ($recentSession): ?>
                <!-- ✅ NEW: Recent session row at the bottom -->
                <tr style="border-top: 3px solid gold; background-color: rgba(255,215,0,0.15);">
                    <td>🕹️</td>
                    <td><?= htmlspecialchars($recentSession['playerName']) ?> <em style="font-size:0.8em;">(Last Game)</em></td>
                    <td><?= $recentSession['maxWave'] ?? '-' ?></td>
                    <td><?= $recentSession['score'] ?? 0 ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="lobby.php" class="btn">Back to camp</a>
    </div>
</body>
</html>