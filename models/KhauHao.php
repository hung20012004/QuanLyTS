<?php
// models/TaiSan.php

class KhauHao {
    private $conn;
    private $table_name = "khau_hao";

    public $tai_san_id;
    public $ngay_khau_hao;
    public $so_tien;
    public $khau_hao_id;
    public $loai_tai_san_id;
    public function __construct($db) {
        $this->conn = $db;
    }
    public function readAll() {
        $query = "SELECT * 
                  FROM " . $this->table_name ."";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

     public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET tai_san_id=:tai_san_id, ngay_khau_hao=:ngay_khau_hao, so_tien=:so_tien";

        $stmt = $this->conn->prepare($query);

        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->ngay_khau_hao = htmlspecialchars(strip_tags($this->ngay_khau_hao));
        $this->so_tien = htmlspecialchars(strip_tags($this->so_tien));

        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':ngay_khau_hao', $this->ngay_khau_hao);
        $stmt->bindParam(':so_tien', $this->so_tien);

        if ($stmt->execute()) {
            return true;
        }

        // Nếu có lỗi, ghi nhật ký lỗi ra màn hình
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function read() {
        $query = "SELECT * 
                  FROM " . $this->table_name . " kh
                  JOIN tai_san ts ON ts.tai_san_id = kh.tai_san_id
                  ORDER BY .ten_tai_san ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT * 
                  FROM " . $this->table_name . " kh
                  JOIN tai_san ts ON ts.tai_san_id = kh.tai_san_id
                  WHERE ts.tai_san_id = ".$id."";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function readKhAll($id) {
        $query = "SELECT * 
                  FROM " . $this->table_name . " kh
                  JOIN tai_san ts ON ts.tai_san_id = kh.tai_san_id
                  WHERE kh.khau_hao_id = ".$id."";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function readtaisan($id) {
       
       $query = "SELECT * 
                  FROM  tai_san
                  WHERE tai_san_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET tai_san_id=:tai_san_id, ngay_khau_hao=:ngay_khau_hao, so_tien=:so_tien
                  WHERE khau_hao_id = ".$this->khau_hao_id."";

        $stmt = $this->conn->prepare($query);

        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->ngay_khau_hao = htmlspecialchars(strip_tags($this->ngay_khau_hao));
        $this->so_tien = htmlspecialchars(strip_tags($this->so_tien));

        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':ngay_khau_hao', $this->ngay_khau_hao);
        $stmt->bindParam(':so_tien', $this->so_tien);

        if ($stmt->execute()) {
            return true;
        }

        // Nếu có lỗi, ghi nhật ký lỗi ra màn hình
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE khau_hao_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function createOrUpdate($data) {
        // $this->ten_tai_san = $data['ten_tai_san'];
        // $this->mo_ta = $data['mo_ta'] ?? '';
        // $this->so_luong = $data['so_luong'];
        // $this->loai_tai_san_id = $data['loai_tai_san_id'];
        // return $this->create();
        // // Kiểm tra xem tài sản đã tồn tại chưa
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
