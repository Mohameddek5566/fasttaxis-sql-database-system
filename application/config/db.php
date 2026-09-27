<?php
// config/db.php
// Database connection for XAMPP MySQL

$host = getenv("DB_HOST") ?: "127.0.0.1";
$db   = getenv("DB_NAME") ?: "fasttaxis_assignment";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASSWORD") ?: "";
$port = (int) (getenv("DB_PORT") ?: 3306);

$mysqli = new mysqli($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
?>
