<?php
// File: controller/ChiTietTheLoaiController.php

class ChiTietTheLoaiController {
    
    private $truyenModel;
    private $theLoaiModel;
    
    public function __construct() {
        require_once __DIR__ . '/../model/TruyenModel.php';
        require_once __DIR__ . '/../model/TheLoaiModel.php';
        
        $this->truyenModel = new TruyenModel();
        $this->theLoaiModel = new TheLoaiModel();
    }
    
    // Hiển thị danh sách truyện theo thể loại
    public function index() {
        // Lấy id thể loại từ URL
        $id_theloai = $_GET['id'] ?? null;
        
        // Nếu không có id, chuyển về trang chủ
        if (!$id_theloai) {
            header('Location: /web_doc_truyen/frontend/public/index.php');
            exit();
        }
        
        // Lấy thông tin thể loại
        $theLoai = $this->theLoaiModel->getById($id_theloai);
        
        // Nếu không tìm thấy thể loại, chuyển về trang chủ
        if (!$theLoai) {
            $_SESSION['error'] = 'Không tìm thấy thể loại này!';
            header('Location: /web_doc_truyen/frontend/public/index.php');
            exit();
        }
        
        // Lấy danh sách truyện theo thể loại
        $truyens = $this->truyenModel->getTruyenByTheLoai($id_theloai);
        
        // Gán tên thể loại để hiển thị
        $tenTheLoai = $theLoai['ten_theloai'];
        
        // Hiển thị view
        require __DIR__ . '/../view/theloai/chitiettheloai.php';
    }
}
?>