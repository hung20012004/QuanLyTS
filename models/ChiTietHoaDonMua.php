<?php
// models/ChiTietHoaDonMua.php

class ChiTietHoaDonMua {
    private $conn;
    private $table_name = "chi_tiet_hoa_don_mua";

    public $chi_tiet_id;
    public $hoa_don_id;
    public $tai_san_id;
    public $so_luong;
    public $don_gia;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET hoa_don_id=:hoa_don_id, tai_san_id=:tai_san_id, so_luong=:so_luong, don_gia=:don_gia";

        $stmt = $this->conn->prepare($query);

        $this->hoa_don_id = htmlspecialchars(strip_tags($this->hoa_don_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->don_gia = htmlspecialchars(strip_tags($this->don_gia));

        $stmt->bindParam(':hoa_don_id', $this->hoa_don_id);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':don_gia', $this->don_gia);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    public function readByHoaDonIdAndTaiSanId($hoa_don_id, $tai_san_id) {
        $query = "SELECT *
                  FROM " . $this->table_name . "
                  WHERE hoa_don_id = :hoa_don_id AND tai_san_id = :tai_san_id
                  LIMIT 1";
    
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':hoa_don_id', $hoa_don_id);
        $stmt->bindParam(':tai_san_id', $tai_san_id);
    
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function readByHoaDonId($hoa_don_id) {
        $query = "SELECT *
                  FROM " . $this->table_name . " ct
                  INNER JOIN tai_san ts ON ct.tai_san_id = ts.tai_san_id
                  INNER JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
                  WHERE ct.hoa_don_id = :hoa_don_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hoa_don_id', $hoa_don_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByChiTietId($chi_tiet_id) {
        $query = "SELECT ct.*, ts.ten_tai_san, hd.ngay_mua
                  FROM " . $this->table_name . " ct
                  INNER JOIN tai_san ts ON ct.tai_san_id = ts.tai_san_id
                  INNER JOIN hoa_don_mua hd ON ct.hoa_don_id = hd.hoa_don_id
                  WHERE ct.chi_tiet_id = :chi_tiet_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':chi_tiet_id', $chi_tiet_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update() {
        try{
        $query = "UPDATE " . $this->table_name . " 
                  SET so_luong=:so_luong, don_gia=:don_gia 
                  WHERE chi_tiet_id=:chi_tiet_id";

        $stmt = $this->conn->prepare($query);

        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->don_gia = htmlspecialchars(strip_tags($this->don_gia));
        $this->chi_tiet_id = htmlspecialchars(strip_tags($this->chi_tiet_id));
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':don_gia', $this->don_gia);
        $stmt->bindParam(':chi_tiet_id', $this->chi_tiet_id);

        return $stmt->execute();
        }catch(Exception $e){
            $this->create();
        }
    }

    public function delete($chi_tiet_id) {
        $query1 = "DELETE FROM vi_tri_chi_tiet WHERE chi_tiet_id = ? AND vi_tri_id = '1'";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(1, $chi_tiet_id);
        $stmt1->execute();

        // Xóa bản ghi trong bảng chi tiết hóa đơn mua
        $query2 = "DELETE FROM " . $this->table_name . " WHERE chi_tiet_id = ?";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(1, $chi_tiet_id);
        $stmt2->execute();
    }
    public function deleteByHoaDonId($hoa_don_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE hoa_don_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $hoa_don_id);
        return $stmt->execute();
    }
}
?>