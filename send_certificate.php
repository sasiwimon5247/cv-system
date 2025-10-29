<?php
// send_certificate.php

// เริ่ม session เพื่อใช้งานข้อมูลของผู้ใช้ที่ล็อกอิน (ถ้าจำเป็น)
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
// ต้องเรียกใช้ config.php เพื่อให้ตัวแปร $conn ใช้งานได้
require_once('config.php'); 

// ----------------------------------------------------
// ส่วนที่ 1: กำหนดค่าคงที่สำหรับบัญชีระบบ (SYSTEM SENDER)
// ----------------------------------------------------

// 1.1 กำหนด Role ของบัญชีผู้ใช้ที่เป็นบัญชีระบบ/แอดมิน ที่ใช้ส่งอีเมล
// *** สำคัญ: เปลี่ยน 'admin' เป็นชื่อ Role ที่คุณใช้สำหรับบัญชีผู้ส่งระบบจริง ***
const SYSTEM_SENDER_ROLE = 'user'; 

// 1.2 กำหนด App Password (รหัส 16 ตัว) ที่นี่โดยตรง
// *** แทนที่ 'YOUR_GMAIL_APP_PASSWORD_16_CHARS' ด้วยรหัส 16 ตัวที่สร้างจาก Google ***
const SYSTEM_APP_PASSWORD = 'xlyh pttg vnff bksn'; 

$sender_email = '';
$app_password = SYSTEM_APP_PASSWORD; 

// ----------------------------------------------------
// ส่วนที่ 2: ดึงอีเมลผู้ส่งจากฐานข้อมูล (ตาราง users) โดยใช้ Role
// ----------------------------------------------------
try {
    // ดึง email จากตาราง users โดยใช้ role เป็นเงื่อนไข (LIMIT 1 เพื่อให้ได้มาเพียง 1 บัญชี)
    $stmt_sender = $conn->prepare("SELECT email FROM users WHERE role = ? LIMIT 1"); 
    $stmt_sender->execute([SYSTEM_SENDER_ROLE]);
    $sender_info = $stmt_sender->fetch(PDO::FETCH_ASSOC);

    if (!$sender_info || empty($sender_info['email'])) {
        // หากไม่พบบัญชี admin/system ให้จัดการ Error 
        throw new Exception("ไม่พบข้อมูลอีเมลบัญชีผู้ส่งระบบ (Role: " . SYSTEM_SENDER_ROLE . ") ในตาราง users.");
    }
    
    $sender_email = $sender_info['email'];
    
} catch (Exception $e) {
    // บันทึก Error ลงใน Log และแจ้งเตือนผู้ใช้
    error_log("Database Fetch Error: " . $e->getMessage());
    echo "<script>alert('เกิดข้อผิดพลาดในการดึงข้อมูลอีเมลระบบ: กรุณาติดต่อผู้ดูแล'); window.location.href='activities.php';</script>";
    exit;
}
// ----------------------------------------------------


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // กรองข้อมูลที่รับมาจากฟอร์ม (เพื่อความปลอดภัย)
    $fullname      = htmlspecialchars($_POST['fullname'] ?? '');
    $student_id    = htmlspecialchars($_POST['student_id'] ?? '');
    $reason        = htmlspecialchars($_POST['reason'] ?? '');
    $teacher_name  = htmlspecialchars($_POST['teacher_name'] ?? '');
    // กรองอีเมล
    $teacher_email = filter_var($_POST['teacher_email'] ?? '', FILTER_VALIDATE_EMAIL); 

    if (!$teacher_email) {
        echo "<script>alert('รูปแบบอีเมลอาจารย์ไม่ถูกต้อง'); window.location.href='activities.php';</script>";
        exit;
    }

    // 1. สร้าง Token (32 ตัวอักษร)
$token = bin2hex(random_bytes(16)); // สร้างรหัสที่ไม่ซ้ำกัน 32 ตัว

// 2. บันทึกคำขอลงในตาราง certificate_requests
try {
    // ต้องมี user_id ของนักศึกษาที่ล็อกอินอยู่
    $student_user_id = $_SESSION['user_id']; 

    $stmt_request = $conn->prepare("
        INSERT INTO certificate_requests 
        (user_id, teacher_email, reason, request_token, status, requested_at) 
        VALUES (?, ?, ?, ?, 'PENDING', NOW())
    ");
    $stmt_request->execute([
        $student_user_id,
        $teacher_email,
        $reason,
        $token
    ]);
    
} catch (PDOException $e) {
    error_log("DB Insert Error: " . $e->getMessage());
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกคำขอ: " . $e->getMessage() . "'); window.location.href='activities.php';</script>";
    exit;
}

    $base_url = "http://yourwebsite.com"; 
    // ตรวจสอบชื่อไฟล์ certificate_form.php ให้ถูกต้องตามที่คุณสร้าง
    $cert_link = $base_url . "/certificate_form.php?token=" . $token; 

    $mail = new PHPMailer(true);

    try {
        // ตั้งค่า SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // ใช้ตัวแปรที่ดึงจาก DB และ App Password ที่กำหนดไว้
        $mail->Username   = $sender_email; 
        $mail->Password   = $app_password;

        // ใช้การเข้ารหัส SMTPS และ Port 465 (แนะนำ)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;
        
        // ตั้งค่าภาษาไทย
        $mail->CharSet = 'UTF-8';

        // ผู้ส่ง (ใช้ email ที่ดึงจาก DB)
        $mail->setFrom($sender_email, 'CV System'); 

        // ผู้รับ
        $mail->addAddress($teacher_email, $teacher_name);

        // เนื้อหาอีเมล
        $mail->isHTML(false); // plain text
        $mail->Subject = "คำขอใบรับรองจากนักศึกษา - $fullname";
        $mail->Body    = "
        เรียนคุณอาจารย์ $teacher_name,

        นักศึกษา $fullname ($student_id) ต้องการขอคำรับรอง
        เหตุผล: $reason

        กรุณาพิจารณา

        ขอบคุณครับ/ค่ะ
        CV System
                ";

        $mail->send();
        echo "<script>alert('ส่งคำขอเรียบร้อยแล้ว'); window.location.href='activities.php';</script>";
    } catch (Exception $e) {
        // แสดง Error ที่ละเอียดขึ้น
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        echo "<script>alert('ไม่สามารถส่งอีเมลได้: " . addslashes($mail->ErrorInfo) . "'); window.location.href='activities.php';</script>";
    }
} else {
    // ป้องกันการเข้าถึงโดยตรง
    header('Location: activities.php');
    exit;
}
?>