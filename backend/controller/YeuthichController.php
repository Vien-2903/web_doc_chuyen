<?php
require_once __DIR__ . '/../model/YeuthichModel.php';
require_once(__DIR__ . '/../middleware/AuthMiddleware.php');

class YeuthichController {
    private $model;

    public function __construct() {
        AuthMiddleware::checkLogin();
        
        // Kiểm tra chỉ USER mới được dùng tính năng yêu thích
        if ($_SESSION['user']['vai_tro'] === 'admin') {
            $_SESSION['error'] = 'Chức năng yêu thích chỉ dành cho người dùng!';
            header('Location: /web_doc_truyen/frontend/public/index.php');
            exit();
        }
        
        $this->model = new YeuthichModel();
    }

    // Trang danh sách truyện yêu thích của USER
    public function myFavorites() {
        $id_nguoidung = $_SESSION['user']['id'];
        $truyens = $this->model->getLikedTruyenByUser($id_nguoidung);
        require_once __DIR__ . '/../view/yeuthich/my_favorites.php';
    }

    // Toggle yêu thích qua AJAX (dùng trên trang chủ)
    public function toggleAjax() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit();
        }
        
        $id_nguoidung = $_SESSION['user']['id'];
        $id_truyen = $_POST['id_truyen'] ?? 0;
        
        if (!$id_truyen) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin truyện']);
            exit();
        }
        
        try {
            if ($this->model->isLiked($id_nguoidung, $id_truyen)) {
                // Đã yêu thích → Xóa
                if ($this->model->removeLike($id_nguoidung, $id_truyen)) {
                    echo json_encode([
                        'success' => true, 
                        'action' => 'removed',
                        'message' => 'Đã bỏ yêu thích!'
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không thể bỏ yêu thích']);
                }
            } else {
                // Chưa yêu thích → Thêm
                if ($this->model->addLike($id_nguoidung, $id_truyen)) {
                    echo json_encode([
                        'success' => true, 
                        'action' => 'added',
                        'message' => 'Đã thêm vào yêu thích!'
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không thể thêm yêu thích']);
                }
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
        
        exit();
    }

    // Xóa yêu thích (từ trang My Favorites)
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_nguoidung = $_SESSION['user']['id'];
            $id_truyen = $_POST['id_truyen'] ?? 0;
            
            if ($id_truyen && $this->model->removeLike($id_nguoidung, $id_truyen)) {
                header("Location: /web_doc_truyen/frontend/public/index.php?page=yeuthich&action=myFavorites&msg=removed");
            } else {
                header("Location: /web_doc_truyen/frontend/public/index.php?page=yeuthich&action=myFavorites&msg=error");
            }
            exit();
        }
    }
}
?>