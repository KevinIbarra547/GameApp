<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Fortress Fall</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../game.php">Play Game</a></li>
            <li><a href="../leaderboard.php">Leaderboard</a></li>
            <li><a href="../about.php">About</a></li>
        </ul>
    </nav>
    <main>
        <h1>Create Account</h1>
        <div class="auth-form">
            <form action="signup.php" method="POST">
                <input type="text" name="username" placeholder="Choose Username" required>
                <input type="password" name="password" placeholder="Choose Password" required>
                <button type="submit" class="btn">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </main>
</body>
</html>