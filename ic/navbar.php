<?php
// ไฟล์: includes/navbar.php

// ==========================================================
// 🚩 แก้ไข: การตรวจสอบตัวแปร (Defensive Programming)
// หากตัวแปรเหล่านี้ไม่ได้ถูกกำหนดจากไฟล์ภายนอก ให้กำหนดค่าเริ่มต้นเป็น string เปล่า
// เพื่อป้องกัน Undefined variable warnings และ Deprecated notices
// ==========================================================
if (!isset($username)) {
    // กำหนดค่าเริ่มต้นเป็น 'Guest' หรือ 'ผู้ใช้งาน' หากไม่ได้ล็อกอิน
    $username = $_SESSION['username'] ?? 'ผู้ใช้งาน'; 
}
if (!isset($flash_success)) {
    $flash_success = '';
}
if (!isset($flash_error)) {
    $flash_error = '';
}

// ตรวจสอบสถานะการเข้าสู่ระบบ (เผื่อไว้)
if (!isset($_SESSION['user_id'])) {
    // ป้องกันการเข้าถึงโดยตรง
    // header("Location: login.php");
    // exit;
}
?>
<style>


.navbar {
    background: linear-gradient(to top, #fffaf5 0%, #07407A 50%, #05315C 100%); 
    box-shadow: 0 2px 10px rgba(255, 255, 255, 0.15);
    padding: 15px 5%;
    font-family: 'Prompt', sans-serif;
}

.navbar-brand {
    font-weight: 600;
    font-size: 2.5rem; 
    color: #fa971d; 
}

.text-white {
    font-weight: 500;
    font-size: 1rem;
    color: #ffffff; 
}

.btn-logout {
    background-color: #FF8C00;
    color: white;
    font-weight: 500;
    border-radius: 8px;
    padding: 8px 16px;
    border: none;
    text-decoration: none;
    transition: 0.3s;
}

.btn-logout:hover {
    background-color: #e67e00;
    transform: translateY(-2px);
}
</style>
<nav class="navbar navbar-expand-lg"> 
    <div class="container-fluid">
        <span class="navbar-brand">CV DEV</span>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-white me-3">สวัสดี <?=htmlspecialchars($username)?></span>
            <a href="index.php" class="btn-logout">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<main>
<div class="container mt-4">
    <!-- ส่วนของ Flash Messages -->
    <?php if (!empty($flash_success)): ?>
    <div class="alert alert-success"><?=htmlspecialchars($flash_success)?></div>
    <?php endif; ?>
    <?php if (!empty($flash_error)): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($flash_error)?></div>
    <?php endif; ?>