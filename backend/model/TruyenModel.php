<?php
require_once __DIR__ . '/../database/myconnection.php';

class TruyenModel {
    private $conn;
    private $table = "truyen";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

   
    public function getAll() {
        $query = "SELECT t.*, 
                         tg.ten_tacgia,
                         tg.but_danh
                  FROM {$this->table} t
                  LEFT JOIN tacgia tg ON t.id_tacgia = tg.id
                  ORDER BY t.id DESC";
        
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

    // LẤY 1 TRUYỆN THEO ID
    public function getById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT t.*, 
                         tg.ten_tacgia,
                         tg.but_danh
                  FROM {$this->table} t
                  LEFT JOIN tacgia tg ON t.id_tacgia = tg.id
                  WHERE t.id = '$id'";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
        
        return mysqli_fetch_assoc($result);
    }
    // TÌM KIẾM TRUYỆN
    public function search($keyword) {
        $keyword = mysqli_real_escape_string($this->conn, $keyword);
        $searchTerm = "%{$keyword}%";
    
        $query ="SELECT t.*, 
                        tg.ten_tacgia,
                        tg.but_danh
                FROM {$this->table} t
                LEFT JOIN tacgia tg ON t.id_tacgia = tg.id
                WHERE t.ten_truyen LIKE '$searchTerm' 
                OR tg.ten_tacgia LIKE '$searchTerm'
                OR tg.but_danh LIKE '$searchTerm'
                ORDER BY t.id DESC";
    
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

    // THÊM TRUYỆN MỚI
    public function create($data) {
        $id_tacgia = mysqli_real_escape_string($this->conn, $data['id_tacgia']);
        $ten_truyen = mysqli_real_escape_string($this->conn, $data['ten_truyen']);
        $mo_ta = mysqli_real_escape_string($this->conn, $data['mo_ta']);
        $trang_thai = mysqli_real_escape_string($this->conn, $data['trang_thai']);
        $anh_bia = mysqli_real_escape_string($this->conn, $data['anh_bia']);
       
        
        $query = "INSERT INTO {$this->table} 
                (id_tacgia, ten_truyen, mo_ta, trang_thai, anh_bia) 
                VALUES 
                ('$id_tacgia', '$ten_truyen', '$mo_ta', '$trang_thai', '$anh_bia')";
        
        $result = mysqli_query($this->conn, $query);
        
        // ===== THÊM PHẦN NÀY =====
        if (!$result) {
            // Kiểm tra lỗi duplicate entry
            if (mysqli_errno($this->conn) == 1062) {
                throw new Exception("DUPLICATE_ENTRY");
            }
            throw new Exception(mysqli_error($this->conn));
        }
        // =========================
        
        return $result;
    }
    // CẬP NHẬT TRUYỆN
    public function update($id, $data) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $id_tacgia = mysqli_real_escape_string($this->conn, $data['id_tacgia']);
        $ten_truyen = mysqli_real_escape_string($this->conn, $data['ten_truyen']);
        $mo_ta = mysqli_real_escape_string($this->conn, $data['mo_ta']);
        $trang_thai = mysqli_real_escape_string($this->conn, $data['trang_thai']);
        $anh_bia = mysqli_real_escape_string($this->conn, $data['anh_bia']);
        
        
        $query = "UPDATE {$this->table} 
                  SET id_tacgia = '$id_tacgia',
                      ten_truyen = '$ten_truyen',
                      mo_ta = '$mo_ta',
                      trang_thai = '$trang_thai',
                      anh_bia = '$anh_bia'
                      
                  WHERE id = '$id'";

        $result = mysqli_query($this->conn, $query);
        if (!$result) {
            throw new Exception(mysqli_error($this->conn));
        }

        return $result;
        
    }
    

    // XÓA TRUYỆN
    public function delete($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "DELETE FROM {$this->table} WHERE id = '$id'";
        return mysqli_query($this->conn, $query);
    }

    // LẤY DANH SÁCH TÁC GIẢ (cho dropdown)
    public function getAllTacGia() {
        $query = "SELECT id, 
                         ten_tacgia,
                         but_danh
                  FROM tacgia
                  ORDER BY ten_tacgia";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
        
        $tacgias = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $tacgias[] = $row;
        }
        
