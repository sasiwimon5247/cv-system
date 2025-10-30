<?php
// send_certificate_updated.php

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
const SYSTEM_APP_PASSWORD = 'cmlq dvkk kvik dial'; 

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
    $fullname       = htmlspecialchars($_POST['fullname'] ?? '');
    $student_id     = htmlspecialchars($_POST['student_id'] ?? '');
    $reason         = htmlspecialchars($_POST['reason'] ?? '');
    $teacher_name   = htmlspecialchars($_POST['teacher_name'] ?? '');
    // กรองอีเมล
    $teacher_email = filter_var($_POST['teacher_email'] ?? '', FILTER_VALIDATE_EMAIL); 

    if (!$teacher_email) {
        // ใช้ custom modal แทน alert()
        // ในระบบจริง ควรใช้ $_SESSION['flash_error'] แล้ว redirect
        echo "<script>
            setTimeout(function(){ 
                const messageBox = document.createElement('div');
                messageBox.style.cssText = 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); padding:20px; background:white; border:1px solid #ccc; z-index:1000; box-shadow:0 0 10px rgba(0,0,0,0.5);';
                messageBox.innerHTML = 'รูปแบบอีเมลอาจารย์ไม่ถูกต้อง';
                document.body.appendChild(messageBox);
                setTimeout(function(){ 
                    messageBox.remove();
                    window.location.href='activities.php';
                }, 3000); 
            }, 10);
        </script>";
        exit;
    }

    // ต้องมี user_id ของนักศึกษาที่ล็อกอินอยู่
    $student_user_id = $_SESSION['user_id'] ?? null; 
    
    // ตรวจสอบว่ามีการล็อกอินหรือไม่
    if (!$student_user_id) {
        // ใช้ custom modal แทน alert()
        echo "<script>
            setTimeout(function(){ 
                const messageBox = document.createElement('div');
                messageBox.style.cssText = 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); padding:20px; background:white; border:1px solid #ccc; z-index:1000; box-shadow:0 0 10px rgba(0,0,0,0.5);';
                messageBox.innerHTML = 'กรุณาเข้าสู่ระบบก่อนดำเนินการต่อ';
                document.body.appendChild(messageBox);
                setTimeout(function(){ 
                    messageBox.remove();
                    window.location.href='login.php'; // เปลี่ยนเป็นหน้าล็อกอิน
                }, 3000); 
            }, 10);
        </script>";
        exit;
    }

    // 1. สร้าง Token (32 ตัวอักษร)
    $token = bin2hex(random_bytes(16)); // สร้างรหัสที่ไม่ซ้ำกัน 32 ตัว

    // 2. บันทึกคำขอลงในตาราง certificate_requests
    try {
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
        // ใช้ custom modal แทน alert()
        echo "<script>
            setTimeout(function(){ 
                const messageBox = document.createElement('div');
                messageBox.style.cssText = 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); padding:20px; background:white; border:1px solid #ccc; z-index:1000; box-shadow:0 0 10px rgba(0,0,0,0.5);';
                messageBox.innerHTML = 'เกิดข้อผิดพลาดในการบันทึกคำขอ: " . addslashes($e->getMessage()) . "';
                document.body.appendChild(messageBox);
                setTimeout(function(){ 
                    messageBox.remove();
                    window.location.href='activities.php';
                }, 3000); 
            }, 10);
        </script>";
        exit;
    }

    // 3. เตรียมลิงก์สำหรับอาจารย์
    $base_url = "http://localhost/cv_system"; 
    // ใช้ชื่อไฟล์ certificate_form.php ที่สร้างไว้ก่อนหน้า
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

        // เนื้อหาอีเมล: เปลี่ยนเป็น HTML เพื่อให้ลิงก์กดได้ง่ายขึ้น
        $mail->isHTML(true); 
        $mail->Subject = "คำขอใบรับรอง (Recommendation Letter) จากนักศึกษา - $fullname";
        
        // ************************************************************
        // ปรับปรุงเนื้อหาอีเมลเพื่อใส่ลิงก์
        // ************************************************************
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Tahoma, sans-serif; line-height: 1.6; }
                    .button {
                        display: inline-block;
                        padding: 10px 20px;
                        margin-top: 20px;
                        background-color: #007bff;
                        color: white !important; 
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .note {
                        margin-top: 30px;
                        padding: 15px;
                        border-left: 5px solid #ffc107;
                        background-color: #fff3cd;
                        color: #856404;
                    }
                </style>
            </head>
            <body>
                <p><strong>เรียน อาจารย์/ผู้รับรอง $teacher_name,</strong></p>

                <p>นักศึกษา <strong>$fullname ($student_id)</strong> ได้ส่งคำขอให้ท่านกรอกคำรับรอง (Recommendation Letter) ผ่านระบบ CV System</p>
                
                <p><strong>เหตุผลในการขอ:</strong><br>" . nl2br(htmlspecialchars($reason)) . "</p>

                <p>ท่านสามารถคลิกลิงก์ด้านล่างเพื่อเข้าสู่แบบฟอร์มกรอกคำรับรองได้ทันที</p>

                <p>
                    <a href=\"$cert_link\" class=\"button\">คลิกเพื่อกรอกคำรับรอง</a>
                </p>

                <div class=\"note\">
                    <p><strong>หมายเหตุ:</strong> ลิงก์นี้มีความปลอดภัยและถูกสร้างขึ้นสำหรับคำขอนี้โดยเฉพาะ หลังจากที่ท่านบันทึกคำรับรองแล้ว ลิงก์จะไม่สามารถใช้งานได้อีก</p>
                    <p>หากปุ่มด้านบนไม่สามารถกดได้ กรุณาคัดลอกลิงก์นี้ไปวางในเบราว์เซอร์: <br> $cert_link</p>
                </div>

                <p>ขอบคุณครับ/ค่ะ</p>
                <p>ด้วยความเคารพ,<br>CV System</p>
            </body>
            </html>
        ";
        
        // เนื้อหาสำรองแบบ Plain Text (สำหรับอีเมลที่ไม่รองรับ HTML)
        $mail->AltBody = "เรียนคุณอาจารย์ $teacher_name,\n\nนักศึกษา $fullname ($student_id) ต้องการขอคำรับรอง\nเหตุผล: $reason\n\nกรุณาคลิกลิงก์ต่อไปนี้เพื่อกรอกคำรับรอง: \n$cert_link\n\nขอบคุณครับ/ค่ะ\nCV System";

        $mail->send();
        
        // ใช้ custom modal แทน alert()
        echo "<script>
            setTimeout(function(){ 
                const messageBox = document.createElement('div');
                messageBox.style.cssText = 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); padding:20px; background:white; border:1px solid #28a745; z-index:1000; box-shadow:0 0 10px rgba(0,0,0,0.5); color:#28a745; font-weight:bold;';
                messageBox.innerHTML = 'ส่งคำขอเรียบร้อยแล้ว: อาจารย์จะได้รับอีเมลพร้อมลิงก์กรอกคำรับรอง';
                document.body.appendChild(messageBox);
                setTimeout(function(){ 
                    messageBox.remove();
                    window.location.href='activities.php';
                }, 3000); 
            }, 10);
        </script>";
        
    } catch (Exception $e) {
        // แสดง Error ที่ละเอียดขึ้น
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        // ใช้ custom modal แทน alert()
        echo "<script>
            setTimeout(function(){ 
                const messageBox = document.createElement('div');
                messageBox.style.cssText = 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); padding:20px; background:white; border:1px solid #dc3545; z-index:1000; box-shadow:0 0 10px rgba(0,0,0,0.5); color:#dc3545; font-weight:bold;';
                messageBox.innerHTML = 'ไม่สามารถส่งอีเมลได้: " . addslashes($mail->ErrorInfo) . "';
                document.body.appendChild(messageBox);
                setTimeout(function(){ 
                    messageBox.remove();
                    window.location.href='activities.php';
                }, 3000); 
            }, 10);
        </script>";
    }
} else {
    // ป้องกันการเข้าถึงโดยตรง
    header('Location: activities.php');
    exit;
}
?>