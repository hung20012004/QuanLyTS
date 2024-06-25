<?php
class SinhVien {
    private $conn;
    private $table_name = "tbl_diem";

    public $ID;
    public $Ten;
    public $NgaySinh;
    public $MaSV;
    public $DiemChuyenCan;
    public $DiemGiuaKy;
    public $DiemCuoiKy;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET Ten=:Ten, NgaySinh=:NgaySinh, MaSV=:MaSV, DiemChuyenCan=:DiemChuyenCan, DiemGiuaKy=:DiemGiuaKy, DiemCuoiKy=:DiemCuoiKy";
        $stmt = $this->conn->prepare($query);

        $this->Ten=htmlspecialchars(strip_tags($this->Ten));
        $this->NgaySinh=htmlspecialchars(strip_tags($this->NgaySinh));
        $this->MaSV=htmlspecialchars(strip_tags($this->MaSV));
        $this->DiemChuyenCan=htmlspecialchars(strip_tags($this->DiemChuyenCan));
        $this->DiemGiuaKy=htmlspecialchars(strip_tags($this->DiemGiuaKy));
        $this->DiemCuoiKy=htmlspecialchars(strip_tags($this->DiemCuoiKy));

        $stmt->bindParam(":Ten", $this->Ten);
        $stmt->bindParam(":NgaySinh", $this->NgaySinh);
        $stmt->bindParam(":MaSV", $this->MaSV);
        $stmt->bindParam(":DiemChuyenCan", $this->DiemChuyenCan);
        $stmt->bindParam(":DiemGiuaKy", $this->DiemGiuaKy);
        $stmt->bindParam(":DiemCuoiKy", $this->DiemCuoiKy);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET Ten=:Ten, NgaySinh=:NgaySinh, MaSV=:MaSV, DiemChuyenCan=:DiemChuyenCan, DiemGiuaKy=:DiemGiuaKy, DiemCuoiKy=:DiemCuoiKy WHERE ID=:ID";
        $stmt = $this->conn->prepare($query);

        $this->Ten=htmlspecialchars(strip_tags($this->Ten));
        $this->NgaySinh=htmlspecialchars(strip_tags($this->NgaySinh));
        $this->MaSV=htmlspecialchars(strip_tags($this->MaSV));
        $this->DiemChuyenCan=htmlspecialchars(strip_tags($this->DiemChuyenCan));
        $this->DiemGiuaKy=htmlspecialchars(strip_tags($this->DiemGiuaKy));
        $this->DiemCuoiKy=htmlspecialchars(strip_tags($this->DiemCuoiKy));
        $this->ID=htmlspecialchars(strip_tags($this->ID));

        $stmt->bindParam(":Ten", $this->Ten);
        $stmt->bindParam(":NgaySinh", $this->NgaySinh);
        $stmt->bindParam(":MaSV", $this->MaSV);
        $stmt->bindParam(":DiemChuyenCan", $this->DiemChuyenCan);
        $stmt->bindParam(":DiemGiuaKy", $this->DiemGiuaKy);
        $stmt->bindParam(":DiemCuoiKy", $this->DiemCuoiKy);
        $stmt->bindParam(":ID", $this->ID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->ID=htmlspecialchars(strip_tags($this->ID));

        $stmt->bindParam(1, $this->ID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
