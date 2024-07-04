<?php
// models/ChiTietphieuNhap.php

class ChiTietPhieuNhap
{
    private $conn;
    private $table_name = "chi_tiet_phieu_nhap";

    public $chi_tiet_id;
    public $phieu_nhap_tai_san_id;
    public $tai_san_id;
    public $so_luong;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET phieu_nhap_tai_san_id=:phieu_nhap_tai_san_id, tai_san_id=:tai_san_id, so_luong=:so_luong";

        $stmt = $this->conn->prepare($query);

        $this->phieu_nhap_tai_san_id = htmlspecialchars(strip_tags($this->phieu_nhap_tai_san_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));

        $stmt->bindParam(':phieu_nhap_tai_san_id', $this->phieu_nhap_tai_san_id);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':so_luong', $this->so_luong);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    public function readByPhieuNhapIdAndTaiSanId($phieu_nhap_tai_san_id, $tai_san_id)
    {
        $query = "SELECT *
                  FROM " . $this->table_name . "
                  WHERE phieu_nhap_tai_san_id = :phieu_nhap_tai_san_id AND tai_san_id = :tai_san_id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':phieu_nhap_tai_san_id', $phieu_nhap_tai_san_id);
        $stmt->bindParam(':tai_san_id', $tai_san_id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readByPhieuNhapId($phieu_nhap_tai_san_id)
    {
        $query = "SELECT *
                  FROM " . $this->table_name . " ct
                  INNER JOIN tai_san ts ON ct.tai_san_id = ts.tai_san_id
                  INNER JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
                  WHERE ct.phieu_nhap_tai_san_id = :phieu_nhap_tai_san_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phieu_nhap_tai_san_id', $phieu_nhap_tai_san_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByChiTietId($chi_tiet_id)
    {
        $query = "SELECT ct.*, ts.ten_tai_san, hd.ngay_mua
                  FROM " . $this->table_name . " ct
                  INNER JOIN tai_san ts ON ct.tai_san_id = ts.tai_san_id
                  INNER JOIN phieu_nhap_tai_san_mua hd ON ct.phieu_nhap_tai_san_id = hd.phieu_nhap_tai_san_id
                  WHERE ct.chi_tiet_id = :chi_tiet_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':chi_tiet_id', $chi_tiet_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                  SET so_luong=:so_luong
                  WHERE chi_tiet_id=:chi_tiet_id";

            $stmt = $this->conn->prepare($query);
            $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
            $this->chi_tiet_id = htmlspecialchars(strip_tags($this->chi_tiet_id));
            $stmt->bindParam(':so_luong', $this->so_luong);
            $stmt->bindParam(':chi_tiet_id', $this->chi_tiet_id);

            return $stmt->execute();
        } catch (Exception $e) {
            $this->create();
        }
    }

    public function delete($chi_tiet_id)
    {
        $query1 = "DELETE FROM vi_tri_chi_tiet WHERE chi_tiet_id = ? AND vi_tri_id = '1'";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(1, $chi_tiet_id);
        $stmt1->execute();

        // Xóa bản ghi trong bảng chi tiết hóa đơn mua
        $query2 = "DELETE FROM " . $this->table_name . " WHERE chi_tiet_id = ?";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(1, $chi_tiet_id);
        $stmt2->execute();
    }
    public function deleteByPhieuNhapId($phieu_nhap_tai_san_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE phieu_nhap_tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $phieu_nhap_tai_san_id);
        return $stmt->execute();
    }
    public function getTopAssets($limit = 5)
    {
        $query = "SELECT ts.ten_tai_san, SUM(cthdm.so_luong) as total_quantity
              FROM chi_tiet_phieu_nhap_tai_san_mua cthdm
              JOIN tai_san ts ON cthdm.tai_san_id = ts.tai_san_id
              GROUP BY cthdm.tai_san_id
              ORDER BY total_quantity DESC
              LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getChiTietId($taiSanId, $PhieuNhapId)
    {
        $query = "SELECT chi_tiet_id FROM " . $this->table_name . " WHERE tai_san_id = :tai_san_id AND phieu_nhap_tai_san_id = :phieu_nhap_tai_san_id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':tai_san_id', $taiSanId);
        $stmt->bindParam(':phieu_nhap_tai_san_id', $PhieuNhapId);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['chi_tiet_id'];
        }

        return null;
    }
    public function getQuantityInStock($taiSanId, $PhieuNhapId) {
        // Your logic to fetch quantity in stock based on $taiSanId and $PhieuNhapId
        // Example SQL query (assuming PDO):
        $sql = "SELECT so_luong FROM chi_tiet_phieu_nhap_tai_san_mua WHERE tai_san_id = :tai_san_id AND phieu_nhap_tai_san_id = :phieu_nhap_tai_san_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tai_san_id', $taiSanId, PDO::PARAM_INT);
        $stmt->bindParam(':phieu_nhap_tai_san_id', $PhieuNhapId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['so_luong'];
        } else {
            return 0; // Or handle no results as needed
        }
    }
    
    public function readDetailedByPhieuNhapId($phieuNhapId)
{
    $query = "SELECT ct.*, ts.ten_tai_san, lts.ten_loai_tai_san, lts.loai_tai_san_id 
              FROM chi_tiet_phieu_nhap ct
              JOIN tai_san ts ON ct.tai_san_id = ts.tai_san_id
              JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
              WHERE ct.phieu_nhap_tai_san_id = :phieu_nhap_id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':phieu_nhap_id', $phieuNhapId);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>