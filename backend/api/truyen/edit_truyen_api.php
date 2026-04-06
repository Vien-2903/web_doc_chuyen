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
    echo json_encode([
        "success" => false,
        "message" => "Chỉ chấp nhận phương thức POST"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once(__DIR__ . '/../../controller/TruyenController.php');

$controller = new TruyenController();

$data = [
    'id'          => $_POST['id']          ?? 0,
    'id_tacgia'   => $_POST['id_tacgia']   ?? '',
    'ten_truyen'  => $_POST['ten_truyen']  ?? '',
    'mo_ta'       => $_POST['mo_ta']       ?? '',
    'trang_thai'  => $_POST['trang_thai']  ?? '',
    'anh_bia'     => $_FILES['anh_bia']    ?? null,
    'theloai_ids' => $_POST['theloai_ids'] ?? []
];

$response = $controller->updateTruyenApi($data);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>