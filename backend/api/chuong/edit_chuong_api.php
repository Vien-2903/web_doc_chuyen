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

require_once(__DIR__ . '/../../controller/ChuongController.php');

$controller = new ChuongController();

// Support form-data or JSON body
$input = [];
if (!empty($_POST) || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false)) {
    $input['id'] = $_POST['id'] ?? 0;
    $input['id_truyen'] = $_POST['id_truyen'] ?? 0;
    $input['so_chuong'] = $_POST['so_chuong'] ?? '';
    $input['tieu_de'] = $_POST['tieu_de'] ?? '';
} else {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
}

$data = [
    'id'        => $input['id']        ?? 0,
    'id_truyen' => $input['id_truyen'] ?? 0,
    'so_chuong' => $input['so_chuong'] ?? '',
    'tieu_de'   => $input['tieu_de']   ?? ''
];

$response = $controller->updateChuongApi($data);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>