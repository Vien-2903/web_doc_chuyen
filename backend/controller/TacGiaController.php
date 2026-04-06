<?php
require_once __DIR__ . '/../model/TacGiaModel.php';
require_once(__DIR__ . '/../middleware/AuthMiddleware.php');

class TacGiaController {
    private $model;

    public function __construct() {
        AuthMiddleware::checkLogin();
        AuthMiddleware::checkAdmin();
        $this->model = new TacGiaModel();
    }

    // Danh sách tác giả
    public function index() {
        // Kiểm tra có từ khóa tìm kiếm không
        if(isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
            $keyword = trim($_GET['keyword']);
            $tacgias = $this->model->search($keyword);
        } else {
            $tacgias = $this->model->getAll();
        }
        
        require_once __DIR__ . '/../view/tacgia/tacgia.php';
    }

    // Thêm tác giả
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten_tacgia = trim($_POST['ten_tacgia']);
            $but_danh = trim($_POST['but_danh']);
            $gioi_thieu = trim($_POST['gioi_thieu']);

            $errors = [];

            // Validate
            if(empty($ten_tacgia)) {
                $errors[] = "Tên tác giả không được để trống";
            }

            // Kiểm tra trùng tên tác giả
            if(!empty($ten_tacgia) && $this->model->checkTenTacGiaExists($ten_tacgia)) {
                $errors[] = "Tên tác giả '$ten_tacgia' đã tồn tại trong hệ thống";
            }

            if(empty($errors)) {
                if($this->model->insert($ten_tacgia, $but_danh, $gioi_thieu)) {
                    header("Location: index.php?page=admin&controller=tacgia&success=add");
                    exit;
                } else {
                    $errors[] = "Có lỗi xảy ra khi thêm tác giả";
                }
            }

            // Nếu có lỗi, hiển thị lại form
            require_once __DIR__ . '/../view/tacgia/tacgia_add.php';
            return;
        }

        require_once __DIR__ . '/../view/tacgia/tacgia_add.php';
    }

    // Sửa tác giả
    public function edit() {
        $id = $_GET['id'];
        $tacgia = $this->model->getById($id);

        if(!$tacgia) {
            header("Location: index.php?page=admin&controller=tacgia&msg=not_found");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten_tacgia = trim($_POST['ten_tacgia']);
            $but_danh = trim($_POST['but_danh']);
            $gioi_thieu = trim($_POST['gioi_thieu']);

            $errors = [];

            // Validate
            if(empty($ten_tacgia)) {
                $errors[] = "Tên tác giả không được để trống";
            }

            // Kiểm tra trùng tên tác giả (loại trừ id hiện tại)
            if(!empty($ten_tacgia) && $this->model->checkTenTacGiaExistsExceptId($ten_tacgia, $id)) {
                $errors[] = "Tên tác giả '$ten_tacgia' đã tồn tại trong hệ thống";
            }

            if(empty($errors)) {
                if($this->model->update($id, $ten_tacgia, $but_danh, $gioi_thieu)) {
                    header("Location: index.php?page=admin&controller=tacgia&success=edit");
                    exit;
                } else {
                    $errors[] = "Có lỗi xảy ra khi cập nhật tác giả";
                }
            }

            // Nếu có lỗi, hiển thị lại form
            require_once __DIR__ . '/../view/tacgia/tacgia_edit.php';
            return;
        }

        require_once __DIR__ . '/../view/tacgia/tacgia_edit.php';
    }

    // Xóa tác giả
    public function delete() {
        $id = $_GET['id'];
        
        // ===== KIỂM TRA RÀNG BUỘC TRƯỚC KHI XÓA =====
        if($this->model->isUsedInTruyen($id)) {
            // Đếm số truyện để hiển thị thông báo chi tiết
            $soTruyen = $this->model->countTruyenByTacGia($id);
            header("Location: index.php?controller=tacgia&msg=delete_error&count=$soTruyen");
            exit;
        }
        
        // Nếu không có ràng buộc, tiến hành xóa
        if($this->model->delete($id)) {
            header("Location: index.php?page=admin&controller=tacgia&success=delete");
        } else {
            header("Location: index.php?page=admin&controller=tacgia&msg=delete_failed");
        }
        exit;
    }
}
?>