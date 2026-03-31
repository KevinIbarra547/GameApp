<?php
session_start();

$myInventory = [];
$equippedItems = [];
    $username = $_SESSION['username'] ?? null;
$usersFile = 'account/users.json'; // Put 'account/' back!

        if ($username && file_exists($usersFile)) {

            // NEW: Actually OPEN the file and read the data!
            $usersData = json_decode(file_get_contents($usersFile), true);

        // 1. Make sure their inventory and equipped lists exist so we don't get errors

    // 1. Make sure their inventory and equipped lists exist so we don't get errors
    if (!isset($usersData[$username]['inventory'])) {
        $usersData[$username]['inventory'] = [];
    }
    if (!isset($usersData[$username]['equipped'])) {
        $usersData[$username]['equipped'] = [];
    }

    // 2. Process Equip / Unequip Button Clicks
    if (isset($_POST['action']) && isset($_POST['item'])) {
        $item = $_POST['item'];
        $action = $_POST['action'];

        // If they clicked Equip, and they own it, and it isn't already equipped:
        if ($action === 'equip' && in_array($item, $usersData[$username]['inventory']) && !in_array($item, $usersData[$username]['equipped'])) {
            $usersData[$username]['equipped'][] = $item;
        } 
        // If they clicked Unequip, and it is currently equipped:
        elseif ($action === 'unequip' && in_array($item, $usersData[$username]['equipped'])) {
            // Remove the item from the equipped array
            $usersData[$username]['equipped'] = array_filter($usersData[$username]['equipped'], function($i) use ($item) {
                return $i !== $item;
            });
            // Fix the array numbering after removing an item
            $usersData[$username]['equipped'] = array_values($usersData[$username]['equipped']);
        }

        // Save the changes back to users.json
        file_put_contents($usersFile, json_encode($usersData, JSON_PRETTY_PRINT));
    }

    // 3. Load the final lists to display on the page
    $myInventory = $usersData[$username]['inventory'];
    $equippedItems = $usersData[$username]['equipped'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory - Fortress Fall</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .inventory-list { margin: 20px auto; width: 50%; background-color: #2c3e50; padding: 15px; border-radius: 8px; color: white; }
        .inventory-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #7f8c8d; padding: 10px 0; }
    </style>
</head>
<body>
    <div id="welcome-screen">
        <h1>Your Inventory</h1>
        <p>Equip your purchased items here.</p>

        <div class="inventory-list">
            <?php
            if (count($myInventory) === 0) {
                echo "<p>Your inventory is empty! Go buy something in the shop.</p>";
            } else {
                // NEW: This built-in PHP function counts how many duplicates are in your array!
                $inventoryCounts = array_count_values($myInventory);

                // Now we loop through this new grouped list instead of the raw list
                foreach ($inventoryCounts as $itemName => $count) { 
                    // Check if this specific item is currently equipped
                    $isEquipped = in_array($itemName, $equippedItems);

                    echo "<div class='inventory-item' style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; background: #34495e; padding: 15px; border-radius: 8px; color: white;'>";

                    // NEW: Display the item name AND the quantity multiplier
                    echo "<span><strong>{$itemName}</strong> <span style='color: #f1c40f; font-weight: bold;'>({$count}x)</span></span>";

                    // Create a mini-form for the button
                    echo "<form method='POST' style='margin: 0;'>";
                    echo "<input type='hidden' name='item' value='{$itemName}'>";

                    if ($isEquipped) {
                        // Show UNEQUIP button
                        echo "<input type='hidden' name='action' value='unequip'>";
                        echo "<button type='submit' class='btn' style='background-color: #e74c3c; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;'>Unequip</button>";
                    } else {
                        // Show EQUIP button
                        echo "<input type='hidden' name='action' value='equip'>";
                        echo "<button type='submit' class='btn' style='background-color: #2ecc71; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;'>Equip</button>";
                    }

                    echo "</form>";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <br>
        <a href="lobby.php" class="btn secondary">Back to Camp</a>
    </div>

    <script>
        function equipItem(itemName) {
            alert("Mockup: " + itemName + " equipped!");
        }
    </script>
</body>
</html>