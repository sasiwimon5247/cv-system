<?php
session_start();
require_once('config.php'); // ใช้ $conn (PDO)

// เช็คการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$technical_skills = $soft_skills = "";
$success_msg = "";
$error_msg = "";

// ถ้ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $technical_skills = $_POST['technical_skills'] ?? '';
    $soft_skills = $_POST['soft_skills'] ?? '';

    try {
        // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
        $stmt = $conn->prepare("SELECT user_id FROM skills_info WHERE user_id = ?");
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE skills_info 
                SET technical_skills = ?, soft_skills = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?");
            $stmt->execute([$technical_skills, $soft_skills, $user_id]);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO skills_info (user_id, technical_skills, soft_skills) 
                VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $technical_skills, $soft_skills]);
        }

        $success_msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $error_msg = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} else {
    // โหลดข้อมูลเก่ามาแสดง
    $stmt = $conn->prepare("SELECT technical_skills, soft_skills FROM skills_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $technical_skills = $row['technical_skills'];
        $soft_skills = $row['soft_skills'];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ทักษะ (Skills)</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="style/skill.css" rel="stylesheet">
</head>
<?php
  include('ic/navbar.php');
?>
<body>
<div class="skill-container">
    <h2 class="section-title-box">
        <i class="fa-solid fa-gears section-icon"></i> 
        ทักษะ (Skills)
    </h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form method="post" class="skill-form">
        <div class="mb-3">
            <label for="technical_skills" class="form-label">Technical Skills (ทักษะทางเทคนิค)</label>
            <textarea id="technical_skills" name="technical_skills" class="form-control" rows="4"><?= htmlspecialchars($technical_skills) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="soft_skills" class="form-label">Soft Skills (ทักษะด้านบุคลิกภาพ)</label>
            <textarea id="soft_skills" name="soft_skills" class="form-control" rows="4"><?= htmlspecialchars($soft_skills) ?></textarea>
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
