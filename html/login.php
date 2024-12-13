<?php
// 啟動會話，必須放在所有輸出之前
session_start();

// 檢查是否為 POST 請求並處理登入邏輯
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    include 'db.php'; // 連接資料庫

    $username = $_POST['username'];
    $password = $_POST['password'];

    // 準備並執行查詢
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // 驗證密碼
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id; // 將用戶 ID 存入會話

            // 登入成功，重定向到主頁
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- 登入表單 -->
<form action="login.php" method="POST">
    <input type="text" name="username" placeholder="Username or Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>

<!-- 註冊按鈕 -->
<p>還沒有帳號嗎？ <a href="register.php"><button type="button">註冊</button></a></p>

<!-- 錯誤訊息顯示 -->
<?php if (isset($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>
