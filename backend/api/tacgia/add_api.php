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

require_once __DIR__ . '/../../controller/TacGiaController.php';

$input = json_decode(file_get_contents("php://input"), true) ?: [];

$data = [
	'ten_tacgia' => $_POST['ten_tacgia'] ?? ($input['ten_tacgia'] ?? ''),
	'but_danh' => $_POST['but_danh'] ?? ($input['but_danh'] ?? ''),
	'gioi_thieu' => $_POST['gioi_thieu'] ?? ($input['gioi_thieu'] ?? '')
];

	$controller = new TacGiaController();
	$response = $controller->addApi($data);

http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
exit();
?>
