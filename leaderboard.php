<?php
require_once 'functions.php';

// 1. Read and decode the JSON data
$jsonData = file_get_contents('data/gamePlay.json');
// If the file is empty, default to an empty array so the page doesn't break
$leaderboardArray = json_decode($jsonData, true) ?? [];

// 2. Sort the array. Let's sort by highestWaveReached since that's a main goal in Fortress Fall!
$sortedArray = sortLeaderboard($leaderboardArray, "highestWaveReached");
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
                foreach ($sortedArray as $index => $player) {

                    // Rank is the index + 1 (because PHP arrays start counting at 0!)
                    $rank = $index + 1;

                    // 4. Print an HTML table row (<tr>) and table data cells (<td>) for each player
                    echo "<tr>";
                    echo "<td>{$rank}</td>";
                    echo "<td>{$player['playerName']}</td>";
                    echo "<td>{$player['highestWaveReached']}</td>";
                    echo "<td>{$player['score']}</td>";
                    echo "</tr>"; /* *Agent*2* */
                }
                ?>
            </tbody>
        </table>

        <a href="index.php" class="btn">Back to Main Menu</a>
    </div>
</body>
</html>