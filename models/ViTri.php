<?php
// models/ViTri.php

class ViTri {
    private $conn;
    private $table_name = "vi_tri";

    public $vi_tri_id;
    public $ten_vi_tri;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đọc tất cả vị trí
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readNotKho() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE vi_tri_id > 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tạo vị trí mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET ten_vi_tri=:ten_vi_tri";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_vi_tri = htmlspecialchars(strip_tags($this->ten_vi_tri));

        // bind value
        $stmt->bindParam(':ten_vi_tri', $this->ten_vi_tri);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Đọc thông tin vị trí theo ID
    public function readById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin vị trí
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ten_vi_tri = :ten_vi_tri WHERE vi_tri_id = :vi_tri_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ten_vi_tri = htmlspecialchars(strip_tags($this->ten_vi_tri));
        $this->vi_tri_id = htmlspecialchars(strip_tags($this->vi_tri_id));

        // bind values
        $stmt->bindParam(':ten_vi_tri', $this->ten_vi_tri);
        $stmt->bindParam(':vi_tri_id', $this->vi_tri_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa vị trí
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE vi_tri_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //Kiểm tra đã tồn tại chưa
    public function checkExist($ten_vi_tri) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE ten_vi_tri = :ten_vi_tri";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ten_vi_tri', $ten_vi_tri);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return true;
        }
        return false;
    }
    public function updateViTri($id, $tenViTri, $viTriChiTiets) {
        try {
            // Bắt đầu transaction
            $this->conn->beginTransaction();

            // Cập nhật thông tin chính của vị trí
            $query = "UPDATE vi_tri SET ten_vi_tri = :ten_vi_tri WHERE vi_tri_id = :vi_tri_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ten_vi_tri', $tenViTri);
            $stmt->bindParam(':vi_tri_id', $id);
            $stmt->execute();

            // Xóa các chi tiết vị trí cũ
            $queryDelete = "DELETE FROM vi_tri_chi_tiet WHERE vi_tri_id = :vi_tri_id";
            $stmtDelete = $this->conn->prepare($queryDelete);
            $stmtDelete->bindParam(':vi_tri_id', $id);
            $stmtDelete->execute();

            // Thêm lại các chi tiết vị trí mới
            $queryInsert = "INSERT INTO vi_tri_chi_tiet (vi_tri_id, tai_san_id, so_luong_kho, so_luong_chuyen) VALUES (:vi_tri_id, :tai_san_id, :so_luong_kho, :so_luong_chuyen)";
            $stmtInsert = $this->conn->prepare($queryInsert);

            foreach ($viTriChiTiets as $viTriChiTiet) {
                $stmtInsert->bindParam(':vi_tri_id', $id);
                $stmtInsert->bindParam(':tai_san_id', $viTriChiTiet['tai_san_id']);
                $stmtInsert->bindParam(':so_luong_kho', $viTriChiTiet['so_luong_kho']);
                $stmtInsert->bindParam(':so_luong_chuyen', $viTriChiTiet['so_luong_chuyen']);
                $stmtInsert->execute();
            }

            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback nếu có lỗi
            $this->conn->rollBack();
            return false;
        }
    }
    public function getViTriById($id) {
        $query = "SELECT * FROM vi_tri WHERE vi_tri_id = :vi_tri_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vi_tri_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getViTriChiTiets($id) {
        $query = "SELECT vct.vi_tri_id, vct.so_luong, v.ten_vi_tri,
                         cthdm.tai_san_id, cthdm.so_luong as so_luong_kho, cthdm.hoa_don_id,
                         ts.ten_tai_san, lts.ten_loai_tai_san, hd.ngay_mua
                  FROM vi_tri_chi_tiet vct
                  LEFT JOIN chi_tiet_hoa_don_mua cthdm ON vct.chi_tiet_id = cthdm.chi_tiet_id
                  LEFT JOIN tai_san ts ON cthdm.tai_san_id = ts.tai_san_id
                  LEFT JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
                  LEFT JOIN hoa_don_mua hd ON cthdm.hoa_don_id = hd.hoa_don_id
                  LEFT JOIN vi_tri v ON vct.vi_tri_id = v.vi_tri_id
                  WHERE vct.vi_tri_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
