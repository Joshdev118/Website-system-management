<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // If user is not logged in, send them to the login page
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell an Item</title>
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
        .form-container { max-width: 400px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
        input, textarea { width: 100%; margin-bottom: 15px; padding: 8px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>

    <nav style="padding: 10px; background: #f4f4f4; margin-bottom: 20px;">
    <a href="index.php" style="margin-right: 15px;">Home</a>
    <a href="Admin/admin.php" style="color: red; font-weight: bold;">Admin Dashboard</a>
</nav>

<div class="form-container">
    <h2>Post Your Item</h2>
    <form action="upload_system.php" method="POST" enctype="multipart/form-data">
        <label>Item Name</label>
        <input type="text" name="item_name" placeholder="Enter item name" required>

        <label>Description</label>
        <textarea name="description" rows="4" placeholder="Enter item description" required></textarea>

        <label>Price ($)</label>
        <input type="number" step="0.01" name="price" placeholder="Enter price" required>

        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="Enter phone number" required>

        <label>Item Image</label>
        <input type="file" name="image" accept="image/*" title="Select an image" required>

        <button type="submit">Upload Item</button>
    </form>
</div>

</body>
</html>