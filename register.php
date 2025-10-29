<?php
require_once "config.php"; // ต้องมี PDO connection เช่น $conn = new PDO(...);

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ตรวจสอบค่าว่าง
    if (!$username || !$email || !$password || !$confirm) {
        $error = "กรุณากรอกข้อมูลให้ครบ";
    }
    // ตรวจสอบว่ารหัสผ่านตรงกัน
    elseif ($password !== $confirm) {
        $error = "รหัสผ่านไม่ตรงกัน";
    }
    else {
        // ตรวจสอบ username หรือ email ซ้ำ
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "มีชื่อผู้ใช้หรืออีเมลนี้แล้ว";
        } else {
            // แปลงรหัสผ่านเป็น hash ก่อนบันทึก
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashedPassword])) {
                $success = "สมัครสมาชิกสำเร็จ";
            } else {
                $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/register.css" rel="stylesheet">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h3 class="mb-4 text-center text-orange">สมัครสมาชิก</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?> 
                <a href="login.php">เข้าสู่ระบบ</a>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">สมัครสมาชิก</button>
        </form>

        <p class="mt-3 text-center">มีบัญชีแล้ว? <a href="login.php" class="link-orange">เข้าสู่ระบบ</a></p>
    </div>
</div>

</body>
</html>
