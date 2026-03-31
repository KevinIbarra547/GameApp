<?php
session_start();
$equippedItems = [];

// Load the user's equipped items from users.json
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $usersFile = 'account/users.json'; // Make sure this path is correct!

    if (file_exists($usersFile)) {
        $usersData = json_decode(file_get_contents($usersFile), true);
        if (isset($usersData[$username]['equipped'])) {
            $equippedItems = $usersData[$username]['equipped'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Play - PHP Web Game</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Takes your PHP array and turns it into a JavaScript array!
        const myEquippedItems = <?php echo json_encode($equippedItems); ?>;
        console.log("Active Powerups: " + myEquippedItems); 
    </script>
</head>
<body>
    <div id="welcome-screen">
        <h1>Fortress Fall</h1>

        <canvas id="gameCanvas" width="800" height="600" style="background-color: #4CAF50; border: 4px solid #2c3e50; border-radius: 8px;"></canvas>

        <br>
        <a href="lobby.php" class="btn secondary">Quit Game</a>
    </div>

    <script>
        // 1. Get the canvas and drawing tool
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        // --- CORE GAME OBJECTS (Must be declared before we modify them!) ---
        
        // Define the player FIRST
        let player = {
            x: 400,
            y: 300,
            size: 30,
            color: "#3498db",
            speed: 4 
        };

        // Define the boss (This was missing!)
        let boss = {
            x: 400,
            y: 0,
            size: 50,
            color: "purple",
            health: 50,
            maxHealth: 50,
            speed: 1,
            active: false
        };

        // --- GAME VARIABLES ---
        let potions = []; 
        let slimes = []; 
        let traps = []; 
        let walls = [];
        let droppedTraps = [];
        let fireTrails = []; // Added for the Fire Potion
        
        let gamePhase = "BUILD"; 
        let buildTimeLeft = 30; 
        let maxBlocks = 50; 
        let wallSize = 30;
        let activeTool = "BUILD"; 
        let trapInventory = 0; 
        let score = 0; 
        let currentWave = 1; 
        let doubleDamageTime = 0; 
        let customWallColor = "#7f8c8d"; // Default wall color

        // --- CHECK EQUIPPED ITEMS ---
        if (myEquippedItems.includes("Shadow Cloak")) {
            player.color = "#34495e"; 
        }

        if (myEquippedItems.includes("Speed Boots")) {
            player.speed = 8; 
        }

        if (myEquippedItems.includes("Gold Fortress")) {
            customWallColor = "#f1c40f"; // Gold walls
        }
        // Time Warp: Slows down the boss permanently
        if (myEquippedItems.includes("Time Warp")) {
            boss.speed = 0.6; // Assuming normal speed is 1. Adjust this number if needed!
        }

        // --- TRACK KEYS ---
        const keys = {
            ArrowUp: false,
            ArrowDown: false,
            ArrowLeft: false,
            ArrowRight: false
        };

        // --- TIMERS ---
        // Tick down every 1 second
        setInterval(function() {
            if (gamePhase === "BUILD" && buildTimeLeft > 0) { 
                buildTimeLeft--; 
            } else if (gamePhase === "BUILD" && buildTimeLeft === 0) {
                gamePhase = "DEFEND";
                boss.x = (canvas.width / 2) - (boss.size / 2);
                boss.y = 0;
                boss.health = boss.maxHealth; // FULLY HEAL the boss for the new wave!
                boss.active = true; 
            }
            // Tick down the double damage buff
            if (doubleDamageTime > 0) { doubleDamageTime--; }
        }, 1000);
        
        // Potion Spawner: Drops every 10 seconds (or 5s with Wizard Hat)
        let potionDropRate = myEquippedItems.includes("Wizard Hat") ? 5000 : 10000;
        
        setInterval(function() {
            if (gamePhase === "BUILD" || gamePhase === "DEFEND") {
                let pType = "TIME";
                let pColor = "#f1c40f"; 

                if (currentWave >= 2 && Math.random() > 0.5) {
                    pType = "DAMAGE";
                    pColor = "#e74c3c"; 
                }

                potions.push({
                    x: Math.random() * (canvas.width - 20),
                    y: Math.random() * (canvas.height - 20),
                    size: 15, color: pColor, type: pType
                });
            }
        }, potionDropRate);

        // Dropped Trap Spawner: Drops 2 traps every 5 seconds
        setInterval(function() {
            if (gamePhase === "BUILD" || gamePhase === "DEFEND") {
                for (let i = 0; i < 2; i++) {
                    droppedTraps.push({
                        x: Math.random() * (canvas.width - 20),
                        y: Math.random() * (canvas.height - 20),
                        size: 20, 
                        color: "#d35400" 
                    });
                }
            }
        }, 5000);

        // --- CONTROLS ---
        canvas.addEventListener("mousedown", function(event) {
            let clickX = event.offsetX;
            let clickY = event.offsetY;

            if (activeTool === "BUILD" && gamePhase === "BUILD" && walls.length < maxBlocks) {

                // 1. Set our default wall stats
                let currentWallHP = 100;
                let currentWallColor = "gray"; 

                // 2. Check for equipped wall items
                if (myEquippedItems.includes("Gold Fortress")) {
                    currentWallColor = "#f1c40f"; 
                    currentWallHP = 200;
                } else if (myEquippedItems.includes("Crystal Fortress")) {
                    currentWallColor = "#aee2ff"; 
                    currentWallHP = 50; 
                } else if (myEquippedItems.includes("Obsidian Walls")) {
                    currentWallColor = "#1a1a1a"; 
                    currentWallHP = 300; 
                }

                // 3. Build the wall exactly where you clicked!
                walls.push({
                    x: clickX - (wallSize / 2), 
                    y: clickY - (wallSize / 2), 
                    size: wallSize,             
                    hp: currentWallHP,
                    color: currentWallColor 
                });
                
            } else if (activeTool === "DELETE" && gamePhase === "BUILD") {
                for (let i = walls.length - 1; i >= 0; i--) {
                    let w = walls[i];
                    if (clickX >= w.x && clickX <= w.x + w.size && clickY >= w.y && clickY <= w.y + w.size) {
                        walls.splice(i, 1); 
                        break; 
                    }
                }
            } else if (activeTool === "SHOOT") {

                let dx = clickX - player.x;
                let dy = clickY - player.y;
                let distance = Math.sqrt(dx * dx + dy * dy); 

                let slimeColor = "#2ecc71"; 

                if (myEquippedItems.includes("Neon Slimes")) {
                    slimeColor = "#ff00ff"; 
                }

                if (myEquippedItems.includes("Rainbow Slimes")) {
                    const rainbowColors = ["#ff0000", "#ff7f00", "#ffff00", "#00ff00", "#0000ff", "#4b0082", "#9400d3"];
                    let randomIndex = Math.floor(Math.random() * rainbowColors.length);
                    slimeColor = rainbowColors[randomIndex];
                }

                slimes.push({
                    x: player.x + (player.size / 2), 
                    y: player.y + (player.size / 2),
                    vx: (dx / distance) * 7, 
                    vy: (dy / distance) * 7, 
                    size: 10,
                    color: slimeColor 
                });

                // NEW: Check for Double Crossbow and shoot a second one!
                if (myEquippedItems.includes("Double Crossbow")) {
                    slimes.push({
                        // Offset the second slime slightly so they don't overlap perfectly
                        x: player.x + (player.size / 2) + 15, 
                        y: player.y + (player.size / 2) + 15,
                        vx: (dx / distance) * 7, 
                        vy: (dy / distance) * 7, 
                        size: 10,
                        color: slimeColor 
                    });
                }

            } 
        }); 
        
        window.addEventListener("keydown", function(event) {
            if (keys.hasOwnProperty(event.key)) { keys[event.key] = true; }

            if (event.key === "1") { activeTool = "BUILD"; }
            if (event.key === "2") { activeTool = "DELETE"; }
            if (event.key === "3") { activeTool = "SHOOT"; } 
            if (event.key === "4") { activeTool = "TRAP"; } 
            
            if (event.key === "r" || event.key === "R") {
                if (gamePhase === "GAMEOVER" || gamePhase === "VICTORY") { location.reload(); }
            }
            if (event.key === "n" || event.key === "N") {
                if (gamePhase === "VICTORY") {
                    currentWave++; 
                    maxBlocks += 20; 
                    buildTimeLeft = 30; 
                    boss.maxHealth += 250; 

                    potions = []; 
                    droppedTraps = []; 
                    gamePhase = "BUILD"; 
                }
            }
        });
        
        window.addEventListener("keyup", function(event) {
            if (keys.hasOwnProperty(event.key)) { keys[event.key] = false; }
        }); 

        // --- SAVE STATS ---
        function saveGameStats(finalScore, highestWave, wavesBeaten) {
            fetch('account/save_score.php', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    score: finalScore,
                    maxWave: highestWave,
                    wavesCompleted: wavesBeaten
                })
            })
            .then(response => response.json())
            .then(data => { console.log("Server response:", data.message); })
            .catch(error => { console.error("Error saving stats:", error); });
        }

        // --- MAIN GAME LOOP ---
        function gameLoop() {
            // Update player position
            if (keys.ArrowUp && player.y > 0) { player.y -= player.speed; }    
            if (keys.ArrowDown && player.y < canvas.height - player.size) { player.y += player.speed; }  
            if (keys.ArrowLeft && player.x > 0) { player.x -= player.speed; }  
            if (keys.ArrowRight && player.x < canvas.width - player.size) { player.x += player.speed; }

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            
            // Draw walls
            for (let i = 0; i < walls.length; i++) {
                let currentWall = walls[i];

                ctx.fillStyle = currentWall.color; 
                ctx.fillRect(currentWall.x, currentWall.y, currentWall.size, currentWall.size);
            }

            // Draw player
            ctx.fillStyle = player.color;
            ctx.fillRect(player.x, player.y, player.size, player.size); 

            // Fire Potion Logic
            if (myEquippedItems.includes("Fire Potion")) {
                if (Math.random() < 0.2) { // Randomly drop fire while moving
                    fireTrails.push({ x: player.x + 5, y: player.y + 5, size: 20, timer: 60 });
                }
            }

            for (let f = fireTrails.length - 1; f >= 0; f--) {
                let fire = fireTrails[f];
                fire.timer--;
                ctx.fillStyle = "#e67e22"; // Orange fire
                ctx.fillRect(fire.x, fire.y, fire.size, fire.size);
                
                // Burn the boss!
                if (boss.active && boss.x < fire.x + fire.size && boss.x + boss.size > fire.x &&
                    boss.y < fire.y + fire.size && boss.y + boss.size > fire.y) {
                    boss.health -= 0.2; 
                }
                
                if (fire.timer <= 0) fireTrails.splice(f, 1);
            }

            // Handle Traps
            for (let t = traps.length - 1; t >= 0; t--) {
                let currentTrap = traps[t];
                ctx.fillStyle = currentTrap.color;
                ctx.fillRect(currentTrap.x, currentTrap.y, currentTrap.size, currentTrap.size);

                if (boss.x < currentTrap.x + currentTrap.size && boss.x + boss.size > currentTrap.x &&
                    boss.y < currentTrap.y + currentTrap.size && boss.y + boss.size > currentTrap.y) {
                    
                    if (myEquippedItems.includes("Ice Trap")) {
                        boss.speed = 0; // Completely frozen
                        setTimeout(function() { boss.speed = 1; }, 5000); // For 5 seconds
                    } else if (myEquippedItems.includes("Electric Trap")) {
                        boss.speed = 0; // Stunned!
                        let oldColor = boss.color;
                        boss.color = "yellow"; // Visual shock effect
                        setTimeout(function() { boss.speed = 1; boss.color = oldColor; }, 2000);
                    } else {
                        boss.speed = 0.2; // Normal trap slow
                        setTimeout(function() { boss.speed = 1; }, 3000); // Normal 3 seconds
                    }
                    
                    traps.splice(t, 1); 
                }
            }

            // Boss Logic
            if (gamePhase === "DEFEND" && boss.active === true) {
                let isTouchingWall = false; 

                for (let i = walls.length - 1; i >= 0; i--) {
                    let w = walls[i];
                    if (boss.x < w.x + w.size && boss.x + boss.size > w.x &&
                        boss.y < w.y + w.size && boss.y + boss.size > w.y) {
                        
                        isTouchingWall = true; 
                        w.hp -= 1;
                        w.color = "#bdc3c7"; 

                        // Damage the boss while it touches the wall!
                        if (myEquippedItems.includes("Spiked Walls")) {
                            boss.health -= 0.5; // Small constant damage per frame
                        }

                        if (w.hp <= 0) { walls.splice(i, 1); }
                    }
                }

                if (isTouchingWall === false) {
                    if (boss.x < player.x) { boss.x += boss.speed; }
                    if (boss.x > player.x) { boss.x -= boss.speed; }
                    if (boss.y < player.y) { boss.y += boss.speed; }
                    if (boss.y > player.y) { boss.y -= boss.speed; }
                }

                if (boss.x < player.x + player.size && boss.x + boss.size > player.x &&
                    boss.y < player.y + player.size && boss.y + boss.size > player.y) {
                    gamePhase = "GAMEOVER"; 
                    saveGameStats(score, currentWave, currentWave - 1); 
                }

                ctx.fillStyle = boss.color;
                ctx.fillRect(boss.x, boss.y, boss.size, boss.size);

                for (let j = slimes.length - 1; j >= 0; j--) {
                    let s = slimes[j];
                    if (s.x < boss.x + boss.size && s.x + s.size > boss.x &&
                        s.y < boss.y + boss.size && s.y + s.size > boss.y) {
                        
                        if (doubleDamageTime > 0) { boss.health -= 10; } 
                        else { boss.health -= 5; }

                        slimes.splice(j, 1); 

                        if (boss.health <= 0) {
                            boss.active = false;
                            score += 1000; 
                            gamePhase = "VICTORY";
                        }
                    }
                }
            } 

            // Draw Slimes
            for (let i = slimes.length - 1; i >= 0; i--) {
                let s = slimes[i];
                s.x += s.vx;
                s.y += s.vy;
                
                ctx.fillStyle = s.color;
                ctx.fillRect(s.x, s.y, s.size, s.size);

                if (s.x < 0 || s.x > canvas.width || s.y < 0 || s.y > canvas.height) {
                    slimes.splice(i, 1);
                }
            }
        
            // Handle Potions
            for (let k = potions.length - 1; k >= 0; k--) {
                let p = potions[k];
                ctx.fillStyle = p.color;
                ctx.fillRect(p.x, p.y, p.size, p.size);

                if (player.x < p.x + p.size && player.x + player.size > p.x &&
                    player.y < p.y + p.size && player.y + player.size > p.y) {

                    score += 100; 
                    if (p.type === "TIME" && gamePhase === "BUILD") { buildTimeLeft += 5; } 
                    else if (p.type === "DAMAGE") { doubleDamageTime += 30; }
                    potions.splice(k, 1); 
                }
            }

            // Handle Dropped Traps
            for (let dt = droppedTraps.length - 1; dt >= 0; dt--) {
                let d = droppedTraps[dt];
                ctx.fillStyle = d.color;
                ctx.fillRect(d.x, d.y, d.size, d.size);

                if (player.x < d.x + d.size && player.x + player.size > d.x &&
                    player.y < d.y + d.size && player.y + player.size > d.y) {
                    trapInventory++; 
                    droppedTraps.splice(dt, 1); 
                }
            }

            // --- HUD TEXT ---
            ctx.fillStyle = "white"; 
            ctx.font = "20px Arial"; 
            ctx.fillText("Wave: " + currentWave + " | Phase: " + gamePhase, 20, 30); 

            if (gamePhase === "BUILD") {
                ctx.fillText("Time until Boss: " + buildTimeLeft, 20, 60);
                ctx.fillText("Blocks: " + walls.length + " / " + maxBlocks, 20, 90); 
            } else if (gamePhase === "DEFEND") {
                ctx.fillText("DEFEND YOUR FORTRESS!", 20, 60);
                ctx.fillText("Boss HP:", 20, 90);
                ctx.fillStyle = "red";
                ctx.fillRect(110, 75, 150, 15); 
                ctx.fillStyle = "#2ecc71"; 
                ctx.fillRect(110, 75, 150 * (boss.health / boss.maxHealth), 15); 
                ctx.fillStyle = "white"; 

            } else if (gamePhase === "VICTORY") {
                ctx.fillStyle = "gold";
                ctx.font = "40px Arial";
                ctx.fillText("WAVE " + currentWave + " COMPLETE!", canvas.width/2 - 180, canvas.height/2 - 20);
                ctx.font = "20px Arial";
                ctx.fillText("Press 'N' for Next Wave (+30s, +20 Blocks)", canvas.width/2 - 190, canvas.height/2 + 30);
                ctx.fillStyle = "white";
                ctx.fillText("Press 'R' to Restart from Wave 1", canvas.width/2 - 140, canvas.height/2 + 60);
            } else if (gamePhase === "GAMEOVER") {
                ctx.fillStyle = "red";
                ctx.font = "50px Arial";
                ctx.fillText("GAME OVER", canvas.width/2 - 150, canvas.height/2);
                ctx.font = "20px Arial";
                ctx.fillStyle = "white";
                ctx.fillText("Press 'R' to Restart", canvas.width/2 - 90, canvas.height/2 + 40);
            }

            if (doubleDamageTime > 0) {
                ctx.fillStyle = "#e74c3c";
                ctx.fillText("2x DAMAGE ACTIVE: " + doubleDamageTime + "s", 20, 120);
            }

            ctx.fillStyle = "white"; 
            ctx.font = "20px Arial"; 
            ctx.fillText("Tool: " + activeTool, 20, canvas.height - 80);
            ctx.fillText("Traps in Inventory: " + trapInventory, 20, canvas.height - 50);
            ctx.fillText("Score: " + score, 20, canvas.height - 20);

            requestAnimationFrame(gameLoop);
        }

        // Start the game!
        gameLoop();
    </script>
</body>
</html>