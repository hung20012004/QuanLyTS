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
        try {
            // Begin transaction
            $this->db->beginTransaction();
    
            // Update main position information if changed
            $this->viTri->vi_tri_id = $id;
            $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
            $this->viTri->update();
    
            // Process updating or adding position details
            foreach ($_POST['vi_tri_chi_tiets'] as $chiTiet) {
                $vi_tri_chi_tiet_id = $chiTiet['vi_tri_chi_tiet_id'];
                $tai_san_id = $chiTiet['tai_san_id'];
                $so_luong = $chiTiet['so_luong'];
    
                // Check if there's any change in quantity
                $soLuongTruocCapNhat = $this->viTriChiTiet->readSoLuongById($vi_tri_chi_tiet_id);
                $soLuongThayDoi = $so_luong - $soLuongTruocCapNhat;
    
                if ($soLuongThayDoi != 0) {
                    // Perform inventory check only if quantity changes
                    if (!$this->viTriChiTiet->kiemTraKho($tai_san_id, $soLuongThayDoi)) {
                        throw new Exception('Kho không đủ số lượng.');
                    }
    
                    // Update or create position detail
                    if (!empty($vi_tri_chi_tiet_id)) {
                        // Update existing position detail
                        $this->viTriChiTiet->vi_tri_chi_tiet_id = $vi_tri_chi_tiet_id;
                        $this->viTriChiTiet->vi_tri_id = $id;
                        $this->viTriChiTiet->tai_san_id = $tai_san_id;
                        $this->viTriChiTiet->so_luong = $so_luong;
                        $this->viTriChiTiet->update();
                    } else {
                        // Create new position detail
                        $this->viTriChiTiet->vi_tri_id = $id;
                        $this->viTriChiTiet->tai_san_id = $tai_san_id;
                        $this->viTriChiTiet->so_luong = $so_luong;
                        $this->viTriChiTiet->create();
                    }
    
                    // Update inventory
                    $this->viTriChiTiet->updateKho($tai_san_id, $soLuongThayDoi);
                }
            }
    
            // Commit transaction
            $this->db->commit();
    
            // Set success message and redirect to the list page
            $_SESSION['message'] = 'Sửa vị trí thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=vitri");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
        }
    
        // Load data to display the edit form again on error or for further editing
        $viTri = $this->viTri->readById($id);
        $viTriChiTiets = $this->viTriChiTiet->readByViTriId($id);
        $taiSanList = $this->taiSan->read(); // Assuming you have a method to fetch TaiSan list
    
        // Load the view to display the edit form
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
