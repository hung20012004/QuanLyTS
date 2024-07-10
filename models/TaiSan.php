<?php
// models/TaiSan.php

class TaiSan {
    private $conn;
    private $table_name = "tai_san";

    public $tai_san_id;
    public $ten_tai_san;
    public $mo_ta;
    public $loai_tai_san_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ten_tai_san=:ten_tai_san, mo_ta=:mo_ta, loai_tai_san_id=:loai_tai_san_id";

        $stmt = $this->conn->prepare($query);

        $this->ten_tai_san = htmlspecialchars(strip_tags($this->ten_tai_san));
        $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
        $this->loai_tai_san_id = htmlspecialchars(strip_tags($this->loai_tai_san_id));

        $stmt->bindParam(':ten_tai_san', $this->ten_tai_san);
        $stmt->bindParam(':mo_ta', $this->mo_ta);
        $stmt->bindParam(':loai_tai_san_id', $this->loai_tai_san_id);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function read() {
        $query = "SELECT ts.*, lts.ten_loai_tai_san
                  FROM " . $this->table_name . " ts 
                  INNER JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT ts.*, lts.ten_loai_tai_san 
                  FROM " . $this->table_name . " ts 
                  INNER JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id 
                  WHERE ts.tai_san_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAllInStock(){
        $query = "SELECT ts.*, lts.ten_loai_tai_san, vt.so_luong
                  FROM " . $this->table_name . " ts 
                  JOIN vi_tri_chi_tiet vt ON vt.tai_san_id = ts.tai_san_id 
                  JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id 
                  WHERE vt.vi_tri_id = 1 ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update() {
        try{
            $query = "UPDATE " . $this->table_name . " 
                    SET ten_tai_san=:ten_tai_san, mo_ta=:mo_ta, loai_tai_san_id=:loai_tai_san_id 
                    WHERE tai_san_id=:tai_san_id";

            $stmt = $this->conn->prepare($query);

            $this->ten_tai_san = htmlspecialchars(strip_tags($this->ten_tai_san));
            $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
            $this->loai_tai_san_id = htmlspecialchars(strip_tags($this->loai_tai_san_id));
            $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));

            $stmt->bindParam(':ten_tai_san', $this->ten_tai_san);
            $stmt->bindParam(':mo_ta', $this->mo_ta);
            $stmt->bindParam(':loai_tai_san_id', $this->loai_tai_san_id);
            $stmt->bindParam(':tai_san_id', $this->tai_san_id);

            return $stmt->execute();
        }catch(Exception $e){
            $this->create();
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE tai_san_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function createOrUpdate($data) {
        $this->ten_tai_san = $data['ten_tai_san'];
        $this->mo_ta = $data['mo_ta'] ?? '';
        $this->loai_tai_san_id = $data['loai_tai_san_id'];
        return $this->create();
        // Kiểm tra xem tài sản đã tồn tại chưa
    }

    public function checkExists($ten_tai_san, $loai_tai_san_id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " 
                  WHERE ten_tai_san = :ten_tai_san AND loai_tai_san_id = :loai_tai_san_id";
    
        $stmt = $this->conn->prepare($query);
        
        // Làm sạch và gán giá trị cho các tham số
        $ten_tai_san = htmlspecialchars(strip_tags($ten_tai_san));
        $loai_tai_san_id = htmlspecialchars(strip_tags($loai_tai_san_id));
    
        $stmt->bindParam(':ten_tai_san', $ten_tai_san);
        $stmt->bindParam(':loai_tai_san_id', $loai_tai_san_id);
    
        $stmt->execute();
    
        // Lấy kết quả
        $count = $stmt->fetchColumn();
    
        // Trả về true nếu tài sản đã tồn tại, ngược lại trả về false
        return $count > 0;
    }
    public function getAllTaiSanWithLoaiTaiSan() {
        $query = "SELECT ts.*, lts.ten_loai_tai_san
                  FROM tai_san ts
                  INNER JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLoaiTaiSanIdToZero($id) {
        $sql = "UPDATE " . $this->table_name . " SET loai_tai_san_id = 1 WHERE loai_tai_san_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Get total number of assets
    public function getTotalAssets() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            // Handle PDOException (database errors)
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function getAssetTypeStatistics() {
        try {
            $query = "SELECT lt.ten_loai_tai_san as loai_tai_san, COUNT(ts.tai_san_id) as so_luong 
                      FROM tai_san ts
                      INNER JOIN loai_tai_san lt ON ts.loai_tai_san_id = lt.loai_tai_san_id
                      GROUP BY lt.loai_tai_san_id ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle PDOException (database errors)
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Phương thức để tìm kiếm tài sản
    public function searchTaisan($tenTaiSan, $loaiTaiSan) {
        // Query để lấy dữ liệu từ bảng tài sản với điều kiện tìm kiếm
        $query = "SELECT * FROM tai_san ts 
                  JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id 
                  WHERE ten_tai_san = :tenTaiSan AND ten_loai_tai_san = :loaiTaiSan";
        $stmt = $this->conn->prepare($query);

        // Bind các tham số và thực hiện truy vấn
        $stmt->execute([
            ':tenTaiSan' => '%' . $tenTaiSan . '%',
            ':loaiTaiSan' => '%' . $loaiTaiSan . '%'
        ]);

        // Lấy kết quả từ truy vấn
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
    public function readByLoaiId($loai_id) {
        $query = "SELECT tai_san_id, ten_tai_san FROM " . $this->table_name . " WHERE loai_tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $loai_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function readOne($taiSanId)
    {
        $query = "SELECT * FROM tai_san WHERE tai_san_id = :tai_san_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tai_san_id', $taiSanId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

}
?>
