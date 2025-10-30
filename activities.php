<?php
session_start();
require_once('config.php'); // ใช้ $conn (PDO)

// เช็คการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

$success_msg = "";
$error_msg = "";

// ถ้ามีการ submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['full_name'])) { 
    // เช็คว่าไม่ใช่การส่งจากฟอร์ม modal
    $activities = $_POST['activity'] ?? [];
    $projects   = $_POST['project'] ?? [];

    try {
        // ลบข้อมูลเก่าออกก่อน
        $stmt = $conn->prepare("DELETE FROM activities_info WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // เพิ่มข้อมูลใหม่
        for ($i = 0; $i < max(count($activities), count($projects)); $i++) {
            if (!empty($activities[$i]) || !empty($projects[$i])) {
                $stmt = $conn->prepare("INSERT INTO activities_info (user_id, activity, project) VALUES (?, ?, ?)");
                $stmt->execute([
                    $user_id,
                    $activities[$i] ?? '',
                    $projects[$i] ?? ''
                ]);
            }
        }

        $success_msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $error_msg = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// โหลดข้อมูลเก่า
$stmt = $conn->prepare("SELECT * FROM activities_info WHERE user_id = ?");
$stmt->execute([$user_id]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>กิจกรรม / โปรเจค / ใบรับรอง</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="style/activities.css">
</head>
<body>
<?php
  include('ic/navbar.php');
?>
  <div class="activities-container">
    <h2 class="section-title-box">
        <i class="fa-solid fa-certificate section-icon"></i> 
        กิจกรรม / โปรเจค / ใบรับรอง
    </h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <!-- ฟอร์มหลัก -->
    <form method="post" class="activities-form">
        <div id="activities-wrapper">
            <?php if (!empty($records)): ?>
                <?php foreach ($records as $rec): ?>
                    <div class="activities-item mb-4 p-3 border rounded">
                        <div class="mb-2">
                            <label class="form-label">กิจกรรม</label>
                            <input type="text" name="activity[]" class="form-control" value="<?= htmlspecialchars($rec['activity']) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">โปรเจค</label>
                            <input type="text" name="project[]" class="form-control" value="<?= htmlspecialchars($rec['project']) ?>">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-item">ลบ</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activities-item mb-4 p-3 border rounded">
                    <div class="mb-2">
                        <label class="form-label">กิจกรรม</label>
                        <input type="text" name="activity[]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">โปรเจค</label>
                        <input type="text" name="project[]" class="form-control">
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <button type="button" id="addActivities" class="btn-add">+ เพิ่มกิจกรรม/โปรเจค</button>

        <div class="d-flex justify-content-between mt-3">
            <button type="submit" class="btn-save">บันทึกข้อมูล</button>
            <a href="fill_out.php" class="btn-back">ย้อนกลับ</a>
        </div>
    </form>
    <div class="text-end">
      <button type="button" id="requestCertificate" class="btn-certificate" data-bs-toggle="modal" data-bs-target="#certificateModal">ขอใบรับรอง</button>
    </div>
</div>
<!-- Modal ขอใบรับรอง (อยู่นอกฟอร์มหลัก) -->
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="certificateForm" method="post" action="send_certificate.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="certificateModalLabel">ขอใบรับรอง</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">ชื่อ-นามสกุล</label>
          <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">รหัสนิสิต</label>
          <input type="text" name="stu_id" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">เหตุผลที่ต้องการเอกสาร</label>
          <textarea name="reason" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">ชื่ออาจารย์</label>
          <input type="text" name="teacher_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">อีเมลอาจารย์</label>
          <input type="email" name="teacher_email" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">ส่งคำขอ</button>
      </div>
    </form>
  </div>
</div>
<?php 
include('ic/footer.php'); 
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/activities.js"></script>
</body>
</html>
