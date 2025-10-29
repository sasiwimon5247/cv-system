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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $positions = $_POST['position'] ?? [];
    $companies = $_POST['company'] ?? [];
    $durations = $_POST['duration'] ?? [];
    $descriptions = $_POST['description'] ?? [];

    try {
        // ลบข้อมูลเก่าออกก่อน
        $stmt = $conn->prepare("DELETE FROM experience_info WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // เพิ่มข้อมูลใหม่
        for ($i = 0; $i < count($positions); $i++) {
            if (!empty($positions[$i]) || !empty($companies[$i]) || !empty($durations[$i]) || !empty($descriptions[$i])) {
                $stmt = $conn->prepare("INSERT INTO experience_info (user_id, position, company, duration, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user_id,
                    $positions[$i],
                    $companies[$i],
                    $durations[$i],
                    $descriptions[$i]
                ]);
            }
        }

        $success_msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $error_msg = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// โหลดข้อมูลเก่า
$stmt = $conn->prepare("SELECT * FROM experience_info WHERE user_id = ?");
$stmt->execute([$user_id]);
$experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ประสบการณ์ทำงาน/ฝึกงาน</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Roboto&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link href="style/experience.css" rel="stylesheet">
</head>
<body>
<?php
include('ic/navbar.php');
?>
<div class="experience-container">

    <h2 class="section-title-box">
        <i class="fa-solid fa-briefcase section-icon"></i> 
        ประสบการณ์ทำงาน/ฝึกงาน
    </h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form method="post" class="experience-form" id="experienceForm">
        <div id="experience-wrapper">
            <?php if (!empty($experiences)): ?>
                <?php foreach ($experiences as $exp): ?>
                    <div class="experience-item mb-4 p-3 border rounded">
                        <div class="mb-2">
                            <label class="form-label">ชื่อตำแหน่ง</label>
                            <input type="text" name="position[]" class="form-control" value="<?= htmlspecialchars($exp['position']) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">ชื่อบริษัท/องค์กร</label>
                            <input type="text" name="company[]" class="form-control" value="<?= htmlspecialchars($exp['company']) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">ระยะเวลาทำงาน</label>
                            <input type="text" name="duration[]" class="form-control" value="<?= htmlspecialchars($exp['duration']) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">รายละเอียดงานและความสำเร็จ</label>
                            <textarea name="description[]" class="form-control" rows="3"><?= htmlspecialchars($exp['description']) ?></textarea>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-exp">ลบ</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="experience-item mb-4 p-3 border rounded">
                    <div class="mb-2">
                        <label class="form-label">ชื่อตำแหน่ง</label>
                        <input type="text" name="position[]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">ชื่อบริษัท/องค์กร</label>
                        <input type="text" name="company[]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">ระยะเวลาทำงาน</label>
                        <input type="text" name="duration[]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">รายละเอียดงานและความสำเร็จ</label>
                        <textarea name="description[]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <button type="button" id="addExperience" class="btn-add">+ เพิ่มประสบการณ์</button>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary btn-save">บันทึกข้อมูล</button>
            <a href="fill_out.php" class="btn btn-secondary btn-back">ย้อนกลับ</a>
        </div>
    </form>
</div>

<?php 
include('ic/footer.php'); 
?>

<script src="js/experience.js"></script>
</body>
</html>
