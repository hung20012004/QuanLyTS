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
        $query = "SELECT * FROM " . $this->table_name . " WHERE trang_thai != 0";
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
    
    //Kiểm tra nhà cung cấp đã tồn tại
    public function checkExist($ten_nha_cung_cap) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE ten_nha_cung_cap = :ten_nha_cung_cap";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_nha_cung_cap', $ten_nha_cung_cap);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return true;
        }
        return false;
    }

    //Kiểm tra trạng thái của nhà cung cấp
    public function isActive($ten_nha_cung_cap) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE ten_nha_cung_cap = :ten_nha_cung_cap AND trang_thai = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_nha_cung_cap', $ten_nha_cung_cap);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return true;
        }
        return false;
    }

    public function updateStatusToActive($tenNhaCungCap) {
        try {
            // Chuẩn bị câu lệnh SQL để cập nhật trạng thái
            $query = "UPDATE nha_cung_cap SET trang_thai = 1 WHERE ten_nha_cung_cap = :ten";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ten', $tenNhaCungCap);
            
            // Thực thi câu lệnh SQL
            $stmt->execute();
            
            // Kiểm tra xem có bản ghi nào được cập nhật hay không
            return ($stmt->rowCount() > 0);
        } catch (PDOException $e) {
            // Xử lý ngoại lệ nếu có lỗi khi thực thi câu lệnh SQL
            // Ví dụ: log lỗi, thông báo lỗi, ...
            return false; // Trả về false nếu có lỗi
        }
    }

    public function updateStatusToInactive($id) {
        $query = "UPDATE " . $this->table_name . " SET trang_thai = 0 WHERE nha_cung_cap_id = :nha_cung_cap_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nha_cung_cap_id', $id);
        return $stmt->execute();
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

    // public function delete($id) {
    //     $query = "DELETE FROM " . $this->table_name . " WHERE nha_cung_cap_id = ?";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(1, $id);
    //     if ($stmt->execute()) {
    //         return true;
    //     }
    //     return false;
    // }
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
