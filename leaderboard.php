<?php
// 1. Bring in our functions file so we have access to saveGameData()
require_once 'functions.php';

// 1. Let's add a second test player who got a better score than Kevin
// saveGameData("Jones", 5000, 10, 8, 10); 
// (Note: you can comment this out after you run it once so it doesn't keep adding Jones over and over!)

// 2. Read the data back from the file
$jsonData = file_get_contents('data/gamePlay.json');
$leaderboardArray = json_decode($jsonData, true);

// 3. TEST: Sort the array by score!
$sortedArray = sortLeaderboard($leaderboardArray, "score");

// 4. Print the sorted array
echo "<pre>";
print_r($sortedArray);
echo "</pre>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard - PHP Web Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            
        </ul>
    </nav>
    <main>
        <h1>Leaderboard</h1>
        <p>High scores will be displayed here.</p>
    </main>
</body>
</html>
