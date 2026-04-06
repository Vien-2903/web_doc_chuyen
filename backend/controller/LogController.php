<?php
require_once(__DIR__ . '/../model/NguoiDungModel.php');

class LogController {
	private $nguoiDungModel;

	public function __construct() {
		$this->nguoiDungModel = new NguoiDungModel();
	}

	public function login($data) {
		if (!is_array($data)) {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'message' => 'Dữ liệu gửi lên không hợp lệ'
				]
			];
		}

		$ten_dang_nhap = trim($data['ten_dang_nhap'] ?? '');
		$mat_khau = $data['mat_khau'] ?? '';
		$errors = [];

		if ($ten_dang_nhap === '') {
			$errors[] = 'Tên đăng nhập không được để trống';
		}
		if ($mat_khau === '') {
			$errors[] = 'Mật khẩu không được để trống';
		}

		if (!empty($errors)) {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'errors' => $errors
				]
			];
		}

		$user = $this->nguoiDungModel->login($ten_dang_nhap, $mat_khau);

		if (!$user) {
			return [
				'status' => 401,
				'body' => [
					'success' => false,
					'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
				]
			];
		}

		$_SESSION['user'] = [
			'id' => $user['id'],
			'ten_nguoidung' => $user['ten_dang_nhap'],
			'email' => $user['email'],
			'vai_tro' => $user['vai_tro']
		];

		return [
			'status' => 200,
			'body' => [
				'success' => true,
				'message' => 'Đăng nhập thành công',
				'user' => [
					'id' => $user['id'],
					'ten' => $user['ten_dang_nhap'],
					'email' => $user['email'],
					'vai_tro' => $user['vai_tro']
				]
			]
		];
	}

	public function register($data) {
		if (!is_array($data)) {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'message' => 'Dữ liệu gửi lên không hợp lệ'
				]
			];
		}

		$ten_dang_nhap = trim($data['ten_dang_nhap'] ?? '');
		$email = trim($data['email'] ?? '');
		$mat_khau = $data['mat_khau'] ?? '';
		$mat_khau_confirm = $data['mat_khau_confirm'] ?? '';
		$errors = [];

		if ($ten_dang_nhap === '') {
			$errors[] = 'Tên đăng nhập không được để trống';
		}

		if ($email === '') {
			$errors[] = 'Email không được để trống';
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Email không hợp lệ';
		}

		if ($mat_khau === '') {
			$errors[] = 'Mật khẩu không được để trống';
		} elseif (strlen($mat_khau) < 6) {
			$errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
		}

		if ($mat_khau_confirm === '') {
			$errors[] = 'Vui lòng nhập xác nhận mật khẩu';
		} elseif ($mat_khau !== $mat_khau_confirm) {
			$errors[] = 'Mật khẩu xác nhận không khớp';
		}

		if ($ten_dang_nhap !== '' && $this->nguoiDungModel->checkTenDangNhapExists($ten_dang_nhap)) {
			$errors[] = 'Tên đăng nhập đã tồn tại';
		}

		if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->nguoiDungModel->checkEmailExists($email)) {
			$errors[] = 'Email đã được sử dụng';
		}

		if (!empty($errors)) {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'errors' => $errors
				]
			];
		}

		$created = $this->nguoiDungModel->createNguoiDung($ten_dang_nhap, $mat_khau, $email, 'user');

		if (!$created) {
			return [
				'status' => 500,
				'body' => [
					'success' => false,
					'message' => 'Có lỗi xảy ra khi tạo tài khoản'
				]
			];
		}

		return [
			'status' => 201,
			'body' => [
				'success' => true,
				'message' => 'Đăng ký thành công'
			]
		];
	}

	public function logout($requestMethod = 'GET', $acceptHeader = '') {
		$_SESSION = [];

		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}

		session_destroy();

		if ($requestMethod === 'POST' || strpos((string) $acceptHeader, 'application/json') !== false) {
			return [
				'status' => 200,
				'body' => [
					'success' => true,
					'message' => 'Đăng xuất thành công'
				],
				'redirect' => null
			];
		}

		return [
			'status' => 302,
			'body' => null,
			'redirect' => '/web_doc_truyen/frontend/public/index.html'
		];
	}
}
?>
