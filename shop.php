<?php
session_start();

$usersFile = 'account/users.json'; 
$productsFile = 'data/products.json';
$shopStateFile = 'data/shop_state.json';

// Load User Data & Points
$userPoints = 0;
$username = $_SESSION['username'] ?? null;
if ($username && file_exists($usersFile)) {
    $usersData = json_decode(file_get_contents($usersFile), true);
    $userPoints = $usersData[$username]['highScore'] ?? 0; 
}

// NEW: Load the Shop State FIRST, so we know what is on sale and how much it costs
// *Agent*2*
$shopState = file_exists($shopStateFile) ? json_decode(file_get_contents($shopStateFile), true) : ['nextRefreshTime' => 0, 'currentItems' => []];
$itemsOnSale = $shopState['currentItems'];

// Process "Buy" Button Click
if (isset($_POST['buy_item']) && $username) {
    $itemName = $_POST['buy_item'];

    // Find how much the item costs by looking at the items currently on sale!
    $itemCost = 0;
    foreach ($itemsOnSale as $p) {
        if ($p['name'] === $itemName) {
            $itemCost = $p['cost'];
            break; // Stop looking once we find it
        }
    }

    // Check if they have enough points AND that the item actually costs something (> 0)
    if ($userPoints >= $itemCost && $itemCost > 0) {
        // Deduct points
        $usersData[$username]['highScore'] -= $itemCost;
        $userPoints = $usersData[$username]['highScore']; // Update for the display

        // Add to inventory
        if (!isset($usersData[$username]['inventory'])) {
            $usersData[$username]['inventory'] = [];
        }
        $usersData[$username]['inventory'][] = $itemName; 

        // Save file
        file_put_contents($usersFile, json_encode($usersData, JSON_PRETTY_PRINT));

        $modalTitle = "Purchase Successful!";
        $modalMessage = "You successfully bought the $itemName.";
    } else {
        $modalTitle = "Purchase Failed";
        $modalMessage = "You don't have enough points or the item is invalid.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop - Fortress Fall</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .shop-container { display: flex; gap: 20px; justify-content: center; margin-top: 20px; }
        .shop-item { background-color: #34495e; padding: 20px; border-radius: 8px; text-align: center; color: white; width: 220px; }
        .cost { color: #f1c40f; font-weight: bold; }
    </style>
</head>
<body>
    <body>
        <div class="top-bar">
            Points: <?php echo number_format($userPoints); ?>
        </div>

        <?php if (isset($modalMessage)): ?>
        <div class="modal-overlay" id="purchaseModal">
            <div class="modal-content">
                <h2><?php echo $modalTitle; ?></h2>
                <p><?php echo $modalMessage; ?></p>
                <button class="modal-btn" onclick="document.getElementById('purchaseModal').style.display='none'">OK</button>
            </div>
        </div>
        <?php endif; ?>

        <div id="welcome-screen">
        
    <div id="welcome-screen">
        <h1>Item Shop</h1>
        <p>New items every hour! (Mockup Mode)</p>

        <div class="shop-container">
            <?php
           
            // If there is a message, display it using our new floating CSS class
            if (isset($purchaseMessage)) {
                echo "<div class='notification'>$purchaseMessage</div>"; // |Agent|1|
            }

            foreach ($itemsOnSale as $item) { 
                echo "<div class='shop-item'>";
                echo "<h3>{$item['name']}</h3>";
                echo "<p>{$item['desc']}</p>";
                echo "<p class='cost'>{$item['cost']} Pts</p>";

                // NEW: A form that actually POSTs the item name to the server!
                echo "<form method='POST'>";
                echo "<input type='hidden' name='buy_item' value='{$item['name']}'>";
                echo "<button type='submit' class='btn'>Buy</button>";
                echo "</form>";

                echo "</div>";
            }
            ?>
        </div>

        <br><br>
        <a href="lobby.php" class="btn secondary">Back to camp</a>
    </div>

    <script>
        function buyItem(itemName) {
            alert("Mockup: You 'bought' the " + itemName + "! Check your inventory.");
        }
    </script>
</body>
</html>