        return $tacgias;
    }
    // ================== THỂ LOẠI (truyen_theloai) ==================

    // Lấy tất cả thể loại
    public function getAllTheLoai() {
        $query = "SELECT * FROM theloai ORDER BY ten_theloai";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Lỗi truy vấn thể loại: " . mysqli_error($this->conn));
        }

        $theloais = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $theloais[] = $row;
        }

        return $theloais;
    }

    // Thêm thể loại cho truyện
    public function insertTheLoai($id_truyen, $id_theloai) {
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);
        $id_theloai = mysqli_real_escape_string($this->conn, $id_theloai);

        $query = "INSERT INTO truyen_theloai (id_truyen, id_theloai)
                  VALUES ('$id_truyen', '$id_theloai')";

        return mysqli_query($this->conn, $query);
    }

    // Xóa tất cả thể loại của 1 truyện (dùng khi sửa)
    public function deleteTheLoaiByTruyen($id_truyen) {
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);

        $query = "DELETE FROM truyen_theloai WHERE id_truyen = '$id_truyen'";
        return mysqli_query($this->conn, $query);
    }

    // Lấy danh sách id_theloai của 1 truyện
    public function getTheLoaiByTruyen($id_truyen) {
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);

        $query = "SELECT id_theloai FROM truyen_theloai WHERE id_truyen = '$id_truyen'";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Lỗi truy vấn truyen_theloai: " . mysqli_error($this->conn));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    // Lấy ID truyện vừa thêm
    public function getLastInsertId() {
        return mysqli_insert_id($this->conn);
    }
    // KIỂM TRA TRUYỆN CÓ DỮ LIỆU LIÊN QUAN HAY KHÔNG
    public function hasRelatedData($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        
        // Kiểm tra chương
        $query = "SELECT COUNT(*) as total FROM chuong WHERE id_truyen = '$id'";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row['total'] > 0) {
            return true;
        }
        return false;
    }
   

    // LẤY THÔNG TIN CHI TIẾT TRUYỆN (kèm thể loại)
    public function getTruyenById($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
    
        $query ="SELECT t.*, 
                        tg.ten_tacgia as tac_gia,
                        tg.but_danh,
                        GROUP_CONCAT(tl.ten_theloai SEPARATOR ', ') as the_loai
                FROM {$this->table} t
                LEFT JOIN tacgia tg ON t.id_tacgia = tg.id
                LEFT JOIN truyen_theloai ttl ON t.id = ttl.id_truyen
                LEFT JOIN theloai tl ON ttl.id_theloai = tl.id
                WHERE t.id = '$id'
                GROUP BY t.id";
    
        $result = mysqli_query($this->conn, $query);
    
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }
    
        return mysqli_fetch_assoc($result);
    }
   
    public function getTruyenByTheLoai($id_theloai) {
        $id_theloai = mysqli_real_escape_string($this->conn, $id_theloai);

        $query = "
            SELECT DISTINCT t.*, 
                tg.ten_tacgia,
                tg.but_danh
            FROM truyen t
            LEFT JOIN tacgia tg ON t.id_tacgia = tg.id
            INNER JOIN truyen_theloai ttl ON t.id = ttl.id_truyen
            WHERE ttl.id_theloai = '$id_theloai'
            ORDER BY t.id DESC
        ";

        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die('Lỗi truy vấn truyện theo thể loại: ' . mysqli_error($this->conn));
        }

        $truyens = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $truyens[] = $row;
        }

        return $truyens;
    }
    public function timKiem($keyword) {
        $keyword = mysqli_real_escape_string($this->conn, $keyword);
        $searchTerm = "%{$keyword}%";

        $query = "SELECT t.*, 
                        tg.ten_tacgia,
                        tg.but_danh
                FROM {$this->table} t
                LEFT JOIN tacgia tg ON t.id_tacgia = tg.id
                WHERE t.ten_truyen LIKE '$searchTerm' 
                    OR tg.ten_tacgia LIKE '$searchTerm'
                    OR tg.but_danh LIKE '$searchTerm'
                ORDER BY t.id DESC";

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

}
?>