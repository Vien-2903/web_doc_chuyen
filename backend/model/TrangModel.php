<?php
require_once __DIR__ . '/../database/myconnection.php';

class TrangModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    // Lấy danh sách trang theo chương
    public function getByChuong($id_chuong) {
        $sql = "SELECT * FROM trang WHERE id_chuong = ? ORDER BY so_trang ASC";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_chuong);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    // Tìm kiếm trang trong 1 chương
    
    public function searchBySoTrang($id_chuong, $so_trang) {
        $sql = "
            SELECT * FROM trang
            WHERE id_chuong = ?
            AND so_trang = ?
            ORDER BY so_trang ASC
        ";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $id_chuong, $so_trang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }


    // Lấy 1 trang
    public function getById($id) {
        $sql = "SELECT * FROM trang WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // Kiểm tra trùng số trang trong cùng chương
    public function existsSoTrang($id_chuong, $so_trang) {
        $sql = "SELECT id FROM trang WHERE id_chuong = ? AND so_trang = ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $id_chuong, $so_trang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    // Kiểm tra trùng khi sửa
    public function existsSoTrangExceptId($id_chuong, $so_trang, $id) {
        $sql = "SELECT id FROM trang WHERE id_chuong = ? AND so_trang = ? AND id != ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $id_chuong, $so_trang, $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    // Thêm trang
    public function insert($data) {
        $sql = "INSERT INTO trang (id_chuong, so_trang, noi_dung, anh, loai)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "iisss",
            $data['id_chuong'],
            $data['so_trang'],
            $data['noi_dung'],
            $data['anh'],
            $data['loai']
        );
        
        $result = mysqli_stmt_execute($stmt);
        
        // Throw exception nếu thất bại (giống TruyenModel)
        if (!$result) {
            throw new Exception(mysqli_error($this->conn));
        }
        
        return $result;
    }

    // Cập nhật
    public function update($id, $data) {
        $sql = "UPDATE trang SET so_trang=?, noi_dung=?, anh=?, loai=? WHERE id=?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "isssi",
            $data['so_trang'],
            $data['noi_dung'],
            $data['anh'],
            $data['loai'],
            $id
        );
        return mysqli_stmt_execute($stmt);
    }

    // Xóa
    public function delete($id) {
        $sql = "DELETE FROM trang WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
}