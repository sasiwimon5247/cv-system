<?php
// สมมติว่าไฟล์ config.php มีการเชื่อมต่อ PDO ที่ชื่อว่า $conn
require_once('config.php'); 

$token = $_GET['token'] ?? '';
$error_message = "";
$student_data = null;
$activities_data = [];

if (empty($token) || strlen($token) !== 32) {
    $error_message = "ลิงก์คำรับรองไม่ถูกต้องหรือขาด Token";
} else {
    try {
        // 1. ตรวจสอบ Token และดึงข้อมูลคำขอ
        // ดึงชื่อเต็มจาก personal_info (p.full_name) และรหัสนิสิตจาก education_info (e.student_id)
        $stmt_request = $conn->prepare("
            SELECT 
                cr.id as request_id, cr.status, cr.reason, 
                u.id as user_id, 
                p.full_name, 
                e.student_id 
            FROM certificate_requests cr
            JOIN users u ON cr.user_id = u.id
            JOIN personal_info p ON u.id = p.user_id
            JOIN education_info e ON u.id = e.user_id
            WHERE cr.request_token = ? AND cr.status = 'PENDING'
        ");
        $stmt_request->execute([$token]);
        $request_info = $stmt_request->fetch(PDO::FETCH_ASSOC);

        if (!$request_info) {
            // ตรวจสอบสถานะถ้าไม่ใช่ PENDING
            $stmt_status = $conn->prepare("SELECT status FROM certificate_requests WHERE request_token = ?");
            $stmt_status->execute([$token]);
            $status_check = $stmt_status->fetch(PDO::FETCH_ASSOC);
            
            if ($status_check && $status_check['status'] === 'COMPLETED') {
                $error_message = "คำรับรองนี้ถูกกรอกและส่งไปแล้ว";
            } else {
                $error_message = "ลิงก์คำรับรองไม่ถูกต้องหรือไม่พบคำขอที่รอการดำเนินการ";
            }
        } else {
            // หากพบและสถานะเป็น PENDING
            $student_data = $request_info;
            $user_id = $student_data['user_id'];

            // 2. ดึงข้อมูลกิจกรรม/โปรเจค ของนักศึกษา
            $stmt_activities = $conn->prepare("SELECT activity, project FROM activities_info WHERE user_id = ?");
            $stmt_activities->execute([$user_id]);
            $activities_data = $stmt_activities->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        // บันทึก Error ที่เกิดจากฐานข้อมูล
        error_log("Database Error in certificate_form: " . $e->getMessage());
        $error_message = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์มกรอกคำรับรอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f4f7f6; }
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .section-header {
            color: #3f51b5;
            border-bottom: 2px solid #3f51b5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 5px solid #2196f3;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .activity-item {
            border: 1px solid #cfd8dc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="section-header">แบบฟอร์มกรอกคำรับรอง (Recommendation Letter)</h2>

        <?php if ($error_message): ?>
            <!-- แสดงข้อความ Error -->
            <div class="alert alert-danger p-4" role="alert">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php elseif ($student_data): ?>
            <!-- ข้อมูลนักศึกษา -->
            <div class="info-box">
                <p class="mb-1"><strong>คำขอจาก:</strong> <?= htmlspecialchars($student_data['full_name']) ?></p>
                <p class="mb-1"><strong>รหัสนิสิต:</strong> <?= htmlspecialchars($student_data['student_id']) ?></p>
                <p class="mb-0"><strong>เหตุผลที่ต้องการเอกสาร:</strong> <?= nl2br(htmlspecialchars($student_data['reason'])) ?></p>
            </div>
            
            <!-- แสดงกิจกรรมและโปรเจค -->
            <?php if (!empty($activities_data)): ?>
                <h5 class="section-header">กิจกรรม/โปรเจคที่เกี่ยวข้อง (จาก CV นักศึกษา)</h5>
                <?php foreach ($activities_data as $activity_item): ?>
                    <div class="activity-item">
                        <p class="mb-1"><strong>กิจกรรม:</strong> <?= htmlspecialchars($activity_item['activity']) ?: '-' ?></p>
                        <p class="mb-0"><strong>โปรเจค:</strong> <?= htmlspecialchars($activity_item['project']) ?: '-' ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <hr class="my-4">

            <!-- ฟอร์มกรอกคำรับรอง -->
            <h4 class="text-success mb-3">ส่วนสำหรับอาจารย์/ผู้รับรอง</h4>
            <form action="process_certificate.php" method="post">
                <input type="hidden" name="request_token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="request_id" value="<?= htmlspecialchars($student_data['request_id']) ?>">
                
                <!-- *** เพิ่มช่องกรอกชื่ออาจารย์กลับเข้าไป: ชื่อฟิลด์คือ teacher_name *** -->
                <div class="mb-3">
                    <label for="teacher_name" class="form-label">ชื่อ-นามสกุลอาจารย์/ผู้รับรอง</label>
                    <input type="text" class="form-control" id="teacher_name" name="teacher_name" required>
                </div>
                
                <div class="mb-3">
                    <label for="recommendation_content" class="form-label">เนื้อหาคำรับรอง (Recommendation Letter)</label>
                    <textarea class="form-control" id="recommendation_content" name="recommendation_content" rows="10" required placeholder="กรุณากรอกคำรับรองอย่างละเอียด..."></textarea>
                </div>
                
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg">บันทึกและส่งคำรับรอง</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>