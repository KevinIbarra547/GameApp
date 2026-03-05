<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Fortress Fall</title>
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
        <h1>Login</h1>
        <div class="auth-form">
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </div>
    </main>
</body>
</html>