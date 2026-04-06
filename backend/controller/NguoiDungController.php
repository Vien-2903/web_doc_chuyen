<?php
require_once(__DIR__ . '/../model/NguoiDungModel.php');
require_once(__DIR__ . '/../middleware/AuthMiddleware.php');

class NguoiDungController {
    private $model;

    public function __construct() {
        AuthMiddleware::checkLogin();
        AuthMiddleware::checkAdmin();
        $this->model = new NguoiDungModel();
    }

    // Hiển thị danh sách người dùng
    public function index() {
        // Kiểm tra có từ khóa tìm kiếm không
        if(isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
            $keyword = trim($_GET['keyword']);
            $nguoidung = $this->model->searchNguoiDung($keyword);
        } else {
            $nguoidung = $this->model->getAllNguoiDung();
        }
        
        require_once(__DIR__ . '/../view/users/users.php');
    }
    
    // Hiển thị form thêm mới
    public function create() {
        require_once(__DIR__ . '/../view/users/users_add.php');
    }
    
    // Xử lý thêm mới
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ten_dang_nhap = trim($_POST['ten_dang_nhap']);
            $mat_khau = $_POST['mat_khau'];
            $email = trim($_POST['email']);
            $vai_tro = $_POST['vai_tro'];
            
            $errors = [];
            
            // Validate
            if(empty($ten_dang_nhap)) {
                $errors[] = "Tên đăng nhập không được để trống";
            }
            if(empty($mat_khau)) {
                $errors[] = "Mật khẩu không được để trống";
            } elseif(strlen($mat_khau) < 6) {
                $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
            }
            if(empty($email)) {
                $errors[] = "Email không được để trống";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ";
            }
            
            // Kiểm tra tên đăng nhập đã tồn tại
            if($this->model->checkTenDangNhapExists($ten_dang_nhap)) {
                $errors[] = "Tên đăng nhập đã tồn tại";
            }
            
            // Kiểm tra email đã tồn tại
            if($this->model->checkEmailExists($email)) {
                $errors[] = "Email đã được sử dụng";
            }
            
            if(empty($errors)) {
                if($this->model->createNguoiDung($ten_dang_nhap, $mat_khau, $email, $vai_tro)) {
                    header("Location: /web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&msg=add_success");
                    exit();
                } else {
                    $errors[] = "Có lỗi xảy ra khi thêm người dùng";
                }
            }
            
            // Nếu có lỗi, hiển thị lại form
            require_once(__DIR__ . '/../view/users/users_add.php');
        }
    }
    
    // Hiển thị form sửa
    public function edit() {
        $id = $_GET['id'];
        $nguoidung = $this->model->getNguoiDungById($id);
        if(!$nguoidung) {
            header("Location: index.php?controller=nguoidung&action=index");
            exit();
        }
        require_once(__DIR__ . '/../view/users/users_edit.php');
    }
    
    // Xử lý cập nhật
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $email = trim($_POST['email']);
            $vai_tro = $_POST['vai_tro'];
            
            $errors = [];
        
            // Validate email
            if(empty($email)) {
                $errors[] = "Email không được để trống";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ";
            }
            
            // Validate vai trò
            $valid_roles = ['admin', 'user'];
            if(!in_array($vai_tro, $valid_roles)) {
                $errors[] = "Vai trò không hợp lệ";
            }
            
            if(empty($errors)) {
                // Cập nhật vai trò và email
                if($this->model->updateNguoiDung($id, $email, $vai_tro)) {
                    header("Location: /web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&msg=update_success");
                    exit();
                } else {
                    $errors[] = "Có lỗi xảy ra khi cập nhật thông tin";
                }
            }
            
            $nguoidung = $this->model->getNguoiDungById($id);
            require_once(__DIR__ . '/../view/users/users_edit.php');
        }
    }
    
    // Xóa người dùng
    public function delete() {
        $id = $_GET['id'];
        
        // Kiểm tra xem admin có đang cố xóa chính tài khoản của mình không
        if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $id) {
            header("Location: /web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&msg=cannot_delete_self");
            exit();
        }
        
        if($this->model->deleteNguoiDung($id)) {
            header("Location: /web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&msg=delete_success");
        } else {
            header("Location: /web_doc_truyen/frontend/public/index.php?page=admin&controller=nguoidung&msg=delete_error");
        }
        exit();
    }
}
?>  