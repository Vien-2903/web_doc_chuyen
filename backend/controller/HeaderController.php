<?php
require_once(__DIR__ . '/../model/TheLoaiModel.php');

class HeaderController {
	private $theLoaiModel;

	public function __construct() {
		$this->theLoaiModel = new TheLoaiModel();
	}

	public function getHeaderData($sessionUser, $keyword = '') {
		$danhSachTheLoai = [];
		$thongBaoTheLoai = 'Chưa có thể loại nào';

		try {
			$duLieuTheLoai = $this->theLoaiModel->getAll();

			if (!empty($duLieuTheLoai) && is_array($duLieuTheLoai)) {
				foreach ($duLieuTheLoai as $theLoai) {
					$danhSachTheLoai[] = [
						'id' => (int)($theLoai['id'] ?? 0),
						'ten_theloai' => $theLoai['ten_theloai'] ?? ''
					];
				}
				$thongBaoTheLoai = '';
			}
		} catch (Throwable $e) {
			$thongBaoTheLoai = 'Không tải được dữ liệu thể loại';
		}

		$user = [
			'isLoggedIn' => !empty($sessionUser),
			'displayName' => !empty($sessionUser)
				? ($sessionUser['ten_nguoidung'] ?? $sessionUser['ten_dang_nhap'] ?? 'Tài khoản')
				: ''
		];

		return [
			'status' => 200,
			'body' => [
				'success' => true,
				'danhSachTheLoai' => $danhSachTheLoai,
				'thongBaoTheLoai' => $thongBaoTheLoai,
				'keyword' => (string) $keyword,
				'user' => $user
			]
		];
	}
}

// Backward compatibility when this controller is called directly.
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	header('Content-Type: application/json; charset=utf-8');

	$controller = new HeaderController();
	$response = $controller->getHeaderData($_SESSION['user'] ?? null, $_GET['keyword'] ?? '');

	http_response_code($response['status']);
	echo json_encode($response['body'], JSON_UNESCAPED_UNICODE);
}

