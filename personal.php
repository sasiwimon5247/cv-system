<?php
session_start();
require_once('config.php'); // $conn PDO

// เช็คล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// เริ่มค่าเริ่มต้น
$full_name = $position = $email = $phone = $address = $profile_link = "";
$success_msg = "";
$error_msg = "";

// ถ้ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $position = $_POST['position'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $profile_link = $_POST['profile_link'] ?? '';

    try {
        // ตรวจสอบว่าข้อมูลมีใน DB หรือยัง
        $stmt = $conn->prepare("SELECT user_id FROM personal_info WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->rowCount() > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE personal_info SET full_name = ?, position = ?, email = ?, phone = ?, address = ?, profile_link = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
            $stmt->execute([$full_name, $position, $email, $phone, $address, $profile_link, $user_id]);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO personal_info (user_id, full_name, position, email, phone, address, profile_link) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $full_name, $position, $email, $phone, $address, $profile_link]);
        }
        $success_msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $error_msg = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} else {
    // โหลดข้อมูลเดิมมาแสดง
    $stmt = $conn->prepare("SELECT full_name, position, email, phone, address, profile_link FROM personal_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $full_name = $row['full_name'];
        $position = $row['position'];
        $email = $row['email'];
        $phone = $row['phone'];
        $address = $row['address'];
        $profile_link = $row['profile_link'];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>ข้อมูลส่วนตัว</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="style/personal.css" rel="stylesheet">
</head>
<body>
<?php
include('ic/navbar.php');
?>
<div class="container personal-container">
    <h2 class="section-title-box">
        <i class="fa-solid fa-user section-icon"></i> 
        ข้อมูลส่วนตัว
    </h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?=htmlspecialchars($success_msg)?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?=htmlspecialchars($error_msg)?></div>
    <?php endif; ?>

    <form method="post" action="" class="personal-form p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="full_name" class="form-label">ชื่อ - นามสกุล</label>
            <input type="text" id="full_name" name="full_name" class="form-control" value="<?=htmlspecialchars($full_name)?>">
        </div>
        <div class="mb-3">
            <label for="position" class="form-label">ตำแหน่งที่สมัคร</label>
            <input type="text" id="position" name="position" class="form-control" value="<?=htmlspecialchars($position)?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">อีเมลล์</label>
            <input type="email" id="email" name="email" class="form-control" value="<?=htmlspecialchars($email)?>">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?=htmlspecialchars($phone)?>">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">ที่อยู่</label>
            <textarea id="address" name="address" class="form-control" rows="3"><?=htmlspecialchars($address)?></textarea>
        </div>
        <div class="mb-3">
            <label for="profile_link" class="form-label">ลิ้งค์โปรไฟล์ Github หรือ Portfolio (ถ้ามี)</label>
            <input type="url" id="profile_link" name="profile_link" class="form-control" value="<?=htmlspecialchars($profile_link)?>">
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
