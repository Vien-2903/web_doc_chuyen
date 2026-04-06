<?php
require_once __DIR__ . '/../database/myconnection.php';

class LuuTrangDocModel {
    private $conn;
    private $table = "luu_trang_doc";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // LƯU/CẬP NHẬT TRANG ĐỌC
    // Nếu đã có record thì update, chưa có thì insert
    public function luuTrangDoc($id_nguoidung, $id_truyen, $id_chuong, $so_trang) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);
        $id_chuong = mysqli_real_escape_string($this->conn, $id_chuong);
        $so_trang = mysqli_real_escape_string($this->conn, $so_trang);

        // Kiểm tra đã có chưa
        $check = "SELECT id_nguoidung FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung' AND id_truyen = '$id_truyen'";
        $result = mysqli_query($this->conn, $check);

        if (mysqli_num_rows($result) > 0) {
            // Đã có -> UPDATE
            $query = "UPDATE {$this->table} 
                      SET id_chuong = '$id_chuong', 
                          so_trang = '$so_trang',
                          ngay_cap_nhat = NOW()
                      WHERE id_nguoidung = '$id_nguoidung' 
                      AND id_truyen = '$id_truyen'";
        } else {
            // Chưa có -> INSERT
            $query = "INSERT INTO {$this->table} 
                      (id_nguoidung, id_truyen, id_chuong, so_trang, ngay_cap_nhat)
                      VALUES 
                      ('$id_nguoidung', '$id_truyen', '$id_chuong', '$so_trang', NOW())";
        }

        return mysqli_query($this->conn, $query);
    }

    // LẤY LỊCH SỬ ĐỌC CỦA 1 NGƯỜI DÙNG (TẤT CẢ TRUYỆN)
    public function getLichSuDocByNguoiDung($id_nguoidung) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);

        $query = "SELECT ltd.*, 
                         t.ten_truyen, 
                         t.anh_bia,
                         c.so_chuong, 
                         c.tieu_de as ten_chuong
                  FROM {$this->table} ltd
                  LEFT JOIN truyen t ON ltd.id_truyen = t.id
                  LEFT JOIN chuong c ON ltd.id_chuong = c.id
                  WHERE ltd.id_nguoidung = '$id_nguoidung'
                  ORDER BY ltd.ngay_cap_nhat DESC";

        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    // LẤY VỊ TRÍ ĐỌC CỦA 1 TRUYỆN CỤ THỂ
    public function getViTriDocTruyen($id_nguoidung, $id_truyen) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);

        $query = "SELECT ltd.*, 
                         c.so_chuong, 
                         c.tieu_de as ten_chuong
                  FROM {$this->table} ltd
                  LEFT JOIN chuong c ON ltd.id_chuong = c.id
                  WHERE ltd.id_nguoidung = '$id_nguoidung' 
                  AND ltd.id_truyen = '$id_truyen'";

        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }

        return mysqli_fetch_assoc($result);
    }

    // XÓA LỊCH SỬ ĐỌC 1 TRUYỆN
    public function xoaLichSuDoc($id_nguoidung, $id_truyen) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);

        $query = "DELETE FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung' 
                  AND id_truyen = '$id_truyen'";

        return mysqli_query($this->conn, $query);
    }

    // XÓA TẤT CẢ LỊCH SỬ ĐỌC CỦA NGƯỜI DÙNG
    public function xoaTatCaLichSu($id_nguoidung) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);

        $query = "DELETE FROM {$this->table} WHERE id_nguoidung = '$id_nguoidung'";

        return mysqli_query($this->conn, $query);
    }

    // ĐẾM SỐ TRUYỆN ĐANG ĐỌC
    public function demSoTruyenDangDoc($id_nguoidung) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);

        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung'";

        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    // LẤY DANH SÁCH TRUYỆN ĐỌC GẦN ĐÂY (TOP 10)
    public function getTruyenDocGanDay($id_nguoidung, $limit = 10) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $limit = mysqli_real_escape_string($this->conn, $limit);

        $query = "SELECT ltd.*, 
                         t.ten_truyen, 
                         t.anh_bia,
                         c.so_chuong, 
                         c.tieu_de as ten_chuong
                  FROM {$this->table} ltd
                  LEFT JOIN truyen t ON ltd.id_truyen = t.id
                  LEFT JOIN chuong c ON ltd.id_chuong = c.id
                  WHERE ltd.id_nguoidung = '$id_nguoidung'
                  ORDER BY ltd.ngay_cap_nhat DESC
                  LIMIT $limit";

        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->conn));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    // KIỂM TRA NGƯỜI DÙNG ĐÃ ĐỌC TRUYỆN NÀY CHƯA
    public function daDocTruyen($id_nguoidung, $id_truyen) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);

        $query = "SELECT id_nguoidung FROM {$this->table} 
                  WHERE id_nguoidung = '$id_nguoidung' 
                  AND id_truyen = '$id_truyen'";

        $result = mysqli_query($this->conn, $query);

        return mysqli_num_rows($result) > 0;
    }

    // LẤY PHẦN TRĂM ĐỌC (nếu biết tổng số trang)
    public function tinhPhanTramDoc($id_nguoidung, $id_truyen) {
        $id_nguoidung = mysqli_real_escape_string($this->conn, $id_nguoidung);
        $id_truyen = mysqli_real_escape_string($this->conn, $id_truyen);

        // Lấy vị trí đọc hiện tại
        $viTriDoc = $this->getViTriDocTruyen($id_nguoidung, $id_truyen);
        
        if (!$viTriDoc) {
            return 0;
        }

        // Đếm tổng số trang của truyện
        $query = "SELECT COUNT(*) as tong_trang 
                  FROM trang tr
                  LEFT JOIN chuong c ON tr.id_chuong = c.id
                  WHERE c.id_truyen = '$id_truyen'";

        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        $tong_trang = $row['tong_trang'];

        if ($tong_trang == 0) {
            return 0;
        }

        // Đếm số trang đã đọc (tất cả trang trước vị trí hiện tại)
        $id_chuong = $viTriDoc['id_chuong'];
        $so_trang = $viTriDoc['so_trang'];

        $query = "SELECT COUNT(*) as da_doc
                  FROM trang tr
                  LEFT JOIN chuong c ON tr.id_chuong = c.id
                  WHERE c.id_truyen = '$id_truyen'
                  AND (
                      c.so_chuong < (SELECT so_chuong FROM chuong WHERE id = '$id_chuong')
                      OR (
                          c.id = '$id_chuong' 
                          AND tr.so_trang <= '$so_trang'
                      )
                  )";

        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        $da_doc = $row['da_doc'];

        // Tính phần trăm
        $phan_tram = round(($da_doc / $tong_trang) * 100, 1);

        return $phan_tram;
    }
}
?>