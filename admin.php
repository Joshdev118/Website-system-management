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
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div>
                <h2>Admin Database Management</h2>
                <p>Below is the list of all items currently stored in the system.</p>
            </div>
            <div class="actions">
                <a class="btn" href="index.php">← Back to Store</a>
            </div>
        </div>

        <div class="topbar" style="margin-top: 0; gap: 1rem;">
            <div class="search-wrapper">
                <input type="search" id="searchInput" placeholder="Search by name, description, price or contact...">
                <span class="search-icon">🔍</span>
            </div>
        </div>

        <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Contact info</th>
                <th>Publisher</th>
                <th>Date Posted</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all items from the database with publisher info
            $sql = "SELECT items.*, users.username AS publisher FROM items LEFT JOIN users ON items.user_id = users.id ORDER BY items.id DESC";
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
                    echo "<td>" . ($row['publisher'] ?? 'Unknown') . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='text-align:center;'>No items found in database.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const query = searchInput.value.trim().toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function applySavedTheme() {
        const savedTheme = localStorage.getItem('inventoryTheme') || 'dark';
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
        } else {
            document.body.classList.remove('light-theme');
        }
    }

    searchInput.addEventListener('input', filterTable);
    applySavedTheme();
</script>
</body>
</html>