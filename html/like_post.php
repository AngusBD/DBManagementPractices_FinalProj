<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // 檢查用戶是否已按讚
    $stmt = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // 如果已按讚，則執行取消按讚
        $stmt->close();
        $delete_stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $post_id, $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        header("Location: index.php");
    } else {
        // 如果未按讚，則執行按讚
        $stmt->close();
        $insert_stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $post_id, $user_id);
        $insert_stmt->execute();
        $insert_stmt->close();
        header("Location: index.php");
    }

    // 返回到動態牆頁面
    header("Location: index.php");
    exit();
}
?>
