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

require_once(__DIR__ . '/../../controller/TruyenController.php');

$controller = new TruyenController();

// Support multipart/form-data (form submit with file) or JSON body
$data = [];
if (!empty($_FILES) || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false)) {
    // Form-data submission
    $data = [
        'id_tacgia'   => $_POST['id_tacgia']   ?? '',
        'ten_truyen'  => $_POST['ten_truyen']  ?? '',
        'mo_ta'       => $_POST['mo_ta']       ?? '',
        'trang_thai'  => $_POST['trang_thai']  ?? 'dang_cap_nhat',
        'anh_bia'     => $_FILES['anh_bia']    ?? null,
        'theloai_ids' => $_POST['theloai_ids'] ?? []
    ];
} else {
    // JSON body fallback
    $input = json_decode(file_get_contents("php://input"), true) ?: [];
    $data = [
        'id_tacgia'   => $input['id_tacgia']   ?? '',
        'ten_truyen'  => $input['ten_truyen']  ?? '',
        'mo_ta'       => $input['mo_ta']       ?? '',
        'trang_thai'  => $input['trang_thai']  ?? 'dang_cap_nhat',
        'anh_bia'     => $input['anh_bia']     ?? '',
        'theloai_ids' => $input['theloai_ids'] ?? []
    ];
}

$response = $controller->addTruyenApi($data);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>