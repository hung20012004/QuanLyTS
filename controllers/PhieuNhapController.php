<?php
include_once 'config/database.php';
include_once 'models/PhieuNhap.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ChiTietPhieuNhap.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTriChiTiet.php';

class PhieuNhapController extends Controller
{
    private $db;
    private $phieuNhapModel;
    private $loaiTaiSanModel;
    private $chiTietPhieuNhapModel;
    private $taiSanModel;
    private $viTriChiTietModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuNhapModel = new PhieuNhap($this->db);
        $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
        $this->chiTietPhieuNhapModel = new ChiTietPhieuNhap($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);
    }

    public function index()
    {
        $phieuNhap = $this->phieuNhapModel->readAll();
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $content = 'views/phieu_nhap/index.php';
        include('views/layouts/base.php');
    }

    public function create()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->processCreateForm();
    } else {
        $this->showCreateForm();
    }
}

private function showCreateForm()
{
    $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
    $tai_san_list = $this->taiSanModel->read(); // Lấy tất cả tài sản
    $content = 'views/phieu_nhap/create.php';
    include('views/layouts/base.php');
}

    private function processCreateForm()
    {
        $this->db->beginTransaction();
        try {
            $phieuNhapId = $this->createPhieuNhap();
            $this->createChiTietPhieuNhap($phieuNhapId);
            
            $this->db->commit();
            $_SESSION['message'] = 'Tạo phiếu nhập mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=create");
            exit();
        }
    }

    private function createPhieuNhap()
    {
        $this->phieuNhapModel->user_id = $_SESSION['user_id'];
        $this->phieuNhapModel->ngay_tao = $_POST['ngay_tao'];
        $this->phieuNhapModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuNhapModel->trang_thai = 'DangChoPheDuyet';
        return $this->phieuNhapModel->create();
    }

    private function createChiTietPhieuNhap($phieuNhapId)
    {
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId)) continue;  // Bỏ qua nếu không có tài sản được chọn

            $this->chiTietPhieuNhapModel->phieu_nhap_tai_san_id = $phieuNhapId;
            $this->chiTietPhieuNhapModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuNhapModel->so_luong = $_POST['so_luong'][$index];
            $chiTietId = $this->chiTietPhieuNhapModel->create();

            $this->viTriChiTietModel->vi_tri_id = 1; 
            $this->viTriChiTietModel->so_luong = 0;
            $this->viTriChiTietModel->tai_san_id = $taiSanId;
            $this->viTriChiTietModel->create();
        }
    }

    public function getByLoai()
    {
        if (isset($_GET['loai_id'])) {
            $loai_id = $_GET['loai_id'];
            $taiSanList = $this->taiSanModel->readByLoaiId($loai_id);
            header('Content-Type: application/json');
            echo json_encode($taiSanList);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Missing loai_id parameter"));
        }
    }
    public function edit($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEditForm($id);
        } else {
            $this->showEditForm($id);
        }
    }

    public function showEditForm($id)
    {
        $phieuNhap = $this->phieuNhapModel->readById($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }

        // Sửa đổi truy vấn này để lấy thêm thông tin về loại tài sản và tên tài sản
        $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);
        
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        // var_dump($chiTietPhieuNhap);
        // exit();
        $content = 'views/phieu_nhap/edit.php';
        include('views/layouts/base.php');
    }

    private function processEditForm($id)
    {
        $this->db->beginTransaction();
        try {
            $this->updatePhieuNhap($id);
            $this->updateChiTietPhieuNhap($id);
            
            $this->db->commit();
            $_SESSION['message'] = 'Cập nhật phiếu nhập thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=edit&id=" . $id);
            exit();
        }
    }

    private function updatePhieuNhap($id)
    {
        $this->phieuNhapModel->phieu_nhap_tai_san_id = $id;
        $this->phieuNhapModel->ngay_nhap = $_POST['ngay_nhap'];
        $this->phieuNhapModel->ngay_xac_nhan = $_POST['ngay_xac_nhan'];
        $this->phieuNhapModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuNhapModel->trang_thai = 'DangChoPheDuyet';
        $this->phieuNhapModel->update();
    }

    private function updateChiTietPhieuNhap($phieuNhapId)
    {
        // Delete existing chi tiết
        $this->chiTietPhieuNhapModel->deleteByPhieuNhapId($phieuNhapId);
        
        // Create new chi tiết
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId)) continue;

            $this->chiTietPhieuNhapModel->phieu_nhap_tai_san_id = $phieuNhapId;
            $this->chiTietPhieuNhapModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuNhapModel->so_luong = $_POST['so_luong'][$index];
            $this->chiTietPhieuNhapModel->create();

            $this->viTriChiTietModel->vi_tri_id = 1; 
            $this->viTriChiTietModel->so_luong = 0;
            $this->viTriChiTietModel->tai_san_id = $taiSanId;
            $this->viTriChiTietModel->create();
        }
    }
    public function show($id)
    {
        $phieuNhap = $this->phieuNhapModel->readById($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }
        $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);
        
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_nhap/show.php';
        include('views/layouts/base.php');
    }
    public function delete($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        $this->db->beginTransaction();
        try {
            // Xóa chi tiết phiếu nhập trước
            $this->chiTietPhieuNhapModel->deleteByPhieuNhapId($id);

            // Xóa phiếu nhập
            $this->phieuNhapModel->delete($id);

            $this->db->commit();
            $_SESSION['message'] = 'Xóa phiếu nhập thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        }
    }
    public function xet_duyet($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            if ($action == 'approve') {
                $this->phieuNhapModel->trang_thai = 'DaPheDuyet';
            } elseif ($action == 'reject') {
                $this->phieuNhapModel->trang_thai = 'KhongDuyet';
            }
            
            $this->phieuNhapModel->ngay_xac_nhan = date('Y-m-d');
            $this->phieuNhapModel->phieu_nhap_tai_san_id=$id;
            $this->phieuNhapModel->updateStatus();
            $_SESSION['message'] = 'Cập nhật thông tin thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        }
        else{
            $phieuNhap = $this->phieuNhapModel->readById($id);
            if (!$phieuNhap) {
                die('Phiếu nhập không tồn tại.');
            }
            $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);
            $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
            $tai_san_list = $this->taiSanModel->read();
            $content = 'views/phieu_nhap/xet_duyet.php';
            include('views/layouts/base.php');
        }
    }
    public function nhap_tai_san($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->phieuNhapModel->phieu_nhap_tai_san_id=$id;
            $this->processNhapTaiSan($id);
        } else {
            $this->showNhapTaiSanForm($id);
        }
    }

    private function showNhapTaiSanForm($id)
    {
        $phieuNhap = $this->phieuNhapModel->readById($id);
        
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }
        $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_nhap/nhap_tai_san.php';
        include('views/layouts/base.php');
    }

    private function processNhapTaiSan($id)
    {
        $this->db->beginTransaction();
        try {
            $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readByPhieuNhapId($id);
            foreach ($chiTietPhieuNhap as $chiTiet) {

                // Cập nhật số lượng trong bảng vi_tri_chi_tiet
                $viTriChiTiet = $this->viTriChiTietModel->readByTaiSanAndViTri($chiTiet['tai_san_id'], 1);
                if ($viTriChiTiet) {
                    $newViTriQuantity = $viTriChiTiet['so_luong'] + $chiTiet['so_luong'];
                    $this->viTriChiTietModel->updateQuantity($viTriChiTiet['vi_tri_chi_tiet_id'], $newViTriQuantity);
                } else {
                    $this->viTriChiTietModel->vi_tri_id=1;
                    $this->viTriChiTietModel->tai_san_id=$chiTiet['tai_san_id'];
                    $this->viTriChiTietModel->so_luong=$chiTiet['so_luong'];
                    $this->viTriChiTietModel->create();
                }
            }
            $this->phieuNhapModel->trang_thai='DaNhap';
            // var_dump($this->phieuNhapModel);
            // exit();
            $this->phieuNhapModel->updateStatus();

            $this->db->commit();
            $_SESSION['message'] = 'Nhập tài sản thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=nhap_tai_san&id=" . $id);
            exit();
        }
    }
}