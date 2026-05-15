<?php
// 1. Database Connection
$conn = mysqli_connect("localhost", "root", "", "marketplace");

if (!$conn) {
    die("Database connection failed.");
}

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get text data from form
    $name     = mysqli_real_escape_string($conn, $_POST['item_name']);
    $desc     = mysqli_real_escape_string($conn, $_POST['description']);
    $price    = $_POST['price'];
    $phone    = $_POST['phone'];
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, trim($_POST['category'])) : 'Other';

    $allowedCategories = ['Clothing & Accessories', 'Electronics & Gadgets', 'Home & Furniture', 'Vehicles & Parts', 'Books & Media', 'Sports & Leisure', 'Collectibles & Antiques', 'Appliances', 'Jewelry & Watches', 'Toys & Games', 'Other'];
    if (!in_array($category, $allowedCategories, true)) {
        $category = 'Other';
    }

    $categoryColumnExists = false;
    $colResult = mysqli_query($conn, "SHOW COLUMNS FROM items LIKE 'category'");
    if ($colResult && mysqli_num_rows($colResult) > 0) {
        $categoryColumnExists = true;
    }

    // 3. Handle Image Upload
    $target_dir = "uploads/";
    
    // Create folder if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (!isset($_FILES['images']) || !isset($_FILES['images']['tmp_name']) || !is_array($_FILES['images']['tmp_name'])) {
        echo "Please select at least one image to upload.";
        exit();
    }

    $imageCount = count($_FILES['images']['name']);
    if ($imageCount < 1) {
        echo "Please upload at least one image.";
        exit();
    }

    if ($imageCount > 5) {
        echo "Maximum 5 images allowed.";
        exit();
    }

    $savedImages = [];
    $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];

    for ($i = 0; $i < $imageCount; $i++) {
        if (!isset($_FILES['images']['error'][$i]) || $_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        $tmpName = $_FILES['images']['tmp_name'][$i];
        $imageInfo = getimagesize($tmpName);
        if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes, true)) {
            continue;
        }

        $baseName = basename($_FILES['images']['name'][$i]);
        $imageName = time() . "_" . ($i + 1) . "_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $baseName);
        $targetFile = $target_dir . $imageName;

        if (move_uploaded_file($tmpName, $targetFile)) {
            $savedImages[] = $imageName;
        }
    }

    if (count($savedImages) === 0) {
        echo "Unable to upload any of the selected images.";
        exit();
    }

    // Ensure item_images table exists so we can save additional files
    $createImagesTable = "CREATE TABLE IF NOT EXISTS item_images (
        id int(11) NOT NULL AUTO_INCREMENT,
        item_id int(11) NOT NULL,
        image_path varchar(255) NOT NULL,
        created_at timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id),
        KEY item_id (item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    mysqli_query($conn, $createImagesTable);

    $mainImage = $savedImages[0];
    $user_id = $_SESSION['user_id']; // Get the ID of the logged-in user

    $sql = "INSERT INTO items (item_name, description, price, phone_number, image_path, user_id";
    if ($categoryColumnExists) {
        $sql .= ", category";
    }
    $sql .= ") VALUES ('$name', '$desc', '$price', '$phone', '$mainImage', '$user_id'";
    if ($categoryColumnExists) {
        $sql .= ", '$category'";
    }
    $sql .= ")";

    if (mysqli_query($conn, $sql)) {
        $item_id = mysqli_insert_id($conn);
        foreach ($savedImages as $imageItem) {
            $imageInsert = "INSERT INTO item_images (item_id, image_path) VALUES ('$item_id', '$imageItem')";
            mysqli_query($conn, $imageInsert);
        }

        header("Location: my_items.php?upload=success");
        exit();
    } else {
        echo "Database error: " . mysqli_error($conn);
    }
}
?>