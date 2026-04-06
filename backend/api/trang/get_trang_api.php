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

require_once(__DIR__ . '/../../controller/TrangController.php');
$controller = new TrangController();

if (isset($_GET['id_chuong'])) {
    $response = $controller->getByChuan($_GET['id_chuong']);
} elseif (isset($_GET['id'])) {
    $response = $controller->getByIdApi($_GET['id']);
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Thiếu tham số"], JSON_UNESCAPED_UNICODE);
    exit();
}

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();