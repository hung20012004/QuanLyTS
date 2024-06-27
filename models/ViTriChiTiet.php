<?php
// models/ViTriChiTiet.php

class ViTriChiTiet {
    private $conn;
    private $table_name = "vi_tri_chi_tiet";

    public $vi_tri_chi_tiet_id;
    public $vi_tri_id;
    public $tai_san_id;
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
        $query = "INSERT INTO " . $this->table_name . " SET vi_tri_id=:vi_tri_id, tai_san_id=:tai_san_id, so_luong=:so_luong";

        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));

        // bind values
        $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
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
        $query = "SELECT vi_tri_chi_tiet.*, tai_san.ten_tai_san 
                 FROM " . $this->table_name . "
                 JOIN tai_san ON vi_tri_chi_tiet.tai_san_id = tai_san.tai_san_id 
                 WHERE vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $vi_tri_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin chi tiết vị trí
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET tai_san_id = :tai_san_id, so_luong = :so_luong WHERE vi_tri_chi_tiet_id = :vi_tri_chi_tiet_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->vi_tri_chi_tiet_id = htmlspecialchars(strip_tags($this->vi_tri_chi_tiet_id));

        // bind values
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':vi_tri_chi_tiet_id', $this->vi_tri_chi_tiet_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
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
}
?>
