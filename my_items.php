<?php
session_start();
// Include your database connection file (using mysqli for XAMPP)
include 'db.php'; 

// 1. Security Check: Redirect to login if the session isn't set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// 2. Fetch only the items uploaded by THIS user
// We fetch the new 'status' column we added
$query = "SELECT id, item_name, price, image_path, status, created_at FROM items WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inventory Management</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #555; text-transform: uppercase; font-size: 0.85em; }
        
        .item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        
        /* Status Badge Styling */
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8em; font-weight: bold; display: inline-block; }
        .status-available { background-color: #e1f5fe; color: #01579b; } /* Blue/Active */
        .status-reserved { background-color: #fff3e0; color: #ef6c00; }  /* Orange/Pending */
        .status-sold { background-color: #e8f5e9; color: #2e7d32; }      /* Green/Done */

        .btn-handover { background-color: #28a745; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 0.9em; }
        .btn-handover:hover { background-color: #218838; }
        .disabled-text { color: #999; font-style: italic; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="container">
    <h1>My Listed Assets</h1>
    <p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>

    <table>
        <thead>
            <tr>
                <th>Preview</th>
                <th>Item Name</th>
                <th>Price</th>
                <th>Current Status</th>
                <th>Date Added</th>
                <th>Management Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="<?php echo $row['image_path']; ?>" alt="Item" class="item-img">
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['item_name']); ?></strong></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <?php 
                            $status = $row['status'];
                            // Map the database status to a CSS class
                            $class = 'status-available';
                            if ($status == 'Reserved') $class = 'status-reserved';
                            if ($status == 'Sold') $class = 'status-sold';
                        ?>
                        <span class="badge <?php echo $class; ?>">
                            <?php echo $status; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <?php if ($status != 'Sold'): ?>
                            <a href="confirm_handover.php?id=<?php echo $row['id']; ?>" 
                               class="btn-handover" 
                               onclick="return confirm('Confirming handover will mark this item as SOLD. Continue?')">
                               Confirm Handover
                            </a>
                        <?php else: ?>
                            <span class="disabled-text">Asset Handed Over</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 30px;">
                        No items found in your inventory. <a href="upload.php">Start selling now.</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <a href="index.php" style="text-decoration:none; color:#666;">← Back to Main Dashboard</a>
</div>

</body>
</html>