<?php
session_start();
require_once('config.php'); 

// ใช้ custom modal function แทน alert เพื่อแสดงข้อความและเปลี่ยนหน้า
function show_modal_and_redirect($message, $is_success = true, $redirect_url = 'activities.php') {
    $color = $is_success ? '#28a745' : '#dc3545';
    $border = $is_success ? '#28a745' : '#dc3545';
    $icon = $is_success ? '✅' : '❌';
    
    // กำหนดให้ redirect กลับไปหน้าฟอร์มหากมีปัญหาด้าน Token
    if (strpos($message, 'Token') !== false || strpos($message, 'request_id') !== false) {
        $redirect_url = 'certificate_form.php'; 
    }

    echo "<script>
        setTimeout(function(){ 
            const messageBox = document.createElement('div');
            messageBox.style.cssText = 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); padding:25px; background:white; border:2px solid $border; border-radius: 8px; z-index:1000; box-shadow:0 0 15px rgba(0,0,0,0.3); color:$color; font-weight:bold; max-width: 90vw; text-align: center; font-size: 1.1rem;';
            messageBox.innerHTML = '<div>$icon ' + '" . addslashes($message) . "' + '</div>';
            document.body.appendChild(messageBox);
            setTimeout(function(){ 
                messageBox.remove();
                window.location.href='$redirect_url'; 
            }, 5000); // แสดงข้อความ 5 วินาที ก่อนเปลี่ยนหน้า
        }, 10);
    </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. รับค่าและกรองข้อมูล
    $token = htmlspecialchars($_POST['token'] ?? '');
    $request_id = intval($_POST['request_id'] ?? 0);
    $certificate_text = $_POST['certificate_text'] ?? ''; // ไม่ต้องใช้ htmlspecialchars เพราะเนื้อหาอาจมี formatting

    if (empty($token) || strlen($token) !== 32 || empty($request_id) || empty($certificate_text)) {
        show_modal_and_redirect('ข้อมูลไม่ครบถ้วน หรือ Token ไม่ถูกต้อง กรุณาลองเข้าลิงก์อีกครั้ง', false, 'certificate_form.php');
    }

    try {
        // 2. ตรวจสอบ Token และ request_id อีกครั้งเพื่อยืนยันความปลอดภัยและสถานะ
        $stmt_check = $conn->prepare("
            SELECT status FROM certificate_requests 
            WHERE id = ? AND request_token = ?
        ");
        $stmt_check->execute([$request_id, $token]);
        $request = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            show_modal_and_redirect('ไม่พบคำขอใบรับรองที่ตรงกับ Token นี้ หรือ Token ไม่ถูกต้อง', false, 'certificate_form.php');
        }

        if ($request['status'] === 'COMPLETED') {
            show_modal_and_redirect('คำรับรองนี้ถูกบันทึกเรียบร้อยแล้วก่อนหน้านี้', false, 'activities.php');
        }

        // 3. บันทึกคำรับรองและอัปเดตสถานะ
        $stmt_update = $conn->prepare("
            UPDATE certificate_requests 
            SET certification_text = ?, status = 'COMPLETED', certified_at = NOW() 
            WHERE id = ? AND request_token = ?
        ");
        $stmt_update->execute([
            $certificate_text,
            $request_id,
            $token
        ]);

        // 4. แสดงผลสำเร็จ
        show_modal_and_redirect('บันทึกคำรับรองเสร็จสมบูรณ์แล้ว ระบบจะนำกลับสู่หน้ากิจกรรม', true);

    } catch (PDOException $e) {
        error_log("DB Update Error: " . $e->getMessage());
        show_modal_and_redirect("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage(), false);
    }

} else {
    // ป้องกันการเข้าถึงโดยตรง
    show_modal_and_redirect('ไม่ได้รับอนุญาตให้เข้าถึงหน้านี้โดยตรง', false);
}
?>