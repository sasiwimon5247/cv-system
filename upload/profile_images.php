<?php
// upload/profile_images.php
// ไฟล์นี้จะจัดการการอัปโหลด/ลบรูปโปรไฟล์ และทำการ Redirect กลับไปที่ fill_out.php เสมอ

// ตรวจสอบ session_start อีกครั้งเผื่อกรณีเรียกใช้งานแยก
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('config.php'); // ต้องมี $conn จาก PDO

$user_id = $_SESSION['user_id'] ?? 0;
// เราจะใช้ Session Flash Message แทนตัวแปร $upload_success/$upload_error
// ดังนั้นจึงไม่จำเป็นต้องกำหนดค่าเริ่มต้นของตัวแปรเหล่านี้ที่นี่


// 🔴 ลบรูปโปรไฟล์ (ถ้ามีคำขอลบเข้ามา)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_profile_image'])) {
    
    // ตรวจสอบ User ID
    if ($user_id === 0) {
        $_SESSION['flash_error'] = "กรุณาเข้าสู่ระบบก่อนดำเนินการ";
        header("Location: fill_out.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT profile_image FROM cv_profile WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $image_to_delete = $stmt->fetchColumn();

    if ($image_to_delete) {
        $upload_dir = 'upload/img/';
        $file_path = $upload_dir . $image_to_delete;

        if (file_exists($file_path)) {
            unlink($file_path); // ลบไฟล์จริง
        }

        $stmt = $conn->prepare("UPDATE cv_profile SET profile_image = NULL, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $_SESSION['flash_success'] = "ลบรูปโปรไฟล์เรียบร้อยแล้ว";
    } else {
         $_SESSION['flash_error'] = "ไม่พบรูปโปรไฟล์ที่ต้องการลบ";
    }

    // *** PRG: ทำการ Redirect กลับไปที่ fill_out.php ด้วยเมธอด GET เสมอ ***
    header("Location: fill_out.php");
    exit;
}


// 🔽 อัปโหลดรูปโปรไฟล์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_btn']) && isset($_FILES['profile_image'])) {
    
    // ตรวจสอบ User ID
    if ($user_id === 0) {
        $_SESSION['flash_error'] = "กรุณาเข้าสู่ระบบก่อนดำเนินการ";
        header("Location: fill_out.php");
        exit;
    }

    $upload_dir = 'upload/img/';
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_name = basename($_FILES['profile_image']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_size = $_FILES['profile_image']['size'];
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    // ✅ ตรวจสอบประเภทและขนาดไฟล์
    if (!in_array($file_ext, $allowed_types)) {
        $_SESSION['flash_error'] = "ประเภทไฟล์ไม่ถูกต้อง (ต้องเป็น JPG, PNG, GIF)";
    } elseif ($file_size > 10 * 1024 * 1024) { // 10MB
        $_SESSION['flash_error'] = "ไฟล์มีขนาดเกิน 10MB";
    } else {
        $new_file_name = $user_id . "_" . time() . "." . $file_ext;
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            
            // ลบรูปเก่าออกก่อน (ถ้ามี)
            $stmt_old = $conn->prepare("SELECT profile_image FROM cv_profile WHERE user_id = ?");
            $stmt_old->execute([$user_id]);
            $old_image = $stmt_old->fetchColumn();

            if ($old_image && file_exists($upload_dir . $old_image)) {
                unlink($upload_dir . $old_image);
            }

            // 🔍 INSERT หรือ UPDATE
            $stmt_check = $conn->prepare("SELECT id FROM cv_profile WHERE user_id = ?");
            $stmt_check->execute([$user_id]);

            if ($stmt_check->rowCount() > 0) {
                $stmt = $conn->prepare("UPDATE cv_profile SET profile_image = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
                $stmt->execute([$new_file_name, $user_id]);
            } else {
                $stmt = $conn->prepare("INSERT INTO cv_profile (user_id, profile_image, template_name) VALUES (?, ?, 'default')");
                $stmt->execute([$user_id, $new_file_name]);
            }

            $_SESSION['flash_success'] = "อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว";

        } else {
            $_SESSION['flash_error'] = "เกิดข้อผิดพลาดในการบันทึกไฟล์";
        }
    }
    
    // *** PRG: ทำการ Redirect กลับไปที่ fill_out.php ด้วยเมธอด GET เสมอ ***
    header("Location: fill_out.php");
    exit;
}

// 🔁 โหลดรูปโปรไฟล์ล่าสุด (โค้ดนี้จะทำงานในเมธอด GET เพื่อแสดงผล)
$stmt = $conn->prepare("SELECT profile_image FROM cv_profile WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile_image = $stmt->fetchColumn();

// *** ไม่มีการ echo หรือ output อื่นๆ ที่นี่ เพื่อให้ Header Location ทำงานได้ ***
?>