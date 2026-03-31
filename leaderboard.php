<?php

require_once 'functions.php';
// 1. Read and decode the new users JSON data
$jsonData = file_get_contents('account/users.json');
$usersAssoc = json_decode($jsonData, true) ?? [];

$playerList = [];
// Move the username into the data array so we can display it later
foreach ($usersAssoc as $username => $data) {
    $data['playerName'] = $username; 
    $playerList[] = $data; // |Agent|1|
}

// 2. Sort the array by high score! 
// usort automatically compares two players ($a and $b) to see who has the bigger score
usort($playerList, function($a, $b) {
    return $b['highScore'] <=> $a['highScore']; 
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard - Fortress Fall</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="welcome-screen"> <h1>High Scores</h1>

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
                <?php
                // 3. Loop through our sorted array to display each player
                // We use $index => $player so we can figure out what rank (1st, 2nd, etc.) they are
                // 3. Loop through our sorted array to display each player
                foreach ($playerList as $index => $player) {

                    // Rank is the index + 1 (because PHP arrays start counting at 0!)
                    $rank = $index + 1;

                    // 4. Print an HTML table row (<tr>) and table data cells (<td>) for each player
                    echo "<tr>";
                    echo "<td>{$rank}</td>";
                    echo "<td>{$player['playerName']}</td>";
                    // Using the exact keys we saved in save_score.php!
                    echo "<td>{$player['maxWave']}</td>";
                    echo "<td>{$player['highScore']}</td>"; // |Agent|1|
                    echo "</tr>"; 
                }
                ?>
            </tbody>
        </table>

        <a href="lobby.php" class="btn">Back to camp</a>
    </div>
</body>
</html>