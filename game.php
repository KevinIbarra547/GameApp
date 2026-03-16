<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Play - PHP Web Game</title>
    <link rel="stylesheet" href="style.css">
</head>
    <body>
        <div id="welcome-screen">
            <h1>Fortress Fall</h1>

            <canvas id="gameCanvas" width="800" height="600" style="background-color: #4CAF50; border: 4px solid #2c3e50; border-radius: 8px;"></canvas>

            <br>
            <a href="lobby.php" class="btn secondary">Quit Game</a>
        </div>

        <script>
            let potions = []; // Holds all active potions
            let slimes = []; // Array to hold our slime projectiles
            let traps = []; // Array to hold our sticky traps
            // 1. Get the canvas and drawing tool
            const canvas = document.getElementById("gameCanvas");
            const ctx = canvas.getContext("2d");
            // Game State Trackers
            let gamePhase = "BUILD"; // The game starts in the Build phase
            let buildTimeLeft = 30; // 30 seconds for testing (we will make this 5 mins later)
            let maxBlocks = 50; // The limit of blocks you can place for Wave 1
            let activeTool = "BUILD"; // Can be "BUILD", "DELETE", or "SHOOT"
            let droppedTraps = []; // Array for traps waiting on the ground
            let trapInventory = 0; // How many traps the player is holding in their backpack!
            let score = 0; 
            let currentWave = 1; // Track the current wave!
            let doubleDamageTime = 0; // Timer for the 2x Damage potion

            // The Boss object
            let boss = {
                x: 0, y: 0, size: 60,
                color: "#8e44ad", speed: 2,
                maxHealth: 500, // We need maxHealth to calculate the HP bar properly!
                health: 500,
                active: false 
            };
            // A timer that ticks down every 1 second (1000 milliseconds)
            // A timer that ticks down every 1 second
            // A timer that ticks down every 1 second
            setInterval(function() {
                if (gamePhase === "BUILD" && buildTimeLeft > 0) { buildTimeLeft--; } 
                else if (gamePhase === "BUILD" && buildTimeLeft === 0) {
                    gamePhase = "DEFEND";
                    boss.x = (canvas.width / 2) - (boss.size / 2);
                    boss.y = 0;
                    boss.health = boss.maxHealth; // FULLY HEAL the boss for the new wave!
                    boss.active = true; 
                }
                // Tick down the double damage buff if it is active!
                if (doubleDamageTime > 0) { doubleDamageTime--; }
            }, 1000);
            
            // Potion Spawner: Drops every 10 seconds
            setInterval(function() {
                if (gamePhase === "BUILD" || gamePhase === "DEFEND") {
                    let pType = "TIME";
                    let pColor = "#f1c40f"; // Gold

                    // If it is Wave 2 or higher, 50% chance to spawn a Damage Potion!
                    if (currentWave >= 2 && Math.random() > 0.5) {
                        pType = "DAMAGE";
                        pColor = "#e74c3c"; // Red for extra damage!
                    }

                    potions.push({
                        x: Math.random() * (canvas.width - 20),
                        y: Math.random() * (canvas.height - 20),
                        size: 15, color: pColor, type: pType
                    });
                }
            }, 10000);

            // Dropped Trap Spawner: Drops 2 traps every 5 seconds (5000 ms)
            setInterval(function() {
                if (gamePhase === "BUILD" || gamePhase === "DEFEND") {
                    // Loop twice to spawn 2 traps!
                    for (let i = 0; i < 2; i++) {
                        droppedTraps.push({
                            x: Math.random() * (canvas.width - 20),
                            y: Math.random() * (canvas.height - 20),
                            size: 20, // Slightly smaller while on the ground
                            color: "#d35400" // A dark orange color for uncollected traps
                        });
                    }
                }
            }, 5000);

            // 2. Define our player with a speed property!
            let player = {
                x: 400,
                y: 300,
                size: 30,
                color: "#3498db",
                speed: 4 // The player moves 5 pixels per frame
            };

            // 3. Create a tracker to remember which keys are currently being pressed down
            const keys = {
                ArrowUp: false,
                ArrowDown: false,
                ArrowLeft: false,
                ArrowRight: false
            };

            // ... (Keep your canvas, ctx, player, and keys setup up here) ...

            // 1. Create an empty array to hold all the walls the player builds
            let walls = [];
            let wallSize = 30; // Every wall block will be 30x30 pixels

            // 2. Listen for mouse clicks on the canvas!
            // 2. Listen for mouse clicks on the canvas!
            // 2. Listen for mouse clicks on the canvas!
            // 2. Listen for mouse clicks on the canvas!
            // 2. Listen for mouse clicks on the canvas!
            canvas.addEventListener("mousedown", function(event) {
                let clickX = event.offsetX;
                let clickY = event.offsetY;

                // IF holding BUILD tool: Add a block
                if (activeTool === "BUILD" && gamePhase === "BUILD" && walls.length < maxBlocks) {
                    walls.push({
                        x: clickX - (wallSize / 2),
                        y: clickY - (wallSize / 2),
                        size: wallSize,
                        color: "#7f8c8d",
                        hp: 100
                    });
                } 
                // IF holding DELETE tool: Remove a block
                else if (activeTool === "DELETE" && gamePhase === "BUILD") {
                    for (let i = walls.length - 1; i >= 0; i--) {
                        let w = walls[i];
                        if (clickX >= w.x && clickX <= w.x + w.size && clickY >= w.y && clickY <= w.y + w.size) {
                            walls.splice(i, 1); 
                            break; 
                        }
                    }
                }
                // IF holding SHOOT tool: Fire a slime projectile!
                else if (activeTool === "SHOOT") {
                    let dx = clickX - player.x;
                    let dy = clickY - player.y;
                    let distance = Math.sqrt(dx * dx + dy * dy); 

                    slimes.push({
                        x: player.x + (player.size / 2), 
                        y: player.y + (player.size / 2),
                        vx: (dx / distance) * 7, 
                        vy: (dy / distance) * 7, 
                        size: 10,
                        color: "#2ecc71" 
                    });
                }
                // IF holding TRAP tool: Place a sticky trap (ONLY if you have inventory!)
                else if (activeTool === "TRAP" && gamePhase === "BUILD") {
                    if (trapInventory > 0) {
                        traps.push({
                            x: clickX - 15,
                            y: clickY - 15,
                            size: 30,
                            color: "#e67e22" // A sticky, gooey orange color
                        });
                        trapInventory--; // Subtract 1 from your inventory!
                    }
                }
            });
            
                window.addEventListener("keydown", function(event) {
                    if (keys.hasOwnProperty(event.key)) {
                        keys[event.key] = true;
                    }

                    // Switch tools using the number keys!
                    if (event.key === "1") { activeTool = "BUILD"; }
                    if (event.key === "2") { activeTool = "DELETE"; }
                    if (event.key === "3") { activeTool = "SHOOT"; } /* *Agent*2* */
                    if (event.key === "4") { activeTool = "TRAP"; } /* *Agent*2* */
                    // Restart the game by pressing 'R'
                    if (event.key === "r" || event.key === "R") {
                        if (gamePhase === "GAMEOVER" || gamePhase === "VICTORY") {
                            location.reload(); // This instantly reloads the webpage to start fresh!
                        }
                    }
                    // Go to the next wave by pressing 'N'
                    // Go to Next Wave by pressing 'N'
                    // Go to Next Wave by pressing 'N'
                    if (event.key === "n" || event.key === "N") {
                        if (gamePhase === "VICTORY") {
                            currentWave++; // Go up a wave!
                            maxBlocks += 20; // Give 20 extra blocks
                            buildTimeLeft = 30; // Reset the 30 second timer
                            boss.maxHealth += 250; // Boss gets 250 MORE health!

                            // --- NEW CODE: Clear uncollected items from the floor ---
                            potions = []; 
                            droppedTraps = []; 

                            gamePhase = "BUILD"; // Send player back to build mode
                        }
                    }
            });
           
            window.addEventListener("keyup", function(event) {
                if (keys.hasOwnProperty(event.key)) { keys[event.key] = false; }
            }); 

            // 3. THE GAME LOOP
                        function gameLoop() {
                        // Step A: Update player position (with boundaries!)
                        if (keys.ArrowUp && player.y > 0) { player.y -= player.speed; }    
                        if (keys.ArrowDown && player.y < canvas.height - player.size) { player.y += player.speed; }  
                        if (keys.ArrowLeft && player.x > 0) { player.x -= player.speed; }  
                        if (keys.ArrowRight && player.x < canvas.width - player.size) { player.x += player.speed; }

                        // Step B: Clear the old canvas
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        // Step C: Draw all the custom walls the player built!
                        for (let i = 0; i < walls.length; i++) {
                            let currentWall = walls[i];
                            ctx.fillStyle = currentWall.color;
                            ctx.fillRect(currentWall.x, currentWall.y, currentWall.size, currentWall.size);
                        }

                        // Step D: Draw the player
                        ctx.fillStyle = player.color;
                        ctx.fillRect(player.x, player.y, player.size, player.size); 

                        // Step I: Draw Traps and Check if the Boss steps on them!
                        for (let t = traps.length - 1; t >= 0; t--) {
                            let currentTrap = traps[t];

                            // Draw the trap
                            ctx.fillStyle = currentTrap.color;
                            ctx.fillRect(currentTrap.x, currentTrap.y, currentTrap.size, currentTrap.size);

                            // Check if the boss stepped on this trap
                            if (boss.x < currentTrap.x + currentTrap.size && boss.x + boss.size > currentTrap.x &&
                                boss.y < currentTrap.y + currentTrap.size && boss.y + boss.size > currentTrap.y) {

                                // The trap caught him! Slow his speed way down.
                                boss.speed = 0.2; 

                                // After 3 seconds (3000 milliseconds), the boss breaks free and speeds back up
                                setTimeout(function() {
                                    boss.speed = 1; 
                                }, 3000);

                                // Destroy the trap so it can't be used twice
                                traps.splice(t, 1); 
                            }
                        }

                        // Step F: Draw and move the Boss (Only in the DEFEND phase!)
                        if (gamePhase === "DEFEND" && boss.active === true) {

                            let isTouchingWall = false; // Our new flag!

                            // 1. Check if the boss is crashing into any walls FIRST
                            for (let i = walls.length - 1; i >= 0; i--) {
                                let w = walls[i];

                                // Bounding Box Collision
                                if (boss.x < w.x + w.size && boss.x + boss.size > w.x &&
                                    boss.y < w.y + w.size && boss.y + boss.size > w.y) {

                                    isTouchingWall = true; // He hit a wall! 

                                    // The boss chews on the wall
                                    w.hp -= 1;
                                    w.color = "#bdc3c7"; 

                                    // Destroy wall if health drops to 0
                                    if (w.hp <= 0) {
                                        walls.splice(i, 1); 
                                    }
                                }
                            }

                            // 2. Simple AI: ONLY walk forward if he is NOT touching a wall
                            if (isTouchingWall === false) {
                                if (boss.x < player.x) { boss.x += boss.speed; }
                                if (boss.x > player.x) { boss.x -= boss.speed; }
                                if (boss.y < player.y) { boss.y += boss.speed; }
                                if (boss.y > player.y) { boss.y -= boss.speed; }
                            }

                            // Did the boss catch the player?
                            if (boss.x < player.x + player.size && boss.x + boss.size > player.x &&
                                boss.y < player.y + player.size && boss.y + boss.size > player.y) {
                                gamePhase = "GAMEOVER"; // The boss got you!
                            }

                            // Draw the giant boss on the canvas
                            ctx.fillStyle = boss.color;
                            ctx.fillRect(boss.x, boss.y, boss.size, boss.size);

                            // Check if any slimes are hitting the boss
                            for (let j = slimes.length - 1; j >= 0; j--) {
                                let s = slimes[j];

                                if (s.x < boss.x + boss.size && s.x + s.size > boss.x &&
                                    s.y < boss.y + boss.size && s.y + s.size > boss.y) {

                                    // Deal 10 damage if buffed, otherwise 5!
                                    if (doubleDamageTime > 0) { boss.health -= 10; } 
                                    else { boss.health -= 5; }

                                    slimes.splice(j, 1); 

                                    if (boss.health <= 0) {
                                        boss.active = false;
                                        score += 1000; // +1000 for defeating the boss!
                                        gamePhase = "VICTORY";
                                    }
                                }
                            }
                        } // End of Boss Logic
                // Step G: Move and draw the Slimes!
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
            
                // Step H: Draw the Potions!
                // Step H: Draw and Collect Potions!
                for (let k = potions.length - 1; k >= 0; k--) {
                    let p = potions[k];

                    // Draw the potion (a shiny gold square)
                    ctx.fillStyle = p.color;
                    ctx.fillRect(p.x, p.y, p.size, p.size);

                    // Check if the PLAYER touches the potion
                // Check if the PLAYER touches the potion
                if (player.x < p.x + p.size && player.x + player.size > p.x &&
                    player.y < p.y + p.size && player.y + player.size > p.y) {

                    score += 100; // Potion score

                    if (p.type === "TIME" && gamePhase === "BUILD") {
                        buildTimeLeft += 5; 
                    } else if (p.type === "DAMAGE") {
                        doubleDamageTime += 30; // 30 SECONDS of double damage!
                    }
                    potions.splice(k, 1); 
                }
                }
                // Step J: Draw and Collect Dropped Traps
                for (let dt = droppedTraps.length - 1; dt >= 0; dt--) {
                    let d = droppedTraps[dt];

                    // Draw the uncollected trap on the ground
                    ctx.fillStyle = d.color;
                    ctx.fillRect(d.x, d.y, d.size, d.size);

                    // Check if the player runs over it
                    if (player.x < d.x + d.size && player.x + player.size > d.x &&
                        player.y < d.y + d.size && player.y + player.size > d.y) {

                        trapInventory++; // Add 1 trap to your inventory!
                        droppedTraps.splice(dt, 1); // Remove it from the ground
                    }
                }
                // --- DRAW THE TEXT HUD ---
                    // --- DRAW THE TEXT HUD ---
                    ctx.fillStyle = "white"; 
                    ctx.font = "20px Arial"; 
                    ctx.fillText("Wave: " + currentWave + " | Phase: " + gamePhase, 20, 30); // Wave added!

                    if (gamePhase === "BUILD") {
                        ctx.fillText("Time until Boss: " + buildTimeLeft, 20, 60);
                        ctx.fillText("Blocks: " + walls.length + " / " + maxBlocks, 20, 90); 
                    } else if (gamePhase === "DEFEND") {
                        ctx.fillText("DEFEND YOUR FORTRESS!", 20, 60);
                        ctx.fillText("Boss HP:", 20, 90);
                        ctx.fillStyle = "red";
                        ctx.fillRect(110, 75, 150, 15); 
                        ctx.fillStyle = "#2ecc71"; 

                        // Fixed Math: Use boss.maxHealth so the bar scales perfectly every wave!
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

                    // Draw the Double Damage Timer if active!
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
