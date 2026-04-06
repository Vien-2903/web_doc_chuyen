<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit();
}

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode([
		"success" => false,
		"message" => "Chỉ chấp nhận phương thức POST"
	], JSON_UNESCAPED_UNICODE);
	exit();
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$data = [];

if (stripos($contentType, 'application/json') !== false) {
	$raw = file_get_contents("php://input");
	$data = json_decode($raw, true);

	if (!is_array($data)) {
		http_response_code(400);
		echo json_encode([
			"success" => false,
			"message" => "Dữ liệu JSON không hợp lệ"
		], JSON_UNESCAPED_UNICODE);
		exit();
	}
} else {
	$data = $_POST;
}

require_once(__DIR__ . '/../../controller/HoSoController.php');

$controller = new HoSoController();
$avatarFile = $_FILES['avatar'] ?? null;
$response = $controller->updateCurrentUserProfileData($_SESSION['user'] ?? null, $data, $avatarFile);

http_response_code($response['status'] ?? 500);
echo json_encode($response['body'] ?? [
	"success" => false,
	"message" => "Có lỗi không xác định"
], JSON_UNESCAPED_UNICODE);
exit();
?>
