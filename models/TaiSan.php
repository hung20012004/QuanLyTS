<?php
class TaiSan {
    private $conn;
    private $table_name = "tai_san";

    public $tai_san_id;
    public $ten_tai_san;
    public $mo_ta;
    public $so_luong;
    public $loai_tai_san_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả tài sản
    public function read() {
        $query = "SELECT tai_san.*, loai_tai_san.ten_loai_tai_san 
                  FROM " . $this->table_name . " 
                  JOIN loai_tai_san ON tai_san.loai_tai_san_id = loai_tai_san.loai_tai_san_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo tài sản mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET ten_tai_san=:ten_tai_san, mo_ta=:mo_ta, so_luong=:so_luong, loai_tai_san_id=:loai_tai_san_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_tai_san = htmlspecialchars(strip_tags($this->ten_tai_san));
        $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->loai_tai_san_id = htmlspecialchars(strip_tags($this->loai_tai_san_id));

        // bind values
        $stmt->bindParam(':ten_tai_san', $this->ten_tai_san);
        $stmt->bindParam(':mo_ta', $this->mo_ta);
        $stmt->bindParam(':so_luong', $this->so_luong, PDO::PARAM_INT);
        $stmt->bindParam(':loai_tai_san_id', $this->loai_tai_san_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đọc thông tin tài sản theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin tài sản
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ten_tai_san = :ten_tai_san, mo_ta = :mo_ta, so_luong = :so_luong, loai_tai_san_id = :loai_tai_san_id WHERE tai_san_id = :tai_san_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_tai_san = htmlspecialchars(strip_tags($this->ten_tai_san));
        $this->mo_ta = htmlspecialchars(strip_tags($this->mo_ta));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->loai_tai_san_id = htmlspecialchars(strip_tags($this->loai_tai_san_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));

        // bind values
        $stmt->bindParam(':ten_tai_san', $this->ten_tai_san);
        $stmt->bindParam(':mo_ta', $this->mo_ta);
        $stmt->bindParam(':so_luong', $this->so_luong, PDO::PARAM_INT);
        $stmt->bindParam(':loai_tai_san_id', $this->loai_tai_san_id, PDO::PARAM_INT);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Cập nhật loai_tai_san_id về 0 khi loại tài sản bị xóa
    public function updateLoaiTaiSanIdToZero($loaiTaiSanId) {
        $query = "UPDATE " . $this->table_name . " SET loai_tai_san_id = 0 WHERE loai_tai_san_id = :loai_tai_san_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':loai_tai_san_id', $loaiTaiSanId, PDO::PARAM_INT);
            
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
