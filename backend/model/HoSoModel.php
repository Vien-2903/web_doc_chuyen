<?php
require_once(__DIR__ . '/../database/myconnection.php');

class HoSoModel {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Lấy tất cả hồ sơ người dùng với thông tin đăng nhập
    public function getAllHoSo() {
        $sql = "SELECT h.*, n.ten_dang_nhap, n.email, n.vai_tro 
                FROM hoso_nguoidung h 
                INNER JOIN nguoidung n ON h.id_nguoidung = n.id 
                ORDER BY h.ngay_tao DESC";
        $result = mysqli_query($this->conn, $sql);
        $data = [];
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    // Lấy hồ sơ theo ID người dùng
    public function getHoSoByUserId($id_nguoidung) {
        $sql = "SELECT h.*, n.ten_dang_nhap, n.email, n.vai_tro 
                FROM hoso_nguoidung h 
                INNER JOIN nguoidung n ON h.id_nguoidung = n.id 
                WHERE h.id_nguoidung = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_nguoidung);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
    
    // Kiểm tra hồ sơ đã tồn tại chưa
    public function checkHoSoExists($id_nguoidung) {
        $sql = "SELECT id_nguoidung FROM hoso_nguoidung WHERE id_nguoidung = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_nguoidung);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
    
    // Thêm hồ sơ người dùng
    public function createHoSo($id_nguoidung, $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi, $avatar = null) {
        $sql = "INSERT INTO hoso_nguoidung (id_nguoidung, ho_ten, ngay_sinh, gioi_tinh, so_dien_thoai, dia_chi, avatar) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssss", $id_nguoidung, $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi, $avatar);
        return mysqli_stmt_execute($stmt);
    }
    
    // Cập nhật hồ sơ người dùng (không cập nhật avatar)
    public function updateHoSo($id_nguoidung, $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi) {
        $sql = "UPDATE hoso_nguoidung 
                SET ho_ten = ?, ngay_sinh = ?, gioi_tinh = ?, so_dien_thoai = ?, dia_chi = ? 
                WHERE id_nguoidung = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $ho_ten, $ngay_sinh, $gioi_tinh, $so_dien_thoai, $dia_chi, $id_nguoidung);
        return mysqli_stmt_execute($stmt);
    }
    
    // Cập nhật avatar
    public function updateAvatar($id_nguoidung, $avatar) {
        $sql = "UPDATE hoso_nguoidung SET avatar = ? WHERE id_nguoidung = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $avatar, $id_nguoidung);
        return mysqli_stmt_execute($stmt);
    }
    
    // Lấy avatar hiện tại
    public function getAvatar($id_nguoidung) {
        $sql = "SELECT avatar FROM hoso_nguoidung WHERE id_nguoidung = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_nguoidung);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['avatar'] : null;
    }
    
    // Xóa hồ sơ người dùng
    public function deleteHoSo($id_nguoidung) {
        $sql = "DELETE FROM hoso_nguoidung WHERE id_nguoidung = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_nguoidung);
        return mysqli_stmt_execute($stmt);
    }
    
    // Tìm kiếm hồ sơ theo tên hoặc số điện thoại
    public function searchHoSo($keyword) {
        $keyword = "%{$keyword}%";
        $sql = "SELECT h.*, n.ten_dang_nhap, n.email, n.vai_tro 
                FROM hoso_nguoidung h 
                INNER JOIN nguoidung n ON h.id_nguoidung = n.id 
                WHERE h.ho_ten LIKE ? OR h.so_dien_thoai LIKE ? OR n.ten_dang_nhap LIKE ? OR n.email LIKE ?
                ORDER BY h.ngay_tao DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $keyword, $keyword, $keyword, $keyword);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    // Lọc hồ sơ theo giới tính
    public function filterByGioiTinh($gioi_tinh) {
        $sql = "SELECT h.*, n.ten_dang_nhap, n.email, n.vai_tro 
                FROM hoso_nguoidung h 
                INNER JOIN nguoidung n ON h.id_nguoidung = n.id 
                WHERE h.gioi_tinh = ?
                ORDER BY h.ngay_tao DESC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $gioi_tinh);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    // Đếm tổng số hồ sơ
    public function countTotalHoSo() {
        $sql = "SELECT COUNT(*) as total FROM hoso_nguoidung";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    // Đếm số hồ sơ theo giới tính
    public function countByGioiTinh($gioi_tinh) {
        $sql = "SELECT COUNT(*) as total FROM hoso_nguoidung WHERE gioi_tinh = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $gioi_tinh);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    // Kiểm tra số điện thoại đã tồn tại
    public function checkSoDienThoaiExists($so_dien_thoai, $exclude_id = null) {
        if($exclude_id) {
            $sql = "SELECT id_nguoidung FROM hoso_nguoidung WHERE so_dien_thoai = ? AND id_nguoidung != ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $so_dien_thoai, $exclude_id);
        } else {
            $sql = "SELECT id_nguoidung FROM hoso_nguoidung WHERE so_dien_thoai = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $so_dien_thoai);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
    
    // Lấy hồ sơ người dùng mới nhất
    public function getLatestHoSo($limit = 10) {
        $sql = "SELECT h.*, n.ten_dang_nhap, n.email, n.vai_tro 
                FROM hoso_nguoidung h 
                INNER JOIN nguoidung n ON h.id_nguoidung = n.id 
                ORDER BY h.ngay_tao DESC 
                LIMIT ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $limit);
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