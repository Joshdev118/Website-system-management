<?php
include 'db.php';
session_start();

// Simple admin password check
$admin_password = 'admin123'; // Change this to your desired password

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = 'Incorrect password.';
    }
}

if (!isset($_SESSION['admin_logged_in'])) {
    // Show password form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <link rel="stylesheet" href="css/admin.css">
        <style>
            .login-container {
                max-width: 400px;
                margin: 100px auto;
                padding: 20px;
                background: var(--surface);
                border-radius: 10px;
                box-shadow: 0 10px 30px var(--shadow);
            }
            .login-container h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
            }
            .form-group input {
                width: 100%;
                padding: 10px;
                border: 1px solid var(--border);
                border-radius: 5px;
                background: var(--surface-muted);
                color: var(--text-light);
            }
            .btn {
                width: 100%;
                padding: 10px;
                background: var(--accent-blue);
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .btn:hover {
                background: var(--accent-gold);
            }
            .error {
                color: red;
                text-align: center;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Admin Access</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="password">Admin Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// If logged in, show admin panel
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div>
                <h2>Admin Panel</h2>
                <p>Welcome to the admin dashboard.</p>
            </div>
            <div class="actions">
                <a class="btn" href="index.php">← Back to Store</a>
                <a class="btn" href="?logout=1">Logout</a>
            </div>
        </div>

        <div style="text-align: center; padding: 50px;">
            <h3>Admin Access Granted</h3>
            <p>You are now logged in as admin.</p>
            <!-- Add any admin functions here if needed -->
        </div>
    </div>

    <script>
        // Apply saved theme
        function applySavedTheme() {
            const savedTheme = localStorage.getItem('inventoryTheme') || 'dark';
            if (savedTheme === 'light') {
                document.body.classList.add('light-theme');
            } else {
                document.body.classList.remove('light-theme');
            }
        }
        applySavedTheme();
    </script>
</body>
</html>

<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}
?>