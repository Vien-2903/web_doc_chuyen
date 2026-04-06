<?php
require_once __DIR__ . '/../model/BinhluanModel.php';
require_once __DIR__ . '/../model/TruyenModel.php';
require_once __DIR__ . '/../model/ChuongModel.php';
require_once(__DIR__ . '/../middleware/AuthMiddleware.php');

class BinhluanController {
    private $model;
    private $truyenModel;
    private $chuongModel;

    public function __construct() {
        AuthMiddleware::checkLogin();
        $this->model = new BinhluanModel();
        $this->truyenModel = new TruyenModel();
        $this->chuongModel = new ChuongModel();
    }

    // ==================== CHUNG CHO CẢ ADMIN & USER ====================
    
    // Hiển thị danh sách truyện có bình luận
    public function index() {
        $isAdmin = $_SESSION['user']['vai_tro'] === 'admin';
        
        if ($isAdmin) {
            // Admin: Lấy tất cả truyện
            $truyens = $this->model->getTruyenWithComments();
        } else {
            // User: Chỉ lấy truyện mà user đã bình luận
            $id_nguoidung = $_SESSION['user']['id'];
            $truyens = $this->model->getTruyenWithUserComments($id_nguoidung);
        }
        
        require_once __DIR__ . '/../view/binhluan/list_truyen.php';
    }

    // Hiển thị bình luận của 1 truyện (CẢ ADMIN VÀ USER)
    public function viewComments() {
        $id_truyen = $_GET['id_truyen'];
        $truyen = $this->truyenModel->getById($id_truyen);
        $comments = $this->model->getByTruyen($id_truyen);
        
        // Dùng CHUNG 1 file view
        require_once __DIR__ . '/../view/binhluan/view_comments.php';
    }

    // Form thêm bình luận (CẢ ADMIN VÀ USER)
    public function addForm() {
        $id_truyen = $_GET['id_truyen'];
        $truyen = $this->truyenModel->getById($id_truyen);
        $chuongs = $this->getChuongsByTruyen($id_truyen);
        $total_comments = $this->model->countByTruyen($id_truyen);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_chuong = $_POST['id_chuong'];
            $noi_dung = trim($_POST['noi_dung']);
            
            $error = null;
            if (empty($id_chuong)) {
                $error = "Vui lòng chọn chương";
            } elseif (empty($noi_dung)) {
                $error = "Nội dung bình luận không được để trống";
            } elseif (strlen($noi_dung) < 10) {
                $error = "Nội dung bình luận phải có ít nhất 10 ký tự";
            } else {
                $data = [
                    'id_nguoidung' => $_SESSION['user']['id'],
                    'id_chuong' => $id_chuong,
                    'noi_dung' => $noi_dung
                ];
                
                if ($this->model->create($data)) {
                    header("Location: index.php?page=binhluan&action=viewComments&id_truyen={$id_truyen}&success=added");
                    exit();
                } else {
                    $error = "Không thể thêm bình luận. Vui lòng thử lại!";
                }
            }
        }
        
        require_once __DIR__ . '/../view/binhluan/add_form.php';
    }

    // Sửa bình luận (CẢ ADMIN VÀ USER - chỉ sửa của mình)
    public function edit() {
        $id = $_GET['id'];
        $comment = $this->model->getById($id);
        
        // Chỉ được sửa bình luận của chính mình
        if ($comment['id_nguoidung'] != $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Bạn chỉ có thể sửa bình luận của chính mình!';
            header("Location: index.php?page=binhluan&action=index");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $noi_dung = trim($_POST['noi_dung']);
            
            if (empty($noi_dung)) {
                $error = "Nội dung bình luận không được để trống";
            } elseif (strlen($noi_dung) < 10) {
                $error = "Nội dung bình luận phải có ít nhất 10 ký tự";
            } else {
                if ($this->model->update($id, $noi_dung)) {
                    $chuong = $this->model->getChuongInfo($comment['id_chuong']);
                    header("Location: index.php?page=binhluan&action=viewComments&id_truyen=" . $chuong['id_truyen'] . "&success=edit");
                    exit();
                } else {
                    $error = "Cập nhật bình luận thất bại";
                }
            }
        }
        
        require_once __DIR__ . '/../view/binhluan/edit.php';
    }

    // Xóa bình luận (ADMIN xóa tất cả, USER chỉ xóa của mình)
    public function delete() {
        $id = $_GET['id'];
        $comment = $this->model->getById($id);
        $chuong = $this->model->getChuongInfo($comment['id_chuong']);
        
        $isAdmin = $_SESSION['user']['vai_tro'] === 'admin';
        $isOwner = $comment['id_nguoidung'] == $_SESSION['user']['id'];
        
        // ADMIN xóa được tất cả, USER chỉ xóa của mình
        if (!$isAdmin && !$isOwner) {
            $_SESSION['error'] = 'Bạn chỉ có thể xóa bình luận của chính mình!';
            header("Location: index.php?page=binhluan&action=index");
            exit();
        }
        
        if ($this->model->delete($id)) {
            header("Location: index.php?page=binhluan&action=viewComments&id_truyen=" . $chuong['id_truyen'] . "&success=delete");
        } else {
            header("Location: index.php?page=binhluan&action=viewComments&id_truyen=" . $chuong['id_truyen'] . "&error=delete");
        }
        exit();
    }

    // ==================== HELPER METHODS ====================
    
    // Hàm lấy danh sách chương của truyện
    private function getChuongsByTruyen($id_truyen) {
        require_once __DIR__ . '/../database/myconnection.php';
        $database = new Database();
        $conn = $database->connect();
        
        $id_truyen = mysqli_real_escape_string($conn, $id_truyen);
        $query = "SELECT * FROM chuong WHERE id_truyen = '$id_truyen' ORDER BY so_chuong ASC";
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($conn));
        }
        
        $chuongs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $chuongs[] = $row;
        }
        
        return $chuongs;
    }
}
?>