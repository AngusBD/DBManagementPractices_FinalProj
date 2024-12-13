<?php
$host = 'db'; // Docker Compose 中 MySQL 的服務名稱
$user = 'root';
$password = 'root_password'; // docker-compose.yml 中設置的 MYSQL_ROOT_PASSWORD
$database = 'my_database';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
