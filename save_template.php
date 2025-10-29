<?php
session_start();
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล

header('Content-Type: application/json');

// ตรวจสอบว่ามีผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้']);
    exit;
}

$user_id = $_SESSION['user_id'];
$template_name = $_POST['template_name'] ?? '';

if (empty($template_name)) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบชื่อเทมเพลต']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE cv_profile SET template_name = :template_name WHERE user_id = :user_id");
    $stmt->bindParam(':template_name', $template_name);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'บันทึกเทมเพลตเรียบร้อยแล้ว']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
