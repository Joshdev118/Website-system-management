<?php
// 1. Database Connection
$conn = mysqli_connect("localhost", "root", "", "marketplace");

if (!$conn) {
    die("Database connection failed.");
}

// 2. Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get text data from form
    $name  = mysqli_real_escape_string($conn, $_POST['item_name']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $phone = $_POST['phone'];

    // 3. Handle Image Upload
    $target_dir = "uploads/";
    
    // Create folder if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        
        // 4. Insert into Database
        session_start();
        $user_id = $_SESSION['user_id']; // Get the ID of the logged-in user

        $sql = "INSERT INTO items (item_name, description, price, phone_number, image_path, user_id) 
        VALUES ('$name', '$desc', '$price', '$phone', '$image_name', '$user_id')";


        if (mysqli_query($conn, $sql)) {
            // Success! Redirect back to the marketplace or a success page
            header("Location: my_items.php?upload=success");
            exit();
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>