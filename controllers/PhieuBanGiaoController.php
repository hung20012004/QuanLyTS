<?php
include_once 'config/database.php';
include_once 'models/PhieuBanGiao.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/PhieuBanGiaoChiTiet.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTri.php';
include_once 'models/User.php';

class PhieuBanGiaoController extends Controller
{
    private $db;
    private $loaiTaiSanModel;
    private $phieuBanGiaoModel;
    private $phieuBanGiaoChiTietModel;
    private $taiSanModel;
    private $viTriModel;
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuBanGiaoModel = new PhieuBanGiao($this->db);
        $this->phieuBanGiaoChiTietModel = new PhieuBanGiaoChiTiet($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriModel = new ViTri($this->db);
        $this->userModel = new User($this->db);
        $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
    }

    public function index()
    {
        $phieuBanGiao = $this->phieuBanGiaoModel->readAll();
        $content = 'views/phieu_ban_giao/index.php';
        include ('views/layouts/base.php');
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
        $user_nhan_id = $_SESSION['user_id'] ?? $_GET['user_id'];
        $user_nhan = $this->userModel->readById($user_nhan_id);
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $vi_tri_list = $this->viTriModel->readByKhoa($user_nhan['khoa']);
        $tai_san_list = $this->taiSanModel->read();

        $content = 'views/phieu_ban_giao/create.php';
        include ('views/layouts/base.php');
    }

    private function processCreateForm()
    {
        $this->db->beginTransaction();
        try {
            $phieuBanGiaoId = $this->createPhieuBanGiao();
            $this->createChiTietPhieuBanGiao($phieuBanGiaoId);

            $this->db->commit();
            $_SESSION['message'] = 'Tạo phiếu bàn giao mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieubangiao&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieubangiao&action=create");
            exit();
        }
    }

    private function createPhieuBanGiao()
    {
        $this->phieuBanGiaoModel->user_ban_giao_id = $_SESSION['user_id'];
        $this->phieuBanGiaoModel->user_nhan_id = $_POST['user_nhan_id'];
        $this->phieuBanGiaoModel->user_duyet_id = null;
        $this->phieuBanGiaoModel->vi_tri_id = $_POST['vi_tri_id'];
        $this->phieuBanGiaoModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuBanGiaoModel->ngay_gui = date('Y-m-d');
        $this->phieuBanGiaoModel->trang_thai = 'DaGui';
        return $this->phieuBanGiaoModel->create();
    }

