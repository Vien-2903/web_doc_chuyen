<?php
require_once __DIR__ . '/../model/TacGiaModel.php';

class TacGiaController {
	private $model;

	public function __construct() {
		$this->model = new TacGiaModel();
	}

	public function addApi($data) {
		$ten_tacgia = trim($data['ten_tacgia'] ?? '');
		$but_danh = trim($data['but_danh'] ?? '');
		$gioi_thieu = trim($data['gioi_thieu'] ?? '');

		if ($ten_tacgia === '') {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'message' => 'Tên tác giả không được để trống'
				]
			];
		}

		if ($this->model->checkTenTacGiaExists($ten_tacgia)) {
			return [
				'status' => 409,
				'body' => [
					'success' => false,
					'message' => 'Tên tác giả đã tồn tại'
				]
			];
		}

		$ok = $this->model->insert($ten_tacgia, $but_danh, $gioi_thieu);

		if ($ok) {
			return [
				'status' => 201,
				'body' => [
					'success' => true,
					'message' => 'Thêm tác giả thành công',
					'data' => [
						'ten_tacgia' => $ten_tacgia,
						'but_danh' => $but_danh,
						'gioi_thieu' => $gioi_thieu
					]
				]
			];
		}

		return [
			'status' => 500,
			'body' => [
				'success' => false,
				'message' => 'Có lỗi xảy ra khi thêm tác giả'
			]
		];
	}

	public function updateApi($data) {
		$id = $data['id'] ?? null;
		$ten_tacgia = trim($data['ten_tacgia'] ?? '');
		$but_danh = trim($data['but_danh'] ?? '');
		$gioi_thieu = trim($data['gioi_thieu'] ?? '');

		if ($id === null || !is_numeric($id) || (int)$id <= 0) {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'message' => 'ID tác giả không hợp lệ'
				]
			];
		}

		$id = (int)$id;

		if ($ten_tacgia === '') {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'message' => 'Tên tác giả không được để trống'
				]
			];
		}

		$currentTacGia = $this->model->getById($id);

		if (!$currentTacGia) {
			return [
				'status' => 404,
				'body' => [
					'success' => false,
					'message' => 'Không tìm thấy tác giả'
				]
			];
		}

		if ($this->model->checkTenTacGiaExistsExceptId($ten_tacgia, $id)) {
			return [
				'status' => 409,
				'body' => [
					'success' => false,
					'message' => 'Tên tác giả đã tồn tại'
				]
			];
		}

		$ok = $this->model->update($id, $ten_tacgia, $but_danh, $gioi_thieu);

		if ($ok) {
			return [
				'status' => 200,
				'body' => [
					'success' => true,
					'message' => 'Cập nhật tác giả thành công',
					'data' => [
						'id' => $id,
						'ten_tacgia' => $ten_tacgia,
						'but_danh' => $but_danh,
						'gioi_thieu' => $gioi_thieu
					]
				]
			];
		}

		return [
			'status' => 500,
			'body' => [
				'success' => false,
				'message' => 'Có lỗi xảy ra khi cập nhật tác giả'
			]
		];
	}

	public function deleteApi($data) {
		$id = $data['id'] ?? null;

		if ($id === null || !is_numeric($id) || (int)$id <= 0) {
			return [
				'status' => 400,
				'body' => [
					'success' => false,
					'message' => 'ID tác giả không hợp lệ'
				]
			];
		}

		$id = (int)$id;
		$currentTacGia = $this->model->getById($id);

		if (!$currentTacGia) {
			return [
				'status' => 404,
				'body' => [
					'success' => false,
					'message' => 'Không tìm thấy tác giả'
				]
			];
		}

		if ($this->model->hasRelatedData($id)) {
			$count = $this->model->countRelatedRows($id);
			return [
				'status' => 409,
				'body' => [
					'success' => false,
					'message' => 'Không thể xóa, tác giả đang có dữ liệu liên quan.',
					'data' => [
						'so_du_lieu_lien_quan' => (int)$count
					]
				]
			];
		}

		$ok = $this->model->delete($id);

		if ($ok) {
			return [
				'status' => 200,
				'body' => [
					'success' => true,
					'message' => 'Xóa tác giả thành công',
					'data' => [
						'id' => $id
					]
				]
			];
		}

		return [
			'status' => 500,
			'body' => [
				'success' => false,
				'message' => 'Có lỗi xảy ra khi xóa tác giả'
			]
		];
	}
}
?>
