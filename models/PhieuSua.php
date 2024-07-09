<?php
class PhieuSua {
    private $conn;
    private $table_name = "phieu_sua";

    public $phieu_sua_id;
    public $ngay_yeu_cau;
    public $ngay_sua_chua;
    public $ngay_hoan_thanh;
    public $mo_ta;
    public $user_yeu_cau_id;
    public $user_sua_chua_id;
    public $vi_tri_id;
    public $trang_thai;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT ps.*, u1.ten AS user_yeu_cau_name, u2.ten AS user_sua_chua_name, vt.*
                  FROM " . $this->table_name . " ps
                  LEFT JOIN users u1 ON ps.user_yeu_cau_id = u1.user_id
                  LEFT JOIN users u2 ON ps.user_sua_chua_id = u2.user_id
                  LEFT JOIN vi_tri vt ON ps.vi_tri_id = vt.vi_tri_id
                  ORDER BY ps.ngay_yeu_cau DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $sql = "SELECT user_id, ten FROM users WHERE role = 'KyThuat' ORDER BY ten";
        return $this->conn->query($sql);
    }

    public function readAllPaginated($page = 1, $recordsPerPage = 10) {
        $start = ($page - 1) * $recordsPerPage;
        $query = "SELECT ps.*, u1.ten AS user_yeu_cau_name, u2.ten AS user_sua_chua_name
                  FROM " . $this->table_name . " ps
                  LEFT JOIN users u1 ON ps.user_yeu_cau_id = u1.user_id
                  LEFT JOIN users u2 ON ps.user_sua_chua_id = u2.user_id
                  ORDER BY ps.ngay_yeu_cau DESC
                  LIMIT :start, :records";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start", $start, PDO::PARAM_INT);
        $stmt->bindParam(":records", $recordsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT ps.*, u1.ten AS user_yeu_cau_name, u2.ten AS user_sua_chua_name, vt.*
                  FROM " . $this->table_name . " ps
                  LEFT JOIN users u1 ON ps.user_yeu_cau_id = u1.user_id
                  LEFT JOIN users u2 ON ps.user_sua_chua_id = u2.user_id
                  LEFT JOIN vi_tri vt ON ps.vi_tri_id = vt.vi_tri_id
                  WHERE ps.phieu_sua_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ngay_yeu_cau=:ngay_yeu_cau, mo_ta=:mo_ta, user_yeu_cau_id=:user_yeu_cau_id, vi_tri_id=:vi_tri_id, trang_thai=:trang_thai";

        $stmt = $this->conn->prepare($query);

        $this->ngay_yeu_cau = htmlspecialchars(strip_tags($this->ngay_yeu_cau));
        $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
        $this->user_yeu_cau_id = htmlspecialchars(strip_tags($this->user_yeu_cau_id));
        $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));
        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));

        $stmt->bindParam(':ngay_yeu_cau', $this->ngay_yeu_cau);
        $stmt->bindParam(':mo_ta', $this->mo_ta);
        $stmt->bindParam(':user_yeu_cau_id', $this->user_yeu_cau_id);
        $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);
        $stmt->bindParam(':trang_thai', $this->trang_thai);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET mo_ta=:mo_ta, vi_tri_id=:vi_tri_id 
                  WHERE phieu_sua_id=:phieu_sua_id";

        $stmt = $this->conn->prepare($query);

        $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
        $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));
        $this->phieu_sua_id = htmlspecialchars(strip_tags($this->phieu_sua_id));

        $stmt->bindParam(':mo_ta', $this->mo_ta);
        $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);
        $stmt->bindParam(':phieu_sua_id', $this->phieu_sua_id);

        return $stmt->execute();
    }

    public function updateFix() {
        $query = "UPDATE " . $this->table_name . " 
                  SET user_sua_chua_id=:user_sua_chua_id, ngay_sua_chua=:ngay_sua_chua, trang_thai=:trang_thai 
                  WHERE phieu_sua_id=:phieu_sua_id";
        $stmt = $this->conn->prepare($query);

        $this->user_sua_chua_id = htmlspecialchars(strip_tags($this->user_sua_chua_id));
        $this->ngay_sua_chua = htmlspecialchars(strip_tags($this->ngay_sua_chua));
        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));
        $this->phieu_sua_id = htmlspecialchars(strip_tags($this->phieu_sua_id));

        $stmt->bindParam(':user_sua_chua_id', $this->user_sua_chua_id);
        $stmt->bindParam(':ngay_sua_chua', $this->ngay_sua_chua);
        $stmt->bindParam(':trang_thai', $this->trang_thai);
        $stmt->bindParam(':phieu_sua_id', $this->phieu_sua_id);

        return $stmt->execute();
    }

    public function updateStatus(){
        $query = "UPDATE " . $this->table_name . " 
                  SET trang_thai=:trang_thai 
                  WHERE phieu_sua_id=:phieu_sua_id";
                  $stmt = $this->conn->prepare($query);

        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));
        $this->phieu_sua_id = htmlspecialchars(strip_tags($this->phieu_sua_id));

        $stmt->bindParam(':trang_thai', $this->trang_thai);
        $stmt->bindParam(':phieu_sua_id', $this->phieu_sua_id);

        return $stmt->execute();
    }
    public function updateStatusAndDay(){
        $query = "UPDATE " . $this->table_name . " 
                  SET trang_thai=:trang_thai, ngay_hoan_thanh =:ngay_hoan_thanh 
                  WHERE phieu_sua_id=:phieu_sua_id";
                  $stmt = $this->conn->prepare($query);

        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));
        $this->ngay_hoan_thanh = htmlspecialchars(strip_tags($this->ngay_hoan_thanh));
        $this->phieu_sua_id = htmlspecialchars(strip_tags($this->phieu_sua_id));

        $stmt->bindParam(':trang_thai', $this->trang_thai);
        $stmt->bindParam(':ngay_hoan_thanh', $this->ngay_hoan_thanh);
        $stmt->bindParam(':phieu_sua_id', $this->phieu_sua_id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE phieu_sua_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function getRepairFormsByUser($user_sua_chua_id = null) {
        $sql = "SELECT ps.phieu_sua_id, ps.trang_thai, ps.ngay_yeu_cau, ps.ngay_sua_chua, ps.ngay_hoan_thanh, 
                       u.ten as user_sua_chua_name
                FROM phieu_sua ps
                LEFT JOIN users u ON ps.user_sua_chua_id = u.user_id";
        
        $params = [];
        
        if ($user_sua_chua_id !== null) {
            $sql .= " WHERE ps.user_sua_chua_id = ?";
            $params[] = $user_sua_chua_id;
        }
        
        $sql .= " ORDER BY ps.ngay_yeu_cau DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function statistic() {
        $statistics = [];

        // Tổng số phiếu sửa đã xử lý
        $query = "SELECT COUNT(*) as total FROM phieu_sua WHERE trang_thai = 'Đã hoàn thành'";
        $result = $this->conn->query($query);
        $statistics['totalProcessed'] = $result->fetch_assoc()['total'];

        // Số phiếu sửa chưa xử lý
        $query = "SELECT COUNT(*) as total FROM phieu_sua WHERE trang_thai != 'Đã hoàn thành'";
        $result = $this->conn->query($query);
        $statistics['totalUnprocessed'] = $result->fetch_assoc()['total'];

        // Những phiếu mới hoàn thành gần đây
        $query = "SELECT * FROM phieu_sua WHERE trang_thai = 'Đã hoàn thành' ORDER BY ngay_hoan_thanh DESC LIMIT 5";
        $result = $this->conn->query($query);
        $statistics['recentCompleted'] = $result->fetch_all(MYSQLI_ASSOC);

        // Những vị trí mới gửi phiếu gần đây
        $query = "SELECT * FROM phieu_sua ORDER BY ngay_yeu_cau DESC LIMIT 5";
        $result = $this->conn->query($query);
        $statistics['recentRequests'] = $result->fetch_all(MYSQLI_ASSOC);

        // Vị trí gửi nhiều phiếu nhất
        $query = "SELECT vi_tri_id, COUNT(*) as total FROM phieu_sua GROUP BY vi_tri_id ORDER BY total DESC LIMIT 1";
        $result = $this->conn->query($query);
        $statistics['mostRequests'] = $result->fetch_assoc();

        // Vị trí gửi ít phiếu nhất
        $query = "SELECT vi_tri_id, COUNT(*) as total FROM phieu_sua GROUP BY vi_tri_id ORDER BY total ASC LIMIT 1";
        $result = $this->conn->query($query);
        $statistics['leastRequests'] = $result->fetch_assoc();

        return $statistics;
    }
    
    public function getTotalProcessed() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM phieu_sua WHERE trang_thai = 'DaHoanThanh'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTotalUnprocessed() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM phieu_sua WHERE trang_thai = 'DaNhan'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getRecentCompleted($limit = 5) {
        $stmt = $this->conn->prepare("SELECT phieu_sua_id, ngay_yeu_cau, ngay_sua_chua, ngay_hoan_thanh, mo_ta 
                                    FROM phieu_sua 
                                    WHERE trang_thai = 'DaHoanThanh' 
                                    ORDER BY ngay_hoan_thanh DESC 
                                    LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentRequests($limit = 5) {
        $stmt = $this->conn->prepare("SELECT phieu_sua_id, ngay_yeu_cau, ngay_sua_chua, ngay_hoan_thanh, mo_ta 
                                    FROM phieu_sua 
                                    WHERE trang_thai = 'DaGui'
                                    ORDER BY ngay_yeu_cau DESC 
                                    LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentReceiveds($limit = 5) {
        $stmt = $this->conn->prepare("SELECT phieu_sua_id, ngay_yeu_cau, ngay_sua_chua, ngay_hoan_thanh, trang_thai
                                    FROM phieu_sua 
                                    WHERE trang_thai IN ('DaNhan', 'DaHoanThanh') 
                                    ORDER BY ngay_yeu_cau DESC 
                                    LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMostRequests() {
        $stmt = $this->conn->prepare("SELECT vt.ten_vi_tri, COUNT(*) as total 
                                    FROM phieu_sua ps 
                                    JOIN vi_tri vt ON ps.vi_tri_id = vt.vi_tri_id
                                    GROUP BY ps.vi_tri_id 
                                    ORDER BY total DESC 
                                    LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLeastRequests() {
        $stmt = $this->conn->prepare("SELECT vt.ten_vi_tri, COUNT(*) as total 
                                    FROM phieu_sua ps
                                    JOIN vi_tri vt ON ps.vi_tri_id = vt.vi_tri_id
                                    GROUP BY ps.vi_tri_id 
                                    ORDER BY total ASC 
                                    LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
