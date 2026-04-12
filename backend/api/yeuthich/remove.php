<?php
header("Content-Type: application/json; charset=UTF-8");

require_once(__DIR__ . '/../../controller/YeuthichController.php');

$controller = new YeuthichController(true);

$input = json_decode(file_get_contents("php://input"), true) ?: [];

$data = [
    'id_nguoidung' => $input['id_nguoidung'] ?? '',
    'id_truyen'    => $input['id_truyen'] ?? ''
];

$response = $controller->removeLikeApi($data);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>