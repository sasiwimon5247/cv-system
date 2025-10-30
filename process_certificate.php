<?php
session_start();
require_once('config.php'); 

// ----------------------------------------------------
// ฟังก์ชันสำหรับแสดงผลลัพธ์และออกจากระบบ
// ----------------------------------------------------
function showResult($message, $is_success = false) {
    // กำหนดสีและหัวข้อตามผลลัพธ์
    $color = $is_success ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
    $title_text = $is_success ? 'สำเร็จ!' : 'เกิดข้อผิดพลาด!';

    // แสดง HTML เพื่อแจ้งผู้ใช้
    echo "<!DOCTYPE html>
    <html lang=\"th\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>ผลลัพธ์การบันทึกคำรับรอง</title>
        <script src=\"https://cdn.tailwindcss.com\"></script>
        <style> .font-inter { font-family: 'Inter', sans-serif; } </style>
    </head>
    <body class=\"bg-gray-100 flex items-center justify-center min-h-screen font-inter\">
        <div class=\"w-full max-w-xl bg-white p-8 rounded-xl shadow-2xl border-t-4 border-indigo-500 text-center\">
            <h1 class=\"text-3xl font-extrabold text-center text-indigo-700 mb-6\">
                ผลลัพธ์การบันทึกคำรับรอง
            </h1>
            <div class=\"$color px-4 py-3 rounded-lg relative mb-6\">
                <strong class=\"font-bold block text-xl mb-2\">$title_text</strong>
                <span class=\"block text-lg\">" . htmlspecialchars($message) . "</span>
            </div>
            <!-- ปุ่ม ปิดหน้านี้ สามารถใช้ history.back() หรือ window.close() -->
            <a href=\"#\" onclick=\"window.history.back()\" class=\"mt-4 inline-block px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition duration-300\">
                ย้อนกลับไปหน้าฟอร์ม
            </a>
        </div>
    </body>
    </html>";
    exit;
}

// ----------------------------------------------------
// 2. รับค่าจากฟอร์ม (POST)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // แก้ไขข้อความเพื่อบอกให้ชัดเจนว่าเกิดจาก GET Request
    showResult("เกิดข้อผิดพลาด: ต้องส่งข้อมูลผ่านฟอร์มเท่านั้น (POST Request)", false);
}

// แก้ไข: เปลี่ยนจาก $_POST['token'] เป็น $_POST['request_token'] เพื่อให้ตรงกับ certificate_form.php
$token = $_POST['request_token'] ?? '';
$request_id = $_POST['request_id'] ?? 0;
$teacher_name = $_POST['teacher_name'] ?? '';
// แก้ไข: เปลี่ยนจาก $_POST['certificate_text'] เป็น $_POST['recommendation_content']
$certificate_text = $_POST['recommendation_content'] ?? '';

// ----------------------------------------------------
// *** DEBUGGING BLOCK: แสดงค่าที่รับมาเพื่อยืนยันว่า Token ถูกส่งมาหรือไม่ ***
// หากต้องการตรวจสอบ ให้ลบ // ออกจากบรรทัดด้านล่าง และบันทึก
// บรรทัดนี้จะถูกลบออกเมื่อการแก้ไขสำเร็จ 
// showResult("DEBUG - ข้อมูลที่ได้รับ:<br>Token: " . $token . "<br>ID: " . $request_id . "<br>Name: " . $teacher_name . "<br>Content Length: " . strlen($certificate_text) . " ตัวอักษร", true);
// ----------------------------------------------------


// ----------------------------------------------------
// 3. ตรวจสอบข้อมูลเบื้องต้น
// ----------------------------------------------------
if (empty($token) || strlen($token) !== 32) {
    // โค้ดจะมาถึงตรงนี้ ถ้า $token เป็นค่าว่างหรือความยาวไม่ตรง (ยืนยันว่าเกิดจาก input ไม่ถูกส่งมา)
    showResult("ลิงก์คำรับรองไม่ถูกต้องหรือขาด Token"); 
}

if (empty($request_id) || empty($teacher_name) || empty($certificate_text)) {
    showResult("ข้อมูลไม่ครบถ้วน: ต้องกรอกชื่อผู้รับรอง, เนื้อหาคำรับรอง, และ ID คำขอต้องมีค่า");
}

// ----------------------------------------------------
// 4. ประมวลผลและบันทึกข้อมูลลงฐานข้อมูล
// ----------------------------------------------------
try {
    // 4.1 ตรวจสอบความถูกต้องของ Token และ ID คำขอ
    $stmt = $conn->prepare("
        SELECT status
        FROM certificate_requests 
        WHERE id = ? AND request_token = ?
    ");
    $stmt->execute([$request_id, $token]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        showResult("ไม่พบคำขอใบรับรองที่ตรงกับ ID และ Token ที่ให้มาในฐานข้อมูล");
    }

    if ($request['status'] === 'COMPLETED') {
        showResult("คำรับรองนี้ถูกกรอกและบันทึกเรียบร้อยแล้วก่อนหน้านี้", true);
    }
    
    // 4.2 อัปเดตคำขอ: บันทึกข้อมูลคำรับรอง, ชื่อผู้รับรอง, เปลี่ยนสถานะ
    $stmt = $conn->prepare("
        UPDATE certificate_requests
        SET 
            teacher_name = ?, 
            certification_text = ?, 
            status = 'COMPLETED',
            certified_at = NOW()
        WHERE id = ? AND request_token = ?
    ");
    $stmt->execute([
        $teacher_name, 
        $certificate_text, 
        $request_id, 
        $token
    ]);

    if ($stmt->rowCount() > 0) {
        showResult("บันทึกคำรับรองเสร็จสมบูรณ์แล้ว", true);
    } else {
        // อาจเกิดขึ้นเมื่อไม่มีแถวใดถูกอัปเดต (เช่น Token ถูกอัปเดตไปแล้วหรือ ID ไม่ตรง)
        showResult("ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง");
    }

} catch (PDOException $e) {
    showResult("ข้อผิดพลาดในการบันทึกฐานข้อมูล: " . $e->getMessage());
    error_log("DB Error in process_certificate: " . $e->getMessage());
}
?>