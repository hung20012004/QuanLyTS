<?php
// models/TaiSan.php

class TaiSan {
    private $conn;
    private $table_name = "tai_san";

    public $tai_san_id;
    public $ten_tai_san;
    public $mo_ta;
    public $so_luong;
    public $loai_tai_san_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ten_tai_san=:ten_tai_san, mo_ta=:mo_ta, so_luong=:so_luong, loai_tai_san_id=:loai_tai_san_id";

        $stmt = $this->conn->prepare($query);

        $this->ten_tai_san = htmlspecialchars(strip_tags($this->ten_tai_san));
        $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->loai_tai_san_id = htmlspecialchars(strip_tags($this->loai_tai_san_id));

        $stmt->bindParam(':ten_tai_san', $this->ten_tai_san);
        $stmt->bindParam(':mo_ta', $this->mo_ta);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':loai_tai_san_id', $this->loai_tai_san_id);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function read() {
        $query = "SELECT ts.*, lts.ten_loai_tai_san 
                  FROM " . $this->table_name . " ts
                  LEFT JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
                  ORDER BY ts.ten_tai_san ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT ts.*, lts.ten_loai_tai_san 
                  FROM " . $this->table_name . " ts
                  LEFT JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
                  WHERE ts.tai_san_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        try{
            $query = "UPDATE " . $this->table_name . " 
                    SET ten_tai_san=:ten_tai_san, mo_ta=:mo_ta, so_luong=:so_luong, loai_tai_san_id=:loai_tai_san_id 
                    WHERE tai_san_id=:tai_san_id";

            $stmt = $this->conn->prepare($query);

            $this->ten_tai_san = htmlspecialchars(strip_tags($this->ten_tai_san));
            $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
            $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
            $this->loai_tai_san_id = htmlspecialchars(strip_tags($this->loai_tai_san_id));
            $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));

            $stmt->bindParam(':ten_tai_san', $this->ten_tai_san);
            $stmt->bindParam(':mo_ta', $this->mo_ta);
            $stmt->bindParam(':so_luong', $this->so_luong);
            $stmt->bindParam(':loai_tai_san_id', $this->loai_tai_san_id);
            $stmt->bindParam(':tai_san_id', $this->tai_san_id);

            return $stmt->execute();
        }catch(Exception $e){
            $this->create();
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function createOrUpdate($data) {
        $this->ten_tai_san = $data['ten_tai_san'];
        $this->mo_ta = $data['mo_ta'] ?? '';
        $this->so_luong = $data['so_luong'];
        $this->loai_tai_san_id = $data['loai_tai_san_id'];
        return $this->create();
        // Kiểm tra xem tài sản đã tồn tại chưa
    }

    private function readByNameAndType($ten_tai_san, $loai_tai_san_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE ten_tai_san = :ten_tai_san AND loai_tai_san_id = :loai_tai_san_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_tai_san', $ten_tai_san);
        $stmt->bindParam(':loai_tai_san_id', $loai_tai_san_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
