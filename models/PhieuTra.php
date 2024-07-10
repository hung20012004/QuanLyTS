<?php
class PhieuTra{
    private $conn;
    private $table_name = "phieu_tra";

    public $phieu_tra_id;
    public $user_tra_id;
    public $user_nhan_id;
    public $user_duyet_id;
    public $ngay_gui;
    public $ngay_kiem_tra;
    public $ngay_duyet;
    public $ngay_tra;
    public $trang_thai;
    public $ghi_chu;

     public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM phieu_tra ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByID($id)
    {
        $query = "SELECT * FROM phieu_tra WHERE phieu_tra_id = ".$id."";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserName($user_id)
    {
        $query = "SELECT * FROM users WHERE user_id = ".$user_id." ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readAllTaiSanbyKhoa($khoa_id)
    {
         $query = "SELECT vtct.tai_san_id, ts.ten_tai_san, vtct.vi_tri_id
                  FROM vi_tri_chi_tiet vtct
                  INNER JOIN tai_san ts ON ts.tai_san_id = vtct.tai_san_id
                  INNER JOIN vi_tri vt ON vt.vi_tri_id = vtct.vi_tri_id
                  WHERE vt.khoa = '".$khoa_id."' ";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_tra_id=:user_tra_id, ngay_gui=:ngay_gui, trang_thai=:trang_thai, ghi_chu =:ghi_chu";

        $stmt = $this->conn->prepare($query);

        $this->user_tra_id = htmlspecialchars(strip_tags($this->user_tra_id));
        $this->ngay_gui = htmlspecialchars(strip_tags($this->ngay_gui));
        $this->trang_thai = htmlspecialchars(strip_tags($this->trang_thai));
        $this->ghi_chu = htmlspecialchars(strip_tags($this->ghi_chu));

        $stmt->bindParam(":user_tra_id", $this->user_tra_id);
        $stmt->bindParam(":ngay_gui", $this->ngay_gui);
        $stmt->bindParam(":trang_thai", $this->trang_thai);
        $stmt->bindParam(":ghi_chu", $this->ghi_chu);

        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;
    }

     public function readById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE phieu_tra_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                    SET ghi_chu =:ghi_chu
                  WHERE phieu_tra_id = :phieu_tra_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ghi_chu", $this->ghi_chu);
        $stmt->bindParam(":phieu_tra_id", $this->phieu_tra_id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

      public function updateStatusPhieuKiemTra()
{
    $query = "UPDATE " . $this->table_name . "
              SET trang_thai = :trang_thai,
              ngay_kiem_tra = :ngay_kiem_tra,
              user_nhan_id = :user_nhan_id
              WHERE phieu_tra_id = :phieu_tra_id";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":trang_thai", $this->trang_thai);
    $stmt->bindParam(":ngay_kiem_tra", $this->ngay_kiem_tra);
    $stmt->bindParam(":user_nhan_id", $this->user_nhan_id);
    $stmt->bindParam(":phieu_tra_id", $this->phieu_tra_id);

    if($stmt->execute()){
        return true;
    }
    return false;
}

 public function updateStatusXetDuyet()
{
    $query = "UPDATE " . $this->table_name . "
              SET trang_thai = :trang_thai,
              ngay_duyet = :ngay_duyet,
              user_duyet_id = :user_duyet_id
              WHERE phieu_tra_id = :phieu_tra_id";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":trang_thai", $this->trang_thai);
    $stmt->bindParam(":ngay_duyet", $this->ngay_duyet);
      $stmt->bindParam(":user_duyet_id", $this->user_duyet_id);
    $stmt->bindParam(":phieu_tra_id", $this->phieu_tra_id);

    if($stmt->execute()){
        return true;
    }
    return false;
}

public function updateStatusTra()
{
    $query = "UPDATE " . $this->table_name . "
              SET trang_thai = :trang_thai,
              ngay_tra = :ngay_tra,
              user_tra_id = :user_tra_id
              WHERE phieu_tra_id = :phieu_tra_id";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":trang_thai", $this->trang_thai);
    $stmt->bindParam(":user_tra_id", $this->user_tra_id);
    $stmt->bindParam(":ngay_tra", $this->ngay_tra);
    $stmt->bindParam(":phieu_tra_id", $this->phieu_tra_id);

    if($stmt->execute()){
        return true;
    }
    return false;
}

 public function getSoLuongTSPhongBan($vi_tri_id, $tai_san_id)
    {
        $query = "SELECT so_luong FROM vi_tri_chi_tiet WHERE vi_tri_id = ? AND tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$vi_tri_id, $tai_san_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['so_luong'];
        }
        return 0;
    }

   public function updateSoluongVitri($vi_tri_id, $tai_san_id, $so_luong) 
   {
        $query = "UPDATE vi_tri_chi_tiet 
              SET so_luong = :so_luong 
              WHERE vi_tri_id = :vi_tri_id AND tai_san_id = :tai_san_id";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':so_luong', $so_luong);
    $stmt->bindParam(':vi_tri_id', $vi_tri_id);
    $stmt->bindParam(':tai_san_id', $tai_san_id);

    if ($stmt->execute()) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
   }

   public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE phieu_tra_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function travekho($tai_san_id, $so_luong){
        try {
        $query = "SELECT so_luong FROM vi_tri_chi_tiet WHERE vi_tri_id = ? AND tai_san_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([1, $tai_san_id]);
         $so_luong_kho = $stmt->fetch(PDO::FETCH_ASSOC);

        if($so_luong_kho!=false)
        {
            $so_luong_update = $so_luong_kho['so_luong'] + $so_luong;
            $sql = "UPDATE vi_tri_chi_tiet SET so_luong=:so_luong WHERE vi_tri_id =? AND tai_san_id = ?";
            $stmt1 = $this->conn->prepare($sql);
            $stmt1->bindValue(':so_luong', $so_luong_update, PDO::PARAM_INT);
            $stmt1->bindValue(':vi_tri_id', 1, PDO::PARAM_INT);
            $stmt1->bindValue(':tai_san_id', $tai_san_id, PDO::PARAM_INT);

        }
        else {
            $sql = "INSERT INTO vi_tri_chi_tiet SET so_luong=:so_luong, vi_tri_id =:vi_tri_id, tai_san_id=:tai_san_id";
            $stmt1 = $this->conn->prepare($sql);
            $stmt1->bindValue(':so_luong', $so_luong, PDO::PARAM_INT);
            $stmt1->bindValue(':vi_tri_id', 1, PDO::PARAM_INT);
            $stmt1->bindValue(':tai_san_id', $tai_san_id, PDO::PARAM_INT);
        }

        if ($stmt1->execute()) {
            return true;
        } else {
            return false;
        }
    }
        catch (Exception $e) {
        // Xử lý ngoại lệ nếu có lỗi xảy ra
        echo "Error: " . $e->getMessage();
        return false;
        }
}

}