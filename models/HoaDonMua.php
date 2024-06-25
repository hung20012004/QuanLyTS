<?php
// models/HoaDon.php

class HoaDonMua {
    private $conn;
    private $table_name = "hoa_don_mua";

    public $hoa_don_mua_id;
    public $ngay_mua;
    public $tong_gia_tri;
    public $nha_cung_cap_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE hoa_don_mua_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET ngay_mua=:ngay_mua, tong_gia_tri=:tong_gia_tri, nha_cung_cap_id=:nha_cung_cap_id";

        $stmt = $this->conn->prepare($query);

        $this->ngay_mua = htmlspecialchars(strip_tags($this->ngay_mua));
        $this->tong_gia_tri = htmlspecialchars(strip_tags($this->tong_gia_tri));
        $this->nha_cung_cap_id = htmlspecialchars(strip_tags($this->nha_cung_cap_id));

        $stmt->bindParam(':ngay_mua', $this->ngay_mua);
        $stmt->bindParam(':tong_gia_tri', $this->tong_gia_tri);
        $stmt->bindParam(':nha_cung_cap_id', $this->nha_cung_cap_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ngay_mua=:ngay_mua, tong_gia_tri=:tong_gia_tri, nha_cung_cap_id=:nha_cung_cap_id WHERE hoa_don_mua_id=:hoa_don_mua_id";

        $stmt = $this->conn->prepare($query);

        $this->ngay_mua = htmlspecialchars(strip_tags($this->ngay_mua));
        $this->tong_gia_tri = htmlspecialchars(strip_tags($this->tong_gia_tri));
        $this->nha_cung_cap_id = htmlspecialchars(strip_tags($this->nha_cung_cap_id));
        $this->hoa_don_mua_id = htmlspecialchars(strip_tags($this->hoa_don_mua_id));

        $stmt->bindParam(':ngay_mua', $this->ngay_mua);
        $stmt->bindParam(':tong_gia_tri', $this->tong_gia_tri);
        $stmt->bindParam(':nha_cung_cap_id', $this->nha_cung_cap_id);
        $stmt->bindParam(':hoa_don_mua_id', $this->hoa_don_mua_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE hoa_don_mua_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
