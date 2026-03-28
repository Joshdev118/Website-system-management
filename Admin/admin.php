<?php 
include 'db.php'; 
session_start();

// Check if logged in AND if they are an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 1) {
    // Not an admin! Redirect to home or show an error
    header("Location: AdminSecurity.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Items</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #333; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .img-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .status-msg { color: green; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Admin Database Management</h2>
    <p>Below is the list of all items currently stored in the system.</p>
    <a href="upload.php">← Back to Store</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Contact info</th>
                <th>Date Posted</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all items from the database
            $sql = "SELECT * FROM items ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    // Display the image from the uploads folder
                    echo "<td><img src='uploads/" . $row['image_path'] . "' class='img-preview'></td>";
                    echo "<td>" . $row['item_name'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>$" . number_format($row['price'], 2) . "</td>";
                    echo "<td>" . $row['phone_number'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>No items found in database.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>