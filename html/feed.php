<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id']; // 當前登入用戶的 ID

// 獲取所有貼文
$result = $conn->query("SELECT posts.id, posts.content, posts.image, posts.created_at, users.username, posts.user_id
                        FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        ORDER BY posts.created_at DESC");

while ($post = $result->fetch_assoc()):
?>
    <div class="post">
        <!-- 顯示貼文內容 -->
        <h3><?php echo htmlspecialchars($post['username']); ?></h3>
        <p><?php echo htmlspecialchars($post['content']); ?></p>
        <?php if ($post['image']): ?>
            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post image" style="max-width: 100%;">
        <?php endif; ?>
        <small>發佈時間: <?php echo htmlspecialchars($post['created_at']); ?></small>

        <!-- 僅允許貼文擁有者編輯和刪除 -->
        <?php if ($post['user_id'] == $user_id): ?>
            <!-- 編輯按鈕 -->
            <form action="edit_post.php" method="POST" style="display: inline;">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit">編輯</button>
            </form>

            <!-- 刪除按鈕 -->
            <form action="delete_post.php" method="POST" style="display: inline;">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit" onclick="return confirm('確定要刪除這則貼文嗎？');">刪除</button>
            </form>
        <?php endif; ?>
    </div>
    <hr>
<?php endwhile; ?>
