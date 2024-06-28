<?php
include_once 'config/database.php';
include_once 'models/ViTri.php';
include_once 'models/ViTriChiTiet.php';
include_once 'models/TaiSan.php';

class ViTriController extends Controller {
    private $db;
    private $viTri;
    private $viTriChiTiet;
    private $taiSan;
    private $loaiTaiSan;
    private $hoaDonMua;
    private$chiTietHoaDon;

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
            // Handle the case where no vi_tri is found with the given id
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
                    $content = 'views/vitris/create.php'; // Hiển thị lại form tạo mới
                    include('views/layouts/base.php');
                    return; // Dừng hàm để ngăn không chuyển hướng
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
        $viTri = $this->viTri->readById($id);
        if (!$viTri) {
            $_SESSION['message'] = 'Không tìm thấy vị trí!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=vitri");
            return;
        }
        $vi_tri_chi_tiet = $this->viTriChiTiet->readByViTriId($id);
    
        if ($_POST) {
            $this->db->beginTransaction();
            try {
                // Update vi_tri
                $this->viTri->vi_tri_id = $id;
                $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
                if($this->viTri->checkExist($_POST['ten_vi_tri']) && $this->viTri->ten_vi_tri != $_POST['ten_vi_tri']){
                    $_SESSION['message'] = 'Vị trí đã tồn tại!';
                    $_SESSION['message_type'] = 'danger';
                    $content = 'views/vitris/edit.php'; // Hiển thị lại form tạo mới
                    include('views/layouts/base.php');
                    return; // Dừng hàm để ngăn không chuyển hướng
                }
                $this->viTri->update();
                
                // Update vi_tri_chi_tiet
                $this->viTriChiTiet->vi_tri_id = $id;
                for ($i = 0; $i < count($_POST['chi_tiet_id']); $i++) {
                    $this->viTriChiTiet->so_luong = $_POST['so_luong'][$i];
                    $this->viTriChiTiet->chi_tiet_id = $_POST['chi_tiet_id'][$i];
    
                    if ($_POST['chi_tiet_id'][$i] != '') {
                        $this->viTriChiTiet->update($_POST['chi_tiet_id'][$i],$id,$_POST['so_luong'][$i]);
                    } else {
                        $this->viTriChiTiet->create();
                    }
    
                    // Get ngay_mua and tai_san_id from chi_tiet_hoa_don_mua
                    // $chiTietHoaDonMua = $this->chiTietHoaDon->readByChiTietId($_POST['chi_tiet_id'][$i]);
                    // if ($chiTietHoaDonMua) {
                    //     $ngay_mua = $chiTietHoaDonMua['ngay_mua'];
                    //     $tai_san_id = $chiTietHoaDonMua['tai_san_id'];
                    //     // You can use $ngay_mua and $tai_san_id as needed
                    // }
                }
    
                // Delete removed vi_tri_chi_tiet
                foreach ($vi_tri_chi_tiet as $detail) {
                    $found = false;
                    for ($i = 0; $i < count($_POST['chi_tiet_id']); $i++) {
                        if ($detail['id'] == $_POST['chi_tiet_id'][$i]) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $this->viTriChiTiet->delete($detail['vi_tri_chi_tiet_id']);
                    }
                }
    
                $this->db->commit();
                $_SESSION['message'] = 'Sửa vị trí thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=vitri");
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }
        $loaiTaiSan = $this->loaiTaiSan->readAll();
        $tai_san_list = $this->taiSan->read();
        $viTriChiTiets = $this->viTriChiTiet->readByViTriId($id);
        $content = 'views/vitris/edit.php';
        include('views/layouts/base.php');
    }
    
    public function delete($id) {
        // First, transfer assets back to the main storage
        $viTriChiTiets = $this->viTriChiTiet->readByViTriId($id);
        foreach ($viTriChiTiets as $chiTiet) {
            // Logic to transfer assets back to the main storage goes here
            // For example: $this->transferToMainStorage($chiTiet['tai_san_id'], $chiTiet['so_luong']);
        }

        // Next, delete chi tiet
        foreach ($viTriChiTiets as $chiTiet) {
            $this->viTriChiTiet->delete($chiTiet['vi_tri_chi_tiet_id']);
        }

        // Finally, delete the main vi tri
        if ($this->viTri->delete($id)) {
            $_SESSION['message'] = 'Xóa vị trí thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=vitri");
        } else {
            $_SESSION['message'] = 'Xóa thất bại!';
            $_SESSION['message_type'] = 'danger';
        }
    }
}
?>
