<?php
// certificate_form.php

require_once('config.php'); 
// ไม่ต้องใช้ session_start() เพราะอาจารย์ไม่ได้ล็อกอิน

$message = "";
$error = "";
$request_data = null;
$token = $_GET['token'] ?? '';

// ************************************************************
// A. ตรวจสอบ Token และโหลดข้อมูลคำขอ
// ************************************************************
if (!empty($token)) {
    try {
        $stmt = $conn->prepare("
            SELECT r.*, u.fullname AS student_fullname
            FROM certificate_requests r
            JOIN users u ON r.user_id = u.id
            WHERE r.request_token = ? AND r.status = 'PENDING'
        ");
        $stmt->execute([$token]);
        $request_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request_data) {
            $error = "ลิงก์นี้ไม่ถูกต้อง หรือคำขอได้ถูกดำเนินการไปแล้ว";
        }
    } catch (PDOException $e) {
        $error = "เกิดข้อผิดพลาดในการโหลดข้อมูล: " . $e->getMessage();
    }
} else {
    $error = "ไม่พบรหัส Token ในลิงก์";
}

// ************************************************************
// B. จัดการการ Submit ฟอร์มคำรับรองของอาจารย์
// ************************************************************
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_certification'])) {
    
    $received_token = $_POST['token'] ?? '';
    $certification_text = $_POST['certification_text'] ?? '';

    // ตรวจสอบ Token อีกครั้ง และป้องกันการ Submit ซ้ำ
    if ($request_data && $request_data['request_token'] === $received_token) {
        try {
            // อัปเดตข้อมูลคำรับรองและเปลี่ยนสถานะ
            $stmt_update = $conn->prepare("
                UPDATE certificate_requests 
                SET certification_text = ?, status = 'GRANTED' 
                WHERE request_token = ? AND status = 'PENDING'
            ");
            $stmt_update->execute([$certification_text, $received_token]);

            if ($stmt_update->rowCount() > 0) {
                $message = "บันทึกคำรับรองเรียบร้อยแล้ว ขอบคุณครับ/ค่ะ";
                $request_data['status'] = 'GRANTED'; // อัปเดตสถานะในหน้าจอ
            } else {
                $error = "ไม่สามารถบันทึกข้อมูลได้ เนื่องจากคำขอนี้อาจถูกดำเนินการไปแล้ว";
            }
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
        }
    } else {
        $error = "ข้อมูลไม่ถูกต้องหรือลิงก์หมดอายุ";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แบบฟอร์มกรอกคำรับรอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="card-title">แบบฟอร์มกรอกคำรับรอง</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($request_data && $request_data['status'] === 'PENDING'): ?>
            <p><strong>นักศึกษา:</strong> <?= htmlspecialchars($request_data['student_fullname']) ?></p>
            <p><strong>เหตุผลในการขอ:</strong> <?= nl2br(htmlspecialchars($request_data['reason'])) ?></p>
            
            <form method="post">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="mb-3">
                    <label for="certification_text" class="form-label">คำรับรองสำหรับนักศึกษา:</label>
                    <textarea name="certification_text" id="certification_text" class="form-control" rows="8" required></textarea>
                </div>
                
                <button type="submit" name="submit_certification" class="btn btn-primary">บันทึกคำรับรอง</button>
            </form>
        
        <?php elseif ($request_data && $request_data['status'] === 'GRANTED'): ?>
            <div class="alert alert-info">คำขอรับรองนี้ถูกดำเนินการและบันทึกข้อมูลเรียบร้อยแล้ว</div>
        
        <?php endif; ?>
    </div>
</div>
</body>
</html>