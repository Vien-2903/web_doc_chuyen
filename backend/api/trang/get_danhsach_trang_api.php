<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Chỉ chấp nhận GET"], JSON_UNESCAPED_UNICODE);
    exit();
}

$id_chuong = intval($_GET['id_chuong'] ?? 0);

if ($id_chuong <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Thiếu hoặc sai id_chuong"], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once(__DIR__ . '/../../controller/TrangController.php');
$controller = new TrangController();

$response = $controller->getByChuong($id_chuong); 

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();