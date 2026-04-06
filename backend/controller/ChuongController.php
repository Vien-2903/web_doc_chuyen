<?php
require_once __DIR__ . '/../model/ChuongModel.php';

class ChuongController {
    private $chuongModel;

    public function __construct() {
        $this->chuongModel = new ChuongModel();
    }

    // API: Lấy tất cả chương
    public function getAllChuongApi() {
        try {
            $data = $this->chuongModel->getAll();
            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'data' => $data
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => 'Lỗi server: ' . $e->getMessage()
                ]
            ];
        }
    }

    // API: Lấy chương theo ID truyện
    public function getChuongByTruyenApi($id_truyen) {
        $id_truyen = intval($id_truyen);
        if ($id_truyen <= 0) {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'ID truyện không hợp lệ'
                ]
            ];
        }

        try {
            $data = $this->chuongModel->getChuongByTruyenId($id_truyen);
            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'data' => $data
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => 'Lỗi server: ' . $e->getMessage()
                ]
            ];
        }
    }

    // API: Thêm chương
    public function addChuongApi($data) {
        $id_truyen = intval($data['id_truyen'] ?? 0);
        $so_chuong = trim($data['so_chuong'] ?? '');
        $tieu_de   = trim($data['tieu_de'] ?? '');

        // Validate
        if ($id_truyen <= 0) {
            return [
                'status' => 400,
                'body' => ['success' => false, 'message' => 'Vui lòng chọn truyện']
            ];
        }
        if ($so_chuong === '') {
            return [
                'status' => 400,
                'body' => ['success' => false, 'message' => 'Số chương không được để trống']
            ];
        }

        try {
            $result = $this->chuongModel->create([
                'id_truyen' => $id_truyen,
                'so_chuong' => $so_chuong,
                'tieu_de'   => $tieu_de
            ]);

            if (!$result) {
                throw new Exception('Thêm chương thất bại');
            }

            return [
                'status' => 201,
                'body' => [
                    'success' => true,
                    'message' => 'Thêm chương thành công'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => 'Lỗi server: ' . $e->getMessage()
                ]
            ];
        }
    }

    // API: Sửa chương
    public function updateChuongApi($data) {
        $id        = intval($data['id'] ?? 0);
        $id_truyen = intval($data['id_truyen'] ?? 0);
        $so_chuong = trim($data['so_chuong'] ?? '');
        $tieu_de   = trim($data['tieu_de'] ?? '');

        if ($id <= 0) {
            return [
                'status' => 400,
                'body' => ['success' => false, 'message' => 'ID chương không hợp lệ']
            ];
        }
        if ($so_chuong === '') {
            return [
                'status' => 400,
                'body' => ['success' => false, 'message' => 'Số chương không được để trống']
            ];
        }

        $existing = $this->chuongModel->getById($id);
        if (!$existing) {
            return [
                'status' => 404,
                'body' => ['success' => false, 'message' => 'Không tìm thấy chương']
            ];
        }

        try {
            $result = $this->chuongModel->update($id, [
                'id_truyen' => $id_truyen ?: $existing['id_truyen'],
                'so_chuong' => $so_chuong,
                'tieu_de'   => $tieu_de
            ]);

            if (!$result) {
                throw new Exception('Cập nhật thất bại');
            }

            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'message' => 'Cập nhật chương thành công'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => 'Lỗi server: ' . $e->getMessage()
                ]
            ];
        }
    }

    // API: Xóa chương
    public function deleteChuongApi($id) {
        $id = intval($id);

        if ($id <= 0) {
            return [
                'status' => 400,
                'body' => ['success' => false, 'message' => 'ID không hợp lệ']
            ];
        }

        $existing = $this->chuongModel->getById($id);
        if (!$existing) {
            return [
                'status' => 404,
                'body' => ['success' => false, 'message' => 'Không tìm thấy chương']
            ];
        }

        if ($this->chuongModel->hasRelatedData($id)) {
            return [
                'status' => 409,
                'body' => ['success' => false, 'message' => 'Không thể xóa vì chương đang có dữ liệu liên quan']
            ];
        }

        try {
            $result = $this->chuongModel->delete($id);
            if (!$result) {
                throw new Exception('Xóa thất bại');
            }

            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'message' => 'Xóa chương thành công'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => 'Lỗi server: ' . $e->getMessage()
                ]
            ];
        }
    }
}
?>