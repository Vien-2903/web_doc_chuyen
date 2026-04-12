<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Chỉ chấp nhận PUT"
    ]);
    exit();
}

require_once(__DIR__ . '/../../controller/BinhluanController.php');

$controller = new BinhluanController(true);

$input = json_decode(file_get_contents("php://input"), true) ?: [];

$data = [
    'id_binhluan' => $input['id_binhluan'] ?? '',
    'noidung'     => $input['noidung'] ?? ''
];

$response = $controller->update_binhluan_Api($data);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>