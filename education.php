<?php
session_start();
require_once('config.php'); // $conn PDO

// เช็คล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ตั้งค่าตัวแปรเริ่มต้น
$highschool_name = $highschool_plan = $highschool_gpa = "";
$uni_name = $degree = $faculty = $major = $grad_year = $uni_gpa = $stu_id = "";
$success_msg = "";
$error_msg = "";

// ถ้ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $highschool_name = $_POST['highschool_name'] ?? '';
    $highschool_plan = $_POST['highschool_plan'] ?? '';
    $highschool_gpa = $_POST['highschool_gpa'] ?? '';
    $uni_name       = $_POST['uni_name'] ?? '';
    $stu_id        = $_POST['stu_id'] ?? '';
    $degree         = $_POST['degree'] ?? '';
    $faculty        = $_POST['faculty'] ?? '';
    $major          = $_POST['major'] ?? '';
    $grad_year      = $_POST['grad_year'] ?? '';
    $uni_gpa        = $_POST['uni_gpa'] ?? '';

    try {
        // ตรวจสอบว่ามีข้อมูลเดิมหรือไม่
        $stmt = $conn->prepare("SELECT user_id FROM education_info WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->rowCount() > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE education_info 
                SET highschool_name=?, highschool_plan=?, highschool_gpa=?, 
                    uni_name=?, stu_id=?, degree=?, faculty=?, major=?, grad_year=?, uni_gpa=?, 
                    updated_at=CURRENT_TIMESTAMP
                WHERE user_id=?");
            $stmt->execute([
                $highschool_name, $highschool_plan, $highschool_gpa,
                $uni_name, $stu_id, $degree, $faculty, $major, $grad_year, $uni_gpa,
                $user_id
            ]);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO education_info 
                (user_id, highschool_name, highschool_plan, highschool_gpa, 
                 uni_name, stu_id, degree, faculty, major, grad_year, uni_gpa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id, $highschool_name, $highschool_plan, $highschool_gpa,
                $uni_name, $stu_id, $degree, $faculty, $major, $grad_year, $uni_gpa
            ]);
        }
        $success_msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $error_msg = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} else {
    // โหลดข้อมูลเดิม
    $stmt = $conn->prepare("SELECT * FROM education_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $highschool_name = $row['highschool_name'];
        $highschool_plan = $row['highschool_plan'];
        $highschool_gpa  = $row['highschool_gpa'];
        $uni_name        = $row['uni_name'];
        $stu_id          = $row['stu_id'];
        $degree          = $row['degree'];
        $faculty         = $row['faculty'];
        $major           = $row['major'];
        $grad_year       = $row['grad_year'];
        $uni_gpa         = $row['uni_gpa'];
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ประวัติการศึกษา</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Roboto&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link href="style/education.css" rel="stylesheet">
</head>
<body>
<?php
include('ic/navbar.php');
?>
<div class="education-container">
  <h2 class="section-title-box">
      <i class="fa-solid fa-graduation-cap section-icon"></i> 
      ประวัติการศึกษา
  </h2>

  <?php if ($success_msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
  <?php endif; ?>
  <?php if ($error_msg): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
  <?php endif; ?>

  <form method="post" action="" class="education-form p-4">
    <!-- มัธยมศึกษา -->
    <h5 class="text-orange">มัธยมศึกษา</h5>
    <div class="mb-3">
      <label for="highschool_name" class="form-label">ชื่อสถาบัน (มัธยมศึกษา)</label>
      <input type="text" id="highschool_name" name="highschool_name" class="form-control" value="<?= htmlspecialchars($highschool_name) ?>">
    </div>
    <div class="mb-3">
      <label for="highschool_plan" class="form-label">แผนการเรียน</label>
      <input type="text" id="highschool_plan" name="highschool_plan" class="form-control" value="<?= htmlspecialchars($highschool_plan) ?>">
    </div>
    <div class="mb-3">
      <label for="highschool_gpa" class="form-label">ผลการเรียน (เกรดเฉลี่ย)</label>
      <input type="text" id="highschool_gpa" name="highschool_gpa" class="form-control" value="<?= htmlspecialchars($highschool_gpa) ?>">
    </div>

    <!-- อุดมศึกษา -->
    <h5 class="text-orange">อุดมศึกษา</h5>
    <div class="mb-3">
      <label for="uni_name" class="form-label">ชื่อสถาบัน (อุดมศึกษา)</label>
      <input type="text" id="uni_name" name="uni_name" class="form-control" value="<?= htmlspecialchars($uni_name) ?>">
    </div>
    <div class="mb-3">
      <label for="stu_id" class="form-label">รหัสนิสิต/นักศึกษา</label>
      <input type="text" id="stu_id" name="stu_id" class="form-control" value="<?= htmlspecialchars($stu_id) ?>">
    </div>
    <div class="mb-3">
      <label for="degree" class="form-label">ระดับปริญญา</label>
      <input type="text" id="degree" name="degree" class="form-control" value="<?= htmlspecialchars($degree) ?>">
    </div>
    <div class="mb-3">
      <label for="faculty" class="form-label">คณะ</label>
      <input type="text" id="faculty" name="faculty" class="form-control" value="<?= htmlspecialchars($faculty) ?>">
    </div>
    <div class="mb-3">
      <label for="major" class="form-label">สาขาวิชา</label>
      <input type="text" id="major" name="major" class="form-control" value="<?= htmlspecialchars($major) ?>">
    </div>
    <div class="mb-3">
      <label for="grad_year" class="form-label">ปีที่คาดว่าจะสำเร็จการศึกษา</label>
      <input type="text" id="grad_year" name="grad_year" class="form-control" value="<?= htmlspecialchars($grad_year) ?>">
    </div>
    <div class="mb-3">
      <label for="uni_gpa" class="form-label">ผลการเรียน (เกรดเฉลี่ย)</label>
      <input type="text" id="uni_gpa" name="uni_gpa" class="form-control" value="<?= htmlspecialchars($uni_gpa) ?>">
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
