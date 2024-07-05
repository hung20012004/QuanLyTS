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
    private $hoaDonMua;
    private $chiTietHoaDon;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->viTri = new ViTri($this->db);
        $this->viTriChiTiet = new ViTriChiTiet($this->db);
        $this->taiSan = new TaiSan($this->db);
        $this->loaiTaiSan = new LoaiTaiSan($this->db);
        $this->hoaDonMua = new HoaDonMua($this->db);
        $this->chiTietHoaDon = new ChiTietHoaDonMua($this->db);
    }

    public function index() {
        $stmt = $this->viTri->read();
        $viTris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/vitris/index.php';
        include('views/layouts/base.php');
    }

    public function show($id) {
        $viTri = $this->viTri->readById($id);
        if (!$viTri) {
            $_SESSION['message'] = 'Không tìm thấy vị trí!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=vitri");
            exit();
        }
        
        $viTriChiTiets = $this->viTriChiTiet->readByViTriId($id);
        
        $content = 'views/vitris/show.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->db->beginTransaction();
    
            try {
                $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];

                if($this->viTri->checkExist($_POST['ten_vi_tri'])){
                    $_SESSION['message'] = 'Vị trí đã tồn tại!';
                    $_SESSION['message_type'] = 'danger';
                    $content = 'views/vitris/create.php';
                    include('views/layouts/base.php');
                    return;
                }

                if ($this->viTri->create()) {    
                    $this->db->commit();
                    $_SESSION['message'] = 'Tạo vị trí mới thành công!';
                    $_SESSION['message_type'] = 'success';
                    header("Location: index.php?model=vitri");
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
        $stmt = $this->db->prepare("SELECT * FROM vi_tri WHERE vi_tri_id = ?");
        $stmt->execute([$id]);
        $viTri = $stmt->fetch(PDO::FETCH_ASSOC);

        // Lấy chi tiết vị trí
        $stmt = $this->db->prepare("
            SELECT vtct.*, ts.ten_tai_san, ts.tai_san_id, lts.ten_loai_tai_san, lts.loai_tai_san_id, hdm.ngay_mua, hdm.hoa_don_id
            FROM vi_tri_chi_tiet vtct
            JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id = cthd.chi_tiet_id
            JOIN tai_san ts ON cthd.tai_san_id = ts.tai_san_id
            JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
            JOIN hoa_don_mua hdm ON cthd.hoa_don_id = hdm.hoa_don_id
            WHERE vtct.vi_tri_id = ?
        ");
        $stmt->execute([$id]);
        $viTriChiTiets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính số lượng trong kho cho mỗi tài sản
        foreach ($viTriChiTiets as &$detail) {
            $stmt = $this->db->prepare("
                SELECT SUM(cthd.so_luong) - COALESCE(
                    (SELECT SUM(vtct.so_luong) 
                     FROM vi_tri_chi_tiet vtct 
                     WHERE vtct.chi_tiet_id = cthd.chi_tiet_id AND vtct.vi_tri_id != 1), 0
                ) as so_luong_kho
                FROM chi_tiet_hoa_don_mua cthd
                WHERE cthd.tai_san_id = ? AND cthd.hoa_don_id = ?
            ");
            $stmt->execute([$detail['tai_san_id'], $detail['hoa_don_id']]);
            $detail['so_luong_kho'] = $stmt->fetchColumn();
        }

        // Lấy danh sách tài sản
        $stmt = $this->db->query("
            SELECT DISTINCT ts.*, lts.ten_loai_tai_san, lts.loai_tai_san_id
            FROM tai_san ts
            JOIN loai_tai_san lts ON ts.loai_tai_san_id = lts.loai_tai_san_id
            JOIN chi_tiet_hoa_don_mua cthd ON ts.tai_san_id = cthd.tai_san_id
        ");
        $taiSans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy ngày mua cho mỗi tài sản
        $purchaseDates = [];
        foreach ($taiSans as $taiSan) {
            $stmt = $this->db->prepare("
                SELECT DISTINCT hdm.ngay_mua, hdm.hoa_don_id
                FROM hoa_don_mua hdm
                JOIN chi_tiet_hoa_don_mua cthd ON hdm.hoa_don_id = cthd.hoa_don_id
                WHERE cthd.tai_san_id = ?
                ORDER BY hdm.ngay_mua DESC
            ");
            $stmt->execute([$taiSan['tai_san_id']]);
            $purchaseDates[$taiSan['tai_san_id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    $hoaDonId = $_POST['hoa_don_id'][$index];
                    $soLuongChuyen = $_POST['so_luong_chuyen'][$index];

                    // Kiểm tra số lượng có sẵn trong kho
                    $stmt = $this->db->prepare("
                        SELECT SUM(cthd.so_luong) - COALESCE(
                            (SELECT SUM(vtct.so_luong) 
                             FROM vi_tri_chi_tiet vtct 
                             WHERE vtct.chi_tiet_id = cthd.chi_tiet_id AND vtct.vi_tri_id != 1), 0
                        ) as available_quantity
                        FROM chi_tiet_hoa_don_mua cthd
                        WHERE cthd.tai_san_id = ? AND cthd.hoa_don_id = ?
                    ");
                    $stmt->execute([$taiSanId, $hoaDonId]);
                    $availableQuantity = $stmt->fetchColumn();

                    if ($soLuongChuyen > $availableQuantity) {
                        throw new Exception("Không đủ số lượng trong kho cho tài sản ID $taiSanId");
                    }

                    // Lấy chi_tiet_id
                    $stmt = $this->db->prepare("
                        SELECT chi_tiet_id 
                        FROM chi_tiet_hoa_don_mua 
                        WHERE tai_san_id = ? AND hoa_don_id = ?
                    ");
                    $stmt->execute([$taiSanId, $hoaDonId]);
                    $chiTietId = $stmt->fetchColumn();

                    // Thêm mới vào vi_tri_chi_tiet
                    $stmt = $this->db->prepare("
                        INSERT INTO vi_tri_chi_tiet (vi_tri_id, so_luong, chi_tiet_id)
                        VALUES (?, ?, ?)
                    ");
                    if (!$stmt->execute([$id, $soLuongChuyen, $chiTietId])) {
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