<?php
// models/ThanhLy.php

class ThanhLy {
    private $conn;
    private $table_name = "hoa_don_thanh_ly";
    private $details_table_name = "chi_tiet_hoa_don_thanh_ly";
    private $asset_table_name = "tai_san";
    public $ngay_thanh_ly;
    public $taisans = [];
    public $tong_tien ;
    public $hoa_don_id;

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

    public function readAllWithDetails() {
        $query = "SELECT * 
                  FROM " . $this->table_name . "
                  INNER JOIN " . $this->details_table_name . " ON ".$this->table_name.".hoa_don_id = ".$this->details_table_name.".hoa_don_id
                  INNER JOIN " . $this->asset_table_name . "  ON ".$this->details_table_name.".tai_san_id =".$this->asset_table_name.".tai_san_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    
    
    public function create() {

    $this->tong_tien = str_replace('.', '', $this->tong_tien);
    $this->tong_tien = floatval($this->tong_tien);
        // Chèn dữ liệu vào bảng thanh lý
        $query = "INSERT INTO " . $this->table_name . " (ngay_thanh_ly, tong_gia_tri) VALUES (:ngay_thanh_ly, :tong_gia_tri)";
        $stmt = $this->conn->prepare($query);

        // Ràng buộc giá trị
        $stmt->bindParam(':ngay_thanh_ly', $this->ngay_thanh_ly);
        $stmt->bindParam(':tong_gia_tri', $this->tong_tien);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            // Lấy ID của hóa đơn vừa chèn
            $this->hoa_don_id = $this->conn->lastInsertId();

            // Chèn dữ liệu vào bảng chi tiết thanh lý
            $query_detail = "INSERT INTO " . $this->details_table_name . " (hoa_don_id, tai_san_id, so_luong, gia_thanh_ly) VALUES (:hoa_don_id, :tai_san_id, :so_luong, :gia_thanh_ly)";
            $stmt_detail = $this->conn->prepare($query_detail);

            foreach ($this->taisans as $taisan) {
                $stmt_detail->bindParam(':hoa_don_id', $this->hoa_don_id);
                $stmt_detail->bindParam(':tai_san_id', $taisan['id']);
                $stmt_detail->bindParam(':so_luong', $taisan['quantity']);
                $stmt_detail->bindParam(':gia_thanh_ly', $taisan['price']);
                $stmt_detail->execute();
            }

            return true;
        } else {
            throw new Exception('Không thể chèn dữ liệu vào bảng thanh lý.');
        }
    }
    
