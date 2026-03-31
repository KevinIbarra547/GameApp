<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    // Adjust the path to users.json if it needs to be in a different folder
    $file = 'users.json'; 

    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    $currentData = file_get_contents($file);
    $users = json_decode($currentData, true);

    if (isset($users[$username])) {
        $message = "<p style='color: red;'>Username already taken. Try another.</p>";
    } else {
        // Create the new user with base stats
        $users[$username] = [
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "highScore" => 0,
            "maxWave" => 1
        ];

        file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
        $message = "<p style='color: #4CAF50;'>Account created! You can now login.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Fortress Fall</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav>
    </nav>
    <main>
        <h1>Create Account</h1>
        <div class="auth-form">
            <?php echo $message; ?> 
            
            <form action="signup.php" method="POST">
                <input type="text" name="username" placeholder="Choose Username" required>
                <input type="password" name="password" placeholder="Choose Password" required>
                <button type="submit" class="btn">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <p><a href="../index.php">Back to Home</a></p>
        </div>
    </main>
</body>
</html>