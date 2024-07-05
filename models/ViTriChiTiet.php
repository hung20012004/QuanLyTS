<?php
// models/ViTriChiTiet.php

class ViTriChiTiet {
    private $conn;
    private $table_name = "vi_tri_chi_tiet";

    public $vi_tri_chi_tiet_id;
    public $vi_tri_id;
    public $tai_san_id;
    public $so_luong;
    public $trong_kho;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả chi tiết vị trí
    public function read() {
        $query = "SELECT vi_tri_chi_tiet.*, tai_san.ten_tai_san, vi_tri.ten_vi_tri 
                 FROM (( " . $this->table_name . "
                 INNER JOIN chi_tiet_hoa_don_mua ON vi_tri_chi_tiet.tai_san_id = chi_tiet_hoa_don_mua.tai_san_id )
                 INNER JOIN tai_san ON chi_tiet_hoa_don_mua.tai_san_id = tai_san.tai_san_id)
                 INNER JOIN vi_tri ON vi_tri.vi_tri_id = vi_tri_chi_tiet.vi_tri_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readNotKho() {
        $query = "SELECT vi_tri_chi_tiet.*, tai_san.ten_tai_san, vi_tri.ten_vi_tri 
                 FROM (( " . $this->table_name . "
                 INNER JOIN chi_tiet_hoa_don_mua ON vi_tri_chi_tiet.tai_san_id = chi_tiet_hoa_don_mua.tai_san_id )
                 INNER JOIN tai_san ON chi_tiet_hoa_don_mua.tai_san_id = tai_san.tai_san_id)
                 INNER JOIN vi_tri ON vi_tri.vi_tri_id = vi_tri_chi_tiet.vi_tri_id
                 WHERE vi_tri_chi_tiet.vi_tri_id > 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo chi tiết vị trí mới
    public function create() {
        $query = "SELECT so_luong FROM " . $this->table_name . " WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "INSERT INTO " . $this->table_name . " SET vi_tri_id=:vi_tri_id, tai_san_id=:tai_san_id, so_luong=:so_luong";

            $stmt = $this->conn->prepare($query);
            
            // sanitize
            $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));
            $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
            $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));

            // bind values
            $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);
            $stmt->bindParam(':tai_san_id', $this->tai_san_id);
            $stmt->bindParam(':so_luong', $this->so_luong);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }
    
    public function createOrUpdate($tai_san_id, $vi_tri_id, $so_luong) {
        try {
            $this->conn->beginTransaction();

            // Check if the record exists
            $query = "SELECT so_luong FROM " . $this->table_name . " WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tai_san_id', $tai_san_id);
            $stmt->bindParam(':vi_tri_id', $vi_tri_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Record exists, update it
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $new_so_luong = $row['so_luong'] + $so_luong;

                // Ensure the new quantity is not negative
                if ($new_so_luong < 0) {
                    throw new Exception("Insufficient quantity in inventory for the update.");
                }

                $query = "UPDATE " . $this->table_name . " SET so_luong = :so_luong WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':so_luong', $new_so_luong);
                $stmt->bindParam(':tai_san_id', $tai_san_id);
                $stmt->bindParam(':vi_tri_id', $vi_tri_id);
            } else {
                // Record does not exist, create it
                $query = "INSERT INTO " . $this->table_name . " (tai_san_id, vi_tri_id, so_luong) VALUES (:tai_san_id, :vi_tri_id, :so_luong)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':tai_san_id', $tai_san_id);
                $stmt->bindParam(':vi_tri_id', $vi_tri_id);
                $stmt->bindParam(':so_luong', $so_luong);
            }

            if ($stmt->execute()) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    // Đọc thông tin chi tiết vị trí theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE vi_tri_chi_tiet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Đọc thông tin chi tiết vị trí theo ID vị trí
    public function readByViTriId($vi_tri_id) {
        $query = "SELECT vi_tri_chi_tiet.*, tai_san.*, hoa_don_mua.ngay_mua
                 FROM (( " . $this->table_name . "
                 INNER JOIN chi_tiet_hoa_don_mua ON vi_tri_chi_tiet.tai_san_id = chi_tiet_hoa_don_mua.tai_san_id )
                 INNER JOIN tai_san ON chi_tiet_hoa_don_mua.tai_san_id = tai_san.tai_san_id)
                 INNER JOIN hoa_don_mua ON chi_tiet_hoa_don_mua.hoa_don_id = hoa_don_mua.hoa_don_id
                 WHERE vi_tri_chi_tiet.vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $vi_tri_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin chi tiết vị trí
    public function update($tai_san_id, $vi_tri_id, $so_luong) {
        $query = "UPDATE " . $this->table_name . " SET so_luong = :so_luong WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id";
    
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $tai_san_id = htmlspecialchars(strip_tags($tai_san_id));
        $vi_tri_id = htmlspecialchars(strip_tags($vi_tri_id));
        $so_luong = htmlspecialchars(strip_tags($so_luong));
    
        // bind values
        $stmt->bindParam(':tai_san_id', $tai_san_id);
        $stmt->bindParam(':vi_tri_id', $vi_tri_id);
        $stmt->bindParam(':so_luong', $so_luong);
    
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Trong lớp viTriChiTiet hoặc một lớp quản lý tương ứng
    public function updateKho($chiTietID, $soLuongThayDoi) {
        // Assume there is a table or data structure for kho where vi_tri_id = 0
        $sql = "UPDATE ". $this->table_name ." SET so_luong = so_luong + :soLuongThayDoi WHERE tai_san_id = :chiTietID AND vi_tri_id = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':soLuongThayDoi', $soLuongThayDoi, PDO::PARAM_INT);
        $stmt->bindParam(':chiTietID', $chiTietID, PDO::PARAM_INT);
        $stmt->execute();
    }


    // Xóa chi tiết vị trí
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE vi_tri_chi_tiet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getQuantityByChiTietIdAndViTriId($chiTietId, $viTriId)
    {
        $query = "SELECT so_luong FROM " . $this->table_name . " WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tai_san_id', $chiTietId);
        $stmt->bindParam(':vi_tri_id', $viTriId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['so_luong'] : null;
    }
    public function kiemTraKho($tai_san_id, $soLuongCanThem) {
        // Assume there is a table or data structure for kho where vi_tri_id = 0
        $sql = "SELECT so_luong FROM ". $this->table_name ." WHERE tai_san_id = :tai_san_id AND vi_tri_id = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tai_san_id', $tai_san_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $soLuongTrongKho = $row ? $row['so_luong'] : 0;
    
        // Kiểm tra nếu số lượng trong kho đủ để thực hiện thay đổi
        return ($soLuongTrongKho + $soLuongCanThem >= 0); // Nếu cần giảm số lượng, hãy thay đổi điều kiện này phù hợp
    }

    public function checkExist($id){
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->fetchColumn() > 0) {
            return true;
        }
        return false;
    }
    public function readByTaiSanAndViTri($taiSanId, $viTriId)
{
    $query = "SELECT * FROM vi_tri_chi_tiet WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':tai_san_id', $taiSanId, PDO::PARAM_INT);
    $stmt->bindParam(':vi_tri_id', $viTriId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updateQuantity($viTriChiTietId, $newQuantity)
{
    $query = "UPDATE vi_tri_chi_tiet SET so_luong = :so_luong WHERE vi_tri_chi_tiet_id = :vi_tri_chi_tiet_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':so_luong', $newQuantity, PDO::PARAM_INT);
    $stmt->bindParam(':vi_tri_chi_tiet_id', $viTriChiTietId, PDO::PARAM_INT);
    return $stmt->execute();
}

}
?>
