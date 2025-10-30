<?php
session_start();
require_once('config.php'); 

// ----------------------------------------------------
// 1. ตรวจสอบ Token
// ----------------------------------------------------
$token = $_GET['token'] ?? '';
$error_message = null;
$request_info = null;
$request_id = 0; // กำหนดค่าเริ่มต้นเป็น 0

if (empty($token) || strlen($token) !== 32) {
    $error_message = "ลิงก์คำรับรองไม่ถูกต้องหรือขาด Token";
} else {
    try {
        // 2. ดึงข้อมูลคำขอและสถานะของนักศึกษา
        // แก้ไข: ใช้ JOIN personal_info (p) เพื่อดึงชื่อ (full_name)
        $stmt = $conn->prepare("
            SELECT 
                cr.id, cr.user_id, cr.teacher_email, cr.reason, cr.status,
                p.full_name AS student_full_name, 
                p.email AS student_email
            FROM certificate_requests cr
            JOIN personal_info p ON cr.user_id = p.user_id
            WHERE cr.request_token = ?
        ");
        $stmt->execute([$token]);
        $request_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request_info) {
            $error_message = "ไม่พบคำขอใบรับรองนี้ในระบบ หรือลิงก์ถูกยกเลิกแล้ว";
        } elseif ($request_info['status'] === 'COMPLETED') {
            $error_message = "คำรับรองนี้ถูกกรอกและบันทึกเรียบร้อยแล้ว";
        } else {
            // โหลดข้อมูลสำเร็จ กำหนดค่าตัวแปรสำคัญ
            $student_name = $request_info['student_full_name'];
            $request_id = $request_info['id']; // <--- สำคัญ: กำหนดค่า ID ตรงนี้
            $reason = $request_info['reason'];
        }

    } catch (PDOException $e) {
        // ข้อผิดพลาดในการค้นหาในฐานข้อมูล: 1054 Unknown column 'u.fullname' (แก้ไขโดยใช้ 'p.full_name')
        $error_message = "ข้อผิดพลาดในการค้นหาในฐานข้อมูล: " . $e->getMessage();
        error_log("DB Error in certificate_form: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์มกรอกคำรับรอง</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-inter">

    <div class="w-full max-w-3xl bg-white p-8 rounded-xl shadow-2xl border-t-4 border-indigo-500">
        <h1 class="text-3xl font-extrabold text-center text-indigo-700 mb-8">
            แบบฟอร์มกรอกคำรับรอง (Recommendation Letter)
        </h1>

        <?php if (isset($error_message)): ?>
            <!-- แสดงข้อความ Error -->
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6">
                <strong class="font-bold">เกิดข้อผิดพลาด:</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php else: ?>
            <!-- แสดงฟอร์มเมื่อไม่มีข้อผิดพลาดและสถานะเป็น PENDING -->
            <div class="bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700 p-4 rounded-md mb-6 shadow-sm">
                <p class="font-bold text-lg mb-1">คำขอจากนักศึกษา:</p>
                <p>นักศึกษา: <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($student_name ?? 'N/A'); ?></span></p>
                <p>เหตุผลในการขอ: <span class="text-gray-600"><?php echo nl2br(htmlspecialchars($reason ?? 'N/A')); ?></span></p>
            </div>

            <form action="process_certificate.php" method="POST" class="space-y-6">
                
                <!-- ข้อมูลสำคัญที่ต้องส่งไปประมวลผล (ซ่อนไว้) -->
                <!-- *** สำคัญ: ตัวแปร $request_id ถูกกำหนดค่าแล้วใน PHP ด้านบน *** -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>"> 

                <!-- ส่วนกรอกข้อมูลคำรับรอง -->
                <div>
                    <label for="certificate_text" class="block text-sm font-medium text-gray-700 mb-2">
                        เนื้อหาคำรับรอง (Recommendation Content):
                    </label>
                    <textarea 
                        id="certificate_text" 
                        name="certificate_text" 
                        rows="12" 
                        required 
                        class="w-full p-4 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-inner resize-none"
                        placeholder="กรุณากรอกรายละเอียดคำรับรองที่นี่..."
                    ></textarea>
                </div>
                
                <!-- ปุ่มบันทึก -->
                <div class="flex justify-center pt-6">
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold text-lg rounded-lg shadow-xl hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:scale-[1.02]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v5a1 1 0 102 0V7z" clip-rule="evenodd" />
                          <path d="M7.707 9.293a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414l3-3a1 1 0 011.414 0zM12.293 9.293a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" />
                        </svg>
                        บันทึกคำรับรอง
                    </button>
                </div>

            </form>

        <?php endif; ?>

    </div>
</body>
</html>