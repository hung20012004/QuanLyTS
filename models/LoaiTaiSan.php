<?php
// models/LoaiTaiSan.php

class LoaiTaiSan {
    private $conn;
    private $table_name = "loai_tai_san";

    public $loai_tai_san_id;
    public $ten_loai_tai_san;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả loại tài sản
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Đọc tất cả loại tài sản
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo loại tài sản mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET ten_loai_tai_san=:ten_loai_tai_san";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_loai_tai_san = htmlspecialchars(strip_tags($this->ten_loai_tai_san));

        // bind value
        $stmt->bindParam(':ten_loai_tai_san', $this->ten_loai_tai_san);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đọc thông tin loại tài sản theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE loai_tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin loại tài sản
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ten_loai_tai_san = :ten_loai_tai_san WHERE loai_tai_san_id = :loai_tai_san_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_loai_tai_san = htmlspecialchars(strip_tags($this->ten_loai_tai_san));
        $this->loai_tai_san_id = htmlspecialchars(strip_tags($this->loai_tai_san_id));

        // bind values
        $stmt->bindParam(':ten_loai_tai_san', $this->ten_loai_tai_san);
        $stmt->bindParam(':loai_tai_san_id', $this->loai_tai_san_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa loại tài sản
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE loai_tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
