<?php
class PhieuBanGiaoChiTiet
{
    private $conn;
    private $table_name = "phieu_ban_giao_chi_tiet";

    public $phieu_ban_giao_chi_tiet_id;
    public $phieu_ban_giao_id;
    public $tinh_trang;
    public $so_luong;
    public $tai_san_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET phieu_ban_giao_id=:phieu_ban_giao_id, tinh_trang=:tinh_trang, 
                      so_luong=:so_luong, tai_san_id=:tai_san_id";

        $stmt = $this->conn->prepare($query);

        $this->phieu_ban_giao_id = htmlspecialchars(strip_tags($this->phieu_ban_giao_id));
        $this->tinh_trang = htmlspecialchars(strip_tags($this->tinh_trang));
        $this->so_luong = htmlspecialchars(strip_tags($this->so_luong));
        $this->tai_san_id = htmlspecialchars(strip_tags($this->tai_san_id));

        $stmt->bindParam(":phieu_ban_giao_id", $this->phieu_ban_giao_id);
        $stmt->bindParam(":tinh_trang", $this->tinh_trang);
        $stmt->bindParam(":so_luong", $this->so_luong);
        $stmt->bindParam(":tai_san_id", $this->tai_san_id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function readByPhieuBanGiaoId($phieu_ban_giao_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE phieu_ban_giao_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $phieu_ban_giao_id);
        $stmt->execute();
        return $stmt;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET tinh_trang = :tinh_trang,
                      so_luong = :so_luong,
                      tai_san_id = :tai_san_id
                  WHERE phieu_ban_giao_chi_tiet_id = :phieu_ban_giao_chi_tiet_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":tinh_trang", $this->tinh_trang);
        $stmt->bindParam(":so_luong", $this->so_luong);
        $stmt->bindParam(":tai_san_id", $this->tai_san_id);
        $stmt->bindParam(":phieu_ban_giao_chi_tiet_id", $this->phieu_ban_giao_chi_tiet_id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE phieu_ban_giao_chi_tiet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function deleteByPhieuBanGiaoId($phieu_ban_giao_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE phieu_ban_giao_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $phieu_ban_giao_id);
        
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}