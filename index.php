<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Marketplace | Inventory System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav>
        <div class="nav-left">
            <h2>Community Marketplace</h2>
        </div>
        <div class="nav-right">
            <button type="button" class="profile-button" aria-expanded="false" aria-controls="profile-menu">👤</button>
            <div class="dropdown-menu" id="profile-menu">
                <a href="index.php">Home</a>
                <a href="admin.php">Admin Dashboard</a>
                <a href="my_items.php">My Items</a>
                <a href="login.php">Login</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <h1>System Inventory Marketplace</h1>
    
    <div class="upload-link">
        <a href="upload.php" class="btn-primary">+ Register New Asset</a>
    </div>

    <div class="search-container">
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search items..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn-search">Search</button>
        </form>
    </div>

    <div class="container">
        <?php
        // Handle search
        $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

        // Updated Query: Show everything except SOLD items, with optional search
        $query = "SELECT * FROM items WHERE status != 'Sold'";
        if ($search) {
            $query .= " AND (item_name LIKE '%$search%' OR description LIKE '%$search%')";
        }
        $query .= " ORDER BY created_at DESC";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $status = $row['status'];
                ?>
                <div class="card">
                    <img src="uploads/<?php echo $row['image_path']; ?>" alt="Item Image">
                    
                    <div class="card-content">
                        <span class="status-badge <?php echo ($status == 'Available') ? 'available' : 'reserved'; ?>">
                            <?php echo $status; ?>
                        </span>

                        <span class="price">$<?php echo number_format($row['price'], 2); ?></span>
                        <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <a href="tel:<?php echo $row['phone_number']; ?>" class="phone">
                            📞 Contact Owner: <?php echo $row['phone_number']; ?>
                        </a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div style='grid-column: 1/-1; text-align: center; padding: 50px;'>
                    <h3>No items available for management at this time.</h3>
                  </div>";
        }
        ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var profileButton = document.querySelector('.profile-button');
            var dropdownMenu = document.querySelector('.dropdown-menu');

            if (!profileButton || !dropdownMenu) {
                return;
            }

            profileButton.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdownMenu.classList.toggle('open');
                var expanded = dropdownMenu.classList.contains('open');
                profileButton.setAttribute('aria-expanded', expanded);
            });

            document.addEventListener('click', function(event) {
                if (!dropdownMenu.contains(event.target) && !profileButton.contains(event.target)) {
                    dropdownMenu.classList.remove('open');
                    profileButton.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
</body>
</html>