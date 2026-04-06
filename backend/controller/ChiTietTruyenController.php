<?php
require_once __DIR__ . '/../model/TruyenModel.php';
require_once __DIR__ . '/../model/ChuongModel.php';

class ChiTietTruyenController {
    private $truyenModel;
    private $chuongModel;
    
    public function __construct() {
        // Khởi động session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->truyenModel = new TruyenModel();
        $this->chuongModel = new ChuongModel();
    }
    
    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            echo "<script>
                    alert('Vui lòng đăng nhập để xem truyện!');
                    window.location.href = '/web_doc_truyen/frontend/public/index.php?controller=login&action=login';
                  </script>";
            exit();
        }
        
        // Lấy ID truyện từ URL
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            header('Location: /web_doc_truyen/frontend/public/index.php');
            exit();
        }
        
        // Lấy thông tin chi tiết truyện
        $truyen = $this->truyenModel->getTruyenById($id);
        
        if (!$truyen) {
            header('Location: /web_doc_truyen/frontend/public/index.php');
            exit();
        }
        
        
        // Lấy lại thông tin truyện sau khi tăng lượt xem
        $truyen = $this->truyenModel->getTruyenById($id);
        
        // Lấy danh sách chương của truyện (sắp xếp theo số chương)
        $danhSachChuong = $this->chuongModel->getChuongByTruyenId($id);
        
        // Load view
        require_once __DIR__ . '/../view/truyen/chitiet.php';
    }
}
?>