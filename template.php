<?php
session_start();
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบ user_id
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // ถ้าไม่ล็อกอิน ให้ไปหน้า login
    exit;
}

$user_id = $_SESSION['user_id'];

// ฟังก์ชันสำหรับดึงข้อมูลเดี่ยว (คอลัมน์เดียว)
function fetchDataSingle($conn, $table, $column, $condition = "user_id = :user_id") {
    try {
        $stmt = $conn->prepare("SELECT $column FROM $table WHERE $condition LIMIT 1");
        if (strpos($condition, ':user_id') !== false) {
            $stmt->bindParam(':user_id', $GLOBALS['user_id'], PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("DB Error in fetchDataSingle: " . $e->getMessage());
        return null;
    }
}

// ฟังก์ชันสำหรับดึงข้อมูลเดี่ยว (ทั้งแถวในรูปแบบ Associative Array) - เพิ่มใหม่
function fetchDataAssocSingle($conn, $table, $condition = "user_id = :user_id") {
    try {
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $condition LIMIT 1");
        if (strpos($condition, ':user_id') !== false) {
            $stmt->bindParam(':user_id', $GLOBALS['user_id'], PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // ดึงข้อมูลทั้งแถวเป็น Array
    } catch (PDOException $e) {
        error_log("DB Error in fetchDataAssocSingle: " . $e->getMessage());
        return null;
    }
}

// ฟังก์ชันสำหรับดึงข้อมูลหลายรายการ
function fetchDataMultiple($conn, $table, $condition = "user_id = :user_id") {
    try {
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $condition ORDER BY id DESC"); 
        if (strpos($condition, ':user_id') !== false) {
            $stmt->bindParam(':user_id', $GLOBALS['user_id'], PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("DB Error in fetchDataMultiple: " . $e->getMessage());
        return [];
    }
}

// -------------------------------
// ดึงข้อมูล CV
// -------------------------------

// ดึงข้อมูลคำรับรองแบบทั้งแถว เพื่อให้ได้ทั้งข้อความและชื่อครู
$recommendation_details = fetchDataAssocSingle($conn, 'certificate_requests', "user_id = :user_id AND status = 'certified'");

$cv_data = [
    'profile_img' => fetchDataSingle($conn, 'cv_profile', 'profile_image'),
    'name_th' => fetchDataSingle($conn, 'personal_info', 'full_name'),
    'job_title' => fetchDataSingle($conn, 'personal_info', 'position'),
    'email' => fetchDataSingle($conn, 'personal_info', 'email'),
    'phone' => fetchDataSingle($conn, 'personal_info', 'phone'),
    'address' => fetchDataSingle($conn, 'personal_info', 'address'),
    'portfolio_link' => fetchDataSingle($conn, 'personal_info', 'profile_link'),
    'summary' => fetchDataSingle($conn, 'summary_info', 'summary'),
    'edu_high_school' => fetchDataSingle($conn, 'education_info', 'highschool_name'),
    'edu_high_school_plan' => fetchDataSingle($conn, 'education_info', 'highschool_plan'),
    'edu_high_school_gpa' => fetchDataSingle($conn, 'education_info', 'highschool_gpa'),
    'edu_university' => fetchDataSingle($conn, 'education_info', 'uni_name'),
    'stu_id' => fetchDataSingle($conn, 'education_info', 'stu_id'),
    'edu_degree' => fetchDataSingle($conn, 'education_info', 'degree'),
    'edu_major' => fetchDataSingle($conn, 'education_info', 'major'),
    'edu_graduation_year' => fetchDataSingle($conn, 'education_info', 'grad_year'),
    'edu_university_gpa' => fetchDataSingle($conn, 'education_info', 'uni_gpa'),
    
    // แทนที่ 'reference' ด้วยสองคีย์ใหม่
    'reference_text' => $recommendation_details['certification_text'] ?? '',
    'reference_teacher' => $recommendation_details['teacher_name'] ?? '' // สมมติว่ามีคอลัมน์ teacher_name ในตาราง
];

// ดึงข้อมูลรายการหลายรายการ
$cv_data['work_experience'] = fetchDataMultiple($conn, 'experience_info');

$tech_skills_string = fetchDataSingle($conn, 'skills_info', 'technical_skills');
$cv_data['technical_skills'] = $tech_skills_string ? array_map('trim', explode(',', $tech_skills_string)) : [];

$soft_skills_string = fetchDataSingle($conn, 'skills_info', 'soft_skills');
$cv_data['soft_skills'] = $soft_skills_string ? array_map('trim', explode(',', $soft_skills_string)) : [];

$all_activities = fetchDataMultiple($conn, 'activities_info');
$projects_list = [];
$activities_list = [];
foreach ($all_activities as $item) {
    if (!empty($item['activity']) && !empty($item['project'])) {
        $projects_list[] = ['name' => $item['activity'], 'description' => $item['project']];
    } elseif (!empty($item['activity'])) {
        $activities_list[] = $item['activity'];
    }
}
$cv_data['projects'] = $projects_list;
$cv_data['activities'] = $activities_list;

// เตรียมข้อมูลสำหรับ JS
foreach ($cv_data as $key => $value) {
    if (is_null($value)) $cv_data[$key] = '';
}
$cv_data['profile_img'] = empty($cv_data['profile_img']) 
    ? 'https://via.placeholder.com/250x250?text=Photo'
    : 'upload/img/' . $cv_data['profile_img'];

$js_data = json_encode($cv_data, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>choose template</title>
    
    <link rel="stylesheet" href="style/template-selection.css"> 
    <link rel="stylesheet" href="style/cv-minimalist-focus.css">
    <link rel="stylesheet" href="style/cv-modern-sidebar.css">
    <link rel="stylesheet" href="style/cv-professional-hybrid.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        const USER_CV_DATA = <?php echo $js_data; ?>;
    </script>
</head>
<body>
    <?php
    include('ic/navbar.php');
    ?>
    
    <header class="page-header">
        <h1>✨ เลือกเทมเพลตสำหรับ CV</h1>
        <p>เลือกรูปแบบที่สะท้อนความเป็นคุณมากที่สุด หรือลองดูตัวอย่างเพื่อพิจารณาความเหมาะสม</p>
    </header>

    <div class="back-to-fillout">
        <a href="fill_out.php" class="back-to-fillout-btn">
            <i class="fas fa-arrow-left"></i> ย้อนกลับ
        </a>
    </div>

    <main class="template-selection-main">
        <div class="template-container">
            
            <?php
            // 1. กำหนดข้อมูลเทมเพลตทั้งหมดในรูปของ Array (รอคุณกรอกพาธ)
            $templates = [
                [
                    'id' => 'modern-sidebar',
                    'name' => 'Modern Sidebar',
                    'desc' => 'เน้นความชัดเจน ชื่อและตำแหน่งเด่นชัด พร้อมพื้นที่แสดงสกิลครบถ้วน',
                    'img' => 'image/modern_cv.png'
                ],
                [
                    'id' => 'professional-hybrid',
                    'name' => 'Professional Hybrid',
                    'desc' => 'โครงสร้างแข็งแกร่ง เหมาะสำหรับนักศึกษาหรือผู้หางานที่ต้องการเน้นประสบการณ์',
                    'img' => 'image/profes_cv.png'
                ],
                [
                    'id' => 'minimalist-focus',
                    'name' => 'Minimalist Focus',
                    'desc' => 'สะอาดตา เน้นเนื้อหาและความชัดเจน',
                    'img' => 'image/minimal_cv.png'
                ]
            ];

            // 2. วนซ้ำ (Loop) เพื่อสร้าง Card แต่ละใบ
            foreach ($templates as $template) {
                $id = $template['id'];
                $name = $template['name'];
                $desc = $template['desc'];
                $img = $template['img']; // ดึงพาธรูปภาพ
                ?>

                <section class="template-card cute-card" data-template-id="<?php echo $id; ?>">
                    <div class="template-thumbnail-wrapper">
                        <img src="<?php echo $img; ?>" alt="<?php echo $name; ?> Preview">
                    </div>
                    
                    <div class="card-content">
                        <h3><?php echo $name; ?></h3>
                        <p class="description"><?php echo $desc; ?></p>
                        
                        <div class="card-actions">
                            <button class="btn-select primary-btn" data-template="<?php echo $id; ?>"><i class="fas fa-check"></i> เลือก</button> 
                            <button class="btn-preview" data-template="<?php echo $id; ?>"><i class="fas fa-eye"></i> ดูตัวอย่าง</button>
                            <button class="btn-download btn-download-trigger" data-template="<?php echo $id; ?>" title="ดาวน์โหลด PDF"><i class="fas fa-download"></i></button>
                        </div>
                    </div>
                </section>

                <?php
            } 
            ?>
            
        </div>
        
        <div id="previewModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span> 
                <div id="cvPreviewArea">
                </div>
            </div>
        </div>
    </main>

    <?php 
    include('ic/footer.php'); 
    ?>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script> 
    <script src="js/template.js"></script>


</body>
</html>