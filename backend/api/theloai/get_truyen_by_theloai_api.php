<?php
// backend/api/theloai/get_truyen_by_theloai_api.php

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Chỉ chấp nhận phương thức GET'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once __DIR__ . '/../../database/myconnection.php';
require_once __DIR__ . '/../../model/TheLoaiModel.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID thể loại không hợp lệ'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$model   = new TheLoaiModel();
$theLoai = $model->getById($id);

if (!$theLoai) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy thể loại này'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$truyens = $model->getTruyenByTheLoai($id);

http_response_code(200);
echo json_encode([
    'success'     => true,
    'id'          => $id,
    'ten_theloai' => $theLoai['ten_theloai'],
    'total'       => count($truyens),
    'truyens'     => $truyens
], JSON_UNESCAPED_UNICODE);