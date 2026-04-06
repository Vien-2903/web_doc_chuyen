<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận phương thức POST'], JSON_UNESCAPED_UNICODE);
    exit();
}

$inputId = $_POST['id'] ?? null;
$id = intval($inputId);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ'], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once __DIR__ . '/../../model/TruyenModel.php';
$model = new TruyenModel();

// Check related data (chapters etc.)
try {
    if ($model->hasRelatedData($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Không thể xóa: truyện có dữ liệu liên quan (ví dụ chương)'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $deleted = $model->delete($id);
    if ($deleted) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Xóa truyện thành công'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Xóa thất bại, thử lại'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

exit();
?>