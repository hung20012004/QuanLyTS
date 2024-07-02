<?php
class PhieuNhap {
    private $conn;
    private $table_name = "phieu_nhap_tai_san";

    public $phieu_nhap_tai_san_id;
    public $ngay_nhap;
    public $user_id;
    public $trang_thai;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT pn.*, u.ten AS user_name 
                  FROM " . $this->table_name . " pn
                  LEFT JOIN users u ON pn.user_id = u.user_id
                  ORDER BY pn.ngay_nhap DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAllPaginated($page = 1, $recordsPerPage = 10) {
        $start = ($page - 1) * $recordsPerPage;
        $query = "SELECT pn.*, u.ten AS user_name 
                  FROM " . $this->table_name . " pn
                  LEFT JOIN users u ON pn.user_id = u.user_id
                  ORDER BY pn.ngay_nhap DESC
                  LIMIT :start, :records";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start", $start, PDO::PARAM_INT);
        $stmt->bindParam(":records", $recordsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT pn.*, u.ten AS user_name 
                  FROM " . $this->table_name . " pn
                  LEFT JOIN users u ON pn.user_id = u.user_id
                  WHERE pn.phieu_nhap_tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ngay_nhap=:ngay_nhap, user_id=:user_id, trang_thai=:trang_thai";

        $stmt = $this->conn->prepare($query);

        $this->ngay_nhap = htmlspecialchars(strip_tags($this->ngay_nhap));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));

        $stmt->bindParam(':ngay_nhap', $this->ngay_nhap);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':trang_thai', $this->trang_thai);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET ngay_nhap=:ngay_nhap, user_id=:user_id, trang_thai=:trang_thai 
                  WHERE phieu_nhap_tai_san_id=:phieu_nhap_tai_san_id";

        $stmt = $this->conn->prepare($query);

        $this->ngay_nhap = htmlspecialchars(strip_tags($this->ngay_nhap));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));
        $this->phieu_nhap_tai_san_id = htmlspecialchars(strip_tags($this->phieu_nhap_tai_san_id));

        $stmt->bindParam(':ngay_nhap', $this->ngay_nhap);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':trang_thai', $this->trang_thai);
        $stmt->bindParam(':phieu_nhap_tai_san_id', $this->phieu_nhap_tai_san_id);

        return $stmt->execute();
    }

    public function delete($id) {
        // Bắt đầu transaction
        $this->conn->beginTransaction();
    
        try {
            // Truy vấn chi tiết hóa đơn
            $chiTietQuery = "SELECT chi_tiet_id FROM chi_tiet_phieu_nhap_tai_san WHERE phieu_nhap_tai_san_id = ?";
            $stmt = $this->conn->prepare($chiTietQuery);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $chiTietHoaDon = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Xóa vị trí chi tiết có vi_tri_id = 1 và chi_tiet_id tương ứng
            $deleteViTriChiTietQuery = "DELETE FROM vi_tri_chi_tiet WHERE vi_tri_id = 1 AND chi_tiet_id = ?";
            $stmtDeleteViTriChiTiet = $this->conn->prepare($deleteViTriChiTietQuery);
    
            foreach ($chiTietHoaDon as $chiTiet) {
                $stmtDeleteViTriChiTiet->bindParam(1, $chiTiet['chi_tiet_id']);
                $stmtDeleteViTriChiTiet->execute();
            }
    
            // Xóa chi tiết hóa đơn
            $deleteChiTietQuery = "DELETE FROM chi_tiet_phieu_nhap_tai_san WHERE phieu_nhap_tai_san_id = ?";
            $stmtDeleteChiTiet = $this->conn->prepare($deleteChiTietQuery);
            $stmtDeleteChiTiet->bindParam(1, $id);
            $stmtDeleteChiTiet->execute();
    
            // Xóa hóa đơn
            $deleteHoaDonQuery = "DELETE FROM " . $this->table_name . " WHERE phieu_nhap_tai_san_id = ?";
            $stmtDeleteHoaDon = $this->conn->prepare($deleteHoaDonQuery);
            $stmtDeleteHoaDon->bindParam(1, $id);
            $stmtDeleteHoaDon->execute();
    
            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction nếu có lỗi
            $this->conn->rollBack();
            return false;
        }
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
        $query = "SELECT pn.*, u.ten AS user_name 
                  FROM " . $this->table_name . " pn
                  LEFT JOIN users u ON pn.user_id = u.user_id
                  WHERE pn.ngay_nhap LIKE :search 
                     OR u.ten LIKE :search
                  ORDER BY pn.ngay_nhap DESC
                  LIMIT :start, :records";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(":search", $searchTerm);
        $stmt->bindParam(":start", $start, PDO::PARAM_INT);
        $stmt->bindParam(":records", $recordsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateReport($startDate, $endDate) {
        $query = "SELECT pn.*, u.ten AS user_name 
                  FROM " . $this->table_name . " pn
                  LEFT JOIN users u ON pn.user_id = u.user_id
                  WHERE pn.ngay_nhap BETWEEN :start_date AND :end_date
                  ORDER BY pn.ngay_nhap ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $startDate);
        $stmt->bindParam(":end_date", $endDate);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalInvoices() {
        $query = "SELECT COUNT(*) as total FROM phieu_nhap_tai_san";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalValue() {
        $query = "SELECT SUM(tong_gia_tri) as total FROM phieu_nhap_tai_san";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>
