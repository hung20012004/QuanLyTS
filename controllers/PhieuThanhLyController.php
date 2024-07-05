<?php
include_once 'config/database.php';
include_once 'models/PhieuThanhLy.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ChiTietPhieuThanhLy.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTriChiTiet.php';

class PhieuThanhLyController extends Controller
{
    private $db;
    private $phieuThanhLyModel;
    private $loaiTaiSanModel;
    private $chiTietPhieuThanhLyModel;
    private $taiSanModel;
    private $viTriChiTietModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuThanhLyModel = new PhieuThanhLy($this->db);
        // $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
        $this->chiTietPhieuThanhLyModel = new ChiTietPhieuThanhLy($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);
    }

    public function index()
    {
        $phieuThanhLy = $this->phieuThanhLyModel->readAll();
        $content = 'views/phieu_thanh_ly/index.php';
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
    // $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
    $tai_san_list = $this->phieuThanhLyModel->readTai_San(); // Lấy tất cả tài sản
    $content = 'views/phieu_thanh_ly/create.php';
    include('views/layouts/base.php');
}

    private function processCreateForm()
    {
        $this->db->beginTransaction();
        try {
            $phieuThanhLyId = $this->createPhieuThanhLy();
            $this->createChiTietPhieuThanhLy($phieuThanhLyId);
            
            $this->db->commit();
            $_SESSION['message'] = 'Tạo phiếu nhập mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieuthanhly&action=create");
            exit();
        }
    }

    private function createPhieuThanhLy()
    {
        $this->phieuThanhLyModel->user_id = $_SESSION['user_id'];
        $this->phieuThanhLyModel->ngay_tao = $_POST['ngay_tao'];
        $this->phieuThanhLyModel->ngay_xac_nhan = $_POST['ngay_xac_nhan'];
        $this->phieuThanhLyModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuThanhLyModel->trang_thai = 'DangChoPheDuyet';
        return $this->phieuThanhLyModel->create();
    }

    private function createChiTietPhieuThanhLy($phieuThanhLyId)
    {
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId)) continue;  // Bỏ qua nếu không có tài sản được chọn

            $this->chiTietPhieuThanhLyModel->phieu_thanh_ly_id = $phieuThanhLyId;
            $this->chiTietPhieuThanhLyModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuThanhLyModel->so_luong = $_POST['so_luong'][$index];
            $chiTietId = $this->chiTietPhieuThanhLyModel->create();

            $this->viTriChiTietModel->vi_tri_id = 1; // Giả sử vị trí mặc định là 1
            $this->viTriChiTietModel->so_luong = $_POST['so_luong'][$index];
            $this->viTriChiTietModel->tai_san_id = $taiSanId;
            $this->viTriChiTietModel->create();
        }
    }

    // public function getByLoai()
    // {
    //     if (isset($_GET['loai_id'])) {
    //         $loai_id = $_GET['loai_id'];
    //         $taiSanList = $this->taiSanModel->readByLoaiId($loai_id);
    //         header('Content-Type: application/json');
    //         echo json_encode($taiSanList);
    //     } else {
    //         http_response_code(400);
    //         echo json_encode(array("message" => "Missing loai_id parameter"));
    //     }
    // }
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
        $phieuNhap = $this->phieuThanhLyModel->readById($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }

        // Sửa đổi truy vấn này để lấy thêm thông tin về loại tài sản và tên tài sản
        $chiTietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
        $tai_san_list = $this->phieuThanhLyModel->readTai_San();
        // var_dump($chiTietPhieuNhap);
        // exit();
        $content = 'views/phieu_thanh_ly/edit.php';
        include('views/layouts/base.php');
    }

    private function processEditForm($id)
    {
        $this->db->beginTransaction();
        try {
            $this->updatePhieuThanhLy($id);
            $this->updateChiTietPhieuThanhLy($id);
            
            $this->db->commit();
            $_SESSION['message'] = 'Cập nhật phiếu nhập thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieuthanhly&action=edit&id=" . $id);
            exit();
        }
    }

    private function updatePhieuThanhLy($id)
    {
        $this->phieuThanhLyModel->phieu_thanh_ly_id = $id;
        $this->phieuThanhLyModel->ngay_tao = $_POST['ngay_tao'];
        $this->phieuThanhLyModel->ngay_xac_nhan = $_POST['ngay_xac_nhan'];
        $this->phieuThanhLyModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuThanhLyModel->trang_thai = 'DangChoPheDuyet';
        $this->phieuThanhLyModel->update();
    }

    private function updateChiTietPhieuThanhLy($phieuNhapId)
    {
        // Delete existing chi tiết
        $this->chiTietPhieuThanhLyModel->deleteByPhieuThanhLyId($phieuNhapId);
        
        // Create new chi tiết
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId)) continue;

            $this->chiTietPhieuThanhLyModel->phieu_thanh_ly_id= $phieuNhapId;
            $this->chiTietPhieuThanhLyModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuThanhLyModel->so_luong = $_POST['so_luong'][$index];
            $this->chiTietPhieuThanhLyModel->create();
        }
    }
    public function show($id)
    {
        $phieuThanhLy = $this->phieuThanhLyModel->readById($id);
        if (!$phieuThanhLy) {
            die('Phiếu thanh lý không tồn tại.');
        }

        $chitietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_thanh_ly/show.php';
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
            $this->chiTietPhieuThanhLyModel->deleteByPhieuThanhLyId($id);

            // Xóa phiếu nhập
            $this->phieuThanhLyModel->delete($id);

            $this->db->commit();
            $_SESSION['message'] = 'Xóa phiếu thanh lý thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        }
    }

    public function xet_duyet($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            // var_dump($action);
            // exit();
            if ($action == 'approve') {
                $this->phieuThanhLyModel->trang_thai = 'DaPheDuyet';
            } elseif ($action == 'reject') {
                $this->phieuThanhLyModel->trang_thai = 'KhongDuyet';
            }
            else{

            }
            
            $this->phieuThanhLyModel->ngay_xac_nhan = date('Y-m-d');
            // var_dump($this->phieuThanhLyModel->ngay_xac_nhan);
            // var_dump($this->phieuThanhLyModel->trang_thai);
            // exit();
            $this->phieuThanhLyModel->phieu_thanh_ly_id=$id;
            $this->phieuThanhLyModel->ghi_chu=$_POST['ghi_chu'];
            $this->phieuThanhLyModel->updateStatus();
            $_SESSION['message'] = 'Cập nhật thông tin thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        }
        else{
            $phieuNhap = $this->phieuThanhLyModel->readById($id);
            if (!$phieuNhap) {
                die('Phiếu nhập không tồn tại.');
            }
            $chitietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
            $tai_san_list = $this->taiSanModel->read();
            $content = 'views/phieu_thanh_ly/xet_duyet.php';
            include('views/layouts/base.php');
        }
    }

    public function check_sl_phe_duyet($id)
    {
        $hoa_don = $this->phieuThanhLyModel->readById($id);
    }

    public function search()
    {
        if(isset($_POST['btn_tim_kiem']))
        {
        $query = "SELECT ptl.*, u.ten AS user_name 
                  FROM phieu_thanh_ly ptl
                  LEFT JOIN users u ON ptl.user_id = u.user_id 
                  WHERE ngay_tao = ?";
        $stmt = $this->db->prepare($query);
        $ngay_tk = $_POST['ngay_tk'];
        $stmt->execute([$ngay_tk]);
        $phieuThanhLy=$stmt->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($phieuThanhLy);
        $content = 'views/phieu_thanh_ly/index.php';
        include('views/layouts/base.php');
        }
    }
}