<?php
require_once(__DIR__ . '/../model/LuuTrangDocModel.php');

class LuuTrangDocController {
    private $model;

    public function __construct() {
        $this->model = new LuuTrangDocModel();
    }

    // HIỂN THỊ TRANG LỊCH SỬ ĐỌC
    public function lichSuDoc() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=login');
            exit;
        }

        $id_nguoidung = $_SESSION['user']['id'];

        // Lấy danh sách lịch sử đọc
        $lichSuDoc = $this->model->getLichSuDocByNguoiDung($id_nguoidung);
        // Load view
        require_once(__DIR__ . '/../view/luutrangdoc/lichsudoc.php');
    }

    // XÓA LỊCH SỬ ĐỌC 1 TRUYỆN
    public function xoaLichSu() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=login');
            exit;
        }

        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=lichsudoc');
            exit;
        }

        $id_nguoidung = $_SESSION['user']['id'];
        $id_truyen = $_POST['id_truyen'] ?? null;

        if (!$id_truyen) {
            $_SESSION['error'] = 'Thiếu ID truyện';
            header('Location: /web_doc_truyen/frontend/public/index.php?page=lichsudoc');
            exit;
        }

        // Xóa lịch sử
        $result = $this->model->xoaLichSuDoc($id_nguoidung, $id_truyen);

        if ($result) {
            $_SESSION['success'] = 'Đã xóa lịch sử đọc truyện';
        } else {
            $_SESSION['error'] = 'Lỗi khi xóa lịch sử đọc';
        }

        header('Location: /web_doc_truyen/frontend/public/index.php?page=lichsudoc');
        exit;
    }

    // XÓA TẤT CẢ LỊCH SỬ ĐỌC
    public function xoaTatCaLichSu() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=login');
            exit;
        }

        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_doc_truyen/frontend/public/index.php?page=lichsudoc');
            exit;
        }

        $id_nguoidung = $_SESSION['user']['id'];

        // Xóa tất cả lịch sử
        $result = $this->model->xoaTatCaLichSu($id_nguoidung);

        if ($result) {
            $_SESSION['success'] = 'Đã xóa toàn bộ lịch sử đọc';
        } else {
            $_SESSION['error'] = 'Lỗi khi xóa lịch sử đọc';
        }

        header('Location: /web_doc_truyen/frontend/public/index.php?page=lichsudoc');
        exit;
    }

    // LƯU VỊ TRÍ ĐỌC (gọi khi user đọc truyện)
    public function luuViTri() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            return false;
        }

        $id_nguoidung = $_SESSION['user']['id'];
        $id_truyen = $_POST['id_truyen'] ?? null;
        $id_chuong = $_POST['id_chuong'] ?? null;
        $id_trang = $_POST['id_trang'] ?? null;

        // Validate
        if (!$id_truyen || !$id_chuong || !$id_trang) {
            return false;
        }

        // Lưu vào database
        return $this->model->luuTrangDoc($id_nguoidung, $id_truyen, $id_chuong, $id_trang);
    }

    // KIỂM TRA VỊ TRÍ ĐỌC (gọi khi vào chi tiết truyện)
    public function kiemTraViTriDoc($id_truyen) {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            return null;
        }

        $id_nguoidung = $_SESSION['user']['id'];

        // Lấy vị trí đọc
        return $this->model->getViTriDocTruyen($id_nguoidung, $id_truyen);
    }

    // LẤY TRUYỆN ĐỌC GẦN ĐÂY (dùng cho trang chủ)
    public function getTruyenDocGanDay($limit = 10) {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            return [];
        }

        $id_nguoidung = $_SESSION['user']['id'];
        return $this->model->getTruyenDocGanDay($id_nguoidung, $limit);
    }

    // ĐẾM SỐ TRUYỆN ĐANG ĐỌC
    public function demSoTruyenDangDoc() {
        if (!isset($_SESSION['user'])) {
            return 0;
        }

        $id_nguoidung = $_SESSION['user']['id'];
        return $this->model->demSoTruyenDangDoc($id_nguoidung);
    }

    // KIỂM TRA ĐÃ ĐỌC TRUYỆN CHƯA
    public function daDocTruyen($id_truyen) {
        if (!isset($_SESSION['user'])) {
            return false;
        }

        $id_nguoidung = $_SESSION['user']['id'];
        return $this->model->daDocTruyen($id_nguoidung, $id_truyen);
    }
}
?>