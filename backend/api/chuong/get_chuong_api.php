<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Chỉ chấp nhận phương thức GET"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ'], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once __DIR__ . '/../../model/ChuongModel.php';
$model = new ChuongModel();
$chuong = $model->getById($id);
if (!$chuong) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy chương'], JSON_UNESCAPED_UNICODE);
    exit();
}

http_response_code(200);
echo json_encode(['success' => true, 'chuong' => $chuong], JSON_UNESCAPED_UNICODE);
exit();
?>