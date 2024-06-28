<?php
// models/ViTri.php

class ViTri {
    private $conn;
    private $table_name = "vi_tri";

    public $vi_tri_id;
    public $ten_vi_tri;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả vị trí
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo vị trí mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET ten_vi_tri=:ten_vi_tri";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_vi_tri = htmlspecialchars(strip_tags($this->ten_vi_tri));

        // bind value
        $stmt->bindParam(':ten_vi_tri', $this->ten_vi_tri);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đọc thông tin vị trí theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin vị trí
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ten_vi_tri = :ten_vi_tri WHERE vi_tri_id = :vi_tri_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_vi_tri = htmlspecialchars(strip_tags($this->ten_vi_tri));
        $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));

        // bind values
        $stmt->bindParam(':ten_vi_tri', $this->ten_vi_tri);
        $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa vị trí
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //Kiểm tra đã tồn tại chưa
    public function checkExist($ten_vi_tri) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE ten_vi_tri = :ten_vi_tri";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_vi_tri', $ten_vi_tri);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return true;
        }
        return false;
    }
}
?>
