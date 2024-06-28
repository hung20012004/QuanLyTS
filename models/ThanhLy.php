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
        $query = "SELECT 
        ".$this->table_name.".hoa_don_id, 
        ".$this->table_name.".ngay_thanh_ly, 
        ".$this->table_name.".tong_gia_tri,
        ".$this->details_table_name.".chi_tiet_id,
        ".$this->details_table_name.".tai_san_id,
        ".$this->details_table_name.".so_luong,
        ".$this->details_table_name.".gia_thanh_ly,
        ".$this->asset_table_name.".tai_san_id,
        ".$this->asset_table_name.".ten_tai_san
        FROM " . $this->table_name . "
                  INNER JOIN " . $this->details_table_name . " ON ".$this->table_name.".hoa_don_id = ".$this->details_table_name.".hoa_don_id
                  INNER JOIN " . $this->asset_table_name . "  ON ".$this->details_table_name.".tai_san_id =".$this->asset_table_name.".tai_san_id
                  WHERE ".$this->table_name.".hoa_don_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
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
}
?>