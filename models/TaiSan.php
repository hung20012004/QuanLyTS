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
        $query = "SELECT tai_san.*, loai_tai_san.ten_loai_tai_san 
                  FROM " . $this->table_name . "
                  LEFT JOIN loai_tai_san ON tai_san.loai_tai_san_id = loai_tai_san.loai_tai_san_id
                  ORDER BY tai_san.ten_tai_san ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT ts.*, lts.ten_loai_tai_san 
                  FROM " . $this->table_name . " ts
                  LEFT JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
                  WHERE ts.tai_san_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAllInStock(){
        $query = "SELECT ts.*, lts.ten_loai_tai_san, ct.chi_tiet_id, hd.ngay_mua, vt.so_luong
                  FROM " . $this->table_name . " ts 
                  JOIN chi_tiet_hoa_don_mua ct ON ct.tai_san_id = ts.tai_san_id 
                  JOIN vi_tri_chi_tiet vt ON vt.chi_tiet_id = ct.chi_tiet_id 
                  JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id 
                  JOIN hoa_don_mua hd ON hd.hoa_don_id = ct.hoa_don_id
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
        $query = "DELETE FROM " . $this->table_name . " WHERE tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function createOrUpdate($data) {
        $this->ten_tai_san = $data['ten_tai_san'];
        $this->mo_ta = $data['mo_ta'] ?? '';
        $this->loai_tai_san_id = $data['loai_tai_san_id'];
        return $this->create();
        // Kiểm tra xem tài sản đã tồn tại chưa
    }

    private function readByNameAndType($ten_tai_san, $loai_tai_san_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE ten_tai_san = :ten_tai_san AND loai_tai_san_id = :loai_tai_san_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_tai_san', $ten_tai_san);
        $stmt->bindParam(':loai_tai_san_id', $loai_tai_san_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
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
}
?>
