<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketHub | Buy & Sell Anything</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav>
        <div class="nav-left">
            <h2>🏪 MarketHub</h2>
        </div>
        <div class="nav-right">
            <button id="themeToggle" class="theme-toggle" aria-label="Toggle theme">🌙</button>
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

<?php
    // Search and category filters
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
    $selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : 'All';
    $allowedCategories = ['All', 'Clothing & Accessories', 'Electronics & Gadgets', 'Home & Furniture', 'Vehicles & Parts', 'Books & Media', 'Sports & Leisure', 'Collectibles & Antiques', 'Appliances', 'Jewelry & Watches', 'Toys & Games', 'Other'];
    if (!in_array($selectedCategory, $allowedCategories, true)) {
        $selectedCategory = 'All';
    }

    $categoryColumnExists = false;
    $colResult = mysqli_query($conn, "SHOW COLUMNS FROM items LIKE 'category'");
    if ($colResult && mysqli_num_rows($colResult) > 0) {
        $categoryColumnExists = true;
    }
    if (!$categoryColumnExists) {
        $selectedCategory = 'All';
    }

    $searchParam = isset($_GET['search']) && $_GET['search'] !== '' ? '&search=' . urlencode($_GET['search']) : '';
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover Amazing Products</h1>
            <p>Buy and sell quality items in your community. Connect with local sellers and find exactly what you need.</p>
            <a href="#marketplace" class="btn-primary btn-large">Explore Products</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">📦</div>
            <h3>Wide Selection</h3>
            <p>Thousands of products from local sellers, all in one place.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💰</div>
            <h3>Great Prices</h3>
            <p>Direct from sellers - no middlemen, just fair pricing.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👥</div>
            <h3>Local Community</h3>
            <p>Connect with sellers near you and support local business.</p>
        </div>
    </section>

    <!-- Search Section -->
    <div class="search-section" id="marketplace">
        <div class="search-container">
            <form method="GET" action="index.php">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>">
                <input type="text" name="search" placeholder="Search for products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn-search">Search</button>
            </form>
        </div>

        <div class="category-filters">
            <?php foreach ($allowedCategories as $category):
                $queryString = 'index.php?category=' . urlencode($category) . $searchParam;
                $activeClass = $category === $selectedCategory ? ' active' : '';
            ?>
                <a href="<?php echo htmlspecialchars($queryString); ?>" class="category-button<?php echo $activeClass; ?>">
                    <?php echo htmlspecialchars($category); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="upload-link">
            <a href="upload.php" class="btn-primary">+ List Your Item</a>
        </div>
    </div>

    <!-- Marketplace Listings -->
    <div class="marketplace-section">
        <h2 class="section-title">Featured Listings</h2>
        <div class="container"></div>

        <?php
        // Build marketplace query using search and category filters
        $query = "SELECT items.*, 
                         (SELECT COUNT(*) FROM item_likes WHERE item_likes.item_id = items.id) AS likes_count,
                         CASE WHEN " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0) . " > 0 THEN 
                             (SELECT COUNT(*) FROM item_likes WHERE item_likes.item_id = items.id AND item_likes.user_id = " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0) . ") 
                         ELSE 0 END AS user_liked
                  FROM items WHERE status != 'Sold'";
        if ($search) {
            $query .= " AND (item_name LIKE '%$search%' OR description LIKE '%$search%')";
        }
        if ($selectedCategory !== 'All' && $categoryColumnExists) {
            $query .= " AND category = '" . mysqli_real_escape_string($conn, $selectedCategory) . "'";
        }
        $query .= " ORDER BY created_at DESC";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $status = $row['status'];
                $item_id = $row['id'];
                
                // Fetch all images for this item
                $images = [];
                $tableExists = mysqli_query($conn, "SHOW TABLES LIKE 'item_images'");
                if ($tableExists && mysqli_num_rows($tableExists) > 0) {
                    $img_query = "SELECT image_path FROM item_images WHERE item_id = $item_id ORDER BY created_at ASC";
                    $img_result = mysqli_query($conn, $img_query);
                    while($img_row = mysqli_fetch_assoc($img_result)) {
                        $images[] = $img_row['image_path'];
                    }
                }
                
                // If no images in item_images table, use the legacy image_path
                if (empty($images) && !empty($row['image_path'])) {
                    $images[] = $row['image_path'];
                }
                
                // Use first image for card display
                $displayImage = !empty($images) ? $images[0] : 'placeholder.png';
                ?>
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($displayImage); ?>" alt="Item Image" onclick="openModal(<?php echo $item_id; ?>, <?php echo htmlspecialchars(json_encode($images)); ?>, <?php echo htmlspecialchars(json_encode($row['item_name'])); ?>, <?php echo htmlspecialchars(json_encode($row['description'])); ?>, <?php echo htmlspecialchars(json_encode($row['price'])); ?>, <?php echo htmlspecialchars(json_encode($row['phone_number'])); ?>, <?php echo htmlspecialchars(json_encode($status)); ?>)">
                    
                    <div class="card-content">
                        <div class="card-header">
                            <span class="status-badge <?php echo ($status == 'Available') ? 'available' : 'reserved'; ?>">
                                <?php echo $status; ?>
                            </span>
                            <span class="category-pill"><?php echo htmlspecialchars($row['category'] ?? 'General'); ?></span>
                        </div>

                        <div class="card-body">
                            <span class="price">$<?php echo number_format($row['price'], 2); ?></span>
                            <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>

                        <div class="card-footer">
                            <a href="tel:<?php echo htmlspecialchars($row['phone_number']); ?>" class="phone">
                                📞 Contact: <?php echo htmlspecialchars($row['phone_number']); ?>
                            </a>
                            <div class="like-section">
                                <button class="like-btn <?php echo $row['user_liked'] ? 'liked' : ''; ?>" data-item-id="<?php echo $row['id']; ?>">
                                    ❤️ <span class="like-count"><?php echo $row['likes_count']; ?></span>
                                </button>
                                <button class="btn-secondary" onclick="openModal(<?php echo $item_id; ?>, <?php echo htmlspecialchars(json_encode($images)); ?>, <?php echo htmlspecialchars(json_encode($row['item_name'])); ?>, <?php echo htmlspecialchars(json_encode($row['description'])); ?>, <?php echo htmlspecialchars(json_encode($row['price'])); ?>, <?php echo htmlspecialchars(json_encode($row['phone_number'])); ?>, <?php echo htmlspecialchars(json_encode($status)); ?>)">View Details</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div style='text-align: center; padding: 50px;'>
                    <h3>No items available at this time.</h3>
                    <p style='color: var(--text-lighter);'>Be the first to list something!</p>
                  </div>";
        }
        ?>
        </div>
    </div>

    <!-- Image Review Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <span class="modal-label">Review Item</span>
                    <h2 id="modalTitle"></h2>
                </div>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-inner">
                <div class="modal-left">
                    <div class="image-frame">
                        <img id="modalImage" class="slider-image" src="" alt="Item image">
                        <button class="modal-navigation prev" onclick="changeImage(-1)">&#10094;</button>
                        <button class="modal-navigation next" onclick="changeImage(1)">&#10095;</button>
                    </div>
                    <div class="thumbnail-row" id="thumbnailRow"></div>
                </div>
                <div class="modal-right">
                    <div class="review-details">
                        <div class="detail-row"><strong>Status:</strong> <span id="modalStatus"></span></div>
                        <div class="detail-row"><strong>Price:</strong> <span id="modalPrice"></span></div>
                        <div class="detail-row"><strong>Description:</strong></div>
                        <p id="modalDescription"></p>
                        <div class="detail-row"><strong>Contact:</strong> <a href="#" id="modalContact" class="contact-link"></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/modal.js"></script>
    <script src="js/likes.js"></script>
    <script src="js/interactions.js"></script>

</body>
</html>