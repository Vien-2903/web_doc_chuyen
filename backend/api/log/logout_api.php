<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../../controller/LogController.php');

$controller = new LogController();
$response = $controller->logout($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['HTTP_ACCEPT'] ?? '');

if (!empty($response['redirect'])) {
    header('Location: ' . $response['redirect']);
    exit();
}

header('Content-Type: application/json; charset=utf-8');
http_response_code($response['status'] ?? 200);
echo json_encode($response['body'] ?? [
    'success' => true,
    'message' => 'Đăng xuất thành công'
], JSON_UNESCAPED_UNICODE);
exit();
