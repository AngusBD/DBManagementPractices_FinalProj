<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['following_id'])) {
    $follower_id = $_SESSION['user_id'];
    $following_id = $_POST['following_id'];

    // 確保未重複追蹤
    $stmt = $conn->prepare("SELECT * FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->bind_param("ii", $follower_id, $following_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $stmt->close();
        $insert_stmt = $conn->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $follower_id, $following_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    header("Location: index.php");
    exit();
}
?>
