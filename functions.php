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
  ?>
?>
