<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Community Marketplace</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        h1 { text-align: center; color: #333; }
        .container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        
        /* The Item Card */
        .card { background: white; border-radius: 10px; width: 300px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .card-content { padding: 15px; }
        .price { font-size: 1.25rem; color: #28a745; font-weight: bold; }
        .phone { display: inline-block; margin-top: 10px; padding: 8px 12px; background: #007bff; color: white; border-radius: 5px; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

    <nav>
        <a href="index.php">Home</a>
        <a href="Admin/admin.php">Admin Dashboard</a>
        <a href="my_items.php">My Items</a>
        <a href="login.php">Login</a>
        <a href="logout.php">Logout</a>
    </nav>

    <h1>Second Hand Items For Sale</h1>
    <div style="text-align: center; margin-bottom: 30px;">
        <a href="upload.php" style="text-decoration: none; color: #007bff; font-weight: bold;">+ Sell Your Own Item</a>
    </div>

    <div class="container">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM items ORDER BY created_at DESC");

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="card">
                    <img src="uploads/<?php echo $row['image_path']; ?>" alt="Item Image">
                    
                    <div class="card-content">
                        <span class="price">$<?php echo number_format($row['price'], 2); ?></span>
                        <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <a href="tel:<?php echo $row['phone_number']; ?>" class="phone">
                            📞 Call <?php echo $row['phone_number']; ?>
                        </a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No items for sale yet. Be the first to upload!</p>";
        }
        ?>
    </div>

</body>
</html>