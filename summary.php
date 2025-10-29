<?php
session_start();
require_once('config.php'); // $conn PDO

// เช็คล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$summary = "";
$success_msg = "";
$error_msg = "";

// ถ้ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $summary = $_POST['summary'] ?? '';

    try {
        // ตรวจสอบว่ามีอยู่แล้วหรือไม่
        $stmt = $conn->prepare("SELECT user_id FROM summary_info WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->rowCount() > 0) {
            // update
            $stmt = $conn->prepare("UPDATE summary_info SET summary = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
            $stmt->execute([$summary, $user_id]);
        } else {
            // insert
            $stmt = $conn->prepare("INSERT INTO summary_info (user_id, summary) VALUES (?, ?)");
            $stmt->execute([$user_id, $summary]);
        }
        $success_msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $error_msg = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} else {
    // โหลดข้อมูลเดิม
    $stmt = $conn->prepare("SELECT summary FROM summary_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $summary = $row['summary'];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>สรุปประวัติย่อ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="style/summary.css" rel="stylesheet">
</head>
<body>
<?php
  include('ic/navbar.php');
?>
<div class="container summary-container">
    <h2 class="section-title-box">
        <i class="fa-solid fa-file-alt section-icon"></i> 
        สรุปประวัติย่อ
    </h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form method="post" action="" class="summary-form p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="summary" class="form-label">สรุปจุดเด่น (2-3 บรรทัด)</label>
            <textarea id="summary" name="summary" class="form-control" rows="4" placeholder="เช่น ประสบการณ์ที่เกี่ยวข้อง เป้าหมาย หรือจุดแข็ง"><?= htmlspecialchars($summary) ?></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary btn-save">บันทึกข้อมูล</button>
            <a href="fill_out.php" class="btn btn-secondary btn-back">ย้อนกลับ</a>
        </div>
    </form>
</div>
<?php 
include('ic/footer.php'); 
?>
</body>
</html>
