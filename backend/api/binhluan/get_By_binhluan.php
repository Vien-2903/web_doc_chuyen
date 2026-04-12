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
        "message" => "Chỉ chấp nhận GET"
    ]);
    exit();
}

require_once(__DIR__ . '/../../controller/BinhluanController.php');

$controller = new BinhluanController(true);

$id_truyen = $_GET['id_truyen'] ?? '';

$response = $controller->get_By_binhluan_Api($id_truyen);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>