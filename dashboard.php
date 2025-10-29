<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];
$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>แดชบอร์ด</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="style/dashboard.css" rel="stylesheet" />  <!-- ลิงก์ไฟล์ CSS -->
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-orange">
  <div class="container">
    <a class="navbar-brand text-white" href="#">ระบบสมาชิก</a>
    <div class="d-flex">
      <span class="navbar-text text-white me-3">ยินดีต้อนรับ<strong><?=htmlspecialchars($username)?></strong></span>
      <a href="logout.php" class="btn btn-logout">ออกจากระบบ</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="card shadow p-4">
    <h2 class="text-orange">แดชบอร์ดของคุณ</h2>
    <p>นี่คือหน้าหลักหลังจากที่คุณเข้าสู่ระบบสำเร็จ</p>
    <?php if ($email): ?>
      <p>อีเมลของคุณ: <?=htmlspecialchars($email)?></p>
    <?php endif; ?>
  </div>
</div>

<script src="js/dashboard.js"></script> <!-- ลิงก์ไฟล์ JS (ถ้ามี) -->
</body>
</html>
