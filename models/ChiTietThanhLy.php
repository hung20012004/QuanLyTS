<?php
// models/ChiTietHoaDonMua.php

class ChiTietThanhLy {
    private $conn;
    private $table_name = "chi_tiet_hoa_don_thanh_ly";

    public $chi_tiet_id;
    public $hoa_don_id;
    public $tai_san_id;
    public $so_luong;
    public $gia_thanh_ly;
    public $chi_tiet_vi_tri;

    public function __construct($db) {
        $this->conn = $db;
    }

      public function getById($chi_tiet_id) {
        $stmt = $this->conn->prepare("SELECT * FROM chi_tiet_hoa_don_thanh_ly WHERE chi_tiet_id = ?");
        $stmt->execute([$chi_tiet_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {

        $query = "INSERT INTO  chi_tiet_hoa_don_thanh_ly 
                  SET hoa_don_id=:hoa_don_id, tai_san_id=:tai_san_id, so_luong=:so_luong, gia_thanh_ly=:don_gia";

        $stmt = $this->conn->prepare($query);

        $this->hoa_don_id = htmlspecialchars(strip_tags($this->hoa_don_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->gia_thanh_ly = htmlspecialchars(strip_tags($this->gia_thanh_ly));

        $stmt->bindParam(':hoa_don_id', $this->hoa_don_id);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':don_gia', $this->gia_thanh_ly);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function create_for_update($id, $vtct)
    {
        // $ngay_mua_str = strval($ngay_mua);
          $query = "SELECT *
        FROM  hoa_don_thanh_ly hdtl
                        JOIN chi_tiet_hoa_don_thanh_ly cthdtl ON cthdtl.hoa_don_id = hdtl.hoa_don_id
                        JOIN vi_tri_chi_tiet vtct ON vtct.vi_tri_chi_tiet_id= cthdtl.vi_tri_chi_tiet_id
                        JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id = cthd.chi_tiet_id
                        JOIN hoa_don_mua hdm ON hdm.hoa_don_id = cthd.hoa_don_id
                        JOIN tai_san ts ON ts.tai_san_id = cthd.tai_san_id
        WHERE hdtl.hoa_don_id = ? AND vtct.vi_tri_id = 1 AND cthdtl.vi_tri_chi_tiet_id = ? ";
        $stmt_check = $this->conn->prepare($query);
        $stmt_check->execute([$id, $vtct]);

        if($stmt_check->rowCount() > 0) {
        $query = "INSERT INTO  chi_tiet_hoa_don_thanh_ly 
                  SET hoa_don_id=:hoa_don_id, tai_san_id=:tai_san_id, so_luong=:so_luong, gia_thanh_ly=:don_gia, vi_tri_chi_tiet_id=:chi_tiet_vi_tri";

        $stmt = $this->conn->prepare($query);

        $this->hoa_don_id = htmlspecialchars(strip_tags($this->hoa_don_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->gia_thanh_ly = htmlspecialchars(strip_tags($this->gia_thanh_ly));
        $this->chi_tiet_vi_tri = htmlspecialchars(strip_tags($this->chi_tiet_vi_tri));


        $stmt->bindParam(':hoa_don_id', $this->hoa_don_id);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':don_gia', $this->gia_thanh_ly);
        $stmt->bindParam(':chi_tiet_vi_tri', $this->chi_tiet_vi_tri);
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
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

    public function update() {
        try{
        $query = "UPDATE " . $this->table_name . " 
                  SET so_luong=:so_luong, gia_thanh_ly=:don_gia, tai_san_id=:tai_san_id, vi_tri_chi_tiet_id =:chi_tiet_vi_tri
                  WHERE chi_tiet_id=:chi_tiet_id";

        $stmt = $this->conn->prepare($query);

        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->gia_thanh_ly = htmlspecialchars(strip_tags($this->gia_thanh_ly));
        $this->chi_tiet_id = htmlspecialchars(strip_tags($this->chi_tiet_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->chi_tiet_vi_tri = htmlspecialchars(strip_tags($this->chi_tiet_vi_tri));
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':don_gia', $this->gia_thanh_ly);
        $stmt->bindParam(':chi_tiet_id', $this->chi_tiet_id);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':chi_tiet_vi_tri', $this->chi_tiet_vi_tri);
        return $stmt->execute();
        }catch(Exception $e){
            $this->create();
        }
    }

    public function getByHoaDonId($hoa_don_id) {
    $query = "SELECT  hdtl.hoa_don_id, hdtl.ngay_thanh_ly, hdtl.tong_gia_tri, cttl.chi_tiet_id, cttl.tai_san_id, cttl.so_luong, cttl.gia_thanh_ly, hdm.ngay_mua, ts.ten_tai_san
        FROM  hoa_don_thanh_ly hdtl
        INNER JOIN chi_tiet_hoa_don_thanh_ly cttl ON cttl.hoa_don_id = hdtl.hoa_don_id
        INNER JOIN  tai_san ts ON cttl.tai_san_id =ts.tai_san_id
        INNER JOIN chi_tiet_hoa_don_mua cthd ON cthd.tai_san_id = ts.tai_san_id 
        INNER JOIN hoa_don_mua hdm ON hdm.hoa_don_id = cthd.hoa_don_id
        INNER JOIN vi_tri_chi_tiet vtct ON vtct.chi_tiet_id  = cthd.chi_tiet_id 
        WHERE hdtl.hoa_don_id = ? AND vtct.vi_tri_id = 1 ";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$hoa_don_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE chi_tiet_id = :hoa_don_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hoa_don_id', $id);
        $stmt->execute();
    }

    public function deleteByHoaDonId($hoa_don_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE hoa_don_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $hoa_don_id);
        return $stmt->execute();
    }
}
?>