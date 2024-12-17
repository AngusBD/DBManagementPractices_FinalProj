<?php
session_start();
include 'db.php'; // 連接資料庫

// 檢查用戶是否登入
if (!isset($_SESSION['user_id'])) {
    // 如果未登入，重定向到登入頁
    header("Location: login.php");
    exit();
}

// 獲取目前登入的用戶資訊
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// 讀取動態牆的貼文
$posts = $conn->query("SELECT posts.id, posts.content, posts.image, posts.created_at, users.username, posts.user_id
                       FROM posts JOIN users ON posts.user_id = users.id
                       ORDER BY posts.created_at DESC");
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>簡易社交網路平台</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 10px 20px;
        }
        .header a {
            color: white;
            text-decoration: none;
            margin-right: 10px;
        }
        .container {
            padding: 20px;
        }
        .post {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .post img {
            max-width: 100%;
            margin-top: 10px;
        }
        .post-actions {
            margin-top: 10px;
        }
        .post-actions button {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>簡易社交網路平台</h1>
        <span>歡迎, <?php echo htmlspecialchars($username); ?>!</span>
        <a href="post.php"><button type="button">發表貼文</button></a>
        <a href="logout.php"><button type="button">登出</button></a>
    </div>
    <div class="container">
        <h2>動態牆</h2>
        <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($post['username']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <?php if ($post['image']): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post image">
                <?php endif; ?>
                <small>發佈時間: <?php echo htmlspecialchars($post['created_at']); ?></small>

                <!-- 按讚按鈕 -->
                <?php
                $likes_stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
                $likes_stmt->bind_param("i", $post['id']);
                $likes_stmt->execute();
                $likes_stmt->bind_result($like_count);
                $likes_stmt->fetch();
                $likes_stmt->close();
                ?>
                <form action="like_post.php" method="POST">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit">👍 按讚 (<?php echo $like_count; ?>)</button>
                </form>

                <!-- 貼文動作 -->
                <?php if ($post['user_id'] == $user_id): ?>
                    <div class="post-actions">
                        <form action="edit_post.php" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit">編輯</button>
                        </form>
                        <form action="delete_post.php" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" onclick="return confirm('確定要刪除這則貼文嗎？');">刪除</button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- 追蹤按鈕 -->
                <?php if ($post['user_id'] != $user_id): ?>
                    <form action="follow_user.php" method="POST">
                        <input type="hidden" name="following_id" value="<?php echo $post['user_id']; ?>">
                        <button type="submit">追蹤</button>
                    </form>
                <?php endif; ?>

                <!-- 留言區域 -->
                <form action="comment_post.php" method="POST">
                    <textarea name="comment" placeholder="留言..." required></textarea>
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit">發送</button>
                </form>

                <!-- 顯示留言 -->
                <?php
                $comments_stmt = $conn->prepare("SELECT comments.comment, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY comments.created_at ASC");
                $comments_stmt->bind_param("i", $post['id']);
                $comments_stmt->execute();
                $comments_stmt->bind_result($comment_text, $comment_username);

                while ($comments_stmt->fetch()): ?>
                    <p><strong><?php echo htmlspecialchars($comment_username); ?>:</strong> <?php echo htmlspecialchars($comment_text); ?></p>
                <?php endwhile; 
                $comments_stmt->close();
                ?>

            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
