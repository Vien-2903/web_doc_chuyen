<?php
require_once __DIR__ . '/../database/myconnection.php';

class ChuongModel {
    public $conn;
    private $table = "chuong";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // LẤY TẤT CẢ CHƯƠNG (kèm tên truyện)
    public function getAll() {
        $query = "SELECT c.*, t.ten_truyen 
                  FROM {$this->table} c
                  LEFT JOIN truyen t ON c.id_truyen = t.id
                  ORDER BY c.id_truyen, c.so_chuong ASC";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
        
        $chuongs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $chuongs[] = $row;
        }
        
        return $chuongs;
    }

    // LẤY 1 CHƯƠNG THEO ID
    public function getById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM {$this->table} WHERE id = '$id'";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
        
        return mysqli_fetch_assoc($result);
    }
    // TÌM KIẾM CHƯƠNG
    public function search($keyword) {
        $keyword = mysqli_real_escape_string($this->conn, $keyword);
        $searchTerm = "%{$keyword}%";
    
        $query ="SELECT c.*, t.ten_truyen 
                FROM {$this->table} c
                LEFT JOIN truyen t ON c.id_truyen = t.id
                WHERE c.tieu_de LIKE '$searchTerm' 
                OR t.ten_truyen LIKE '$searchTerm'
                OR c.so_chuong LIKE '$searchTerm'
                ORDER BY c.id_truyen, c.so_chuong ASC";
    
        $result = mysqli_query($this->conn, $query);
    
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
    
        $chuongs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $chuongs[] = $row;
        }
    
        return $chuongs;
    }

    // THÊM CHƯƠNG MỚI
    public function create($data) {
        $id_truyen = mysqli_real_escape_string($this->conn, $data['id_truyen']);
        $so_chuong = mysqli_real_escape_string($this->conn, $data['so_chuong']);
        $tieu_de = mysqli_real_escape_string($this->conn, $data['tieu_de']);
        
        $query = "INSERT INTO {$this->table} 
                  (id_truyen, so_chuong, tieu_de) 
                  VALUES 
                  ('$id_truyen', '$so_chuong', '$tieu_de')";
        
        $result = mysqli_query($this->conn, $query);
        if (!$result) {
            throw new Exception(mysqli_error($this->conn));
        }
        return $result;
    }

    // CẬP NHẬT CHƯƠNG
    public function update($id, $data) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $id_truyen = mysqli_real_escape_string($this->conn, $data['id_truyen']);
        $so_chuong = mysqli_real_escape_string($this->conn, $data['so_chuong']);
        $tieu_de = mysqli_real_escape_string($this->conn, $data['tieu_de']);
        
        $query = "UPDATE {$this->table} 
                  SET id_truyen = '$id_truyen',
                      so_chuong = '$so_chuong',
                      tieu_de = '$tieu_de'
                  WHERE id = '$id'";
        
        return mysqli_query($this->conn, $query);
    }

    // XÓA CHƯƠNG
    public function delete($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "DELETE FROM {$this->table} WHERE id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // LẤY DANH SÁCH TRUYỆN (cho dropdown)
    public function getAllTruyen() {
        $query = "SELECT id, ten_truyen FROM truyen ORDER BY ten_truyen";
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
        
        $truyens = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $truyens[] = $row;
        }
        
        return $truyens;
    }
    // KIỂM TRA CHƯƠNG CÓ DỮ LIỆU LIÊN QUAN HAY KHÔNG
    public function hasRelatedData($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        
        // Kiểm tra trang
        $query = "SELECT COUNT(*) as total FROM trang WHERE id_chuong = '$id'";
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row['total'] > 0) {
                return true;
            }
        }
        
        // Kiểm tra bình luận
        $query = "SELECT COUNT(*) as total FROM binhluan WHERE id_chuong = '$id'";
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row['total'] > 0) {
                return true;
            }
        }
        
        // Kiểm tra lưu trang đọc
        $query = "SELECT COUNT(*) as total FROM luu_trang_doc WHERE id_chuong = '$id'";
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row['total'] > 0) {
                return true;
            }
        }
        
        return false;
    }
    // LẤY DANH SÁCH CHƯƠNG THEO ID TRUYỆN
    public function getChuongByTruyenId($id_truyen) {
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);
    
        $query="SELECT id, so_chuong, tieu_de as ten_chuong, ngay_dang, id_truyen
                FROM {$this->table} 
                WHERE id_truyen = '$id_truyen'
                ORDER BY so_chuong ASC";
    
        $result = mysqli_query($this->conn, $query);
    
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
    
            $chuongs = [];
    while ($row = mysqli_fetch_assoc($result)) {
            $chuongs[] = $row;
        }
    
        return $chuongs;
    }
}
?>