<?php
// models/ViTriChiTiet.php

class ViTriChiTiet {
    private $conn;
    private $table_name = "vi_tri_chi_tiet";

    public $vi_tri_chi_tiet_id;
    public $vi_tri_id;
    public $chi_tiet_id;
    public $so_luong;
    public $trong_kho;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả chi tiết vị trí
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo chi tiết vị trí mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET vi_tri_id=:vi_tri_id, chi_tiet_id=:chi_tiet_id, so_luong=:so_luong";

        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));
        $this->chi_tiet_id = htmlspecialchars(strip_tags($this->chi_tiet_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));

        // bind values
        $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);
        $stmt->bindParam(':chi_tiet_id', $this->chi_tiet_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đọc thông tin chi tiết vị trí theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE vi_tri_chi_tiet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Đọc thông tin chi tiết vị trí theo ID vị trí
    public function readByViTriId($vi_tri_id) {
        $query = "SELECT vi_tri_chi_tiet.*, tai_san.*, hoa_don_mua.ngay_mua
                 FROM (( " . $this->table_name . "
                 INNER JOIN chi_tiet_hoa_don_mua ON vi_tri_chi_tiet.chi_tiet_id = chi_tiet_hoa_don_mua.chi_tiet_id )
                 INNER JOIN tai_san ON chi_tiet_hoa_don_mua.tai_san_id = tai_san.tai_san_id)
                 INNER JOIN hoa_don_mua ON chi_tiet_hoa_don_mua.hoa_don_id = hoa_don_mua.hoa_don_id
                 WHERE vi_tri_chi_tiet.vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $vi_tri_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin chi tiết vị trí
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET chi_tiet_id = :chi_tiet_id, so_luong = :so_luong WHERE vi_tri_chi_tiet_id = :vi_tri_chi_tiet_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->chi_tiet_id = htmlspecialchars(strip_tags($this->chi_tiet_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->vi_tri_chi_tiet_id = htmlspecialchars(strip_tags($this->vi_tri_chi_tiet_id));

        // bind values
        $stmt->bindParam(':chi_tiet_id', $this->chi_tiet_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':vi_tri_chi_tiet_id', $this->vi_tri_chi_tiet_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Trong lớp viTriChiTiet hoặc một lớp quản lý tương ứng
    public function updateKho($chiTietID, $soLuongThayDoi) {
        // Assume there is a table or data structure for kho where vi_tri_id = 0
        $sql = "UPDATE ". $this->table_name ." SET so_luong = so_luong + :soLuongThayDoi WHERE chi_tiet_id = :chiTietID AND vi_tri_id = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':soLuongThayDoi', $soLuongThayDoi, PDO::PARAM_INT);
        $stmt->bindParam(':chiTietID', $chiTietID, PDO::PARAM_INT);
        $stmt->execute();
    }


    // Xóa chi tiết vị trí
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE vi_tri_chi_tiet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function kiemTraKho($taiSanId, $soLuongCanThem) {
        // Assume there is a table or data structure for kho where vi_tri_id = 0
        $sql = "SELECT so_luong FROM ". $this->table_name ." WHERE chi_tiet_id = :taiSanId AND vi_tri_id = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':taiSanId', $taiSanId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $soLuongTrongKho = $row ? $row['so_luong'] : 0;
    
        // Kiểm tra nếu số lượng trong kho đủ để thực hiện thay đổi
        return ($soLuongTrongKho + $soLuongCanThem >= 0); // Nếu cần giảm số lượng, hãy thay đổi điều kiện này phù hợp
    }
    
    //Cộng tài sản trùng
    public function congSoLuongTrungLap($taiSanId) {
        $sql = "UPDATE vi_tri_chi_tiet SET so_luong = so_luong + (
                SELECT SUM(so_luong) FROM ". $this->table_name ." WHERE vi_tri_id = :viTriId AND chi_tiet_id = :taiSanId
            ) WHERE vi_tri_id = :viTriId AND chi_tiet_id = :taiSanId";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':viTriId', $this->vi_tri_id, PDO::PARAM_INT);
        $stmt->bindParam(':taiSanId', $taiSanId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

}
?>
