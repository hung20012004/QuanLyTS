<?php
class ChiTietHoaDonMua {
    private $conn;
    private $table_name = "chi_tiet_hoa_don_mua";

    public $chi_tiet_id;
    public $hoa_don_id;
    public $tai_san_id;
    public $so_luong;
    public $don_gia;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET hoa_don_id=:hoa_don_id, tai_san_id=:tai_san_id, so_luong=:so_luong, don_gia=:don_gia";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->hoa_don_id = htmlspecialchars(strip_tags($this->hoa_don_id));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->don_gia = htmlspecialchars(strip_tags($this->don_gia));

        // bind values
        $stmt->bindParam(':hoa_don_id', $this->hoa_don_id);
        $stmt->bindParam(':tai_san_id', $this->tai_san_id);
        $stmt->bindParam(':so_luong', $this->so_luong);
        $stmt->bindParam(':don_gia', $this->don_gia);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
