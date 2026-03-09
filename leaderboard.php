<?php
// 1. Bring in our functions file so we have access to saveGameData()
require_once 'functions.php';

// 2. TEST: Write a new score! 
// Let's pretend a player named "Kevin" just lost to the boss on Wave 5.
// Parameters: Name, Score, Highest Wave, Potions Collected, Current Wave
saveGameData("Kevin", 2500, 5, 3, 5); 

// 3. TEST: Read the data back!
// Open the JSON file and read all the text inside
$jsonData = file_get_contents('data/gamePlay.json');

// Translate that JSON text back into a PHP array
$leaderboardArray = json_decode($jsonData, true); // |Agent|1|

// 4. Print the raw array to the screen so we can verify it worked
echo "<pre>"; // <pre> makes the array print in a readable, stacked format
print_r($leaderboardArray);
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
