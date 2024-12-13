<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_post'])) {
    include 'db.php';

    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];
    $image = null;

    if (!empty($_FILES['image']['name'])) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $content, $image);

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<form action="post.php" method="POST" enctype="multipart/form-data">
    <textarea name="content" placeholder="Write something..." required></textarea>
    <input type="file" name="image">
    <button type="submit" name="submit_post">Post</button>
</form>