    private function createChiTietPhieuBanGiao($phieuBanGiaoId)
    {
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId))
                continue;

            $this->phieuBanGiaoChiTietModel->phieu_ban_giao_id = $phieuBanGiaoId;
            $this->phieuBanGiaoChiTietModel->tai_san_id = $taiSanId;
            $this->phieuBanGiaoChiTietModel->so_luong = $_POST['so_luong'][$index];
            $this->phieuBanGiaoChiTietModel->tinh_trang = 'Moi';
            $this->phieuBanGiaoChiTietModel->create();
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEditForm($id);
        } else {
            $this->showEditForm($id);
        }
    }

    private function showEditForm($id)
    {
        $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
        if (!$phieuBanGiao) {
            die('Phiếu bàn giao không tồn tại.');
        }

        $chiTietPhieuBanGiao = $this->phieuBanGiaoChiTietModel->readByPhieuBanGiaoId($id);
        $user_nhan = $this->userModel->readById($phieuBanGiao['user_nhan_id']);
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $vi_tri_list = $this->viTriModel->readByKhoa($user_nhan['khoa']);
        $tai_san_list = $this->taiSanModel->read();

        // Lấy thông tin chi tiết về tài sản và loại tài sản
        $chiTietPhieuBanGiaoWithDetails = array();
        foreach ($chiTietPhieuBanGiao as $chiTiet) {
            $taiSan = $this->taiSanModel->readById($chiTiet['tai_san_id']);
            $chiTiet['ten_tai_san'] = $taiSan['ten_tai_san'];
            $chiTiet['loai_tai_san_id'] = $taiSan['loai_tai_san_id'];
            $chiTietPhieuBanGiaoWithDetails[] = $chiTiet;
        }

        $content = 'views/phieu_ban_giao/edit.php';
        include ('views/layouts/base.php');
    }

    private function processEditForm($id)
    {
        $this->db->beginTransaction();
        try {
            $this->updatePhieuBanGiao($id);
            $this->updateChiTietPhieuBanGiao($id);

            $this->db->commit();
            $_SESSION['message'] = 'Cập nhật phiếu bàn giao thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieubangiao&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieubangiao&action=edit&id=$id");
            exit();
        }
    }

    private function updatePhieuBanGiao($id)
    {
        $this->phieuBanGiaoModel->user_ban_giao_id = $_SESSION['user_id'];
        $this->phieuBanGiaoModel->user_nhan_id = $_POST['user_nhan_id'];
        $this->phieuBanGiaoModel->vi_tri_id = $_POST['vi_tri_id'];
        $this->phieuBanGiaoModel->trang_thai = 'DaGui';
        $this->phieuBanGiaoModel->ngay_gui = date('Y-m-d');
        $this->phieuBanGiaoModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuBanGiaoModel->phieu_ban_giao_id = $id;
        $this->phieuBanGiaoModel->update();
    }

    private function updateChiTietPhieuBanGiao($id)
    {
        // Xóa chi tiết cũ
        $this->phieuBanGiaoChiTietModel->deleteByPhieuBanGiaoId($id);

        // Thêm chi tiết mới
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId))
                continue;

            $this->phieuBanGiaoChiTietModel->phieu_ban_giao_id = $id;
            $this->phieuBanGiaoChiTietModel->tai_san_id = $taiSanId;
            $this->phieuBanGiaoChiTietModel->so_luong = $_POST['so_luong'][$index];
            $this->phieuBanGiaoChiTietModel->tinh_trang = '';
            $this->phieuBanGiaoChiTietModel->create();
        }
    }
    public function xet_duyet($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            if ($action == 'approve') {
                $this->phieuBanGiaoModel->trang_thai = 'DaPheDuyet';
                $this->phieuBanGiaoModel->ngay_duyet = date('Y-m-d');
            } elseif ($action == 'reject') {
                $this->phieuBanGiaoModel->trang_thai = 'KhongDuyet';
                $this->phieuBanGiaoModel->ngay_duyet = date('Y-m-d');
            }
            $this->phieuBanGiaoModel->phieu_ban_giao_id = $id;
            $this->phieuBanGiaoModel->update();
            header("Location: index.php?model=phieubangiao&action=index");
            exit();
        } else {
            $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
            if (!$phieuBanGiao) {
                die('Phiếu bàn giao không tồn tại.');
            }

            $user_duyet = $this->userModel->readById($_SESSION['user_id']);

            $content = 'views/phieu_ban_giao/xet_duyet.php';
            include ('views/layouts/base.php');
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->phieuBanGiaoChiTietModel->deleteByPhieuBanGiaoId($id);
            $this->phieuBanGiaoModel->delete($id);
            $_SESSION['message'] = 'Xóa phiếu bàn giao thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieubangiao&action=index");
            exit();
        } else {
            $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
            if (!$phieuBanGiao) {
                die('Phiếu bàn giao không tồn tại.');
            }

            $content = 'views/phieu_ban_giao/delete.php';
            include ('views/layouts/base.php');
        }
    }
    public function show($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing ID.');
        }

        // Fetch the main Phieu Ban Giao data
        $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
        if (!$phieuBanGiao) {
            die('Phiếu bàn giao không tồn tại.');
        }

        // Fetch related data
        $nguoiNhan = $this->userModel->readById($phieuBanGiao['user_nhan_id']);
        $nguoiBanGiao = $this->userModel->readById($phieuBanGiao['user_ban_giao_id']);
        $nguoiDuyet = $phieuBanGiao['user_duyet_id'] ? $this->userModel->readById($phieuBanGiao['user_duyet_id']) : null;
        $viTri = $this->viTriModel->readById($phieuBanGiao['vi_tri_id']);

        // Fetch Chi Tiet Phieu Ban Giao
        $chiTietPhieuBanGiao = $this->phieuBanGiaoChiTietModel->readByPhieuBanGiaoId($id);

        // Fetch additional data for each Chi Tiet
        $chiTietWithAdditionalData = array();
        foreach ($chiTietPhieuBanGiao as $chiTiet) {
            $taiSan = $this->taiSanModel->readById($chiTiet['tai_san_id']);
            $loaiTaiSan = $this->loaiTaiSanModel->readById($taiSan['loai_tai_san_id']);
            $chiTiet['ten_tai_san'] = $taiSan['ten_tai_san'];
            $chiTiet['ten_loai_tai_san'] = $loaiTaiSan['ten_loai_tai_san'];
            $chiTietWithAdditionalData[] = $chiTiet;
        }

        // Load the view
        $content = 'views/phieu_ban_giao/show.php';
        include ('views/layouts/base.php');
    }
    
}
?>