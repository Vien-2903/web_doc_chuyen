<?php
header("Content-Type: application/json; charset=UTF-8");

require_once(__DIR__ . '/../../controller/TheLoaiController.php');

$controller = new TheLoaiController();

// lấy id từ URL
$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "Thiếu ID"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// gọi đúng hàm
$response = $controller->getByIdApi($id);

// trả kết quả
http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();