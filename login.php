<?php 
session_start(); 
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        // Login successful! Save user info in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        
        header("Location: index.php");
        exit();
    } else {
        $error = "You entered the wrong information";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Two Hands Marketplace</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Login</h2>
            
            <?php if(isset($error)) echo "<p class='error-message'>❌ $error</p>"; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        placeholder="Enter your email" 
                        required
                        class="form-input"
                    >
                    <span class="input-hint">Please enter a valid email address</span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        placeholder="Enter your password" 
                        required
                        class="form-input"
                    >
                    <span class="input-hint">Password is required</span>
                </div>

                <button type="submit" class="login-button">Login Now</button>
            </form>

            <p class="register-link">New here? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>