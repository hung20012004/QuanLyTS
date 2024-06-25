<?php
// models/NhaCungCap.php

class NhaCungCap {
    private $conn;
    private $table_name = "nha_cung_cap";

    public $nha_cung_cap_id;
    public $ten_nha_cung_cap;
    public $trang_thai;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả nhà cung cấp
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo nhà cung cấp mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET ten_nha_cung_cap=:ten_nha_cung_cap, trang_thai=1";
    
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->ten_nha_cung_cap = htmlspecialchars(strip_tags($this->ten_nha_cung_cap));
    
        // bind value
        $stmt->bindParam(':ten_nha_cung_cap', $this->ten_nha_cung_cap);
    
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đọc thông tin nhà cung cấp theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nha_cung_cap_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin nhà cung cấp
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ten_nha_cung_cap = :ten_nha_cung_cap WHERE nha_cung_cap_id = :nha_cung_cap_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_nha_cung_cap = htmlspecialchars(strip_tags($this->ten_nha_cung_cap));
        $this->nha_cung_cap_id = htmlspecialchars(strip_tags($this->nha_cung_cap_id));

        // bind values
        $stmt->bindParam(':ten_nha_cung_cap', $this->ten_nha_cung_cap);
        $stmt->bindParam(':nha_cung_cap_id', $this->nha_cung_cap_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa nhà cung cấp
    public function delete($id) {
        $query = "UPDATE " . $this->table_name . " SET trang_thai = 0 WHERE nha_cung_cap_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
