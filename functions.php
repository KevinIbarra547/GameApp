<?php

Function saveGameData($playerName, $score, $highestWaveReached, $potionsCollected, $wave) {

      // The path to the file where we want to save the data
      $file = 'data/gamePlay.json';

      // 1. Read the existing data from the file
      $jsonData = file_get_contents($file);

      // 2. Convert the text into a PHP array. 
      // The ?? [] means "if the file is empty or broken, just start a new empty array"
      $dataArray = json_decode($jsonData, true) ?? [];

      // 3. Create a new array for the current player's game stats based on your project plan
      $newEntry = [
          "playerName" => $playerName,
          "score" => $score,
          "highestWaveReached" => $highestWaveReached,
          "dateTime" => time(), // Automatically grabs the exact current time as a number
          "potionsCollected" => $potionsCollected,
          "wave" => $wave
      ]; /* *Agent*2* */

      // 4. Add the new player's entry to the very bottom of our main array list
      $dataArray[] = $newEntry;

      // 5. Convert the array back into text and save it to the file
      // JSON_PRETTY_PRINT makes it format nicely with line breaks so you can read it easily
      file_put_contents($file, json_encode($dataArray, JSON_PRETTY_PRINT));
  }
function sortLeaderboard($dataArray, $sortBy) {

    // usort takes our array and compares two players at a time (Player A and Player B)
    usort($dataArray, function($a, $b) use ($sortBy) {

        // If we are sorting by name, we want Alphabetical order (A to Z)
        if ($sortBy == "playerName") {
            // The spaceship operator <=> compares the two names.
            // It returns -1, 0, or 1 to tell usort which name goes first.
            return $a[$sortBy] <=> $b[$sortBy]; 
        } 
        // If we are sorting by score or waves, we want Highest to Lowest
        else {
            // Notice we flipped $b and $a here! This makes the biggest numbers go to the top.
            return $b[$sortBy] <=> $a[$sortBy]; 
        }

    }); /* *Agent*2* */

    // Return the newly sorted array
    return $dataArray;
}
  ?>

