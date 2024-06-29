<?php
class TinhTrang {
    private $conn;
    private $table_name = "tinh_trang"; // Tên bảng trong CSDL (giả sử là 'status')

    public $tinh_trang_id; // Trường ID của tình trạng
    public $schedule_id; // Trường ID của lịch bảo trì
    public $mo_ta_tinh_trang; // Mô tả của tình trạng

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (schedule_id, mo_ta_tinh_trang) VALUES (:schedule_id, :mo_ta_tinh_trang)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':schedule_id', $this->schedule_id);
        $stmt->bindParam(':mo_ta_tinh_trang', $this->mo_ta_tinh_trang);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT tt.*, bt.*, vt.ten_vi_tri FROM " . $this->table_name . " tt
                 JOIN maintenance_schedule bt ON bt.schedule_id = tt.schedule_id
                 JOIN vi_tri vt ON vt.vi_tri_id = bt.vi_tri_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readById($id) {
        $query = "SELECT tt.*, vt.ten_vi_tri, bt.* FROM " . $this->table_name . " tt
                  JOIN maintenance_schedule bt ON bt.schedule_id = tt.schedule_id
                  JOIN vi_tri vt ON vt.vi_tri_id = bt.vi_tri_id 
                  WHERE tt.tinh_trang_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET schedule_id = :schedule_id, mo_ta_tinh_trang = :mo_ta_tinh_trang WHERE tinh_trang_id = :tinh_trang_id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':schedule_id', $this->schedule_id);
        $stmt->bindParam(':mo_ta_tinh_trang', $this->mo_ta_tinh_trang);
        $stmt->bindParam(':tinh_trang_id', $this->tinh_trang_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE tinh_trang_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

}
?>
