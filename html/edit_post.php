<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // 驗證貼文是否屬於當前用戶
    $stmt = $conn->prepare("SELECT content, image FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($content, $image);
        $stmt->fetch();
    } else {
        die("無權編輯此貼文或貼文不存在。");
    }
    $stmt->close();
}

// 更新貼文邏輯
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_post'])) {
    $post_id = $_POST['post_id'];
    $updated_content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // 檢查是否上傳了新圖片
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_path = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);

        // 更新內容和圖片
        $stmt = $conn->prepare("UPDATE posts SET content = ?, image = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $updated_content, $image_path, $post_id, $user_id);
    } else {
        // 僅更新內容
        $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $updated_content, $post_id, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "更新失敗。";
    }
}
?>

<!-- 編輯表單 -->
<form action="edit_post.php" method="POST" enctype="multipart/form-data">
    <textarea name="content" required><?php echo htmlspecialchars($content); ?></textarea>
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">

    <!-- 當前圖片顯示 -->
    <?php if (!empty($image)): ?>
        <p>當前圖片：</p>
        <img src="<?php echo htmlspecialchars($image); ?>" alt="Post image" style="max-width: 200px;">
    <?php endif; ?>

    <!-- 新圖片上傳 -->
    <p>更換圖片：</p>
    <input type="file" name="image">

    <button type="submit" name="update_post">更新</button>
</form>