    public function viewcreate() {
        $query = "SELECT * FROM ". $this->asset_table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

   public function update($id) {

    $this->tong_tien = str_replace('.', '', $this->tong_tien);
    $this->tong_tien = floatval($this->tong_tien);
    
    $query = "UPDATE " . $this->table_name . " 
              SET ngay_thanh_ly = :ngay_thanh_ly, tong_gia_tri = :tong_gia_tri 
              WHERE hoa_don_id = ".$id."";

    $stmt = $this->conn->prepare($query);

    // Xử lý dữ liệu đầu vào
    $this->ngay_thanh_ly = htmlspecialchars(strip_tags($this->ngay_thanh_ly));
    $this->tong_tien = htmlspecialchars(strip_tags($this->tong_tien));

    // Gán các giá trị vào các tham số của câu lệnh SQL
    $stmt->bindParam(':ngay_thanh_ly', $this->ngay_thanh_ly);
    $stmt->bindParam(':tong_gia_tri', $this->tong_tien);
    // Thực thi câu lệnh SQL và trả về kết quả
    return $stmt->execute();
}
    public function delete($id) {
       try {
            // Xóa chi tiết hóa đơn
            $chiTietQuery = "SELECT chi_tiet_id FROM chi_tiet_hoa_don_thanh_ly WHERE hoa_don_id = ".$id."";
            $stmt = $this->conn->prepare($chiTietQuery);
            $stmt->execute();
            $chiTietHoaDon = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Xóa chi tiết hóa đơn
            $deleteChiTietQuery = "DELETE FROM chi_tiet_hoa_don_thanh_ly WHERE hoa_don_id = ".$id."";
            $stmtDeleteChiTiet = $this->conn->prepare($deleteChiTietQuery);
            $stmtDeleteChiTiet->execute();

            // Xóa hóa đơn
            $deleteHoaDonQuery = "DELETE FROM " . $this->table_name . " WHERE hoa_don_id = ".$id."";
            $stmtDeleteHoaDon = $this->conn->prepare($deleteHoaDonQuery);
            $stmtDeleteHoaDon->execute();

            // $this->conn->commit();
            return true;
        } catch (Exception $e) {
            return false;
        }
    
    }

     private function updateAssetQuantity($assetId, $quantityChange) {
        $query = "UPDATE " . $this->asset_table_name . " SET so_luong = so_luong + :quantity_change WHERE tai_san_id = :tai_san_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity_change', $quantityChange);
        $stmt->bindParam(':tai_san_id', $assetId);
        $stmt->execute();
    }

    private function deleteDetails($id) {
        $query = "DELETE FROM " . $this->details_table_name . " WHERE hoa_don_id = :hoa_don_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hoa_don_id', $id);
        $stmt->execute();
    }

    public function viewedit($id)
    {
        $query = "SELECT DISTINCT hdtl.hoa_don_id, hdtl.ngay_thanh_ly, 
        hdtl.tong_gia_tri,
        cttl.chi_tiet_id,
        cttl.tai_san_id,
        cttl.so_luong,
        cttl.gia_thanh_ly,
        hdm.ngay_mua,
        ts.ten_tai_san
        FROM  hoa_don_thanh_ly hdtl
        INNER JOIN chi_tiet_hoa_don_thanh_ly cttl ON cttl.hoa_don_id = hdtl.hoa_don_id
        INNER JOIN vi_tri_chi_tiet vtct ON vtct.vi_tri_chi_tiet_id  = cttl.vi_tri_chi_tiet_id
        INNER JOIN chi_tiet_hoa_don_mua cthdm ON vtct.chi_tiet_id = cthdm.chi_tiet_id
        INNER JOIN hoa_don_mua hdm ON hdm.hoa_don_id  = cthdm.hoa_don_id
        INNER JOIN tai_san ts ON cttl.tai_san_id  = ts.tai_san_id
        WHERE hdtl.hoa_don_id = ? AND vtct.vi_tri_id = 1 ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt;
    }
    
    public function show($id)
    {
        $query = "SELECT * 
                  FROM " . $this->table_name . "
                  INNER JOIN " . $this->details_table_name . " ON ".$this->table_name.".hoa_don_id = ".$this->details_table_name.".hoa_don_id
                  INNER JOIN " . $this->asset_table_name . "  ON ".$this->details_table_name.".tai_san_id =".$this->asset_table_name.".tai_san_id
                  WHERE ".$this->table_name.".hoa_don_id = ".$id."";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

        public function getTotalRecords() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function search($searchTerm, $page = 1, $recordsPerPage = 10) {
        $start = ($page - 1) * $recordsPerPage;
        $query = "SELECT hm.*, ncc.ten_nha_cung_cap 
                  FROM " . $this->table_name . " hm
                  LEFT JOIN nha_cung_cap ncc ON hm.nha_cung_cap_id = ncc.nha_cung_cap_id
                  WHERE hm.ngay_mua LIKE :search 
                     OR ncc.ten_nha_cung_cap LIKE :search
                  ORDER BY hm.ngay_mua DESC
                  LIMIT :start, :records";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(":search", $searchTerm);
        $stmt->bindParam(":start", $start, PDO::PARAM_INT);
        $stmt->bindParam(":records", $recordsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getTotalInvoices()
{
    $query = "SELECT COUNT(*) as total FROM hoa_don_thanh_ly";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function getTotalValue()
{
    $query = "SELECT SUM(tong_gia_tri) as total FROM hoa_don_thanh_ly";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function getMonthlyInvoices()
{
    $query = "SELECT DATE_FORMAT(ngay_thanh_ly, '%Y-%m') as month, COUNT(*) as count 
              FROM hoa_don_thanh_ly 
              GROUP BY DATE_FORMAT(ngay_thanh_ly, '%Y-%m') 
              ORDER BY month";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getSupplierInvoices()
{
    $query = "SELECT ncc.ten_nha_cung_cap, COUNT(*) as count 
              FROM hoa_don_mua hdm
              JOIN nha_cung_cap ncc ON hdm.nha_cung_cap_id = ncc.nha_cung_cap_id
              GROUP BY hdm.nha_cung_cap_id
              ORDER BY count DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getTopAssets($limit = 5)
{
    $query = "SELECT ts.ten_tai_san, SUM(cthd.so_luong) as total_quantity
              FROM chi_tiet_hoa_don_thanh_ly cthd
              JOIN tai_san ts ON cthd.tai_san_id = ts.tai_san_id
              GROUP BY cthd.tai_san_id
              ORDER BY total_quantity DESC
              LIMIT :limit";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getNgayMuaByTaiSanId($taiSanId) {
        try {
            $query = "SELECT hdm.ngay_mua FROM hoa_don_mua hdm JOIN chi_tiet_hoa_don_mua cthd
            ON hdm.hoa_don_id = cthd.hoa_don_id
            WHERE tai_san_id = :tai_san_id 
            ORDER BY ngay_mua DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tai_san_id', $taiSanId, PDO::PARAM_INT);
            $stmt->execute();

            $dates = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dates[] = $row['ngay_mua'];
            }
            
            return $dates;
        } catch (PDOException $e) {
            // Handle database errors here
            error_log('Error fetching ngay mua: ' . $e->getMessage());
            return [];
        }
    }

}
?>