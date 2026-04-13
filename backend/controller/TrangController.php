<?php
require_once __DIR__ . '/../model/TrangModel.php';

class TrangController {
    private $model;

    public function __construct() {
        $this->model = new TrangModel();
    }

    // Upload ảnh
    private function uploadImage($file, $subFolder = '') {
        if (empty($file['name'])) return null;

        $target_dir = __DIR__ . "/../uploads/";
        if ($subFolder) {
            $target_dir .= $subFolder . '/';
        }

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $newFileName   = uniqid() . '.' . $imageFileType;
        $target_file   = $target_dir . $newFileName;

        if (getimagesize($file["tmp_name"]) === false) return null;
        if ($file["size"] > 5000000) return null;
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return null;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return "uploads/" . ($subFolder ? $subFolder . "/" : "") . $newFileName;
        }

        return null;
    }

    // Xóa file ảnh
    private function deleteImageFile($imagePath) {
        if (empty($imagePath)) return;
        $fullPath = __DIR__ . '/../' . ltrim($imagePath, '/');
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    // API: Lấy danh sách trang theo chương
    public function getByChuong($id_chuong) {
        $id_chuong = intval($id_chuong);

        if ($id_chuong <= 0) {
            return [
                'status' => 400,
                'body'   => ['success' => false, 'message' => 'ID chương không hợp lệ']
            ];
        }

        try {
            $trangs = $this->model->getByChuong($id_chuong);
            return [
                'status' => 200,
                'body'   => ['success' => true, 'trangs' => $trangs]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'body'   => ['success' => false, 'message' => $e->getMessage()]
            ];
        }
    }

    // API: Lấy 1 trang theo ID
    public function getByIdApi($id) {
        $id = intval($id);

        if ($id <= 0) {
            return [
                'status' => 400,
                'body'   => ['success' => false, 'message' => 'ID không hợp lệ']
            ];
        }

        $trang = $this->model->getById($id);

        if (!$trang) {
            return [
                'status' => 404,
                'body'   => ['success' => false, 'message' => 'Không tìm thấy trang']
            ];
        }

        return [
            'status' => 200,
            'body'   => ['success' => true, 'trang' => $trang]
        ];
    }

    // API: Thêm trang
    public function addApi($data, $files) {
        $id_chuong = intval($data['id_chuong'] ?? 0);
        $so_trang  = intval($data['so_trang']  ?? 0);
        $loai      = trim($data['loai']         ?? 'image');
        $noi_dung  = trim($data['noi_dung']     ?? '');

        if ($id_chuong <= 0) {
            return ['status' => 400, 'body' => ['success' => false, 'message' => 'Vui lòng chọn chương']];
        }

        if ($so_trang <= 0) {
            return ['status' => 400, 'body' => ['success' => false, 'message' => 'Số trang không hợp lệ']];
        }

        if (!in_array($loai, ['text', 'image'])) {
            return ['status' => 400, 'body' => ['success' => false, 'message' => 'Loại không hợp lệ']];
        }

        if ($this->model->existsSoTrang($id_chuong, $so_trang)) {
            return ['status' => 409, 'body' => ['success' => false, 'message' => 'Số trang đã tồn tại trong chương']];
        }

        $anh = '';
        if ($loai === 'image') {
            $anh = $this->uploadImage($files['anh'] ?? [], 'trang');
            if (!$anh) {
                return ['status' => 400, 'body' => ['success' => false, 'message' => 'Upload ảnh thất bại hoặc ảnh không hợp lệ']];
            }
        }

        try {
            $this->model->insert([
                'id_chuong' => $id_chuong,
                'so_trang'  => $so_trang,
                'noi_dung'  => $loai === 'text' ? $noi_dung : '',
                'anh'       => $loai === 'image' ? $anh : '',
                'loai'      => $loai
            ]);

            return ['status' => 201, 'body' => ['success' => true, 'message' => 'Thêm trang thành công']];
        } catch (Exception $e) {
            return ['status' => 500, 'body' => ['success' => false, 'message' => $e->getMessage()]];
        }
    }

    // API: Cập nhật trang
    public function updateApi($data, $files) {
        $id       = intval($data['id']       ?? 0);
        $so_trang = intval($data['so_trang'] ?? 0);
        $loai     = trim($data['loai']       ?? 'image');
        $noi_dung = trim($data['noi_dung']   ?? '');

        if ($id <= 0) {
            return ['status' => 400, 'body' => ['success' => false, 'message' => 'ID không hợp lệ']];
        }

        if ($so_trang <= 0) {
            return ['status' => 400, 'body' => ['success' => false, 'message' => 'Số trang không hợp lệ']];
        }

        $trang = $this->model->getById($id);
        if (!$trang) {
            return ['status' => 404, 'body' => ['success' => false, 'message' => 'Không tìm thấy trang']];
        }

        if ($this->model->existsSoTrangExceptId($trang['id_chuong'], $so_trang, $id)) {
            return ['status' => 409, 'body' => ['success' => false, 'message' => 'Số trang đã tồn tại trong chương']];
        }

        // Giữ ảnh cũ, upload ảnh mới nếu có
        $anh = $trang['anh'];
        $new_anh = $this->uploadImage($files['anh'] ?? [], 'trang');
        if ($new_anh) {
            $this->deleteImageFile($anh);
            $anh  = $new_anh;
            $loai = 'image';
        }

        try {
            $this->model->update($id, [
                'so_trang' => $so_trang,
                'noi_dung' => $noi_dung,
                'anh'      => $anh,
                'loai'     => $loai
            ]);

            return ['status' => 200, 'body' => ['success' => true, 'message' => 'Cập nhật trang thành công']];
        } catch (Exception $e) {
            return ['status' => 500, 'body' => ['success' => false, 'message' => $e->getMessage()]];
        }
    }

    // API: Xóa trang
    public function deleteApi($data) {
        $id = intval($data['id'] ?? 0);

        if ($id <= 0) {
            return ['status' => 400, 'body' => ['success' => false, 'message' => 'ID không hợp lệ']];
        }

        $trang = $this->model->getById($id);
        if (!$trang) {
            return ['status' => 404, 'body' => ['success' => false, 'message' => 'Không tìm thấy trang']];
        }

        try {
            // Xóa file ảnh nếu có
            if (!empty($trang['anh'])) {
                $this->deleteImageFile($trang['anh']);
            }

            $this->model->delete($id);

            return ['status' => 200, 'body' => ['success' => true, 'message' => 'Xóa trang thành công']];
        } catch (Exception $e) {
            return ['status' => 500, 'body' => ['success' => false, 'message' => $e->getMessage()]];
        }
    }
}