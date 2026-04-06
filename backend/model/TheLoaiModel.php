<?php
require_once __DIR__ . '/../database/myconnection.php';

class TheLoaiModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

  
    public function getAll() {
        $sql = "SELECT * FROM theloai ORDER BY id DESC";
        $result = mysqli_query($this->conn, $sql);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    
    public function searchByTen($keyword) {
        $sql = "SELECT * FROM theloai WHERE ten_theloai LIKE ? ORDER BY ten_theloai ASC";
        $stmt = mysqli_prepare($this->conn, $sql);

        $like = '%' . $keyword . '%';
        mysqli_stmt_bind_param($stmt, "s", $like);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getById($id) {
        $sql = "SELECT * FROM theloai WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

  
    public function insert($ten) {
        $sql = "INSERT INTO theloai (ten_theloai) VALUES (?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $ten);
        return mysqli_stmt_execute($stmt);
    }

   
    public function existsByName($ten) {
        $sql = "SELECT id FROM theloai WHERE ten_theloai = ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $ten);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    
    public function existsByNameExceptId($ten, $id) {
        $sql = "SELECT id FROM theloai WHERE ten_theloai = ? AND id != ? LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "si", $ten, $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

 
    public function update($id, $ten) {
        $sql = "UPDATE theloai SET ten_theloai = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "si", $ten, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function deleteTruyenTheLoai($id_theloai) {
        $sql = "DELETE FROM truyen_theloai WHERE id_theloai = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id_theloai);
        return mysqli_stmt_execute($stmt);
    }

  
    public function delete($id) {
        $sql = "DELETE FROM theloai WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

 
    public function getTruyenByTheLoai($id) {
        $sql = "
        SELECT t.*, tg.ten_tacgia, tg.but_danh
        FROM truyen t
        JOIN truyen_theloai ttl ON t.id = ttl.id_truyen
        JOIN tacgia tg ON t.id_tacgia = tg.id
        WHERE ttl.id_theloai = ?
        ";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}