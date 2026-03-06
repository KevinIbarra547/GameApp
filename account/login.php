<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Fortress Fall</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav>
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
            <p><a href="../index.php">Back to Home</a></p>
        </div>
    </main>
</body>
</html>