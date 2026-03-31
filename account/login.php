<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $file = 'users.json';

    if (file_exists($file)) {
        $currentData = file_get_contents($file);
        $users = json_decode($currentData, true);

        // Verify user exists and password is correct
        if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
            // Success! Save their name in the session
            $_SESSION['username'] = $username;
            
            // Redirect them to the game page (Change 'game.php' to whatever your main game file is named!)
            header("Location: ../lobby.php"); 
            exit();
        } else {
            $message = "<p style='color: red;'>Invalid username or password.</p>";
        }
    } else {
        $message = "<p style='color: red;'>No accounts found. Please sign up first.</p>";
    }
}
?>
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
            <?php echo $message; ?>
            
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