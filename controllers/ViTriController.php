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
        //$viTriChiTiets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content = 'views/vitris/show.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->db->beginTransaction();
    
            try {
                $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
                
                if ($this->viTri->create()) {
                    $viTriId = $this->db->lastInsertId();
    
                    for ($i = 0; $i < count($_POST['tai_san_id']); $i++) {
                        $viTriChiTietData = array(
                            'vi_tri_id' => $viTriId,
                            'tai_san_id' => $_POST['tai_san_id'][$i],
                            'so_luong' => $_POST['so_luong'][$i]
                        );
                        var_dump($viTriChiTietData);
                        $this->viTriChiTiet->create($viTriChiTietData);
                    }
    
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
        var_dump($viTriChiTietData);
        $taiSanList = $this->taiSan->read();
        $content = 'views/vitris/create.php';
        include('views/layouts/base.php');
    }
    
        
    
    public function edit($id) {
        if ($_POST) {
            $this->viTri->vi_tri_id = $id;
            $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
    
            if ($this->viTri->update()) {
                foreach ($_POST['vi_tri_chi_tiets'] as $chiTiet) {
                    if (isset($chiTiet['vi_tri_chi_tiet_id'])) {
                        // Update existing chi tiet
                        $this->viTriChiTiet->vi_tri_chi_tiet_id = $chiTiet['vi_tri_chi_tiet_id'];
                        $this->viTriChiTiet->tai_san_id = $chiTiet['tai_san_id'];
                        $this->viTriChiTiet->so_luong = $chiTiet['so_luong'];
                        $this->viTriChiTiet->update();
                    } else {
                        // Create new chi tiet
                        $this->viTriChiTiet->vi_tri_id = $id;
                        $this->viTriChiTiet->tai_san_id = $chiTiet['tai_san_id'];
                        $this->viTriChiTiet->so_luong = $chiTiet['so_luong'];
                        $this->viTriChiTiet->create();
                    }
                }
    
                $_SESSION['message'] = 'Sửa vị trí thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=vitri");
                exit();
            } else {
                $_SESSION['message'] = 'Sửa thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            $viTri = $this->viTri->readById($id);
            $viTriChiTiets = $this->viTriChiTiet->readByViTriId($id);
            $taiSanList = $this->taiSan->read(); // Add this line to get the list of TaiSan
            $content = 'views/vitris/edit.php';
            include('views/layouts/base.php');
        }
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
