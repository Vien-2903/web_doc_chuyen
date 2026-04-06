<?php
require_once __DIR__ . '/../model/TheLoaiModel.php';

class TheLoaiController {
    private $model;

    public function __construct() {
        $this->model = new TheLoaiModel();
    }

    /**
     * API: Lấy tất cả thể loại
     */
    public function getAllApi() {
        try {
            $data = $this->model->getAll();

            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'theloais' => $data
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * API: Lấy 1 thể loại theo ID
     */
    public function getByIdApi($id) {
        $id = intval($id);

        if ($id <= 0) {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'ID không hợp lệ'
                ]
            ];
        }

        $theloai = $this->model->getById($id);

        if (!$theloai) {
            return [
                'status' => 404,
                'body' => [
                    'success' => false,
                    'message' => 'Không tìm thấy thể loại'
                ]
            ];
        }

        return [
            'status' => 200,
            'body' => [
                'success' => true,
                'data' => $theloai
            ]
        ];
    }

    /**
     * API: Thêm thể loại
     */
    public function addApi($data) {
        $ten = trim($data['ten_theloai'] ?? '');

        if ($ten === '') {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'Tên không được để trống'
                ]
            ];
        }

        if ($this->model->existsByName($ten)) {
            return [
                'status' => 409,
                'body' => [
                    'success' => false,
                    'message' => 'Thể loại đã tồn tại'
                ]
            ];
        }

        try {
            $this->model->insert($ten);

            return [
                'status' => 201,
                'body' => [
                    'success' => true,
                    'message' => 'Thêm thành công'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * API: Cập nhật thể loại
     */
    public function updateApi($data) {
        $id = intval($data['id'] ?? 0);
        $ten = trim($data['ten_theloai'] ?? '');

        if ($id <= 0) {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'ID không hợp lệ'
                ]
            ];
        }

        if ($ten === '') {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'Tên không được để trống'
                ]
            ];
        }

        if ($this->model->existsByNameExceptId($ten, $id)) {
            return [
                'status' => 409,
                'body' => [
                    'success' => false,
                    'message' => 'Tên đã tồn tại'
                ]
            ];
        }

        try {
            $this->model->update($id, $ten);

            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'message' => 'Cập nhật thành công'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * API: Xóa thể loại
     */
    public function deleteApi($data) {
        $id = intval($data['id'] ?? 0);

        if ($id <= 0) {
            return [
                'status' => 400,
                'body' => [
                    'success' => false,
                    'message' => 'ID không hợp lệ'
                ]
            ];
        }

        try {
            // ⚠️ Xóa liên kết trước (nếu có bảng trung gian)
            $this->model->deleteTruyenTheLoai($id);

            $this->model->delete($id);

            return [
                'status' => 200,
                'body' => [
                    'success' => true,
                    'message' => 'Xóa thành công'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body' => [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            ];
        }
    }
}