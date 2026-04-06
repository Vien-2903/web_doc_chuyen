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
        "message" => "Chỉ chấp nhận phương thức GET"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

require_once __DIR__ . '/../../model/TacGiaModel.php';

$model = new TacGiaModel();
$result = $model->getAll();

$authors = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $authors[] = [
            'id' => isset($row['id']) ? (int)$row['id'] : null,
            'ten_tacgia' => $row['ten_tacgia'] ?? null,
            'but_danh' => $row['but_danh'] ?? null
        ];
    }
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $authors
], JSON_UNESCAPED_UNICODE);

exit();
?>