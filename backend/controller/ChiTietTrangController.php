<?php
require_once __DIR__ . '/../model/ChuongModel.php';
require_once __DIR__ . '/../model/TrangModel.php';
require_once __DIR__ . '/../model/LuuTrangDocModel.php';

class ChiTietTrangController {
    private $chuongModel;
    private $trangModel;
    private $lichSuModel;
    
    public function __construct() {
        $this->chuongModel = new ChuongModel();
        $this->trangModel = new TrangModel();
        $this->lichSuModel = new LuuTrangDocModel();
    }
    
    // HIỂN THỊ DANH SÁCH TRANG CỦA 1 CHƯƠNG
    public function danhSachTrang() {
        $id_chuong = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id_chuong <= 0) {
            header('Location: index.php');
            exit();
        }
        
        // Lấy thông tin chương
        $chuong = $this->chuongModel->getById($id_chuong);
        
        if (!$chuong) {
            header('Location: index.php');
            exit();
        }
        
        // Lấy danh sách trang của chương (dùng method getByChuong)
        $danhSachTrang = $this->trangModel->getByChuong($id_chuong);
        
        // Load view danh sách trang
        require_once __DIR__ . '/../view/trang/danhsach.php';
    }
    
    // HIỂN THỊ CHI TIẾT 1 TRANG
    public function docTrang() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }
        
        // Lấy thông tin trang
        $trang = $this->trangModel->getById($id);
        
        if (!$trang) {
            header('Location: index.php');
            exit();
        }
        
        // Lấy thông tin chương
        $chuong = $this->chuongModel->getById($trang['id_chuong']);
        
        if (!$chuong) {
            header('Location: index.php');
            exit();
        }
        
        // ==================== LƯU LỊCH SỬ ĐỌC ====================
        // Nếu user đã đăng nhập, lưu vị trí đọc
        if (isset($_SESSION['user'])) {
            $id_nguoidung = $_SESSION['user']['id'];
            $id_truyen = $chuong['id_truyen']; // Lấy id_truyen từ chương
            $id_chuong = $chuong['id'];
            $so_trang = $trang['so_trang'];
            
            // Lưu vào database
            $this->lichSuModel->luuTrangDoc($id_nguoidung, $id_truyen, $id_chuong, $so_trang);
        }
        // =========================================================
        
        // Lấy tất cả trang của chương này
        $allTrangs = $this->trangModel->getByChuong($trang['id_chuong']);
        
        // Tìm trang trước và trang sau
        $trangTruoc = null;
        $trangSau = null;
        
        foreach ($allTrangs as $index => $t) {
            if ($t['id'] == $trang['id']) {
                // Trang trước
                if ($index > 0) {
                    $trangTruoc = $allTrangs[$index - 1];
                }
                // Trang sau
                if ($index < count($allTrangs) - 1) {
                    $trangSau = $allTrangs[$index + 1];
                }
                break;
            }
        }
        
        // Load view chi tiết trang
        require_once __DIR__ . '/../view/trang/chitiettrang.php';
    }
}
?>