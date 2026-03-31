<style>
    /* This internal style block lets us quickly set the page background */
    /* #4CAF50 is the green from your game canvas! */
    body {
        background-color: #4CAF50; 
        color: white; /* Making text white so it's easy to read on green */
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }

    /* Styling our main container to look like a game menu */
    main {
        background-color: rgba(44, 62, 80, 0.9); /* Dark blue with some transparency */
        padding: 30px;
        border-radius: 15px;
        border: 4px solid #2c3e50;
        max-width: 800px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    /* Ensuring our links look like buttons */
    .back-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #e67e22; /* A nice orange to stand out */
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
    }

    .back-btn:hover {
        background-color: #d35400; /* Darker orange when hovering */
    }
</style>
<main>
            <a href="index.php" style="text-decoration: none; color: inherit;">
                <h1>Fortress Fall</h1>
        </a>
        
        <h2>How to Play</h2>
        <p>
            Welcome to Fortress Fall! In this game, you have two phases:
            <br>
            <strong>1. Build Phase:</strong> You have 30 seconds to place blocks and build a fortress.
            <br>
            <strong>2. Defend Phase:</strong> The Boss attacks! Use your slimes and traps to defeat the boss before it reaches you.
        </p>

        <h2>Project Inspiration</h2>
        <p>
            The vision for <em>Fortress Fall</em> was heavily inspired by the game <strong>Fortnite</strong>. 
            My original goal was to create a 3D world where players could search for loot in chests and build 
            complex forts to survive. 
        </p>
        <p>
            Since building a full 3D engine in JavaScript for this project was a bit too complex, I decided 
            to create a unique 2D spin-off. I focused on taking the best parts of the inspiration—building 
            under pressure and defending a base—and turning them into a fun experience.
        </p>

        <h2>Controls</h2>
        <ul>
            <li><strong>Press '1':</strong> Build Mode (Place walls)</li>
            <li><strong>Press '2':</strong> Delete Mode (Remove walls)</li>
            <li><strong>Press '3':</strong> Shoot Mode (Fire slimes at the boss)</li>
            <li><strong>Press '4':</strong> Trap Mode (Place dropped traps)</li>
        </ul>

        <h2>Credits</h2>
        <p>Developed by: Your Name</p>

        <h2>AI Documentation</h2>
        <p>
            During this project, I used an AI assistant to help me with:
            <ul>
                <li>Troubleshooting bugs (like fixing the wall placement logic).</li>
                <li>Writing the logic for my custom power-ups.</li>
                <li>Understanding how HTML canvas drawing works.</li>
            </ul>
    <a href="index.php" class="back-btn">Return to Main Menu</a>
        </p>
    </main>