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

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Thiếu hoặc sai id trang"], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once(__DIR__ . '/../../model/TrangModel.php');
require_once(__DIR__ . '/../../model/ChuongModel.php');

$trangModel  = new TrangModel();
$chuongModel = new ChuongModel();

// Lấy trang hiện tại
$trang = $trangModel->getById($id);
if (!$trang) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Không tìm thấy trang"], JSON_UNESCAPED_UNICODE);
    exit();
}

// Lấy thông tin chương
$chuong = $chuongModel->getById($trang['id_chuong']);
if (!$chuong) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Không tìm thấy chương"], JSON_UNESCAPED_UNICODE);
    exit();
}

// Tìm trang trước và trang sau
$allTrangs  = $trangModel->getByChuong($trang['id_chuong']);
$trangTruoc = null;
$trangSau   = null;

foreach ($allTrangs as $index => $t) {
    if ($t['id'] == $trang['id']) {
        if ($index > 0)                        $trangTruoc = $allTrangs[$index - 1];
        if ($index < count($allTrangs) - 1)    $trangSau   = $allTrangs[$index + 1];
        break;
    }
}

http_response_code(200);
echo json_encode([
    "success"    => true,
    "trang"      => $trang,
    "chuong"     => $chuong,
    "trangTruoc" => $trangTruoc,
    "trangSau"   => $trangSau,
], JSON_UNESCAPED_UNICODE);
exit();