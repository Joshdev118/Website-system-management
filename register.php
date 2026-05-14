<?php 
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    // Secure the password!
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES ('$user', '$email', '$pass')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: login.php?msg=registered");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Two Hands Marketplace</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h2 class="register-title">Create an Account</h2>
            
            <form method="POST" class="register-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username"
                        name="username" 
                        placeholder="Choose a username" 
                        required
                        class="form-input"
                    >
                    <span class="input-hint">Username is required</span>
                </div>

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
                        placeholder="Create a strong password" 
                        required
                        class="form-input"
                    >
                    <span class="input-hint">Password is required</span>
                </div>

                <button type="submit" class="register-button">Register Now</button>
            </form>

            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>