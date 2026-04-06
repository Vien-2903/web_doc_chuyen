<?php
require_once(__DIR__ . '/../database/myconnection.php');

class NguoiDungModel {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Lấy tất cả người dùng
    public function getAllNguoiDung() {
        $sql = "SELECT * FROM nguoidung ORDER BY id DESC";
        $result = mysqli_query($this->conn, $sql);
        $data = [];
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    // Lấy người dùng theo ID
    public function getNguoiDungById($id) {
        $sql = "SELECT * FROM nguoidung WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
    
    // Thêm người dùng mới
    public function createNguoiDung($ten_dang_nhap, $mat_khau, $email, $vai_tro) {
        $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);
        $sql = "INSERT INTO nguoidung (ten_dang_nhap, mat_khau, email, vai_tro) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $ten_dang_nhap, $mat_khau_hash, $email, $vai_tro);
        return mysqli_stmt_execute($stmt);
    }
    
    // Cập nhật người dùng
    public function updateNguoiDung($id, $email, $vai_tro) {
        $sql = "UPDATE nguoidung SET email = ?, vai_tro = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $email, $vai_tro, $id);
        return $stmt->execute();
    }
    
    // Xóa người dùng
    public function deleteNguoiDung($id) {
        $sql = "DELETE FROM nguoidung WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
    
    // Kiểm tra tên đăng nhập đã tồn tại
    public function checkTenDangNhapExists($ten_dang_nhap, $exclude_id = null) {
        if($exclude_id) {
            $sql = "SELECT id FROM nguoidung WHERE ten_dang_nhap = ? AND id != ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $ten_dang_nhap, $exclude_id);
        } else {
            $sql = "SELECT id FROM nguoidung WHERE ten_dang_nhap = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $ten_dang_nhap);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
    // Đăng nhập
    public function login($ten_dang_nhap, $mat_khau) {
        $conn = $this->conn;
        $sql = "SELECT * FROM nguoidung WHERE ten_dang_nhap = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ten_dang_nhap);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        
        if($user && password_verify($mat_khau, $user['mat_khau'])) {
            return $user;
        }
        return false;
    }
    public function checkEmailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM nguoidung WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }
    // Tìm kiếm người dùng theo tên đăng nhập hoặc email
    public function searchNguoiDung($keyword) {
        $keyword = "%{$keyword}%";
        $sql = "SELECT * FROM nguoidung 
                WHERE ten_dang_nhap LIKE ? OR email LIKE ? 
                ORDER BY id DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $keyword, $keyword);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>