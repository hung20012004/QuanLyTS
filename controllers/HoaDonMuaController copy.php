<?php
// controllers/HoaDonMuaController.php
include_once 'config/database.php';
include_once 'models/HoaDonMua.php';
include_once 'models/NhaCungCap.php';
include_once 'models/LoaiTaiSan.php'; // Thêm model LoaiTaiSan vào đây

class HoaDonMuaController extends Controller {
    private $db;
    private $hoaDonMuaModel;
    private $nhaCungCapModel;
    private $loaiTaiSanModel; // Đối tượng model LoaiTaiSan

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->hoaDonMuaModel = new HoaDonMua($this->db);
        $this->nhaCungCapModel = new NhaCungCap($this->db);
        $this->loaiTaiSanModel = new LoaiTaiSan($this->db); // Khởi tạo model LoaiTaiSan
    }

    public function index() {
        $invoices = $this->hoaDonMuaModel->readAll();
        $suppliers = $this->nhaCungCapModel->read();
        $content = 'views/hoa_don_mua/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->hoaDonMuaModel->ngay_mua = $_POST['ngay_mua'];
            $this->hoaDonMuaModel->tong_gia_tri = $_POST['tong_gia_tri'];
            $this->hoaDonMuaModel->nha_cung_cap_id = $_POST['nha_cung_cap_id'];
            if ($this->hoaDonMuaModel->create()) {
                $_SESSION['message'] = 'Tạo hóa đơn mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=hoa_don");
            } else {
                $_SESSION['message'] = 'Tạo mới thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }
        // Truy vấn danh sách loại tài sản từ model LoaiTaiSan
        $stmtLoaiTaiSan = $this->loaiTaiSanModel->readAll(); // Giả sử có phương thức readAll trong LoaiTaiSan model
        $suppliers = $this->nhaCungCapModel->read();
        $content = 'views/hoa_don_mua/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->hoaDonMuaModel->hoa_don_mua_id = $_POST['hoa_don_mua_id'];
            $this->hoaDonMuaModel->ngay_mua = $_POST['ngay_mua'];
            $this->hoaDonMuaModel->tong_gia_tri = $_POST['tong_gia_tri'];
            $this->hoaDonMuaModel->nha_cung_cap_id = $_POST['nha_cung_cap_id'];
            if ($this->hoaDonMuaModel->update()) {
                $_SESSION['message'] = 'Sửa hóa đơn thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=hoa_don");
            } else {
                $_SESSION['message'] = 'Sửa hóa đơn thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            $invoice = $this->hoaDonMuaModel->readById($id);
            if (!$invoice) {
                $_SESSION['message'] = 'Không tìm thấy hóa đơn!';
                $_SESSION['message_type'] = 'danger';
                header("Location: index.php?model=hoadonmua");
            }
            $suppliers = $this->nhaCungCapModel->read();
            $content = 'views/hoa_don_mua/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
        if ($this->hoaDonMuaModel->delete($id)) {
            $_SESSION['message'] = 'Xóa hóa đơn thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Xóa hóa đơn thất bại!';
            $_SESSION['message_type'] = 'danger';
        }
        header("Location: index.php?model=hoadonmua");
    }
}
?>
    