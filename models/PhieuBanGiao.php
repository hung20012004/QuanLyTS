<?php
class PhieuBanGiao
{
    private $conn;
    private $table_name = "phieu_ban_giao";

    public $phieu_ban_giao_id;
    public $user_ban_giao_id;
    public $user_nhan_id;
    public $user_duyet_id;
    public $vi_tri_id;
    public $ghi_chu ;

    public $ngay_gui;
    public $ngay_kiem_tra;
    public $ngay_duyet;
    public $ngay_ban_giao;
    public $trang_thai;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readAll()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE phieu_ban_giao_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_nhan_id=:user_nhan_id, 
                      vi_tri_id=:vi_tri_id, ngay_gui=:ngay_gui, trang_thai=:trang_thai";

        $stmt = $this->conn->prepare($query);

        $this->user_nhan_id = htmlspecialchars(strip_tags($this->user_nhan_id));
        $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));
        $this->ngay_gui = htmlspecialchars(strip_tags($this->ngay_gui));
        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));

        $stmt->bindParam(":user_nhan_id", $this->user_nhan_id);
        $stmt->bindParam(":vi_tri_id", $this->vi_tri_id);
        $stmt->bindParam(":ngay_gui", $this->ngay_gui);
        $stmt->bindParam(":trang_thai", $this->trang_thai);

        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET
                      user_nhan_id = :user_nhan_id,
                      vi_tri_id = :vi_tri_id,
                      ngay_gui = :ngay_gui,
                      trang_thai = :trang_thai
                  WHERE phieu_ban_giao_id = :phieu_ban_giao_id";

        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(":user_nhan_id", $this->user_nhan_id);
        $stmt->bindParam(":vi_tri_id", $this->vi_tri_id);
        $stmt->bindParam(":ngay_gui", $this->ngay_gui);
        $stmt->bindParam(":trang_thai", $this->trang_thai);
        $stmt->bindParam(":phieu_ban_giao_id", $this->phieu_ban_giao_id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE phieu_ban_giao_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function updateStatus()
{
    $query = "UPDATE " . $this->table_name . "
              SET trang_thai = :trang_thai,
                  ngay_kiem_tra = :ngay_kiem_tra,
                  ngay_duyet = :ngay_duyet,
                  ngay_ban_giao = :ngay_ban_giao,
                  user_ban_giao_id = :user_ban_giao_id,
                  user_duyet_id = :user_duyet_id
              WHERE phieu_ban_giao_id = :phieu_ban_giao_id";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":trang_thai", $this->trang_thai);
    $stmt->bindParam(":ngay_kiem_tra", $this->ngay_kiem_tra);
    $stmt->bindParam(":ngay_duyet", $this->ngay_duyet);
    $stmt->bindParam(":ngay_ban_giao", $this->ngay_ban_giao);
    $stmt->bindParam(":user_ban_giao_id", $this->user_ban_giao_id);
    $stmt->bindParam(":user_duyet_id", $this->user_duyet_id);
    $stmt->bindParam(":phieu_ban_giao_id", $this->phieu_ban_giao_id);

    if($stmt->execute()){
        return true;
    }
    return false;
}

}