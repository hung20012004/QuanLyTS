<?php
include_once 'config/database.php';
include_once 'models/ViTri.php';
include_once 'models/ViTriChiTiet.php';
include_once 'models/TaiSan.php';
include_once 'models/LoaiTaiSan.php';

class ViTriController {
    private $db;
    private $viTri;
    private $viTriChiTiet;
    private $taiSan;
    private $loaiTaiSan;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->viTri = new ViTri($this->db);
        $this->viTriChiTiet = new ViTriChiTiet($this->db);
        $this->taiSan = new TaiSan($this->db);
        $this->loaiTaiSan = new LoaiTaiSan($this->db);
    }

    public function index() {
        $stmt = $this->viTri->read();
        $viTris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/vitris/index.php';
        include('views/layouts/base.php');
    }

    public function show($id) {
        $viTri = $this->viTri->readById($id);
        $viTriChiTiets = $this->viTriChiTiet->readByViTriId($id);
        if ($_POST) {
            $this->db->beginTransaction();
            try {
                // Cập nhật thông tin vị trí
                $this->viTri->vi_tri_id = $id;
                $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
                $this->viTri->khoa = $_POST['khoa'];

               if( $this->viTri->update()){
                    $this->db->commit();
                    $_SESSION['message'] = 'Cập nhật vị trí thành công!';
                    $_SESSION['message_type'] = 'success';
                    header("Location: index.php?model=vitri");
                    exit();
               }
               else{
                    throw new Exception('Cập nhật vị trí thất bại!');
               }
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }
        if (!$viTri) {
            $_SESSION['message'] = 'Không tìm thấy vị trí!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=vitri");
            exit();
        }
        $content = 'views/vitris/show.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->db->beginTransaction();
    
            try {
                $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
                $this->viTri->khoa = $_POST['khoa'];
                
                if($this->viTri->checkExist($this->viTri->ten_vi_tri, $this->viTri->khoa)){
                    $_SESSION['message'] = 'Vị trí đã tồn tại!';
                    $_SESSION['message_type'] = 'danger';
                    $content = 'views/vitris/create.php';
                    include('views/layouts/base.php');
                    return;
                }
                echo "dachack";
                if ($this->viTri->create()) {    
                    $this->db->commit();
                    $_SESSION['message'] = 'Thêm vị trí thành công!';
                    $_SESSION['message_type'] = 'success';
                    header("Location: index.php?model=vitri");
                    exit();
                } else {
                    throw new Exception('Tạo mới vị trí thất bại!');
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/vitris/create.php';
        include('views/layouts/base.php');
    }
    


    public function edit($id) {
        // Lấy thông tin vị trí
        $viTri = $this->viTri->readById($id);
    
        // Lấy chi tiết vị trí cũ
        $viTriChiTietsOld = $this->viTriChiTiet->readByViTriId($id);
    
        // Tính số lượng trong kho cho mỗi tài sản
        foreach ($viTriChiTietsOld as &$detail) {
            $stmt = $this->db->prepare("
                SELECT so_luong AS so_luong_kho FROM vi_tri_chi_tiet WHERE tai_san_id = :tai_san_id AND vi_tri_id = 1
            ");
            $stmt->execute([':tai_san_id' => $detail['tai_san_id']]);
            $detail['so_luong_kho'] = $stmt->fetchColumn();
        }
    
        // Lấy danh sách tài sản
        $taiSans = $this->taiSan->read();
    
        if ($_POST) {
            $this->db->beginTransaction();
            try {
                // Cập nhật thông tin vị trí
                $stmt = $this->db->prepare("UPDATE vi_tri SET ten_vi_tri = ? WHERE vi_tri_id = ?");
                if (!$stmt->execute([$_POST['ten_vi_tri'], $id])) {
                    throw new Exception('Cập nhật vị trí thất bại!');
                }
    
                // Xóa chi tiết vị trí cũ
                $stmt = $this->db->prepare("DELETE FROM vi_tri_chi_tiet WHERE vi_tri_id = ?");
                if (!$stmt->execute([$id])) {
                    throw new Exception('Xóa chi tiết vị trí cũ thất bại!');
                }
    
                // Thêm chi tiết vị trí mới
                foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
                    $soLuongChuyen = $_POST['so_luong_chuyen'][$index];
    
                    // Tìm chi tiết vị trí cũ để so sánh
                    $oldDetail = null;
                    foreach ($viTriChiTietsOld as $detail) {
                        if ($detail['tai_san_id'] == $taiSanId) {
                            $oldDetail = $detail;
                            break;
                        }
                    }
    
                    // Kiểm tra số lượng chỉ khi có sự thay đổi số lượng chuyển hoặc khi thêm mới
                    if (!$oldDetail || $soLuongChuyen != $oldDetail['so_luong']) {
                        // Thực hiện kiểm tra số lượng
                        $stmt = $this->db->prepare("
                            SELECT so_luong AS so_luong_kho FROM vi_tri_chi_tiet WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id
                        ");
                        $stmt->execute([':tai_san_id' => $taiSanId, ':vi_tri_id' => 1]);
                        $soLuongKho = $stmt->fetchColumn();
    
                        if ($soLuongChuyen > $soLuongKho) {
                            throw new Exception("Không đủ số lượng trong kho cho tài sản ID $taiSanId");
                        }
                    }
    
                    // Thêm mới vào vi_tri_chi_tiet
                    $stmt = $this->db->prepare("
                        INSERT INTO vi_tri_chi_tiet (vi_tri_id, tai_san_id, so_luong)
                        VALUES (?, ?, ?)
                    ");
                    if (!$stmt->execute([$id, $taiSanId, $soLuongChuyen])) {
                        throw new Exception('Thêm chi tiết vị trí mới thất bại!');
                    }
                }
    
                $this->db->commit();
                $_SESSION['message'] = 'Cập nhật vị trí thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=vitri");
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/vitris/edit.php';
        include('views/layouts/base.php');
    }      

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->db->beginTransaction();
            try {
                // Xóa chi tiết vị trí trước
                $stmt = $this->db->prepare("DELETE FROM vi_tri_chi_tiet WHERE vi_tri_id = ?");
                if (!$stmt->execute([$id])) {
                    throw new Exception('Xóa chi tiết vị trí thất bại!');
                }

                // Xóa vị trí
                $stmt = $this->db->prepare("DELETE FROM vi_tri WHERE vi_tri_id = ?");
                if (!$stmt->execute([$id])) {
                    throw new Exception('Xóa vị trí thất bại!');
                }

                $this->db->commit();
                $_SESSION['message'] = 'Xóa vị trí thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=vitri");
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }
        header("Location: index.php?model=vitri");
    }

    public function getQuantityInStock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taiSanId = $_POST['tai_san_id'];
            $hoaDonId = $_POST['hoa_don_id'];
            
            $stmt = $this->db->prepare("
                SELECT SUM(cthd.so_luong) - COALESCE(
                    (SELECT SUM(vtct.so_luong) 
                     FROM vi_tri_chi_tiet vtct 
                     WHERE vtct.chi_tiet_id = cthd.chi_tiet_id AND vtct.vi_tri_id != 1), 0
                ) as quantity
                FROM chi_tiet_hoa_don_mua cthd
                WHERE cthd.tai_san_id = ? AND cthd.hoa_don_id = ?
            ");
            $stmt->execute([$taiSanId, $hoaDonId]);
            $quantity = $stmt->fetchColumn();
            
            echo json_encode(['quantity' => $quantity]);
            exit;
        }
    }
}