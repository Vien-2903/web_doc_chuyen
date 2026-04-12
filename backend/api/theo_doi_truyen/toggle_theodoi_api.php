<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Chi chap nhan POST'
    ]);
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Ban can dang nhap de theo doi truyen.',
        'login_required' => true
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once(__DIR__ . '/../../controller/TheoDoiTruyenController.php');

$controller = new TheoDoiTruyenController(true);
$input = json_decode(file_get_contents('php://input'), true) ?: [];

$data = [
    'id_nguoidung' => (int)$_SESSION['user']['id'],
    'id_truyen' => $input['id_truyen'] ?? ''
];

$response = $controller->toggleFollowApi($data);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>
