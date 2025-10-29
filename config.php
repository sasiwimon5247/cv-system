<?php
// ข้อมูลการเชื่อมต่อฐานข้อมูล
$db_host = "localhost";
$db_name = "4700315_cv";
$db_user = "4700315_cv";
$db_pass = "1669900522462m";

try {
    // เชื่อมต่อ PDO + กำหนด charset
    $conn = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // แสดง error แบบ exception
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );

    $conn->exec("SET time_zone = '+07:00'");

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>