<?php
require_once 'config.php'; 
$stu_id = isset($_GET['id']) ? trim($_GET['id']) : ''; 
if (!isset($conn)) {
    $status_color = '#F4A261'; 
    $status_text = '⚠️ ไม่สามารถตรวจสอบข้อมูลฐานข้อมูลได้';
    $display_name = 'ฐานข้อมูลไม่พร้อมใช้งาน';
    $display_uni = 'โปรดติดต่อผู้ดูแลระบบ';
    $display_id = $stu_id . ' (DB Error)';
} else {
    $student_data = null;

    if (!empty($stu_id)) {
        $sql = "
            SELECT 
                p.full_name,
                e.uni_name,
                e.stu_id
            FROM education_info e
            INNER JOIN personal_info p ON p.user_id = e.user_id 
            WHERE e.stu_id = :stu_id
            LIMIT 1
        ";
        
        // 💡 เปลี่ยนไปใช้ PDO Prepared Statement เพื่อความปลอดภัย
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':stu_id', $stu_id, PDO::PARAM_STR);
        $stmt->execute();
        
        $student_data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($student_data) {
        // ข้อมูลถูกยืนยันและดึงมาจากฐานข้อมูล
        $display_name = $student_data['full_name'];
        $display_uni = $student_data['uni_name'];
        $display_id = $student_data['stu_id'];
        $status_color = '#4CAF50'; // สีเขียว = พบ
        $status_text = '✅ ข้อมูลยืนยันตัวตน (ยืนยันจากฐานข้อมูล)';
    } else {
        // ไม่พบข้อมูลใน DB แต่รับค่าจาก URL มาได้
        $display_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'ไม่พบชื่อในระบบ';
        $display_uni = isset($_GET['uni']) ? htmlspecialchars($_GET['uni']) : 'ไม่พบสถาบันในระบบ';
        $display_id = empty($stu_id) ? '(ไม่พบรหัส)' : $stu_id . ' (รหัสไม่ตรงกับในระบบ)';
        $status_color = '#DC3545'; // สีแดง = ไม่พบ
        $status_text = '❌ ไม่พบข้อมูลนิสิตในระบบ';
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลยืนยันตัวตนนิสิต</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: <?php echo $status_color; ?>; /* ใช้สีตามสถานะ */
            border-bottom: 2px solid #f4f4f4;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        .info-box {
            text-align: left;
            margin-top: 15px;
            font-size: 1.1em;
        }
        .info-box p {
            margin: 10px 0;
            padding: 8px;
            /* เปลี่ยนสี Border ตามสถานะเพื่อให้ชัดเจนยิ่งขึ้น */
            border-left: 4px solid <?php echo $status_color; ?>; 
            background-color: #fffafb;
        }
        .info-box strong {
            display: inline-block;
            width: 120px;
            color: #212121;
        }
        .note {
            margin-top: 30px;
            color: #616161;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $status_text; ?></h1>

        <div class="info-box">
            <p><strong>ชื่อ-นามสกุล:</strong> <?php echo $display_name; ?></p>
            <p><strong>รหัสนิสิต:</strong> <?php echo $display_id; ?></p>
            <p><strong>มหาวิทยาลัย:</strong> <?php echo $display_uni; ?></p>
        </div>

        <div class="note">
            <p>ข้อมูลนี้ถูกดึงมาจากฐานข้อมูลกลางของระบบ CV DEV เพื่อยืนยันความถูกต้อง</p>
        </div>
    </div>
</body>
</html>