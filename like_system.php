<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

if ($item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item']);
    exit();
}

// Check if user already liked
$check_sql = "SELECT id FROM item_likes WHERE item_id = $item_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    // Unlike: remove like
    $delete_sql = "DELETE FROM item_likes WHERE item_id = $item_id AND user_id = $user_id";
    mysqli_query($conn, $delete_sql);
    $update_sql = "UPDATE items SET likes_count = likes_count - 1 WHERE id = $item_id";
    mysqli_query($conn, $update_sql);
    $liked = false;
} else {
    // Like: add like
    $insert_sql = "INSERT INTO item_likes (item_id, user_id) VALUES ($item_id, $user_id)";
    mysqli_query($conn, $insert_sql);
    $update_sql = "UPDATE items SET likes_count = likes_count + 1 WHERE id = $item_id";
    mysqli_query($conn, $update_sql);
    $liked = true;
}

// Get new count
$count_sql = "SELECT likes_count FROM items WHERE id = $item_id";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$new_count = $count_row['likes_count'];

echo json_encode(['success' => true, 'liked' => $liked, 'count' => $new_count]);
?>