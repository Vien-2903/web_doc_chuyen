<?php
require_once __DIR__ . '/../database/myconnection.php';

class YeuthichModel {
    private $conn;
    private $table = "yeuthich";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Kiểm tra người dùng đã yêu thích truyện chưa
    public function isLiked($id_nguoidung, $id_truyen) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);
        
        $query = "SELECT * FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung' 
                  AND id_truyen = '$id_truyen'";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn isLiked: " . mysqli_error($this->conn));
        }
        
        return mysqli_num_rows($result) > 0;
    }

    // Thêm yêu thích
    public function addLike($id_nguoidung, $id_truyen) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);
        
        if ($this->isLiked($id_nguoidung, $id_truyen)) {
            return false;
        }
        
        $query = "INSERT INTO {$this->table} (id_nguoidung, id_truyen) 
                  VALUES ('$id_nguoidung', '$id_truyen')";
        
        return mysqli_query($this->conn, $query);
    }

    // Xóa yêu thích
    public function removeLike($id_nguoidung, $id_truyen) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);
        
        $query = "DELETE FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung' 
                  AND id_truyen = '$id_truyen'";
        
        return mysqli_query($this->conn, $query);
    }

    // Lấy danh sách truyện yêu thích của người dùng (CHI TIẾT)
    public function getLikedTruyenByUser($id_nguoidung) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        
        // BỎ ngay_tao - không cần cột này nữa
        $query = "SELECT t.*, 
                         tg.ten_tacgia,
                         tg.but_danh
                  FROM {$this->table} yt
                  INNER JOIN truyen t ON yt.id_truyen = t.id
                  LEFT JOIN tacgia tg ON t.id_tacgia = tg.id
                  WHERE yt.id_nguoidung = '$id_nguoidung'
                  ORDER BY yt.id_truyen DESC";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn getLikedTruyenByUser: " . mysqli_error($this->conn));
        }
        
        $truyens = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $truyens[] = $row;
        }
        
        return $truyens;
    }

    // Lấy danh sách ID truyện đã yêu thích (dùng cho home page)
    public function getLikedTruyenIdsByUser($id_nguoidung) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        
        $query = "SELECT id_truyen 
                  FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung'";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn getLikedTruyenIdsByUser: " . mysqli_error($this->conn));
        }
        
        $ids = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ids[] = $row['id_truyen'];
        }
        
        return $ids;
    }

    // Đếm số lượt yêu thích của 1 truyện
    public function countLikes($id_truyen) {
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);
        
        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE id_truyen = '$id_truyen'";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn countLikes: " . mysqli_error($this->conn));
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    // Đếm tổng số truyện yêu thích của user
    public function countUserFavorites($id_nguoidung) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        
        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung'";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn countUserFavorites: " . mysqli_error($this->conn));
        }
        
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
}
?>