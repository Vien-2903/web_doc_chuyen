<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Chỉ chấp nhận DELETE"
    ]);
    exit();
}

require_once(__DIR__ . '/../../controller/BinhluanController.php');

$controller = new BinhluanController(true);

$input = json_decode(file_get_contents("php://input"), true) ?: [];

$id_binhluan = $input['id_binhluan'] ?? '';

$response = $controller->deleteApi($id_binhluan);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>