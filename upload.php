<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="nav-left">
            <h2>🏪 MarketHub</h2>
        </div>
        <div class="nav-right">
            <a href="index.php" class="btn btn-secondary">← Back to Marketplace</a>
            <a href="admin.php" class="btn btn-secondary">Admin Dashboard</a>
        </div>
    </nav>

    <main class="upload-shell">
        <div class="upload-card">
            <div class="upload-header">
                <div>
                    <span class="page-label">New Listing</span>
                    <h1>List Your Item</h1>
                    <p class="upload-note">Create a new listing with multiple images and contact details. Showcase your product with up to 5 high-quality photos.</p>
                </div>
            </div>

            <form class="upload-form" action="upload_system.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="item_name">Item Name</label>
                    <input id="item_name" type="text" name="item_name" placeholder="Enter item name" required>
                </div>

                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Describe the item in detail" required></textarea>
                </div>

                <div>
                    <label for="price">Price ($)</label>
                    <input id="price" type="number" step="0.01" name="price" placeholder="Enter price" required>
                </div>

                <div>
                    <label for="phone">Phone Number</label>
                    <input id="phone" type="text" name="phone" placeholder="Enter phone number" required>
                </div>

                <div>
                    <label for="images">Product Images (up to 5)</label>
                    <div class="image-upload-container">
                        <input id="images" type="file" name="images[]" accept="image/*" multiple required>
                        <p class="image-help-text">You can upload up to 5 images. Supported formats: JPG, PNG, GIF, WebP</p>
                        <div id="imagePreview" class="image-preview-grid"></div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">List Item</button>
            </form>
        </div>
    </main>

    <script>
        // Image preview functionality
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            const files = this.files;
            
            if (files.length > 5) {
                alert('Maximum 5 images allowed');
                this.value = '';
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const previewImg = document.createElement('img');
                    previewImg.src = e.target.result;
                    previewImg.alt = `Preview ${i + 1}`;
                    previewImg.className = 'preview-thumbnail';
                    previewContainer.appendChild(previewImg);
                };

                reader.readAsDataURL(file);
            }
        });

        const savedTheme = localStorage.getItem('inventoryTheme') || 'light';
        document.body.classList.toggle('dark-theme', savedTheme === 'dark');
    </script>
</body>
</html>