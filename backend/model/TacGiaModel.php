<?php
require_once __DIR__ . '/../database/myconnection.php';

class TacGiaModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy danh sách tác giả
    public function getAll() {
        $conn = $this->db->connect();
        $sql = "SELECT * FROM tacgia ORDER BY id DESC";
        return mysqli_query($conn, $sql);
    }

    // Lấy 1 tác giả theo id
    public function getById($id) {
        $conn = $this->db->connect();
        $sql = "SELECT * FROM tacgia WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // Thêm tác giả
    public function insert($ten_tacgia, $but_danh, $gioi_thieu) {
        $conn = $this->db->connect();
        $sql = "INSERT INTO tacgia (ten_tacgia, but_danh, gioi_thieu)
                VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $ten_tacgia, $but_danh, $gioi_thieu);
        return mysqli_stmt_execute($stmt);
    }

    // Cập nhật tác giả
    public function update($id, $ten_tacgia, $but_danh, $gioi_thieu) {
        $conn = $this->db->connect();
        $sql = "UPDATE tacgia SET
                    ten_tacgia = ?,
                    but_danh = ?,
                    gioi_thieu = ?
                WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $ten_tacgia, $but_danh, $gioi_thieu, $id);
        return mysqli_stmt_execute($stmt);
    }

    // Xóa tác giả
    public function delete($id) {
        $conn = $this->db->connect();
        $sql = "DELETE FROM tacgia WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    // Kiểm tra tên tác giả đã tồn tại chưa (dùng khi thêm mới)
    public function checkTenTacGiaExists($ten_tacgia) {
        $conn = $this->db->connect();
        $sql = "SELECT id FROM tacgia WHERE ten_tacgia = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ten_tacgia);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    // Kiểm tra tên tác giả đã tồn tại chưa (dùng khi cập nhật, loại trừ id hiện tại)
    public function checkTenTacGiaExistsExceptId($ten_tacgia, $id) {
        $conn = $this->db->connect();
        $sql = "SELECT id FROM tacgia WHERE ten_tacgia = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $ten_tacgia, $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }
    public function isUsedInTruyen($id_tacgia) {
        $id_tacgia = mysqli_real_escape_string($this->db->connect(), $id_tacgia);
        
        $query = "SELECT COUNT(*) as total FROM truyen WHERE id_tacgia = '$id_tacgia'";
        $result = mysqli_query($this->db->connect(), $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->db->connect()));
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row['total'] > 0;
    }
    public function countTruyenByTacGia($id_tacgia) {
        $id_tacgia = mysqli_real_escape_string($this->db->connect(), $id_tacgia);
        
        $query = "SELECT COUNT(*) as total FROM truyen WHERE id_tacgia = '$id_tacgia'";
        $result = mysqli_query($this->db->connect(), $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->db->connect()));
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    // Tìm kiếm tác giả theo tên hoặc bút danh
    public function search($keyword) {
        $conn = $this->db->connect();
        $keyword = "%{$keyword}%";
        $sql = "SELECT * FROM tacgia 
                WHERE ten_tacgia LIKE ? OR but_danh LIKE ? 
                ORDER BY id DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $keyword, $keyword);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
}