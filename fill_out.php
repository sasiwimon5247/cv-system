<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once('config.php'); // มีการเชื่อมต่อแล้วที่นี่
include('upload/profile_images.php'); // โค้ดนี้จะจัดการ POST และ Redirect

$username = $_SESSION['username'];

// 🚩 1. ดึงและล้าง Session Flash Messages (สำคัญมากสำหรับ PRG)
$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>กรอกข้อมูล CV</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="style/fill_out.css" rel="stylesheet" /> 
</head>
<body>
<?php
include('ic/navbar.php');
?>
<div class="container mt-4">

    <div class="row main-content-row">
        
        <div class="col-lg-4 col-md-12 mb-4">
            
            <form id="uploadForm" action="fill_out.php" method="post" enctype="multipart/form-data" class="upload-profile-form">
    
                <h4 class="form-title mb-4 text-center">รูปโปรไฟล์ของคุณ</h4>
                
                <?php if ($profile_image): ?>
                    <div class="d-flex justify-content-center mb-4">
                        <div class="profile-preview">
                            <img src="upload/img/<?= htmlspecialchars($profile_image) ?>" alt="Profile Image">
                            <form action="" method="post" style="display:inline;"> 
                                <input type="hidden" name="delete_profile_image" value="1">
                                <button type="submit" title="ลบรูป" onclick="return confirm('คุณต้องการลบรูปโปรไฟล์หรือไม่?')">✕</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="profilePic" class="form-label d-block text-start mb-2">
                        แนบ/เปลี่ยนรูป (JPG, PNG) 
                    </label>
                    <input class="form-control form-control-file" type="file" id="profilePic" name="profile_image" accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-upload w-100" name="upload_btn">อัปโหลดรูป</button>
            </form>

            <div class="d-grid mt-3">
                <a href="template.php" class="btn-template-col w-100">เลือกเทมเพลต CV</a>
            </div>

        </div>

        <div class="col-lg-8 col-md-12">
            <h3 class="mb-4 section-title">กรอกข้อมูล</h3>
            <div class="row g-4">
                <?php
                $sections = [
                    ['title' => 'ข้อมูลส่วนตัว', 'link' => 'personal.php', 'icon' => 'fa-user'],
                    ['title' => 'ประวัติย่อ', 'link' => 'summary.php', 'icon' => 'fa-file-alt'],
                    ['title' => 'ประวัติการศึกษา', 'link' => 'education.php', 'icon' => 'fa-graduation-cap'],
                    ['title' => 'ประสบการณ์ทำงาน / ฝึกงาน', 'link' => 'experience.php', 'icon' => 'fa-briefcase'],
                    ['title' => 'ทักษะ (Skills)', 'link' => 'skill.php', 'icon' => 'fa-gears'],
                    ['title' => 'กิจกรรม / โปรเจค / ใบรับรอง', 'link' => 'activities.php', 'icon' => 'fa-certificate']
                ];

                foreach ($sections as $section) {
                    echo '<div class="col-md-4 col-sm-6">'; 
                    echo '  <a href="'.$section['link'].'" class="text-decoration-none">';
                    echo '    <div class="card p-4 text-center h-100 cv-card">';
                    echo '      <i class="fa-solid '.$section['icon'].' cv-icon"></i>';
                    echo '      <h5>'.$section['title'].'</h5>'; 
                    echo '    </div>';
                    echo '  </a>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div> </div>

<?php 
include('ic/footer.php'); 
?>

<script src="js/fill_out.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>