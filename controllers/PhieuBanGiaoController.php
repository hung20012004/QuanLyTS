<?php
include_once 'config/database.php';
include_once 'models/PhieuBanGiao.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ViTriChiTiet.php';
include_once 'models/PhieuBanGiaoChiTiet.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTri.php';
include_once 'models/User.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PhieuBanGiaoController extends Controller
{
    private $db;
    private $loaiTaiSanModel;
    private $phieuBanGiaoModel;
    private $phieuBanGiaoChiTietModel;
    private $taiSanModel;
    private $viTriModel;
    private $userModel;
    private $viTriChiTietModel;

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
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);

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
        $this->phieuBanGiaoModel->user_nhan_id = $_POST['user_nhan_id'];
        $this->phieuBanGiaoModel->user_duyet_id = null;
        $this->phieuBanGiaoModel->vi_tri_id = $_POST['vi_tri_id'];
        $this->phieuBanGiaoModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuBanGiaoModel->ngay_gui = date('Y-m-d');
        $this->phieuBanGiaoModel->trang_thai = 'DaGui';
        // var_dump($this->phieuBanGiaoModel);
        // exit();
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
            $this->phieuBanGiaoChiTietModel->tinh_trang = '';
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
            $ghi_chu_duyet = isset($_POST['ghi_chu_duyet']) ? $_POST['ghi_chu_duyet'] : '';

            if ($action == 'approve') {
                $this->phieuBanGiaoModel->trang_thai = 'DaPheDuyet';
            } elseif ($action == 'reject') {
                $this->phieuBanGiaoModel->trang_thai = 'KhongDuyet';
            }

            $this->phieuBanGiaoModel->ngay_duyet = date('Y-m-d');
            $this->phieuBanGiaoModel->user_duyet_id = $_SESSION['user_id'];
            $this->phieuBanGiaoModel->user_ban_giao_id = $_POST['nguoiBanGiao'];
            $this->phieuBanGiaoModel->ngay_kiem_tra = $_POST['ngayKiemTra'];
            $this->phieuBanGiaoModel->phieu_ban_giao_id = $id;
            // var_dump($this->phieuBanGiaoModel);
            // exit();
            $this->phieuBanGiaoModel->updateStatus();

            header("Location: index.php?model=phieubangiao&action=index");
            exit();
        } else {
            $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
            if (!$phieuBanGiao) {
                die('Phiếu bàn giao không tồn tại.');
            }

            $chiTietPhieuBanGiao = $this->phieuBanGiaoChiTietModel->readByPhieuBanGiaoId($id);
            $chiTietWithAdditionalData = array();
            foreach ($chiTietPhieuBanGiao as $chiTiet) {
                $taiSan = $this->taiSanModel->readById($chiTiet['tai_san_id']);
                $loaiTaiSan = $this->loaiTaiSanModel->readById($taiSan['loai_tai_san_id']);
                $chiTiet['ten_tai_san'] = $taiSan['ten_tai_san'];
                $chiTiet['ten_loai_tai_san'] = $loaiTaiSan['ten_loai_tai_san'];
                $chiTietWithAdditionalData[] = $chiTiet;
            }

            $user_duyet = $this->userModel->readById($_SESSION['user_id']);
            $nguoiNhan = $this->userModel->readById($phieuBanGiao['user_nhan_id']);
            $nguoiBanGiao = $this->userModel->readById($phieuBanGiao['user_ban_giao_id']);
            $viTri = $this->viTriModel->readById($phieuBanGiao['vi_tri_id']);

            $content = 'views/phieu_ban_giao/xet_duyet.php';
            include ('views/layouts/base.php');
        }
    }

    public function delete($id)
    {
        // Kiểm tra xem phiếu bàn giao có tồn tại không
        $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
        if (!$phieuBanGiao) {
            $_SESSION['message'] = 'Phiếu bàn giao không tồn tại.';
            $_SESSION['message_type'] = 'error';
            header("Location: index.php?model=phieubangiao&action=index");
            exit();
        }

        // Kiểm tra quyền của người dùng
        if ($_SESSION['role'] != 'NhanVien' || $phieuBanGiao['trang_thai'] != 'DaGui') {
            $_SESSION['message'] = 'Bạn không có quyền xóa phiếu bàn giao này.';
            $_SESSION['message_type'] = 'error';
            header("Location: index.php?model=phieubangiao&action=index");
            exit();
        }
        try {

            $this->phieuBanGiaoChiTietModel->deleteByPhieuBanGiaoId($id);
            $this->phieuBanGiaoModel->delete($id);


            $_SESSION['message'] = 'Xóa phiếu bàn giao thành công!';
            $_SESSION['message_type'] = 'success';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Có lỗi xảy ra khi xóa phiếu bàn giao. Vui lòng thử lại.';
            $_SESSION['message_type'] = 'error';
        }

        header("Location: index.php?model=phieubangiao&action=index");
        exit();
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
    public function ban_giao($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing ID.');
        }

        $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
        if (!$phieuBanGiao) {
            die('Phiếu bàn giao không tồn tại.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý form submission
            $transactionStarted = false;
            try {
                $this->db->beginTransaction();
                $transactionStarted = true;

                // Cập nhật trạng thái phiếu bàn giao
                $this->phieuBanGiaoModel->phieu_ban_giao_id = $id;
                $this->phieuBanGiaoModel->trang_thai = 'DaGiao';
                $this->phieuBanGiaoModel->ngay_ban_giao = date('Y-m-d');
                $this->phieuBanGiaoModel->updateStatus2();

                // Cập nhật số lượng tài sản tại vị trí
                $chiTietPhieuBanGiao = $this->phieuBanGiaoChiTietModel->readByPhieuBanGiaoId($id);
                foreach ($chiTietPhieuBanGiao as $chiTiet) {
                    $this->viTriChiTietModel->updateSoLuong(
                        $phieuBanGiao['vi_tri_id'],
                        $chiTiet['tai_san_id'],
                        $chiTiet['so_luong']
                    );
                }

                $this->db->commit();
                $_SESSION['message'] = 'Bàn giao tài sản thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=phieubangiao&action=show&id=$id");
                exit();
            } catch (Exception $e) {
                if ($transactionStarted) {
                    $this->db->rollBack();
                }
                $_SESSION['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
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
        $content = 'views/phieu_ban_giao/ban_giao.php';
        include ('views/layouts/base.php');
    }
    public function kiem_tra($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processKiemTraForm($id);
        } else {
            $this->showKiemTraForm($id);
        }
    }

    private function showKiemTraForm($id)
    {
        $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
        if (!$phieuBanGiao || $phieuBanGiao['trang_thai'] !== 'DaGui') {
            die('Phiếu bàn giao không tồn tại hoặc không ở trạng thái chờ kiểm tra.');
        }

        $nguoiNhan = $this->userModel->readById($phieuBanGiao['user_nhan_id']);
        $viTri = $this->viTriModel->readById($phieuBanGiao['vi_tri_id']);
        $chiTietPhieuBanGiao = $this->phieuBanGiaoChiTietModel->readByPhieuBanGiaoId($id);

        $chiTietWithAdditionalData = array();
        foreach ($chiTietPhieuBanGiao as $chiTiet) {
            $taiSan = $this->taiSanModel->readById($chiTiet['tai_san_id']);
            $loaiTaiSan = $this->loaiTaiSanModel->readById($taiSan['loai_tai_san_id']);
            $chiTiet['ten_tai_san'] = $taiSan['ten_tai_san'];
            $chiTiet['ten_loai_tai_san'] = $loaiTaiSan['ten_loai_tai_san'];
            $chiTiet['so_luong_trong_kho'] = $this->viTriChiTietModel->getSoLuongTrongKho(1, $chiTiet['tai_san_id']);
            $chiTietWithAdditionalData[] = $chiTiet;
        }


        $content = 'views/phieu_ban_giao/kiem_tra.php';
        include ('views/layouts/base.php');
    }

    private function processKiemTraForm($id)
    {
        $this->phieuBanGiaoModel->user_ban_giao_id = $_SESSION['user_id'];
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'gui') {
                // Kiểm tra số lượng tài sản
                $chiTietPhieuBanGiao = $this->phieuBanGiaoChiTietModel->readByPhieuBanGiaoId($id);
                foreach ($chiTietPhieuBanGiao as $chiTiet) {
                    $soLuongTrongKho = $this->viTriChiTietModel->getSoLuongTrongKho(1, $chiTiet['tai_san_id']);
                    if ($chiTiet['so_luong'] > $soLuongTrongKho) {
                        $_SESSION['message'] = 'Phiếu gửi không hợp lệ. Số lượng yêu cầu vượt quá số lượng trong kho.';
                        $_SESSION['message_type'] = 'danger';
                        header("Location: index.php?model=phieubangiao&action=kiem_tra&id=$id");
                        exit();
                    }
                }

                // Cập nhật trạng thái phiếu
                $this->phieuBanGiaoModel->phieu_ban_giao_id = $id;
                $this->phieuBanGiaoModel->trang_thai = 'DangChoPheDuyet';
                $this->phieuBanGiaoModel->ngay_kiem_tra = date('Y-m-d');
                $this->phieuBanGiaoModel->updateStatus();

                $_SESSION['message'] = 'Kiểm tra phiếu thành công. Phiếu đã chuyển sang trạng thái chờ phê duyệt.';
                $_SESSION['message_type'] = 'success';
            } elseif ($_POST['action'] === 'huy') {
                // Cập nhật trạng thái phiếu thành 'DaHuy'
                $this->phieuBanGiaoModel->phieu_ban_giao_id = $id;
                $this->phieuBanGiaoModel->ngay_kiem_tra = date('Y-m-d');
                $this->phieuBanGiaoModel->trang_thai = 'DaHuy';
                $this->phieuBanGiaoModel->updateStatus();

                $_SESSION['message'] = 'Phiếu đã được hủy.';
                $_SESSION['message_type'] = 'warning';
            }
        }

        header("Location: index.php?model=phieubangiao&action=index");
        exit();
    }
    public function exportWord($id)
    {
        require 'vendor/autoload.php';

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Fetch data
        $phieuBanGiao = $this->phieuBanGiaoModel->readById($id);
        if (!$phieuBanGiao) {
            die('Phiếu bàn giao không tồn tại.');
        }

        $nguoiNhan = $this->userModel->readById($phieuBanGiao['user_nhan_id']);
        $nguoiBanGiao = $this->userModel->readById($phieuBanGiao['user_ban_giao_id']);
        $viTri = $this->viTriModel->readById($phieuBanGiao['vi_tri_id']);

        $chiTietPhieuBanGiao = $this->phieuBanGiaoChiTietModel->readByPhieuBanGiaoId($id);

        // Add a section to the document
        $section = $phpWord->addSection();

        // Add title
        $section->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('Độc lập - Tự do - Hạnh Phúc', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('---***---', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);
        $section->addText('BIÊN BẢN BÀN GIAO TÀI SẢN, CÔNG CỤ', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);

        // Add general information
        $section->addText('Hôm nay, ngày ' . date('d/m/Y', strtotime($phieuBanGiao['ngay_ban_giao'])) . ' tại ' . $viTri['ten_vi_tri'] . ', chúng tôi gồm:');
        $section->addText('Người bàn giao: ' . $nguoiBanGiao['ten'] . ', Khoa: ' . $nguoiBanGiao['khoa'] . ', MSNV: ' . $nguoiBanGiao['user_id']);
        $section->addText('Người nhận bàn giao: ' . $nguoiNhan['ten'] . ', Khoa: ' . $nguoiNhan['khoa'] . ', MSNV: ' . $nguoiNhan['user_id']);
        $section->addText('Lý do bàn giao: ' . $phieuBanGiao['ghi_chu']);
        $section->addTextBreak(1);
        $section->addText('Cùng bàn giao tài sản, công cụ với nội dung như sau:');

        // Add table of assets
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(500)->addText('Stt', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table->addCell(2000)->addText('Mã tài sản, công cụ', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table->addCell(3000)->addText('Tên tài sản, công cụ', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table->addCell(1000)->addText('Đơn vị', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table->addCell(1000)->addText('Số lượng', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
    

        foreach ($chiTietPhieuBanGiao as $index => $chiTiet) {
            $taiSan = $this->taiSanModel->readById($chiTiet['tai_san_id']);
            $table->addRow();
            $table->addCell(500)->addText($index + 1, [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(2000)->addText($taiSan['tai_san_id'], [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(3000)->addText($taiSan['ten_tai_san']);
            $table->addCell(1000)->addText('Cái', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(1000)->addText($chiTiet['so_luong'], [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            
        }

        // Save file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'BienBanBanGiao_' . $id . '.docx';
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $objWriter->save("php://output");
        exit;
    }
    public function export()
    {
        // Fetch data to be exported
        $phieuBanGiaoList = $this->phieuBanGiaoModel->readAll();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setTitle('Danh sách Phiếu Bàn Giao');
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Phiếu bàn giao tài sản');
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ]);
        // Set the header of the columns and apply styles
        $headers = ['Mã số phiếu', 'Ngày tạo phiếu', 'Ngày kiểm tra', 'Ngày phê duyệt', 'Ngày bàn giao', 'Trạng thái'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:F3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Populate the spreadsheet with data
        $row = 4;
        foreach ($phieuBanGiaoList as $phieu) {
            if (
                ($phieu['user_nhan_id'] == $_SESSION['user_id'] && $_SESSION['role'] == 'NhanVien')
                || ($phieu['user_ban_giao_id'] == $_SESSION['user_id'] && $_SESSION['role'] == 'NhanVienQuanLy')
                || ($phieu['user_ban_giao_id'] == '' && $_SESSION['role'] == 'NhanVienQuanLy')
                || ($_SESSION['role'] == 'QuanLy' && ($phieu['trang_thai'] == 'DaBanGiao' || $phieu['trang_thai'] == 'DangChoPheDuyet' || $phieu['trang_thai'] == 'KhongDuyet' || $phieu['trang_thai'] == 'DaPheDuyet'))
            ) {
                $sheet->setCellValue('A' . $row, $phieu['phieu_ban_giao_id']);
                $sheet->setCellValue('B' . $row, !empty($phieu['ngay_gui']) ? date('d-m-Y', strtotime($phieu['ngay_gui'])) : '');
                $sheet->setCellValue('C' . $row, !empty($phieu['ngay_kiem_tra']) ? date('d-m-Y', strtotime($phieu['ngay_kiem_tra'])) : '');
                $sheet->setCellValue('D' . $row, !empty($phieu['ngay_duyet']) ? date('d-m-Y', strtotime($phieu['ngay_duyet'])) : '');
                $sheet->setCellValue('E' . $row, !empty($phieu['ngay_ban_giao']) ? date('d-m-Y', strtotime($phieu['ngay_ban_giao'])) : '');
                $sheet->setCellValue('F' . $row, $this->getTrangThaiText($phieu['trang_thai']));

                // Apply border to non-header rows
                $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $row++;
            }
        }

        // Set auto column width
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Create a writer and save the file to the server temporarily
        $fileName = 'phieu_ban_giao.xlsx';
        $filePath = __DIR__ . '/' . $fileName; // Save in the current directory of the project
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Return the file as a download
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            unlink($filePath); // Delete temporary file after download
            exit;
        } else {
            echo "File không tồn tại.";
        }
    }

    private function getTrangThaiText($trang_thai)
    {
        switch ($trang_thai) {
            case 'DaGui':
                return 'Đã gửi';
            case 'DaKiemTra':
                return 'Đã kiểm tra';
            case 'DangChoPheDuyet':
                return 'Đang chờ phê duyệt';
            case 'DaPheDuyet':
                return 'Đã phê duyệt';
            case 'DaGiao':
                return 'Đã giao';
            case 'KhongDuyet':
                return 'Không duyệt';
            default:
                return $trang_thai;
        }
    }



}
?>