<?php
include_once 'config/database.php';
include_once 'models/TaiSan.php';
include_once 'models/LoaiTaiSan.php';

class TaiSanController extends Controller {
    private $db;
    private $taiSan;
    private $loaiTaiSan;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->taiSan = new TaiSan($this->db);
        $this->loaiTaiSan = new LoaiTaiSan($this->db);
    }

    public function index() {
        $taiSans = $this->taiSan->read();
        $loaiTS=new LoaiTaiSan($this->db);
        $loaiTaiSans=$loaiTS->read();
        $content = 'views/taisans/index.php';
        
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->taiSan->ten_tai_san = $_POST['ten_tai_san'];
            $this->taiSan->mo_ta = $_POST['mo_ta'];
            $this->taiSan->so_luong = $_POST['so_luong'];
            $this->taiSan->loai_tai_san_id = $_POST['loai_tai_san_id'];
            if ($this->taiSan->create()) {
                $_SESSION['message'] = 'Tạo tài sản mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=taisan");
                exit();
            } else {
                $_SESSION['message'] = 'Tạo mới thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/taisans/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->taiSan->tai_san_id = $id;
            $this->taiSan->ten_tai_san = $_POST['ten_tai_san'];
            $this->taiSan->mo_ta = $_POST['mo_ta'];
            $this->taiSan->so_luong = $_POST['so_luong'];
            $this->taiSan->loai_tai_san_id = $_POST['loai_tai_san_id'];

            if ($this->taiSan->update()) {
                $_SESSION['message'] = 'Sửa tài sản thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=taisan");
                exit();
            } else {
                $_SESSION['message'] = 'Sửa tài sản thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }

        // Đọc thông tin tài sản cần sửa
        $taiSan = $this->taiSan->readById($id);

        // Đọc danh sách loại tài sản
        $stmtLoaiTaiSan = $this->loaiTaiSan->readAll();
        $loaiTaiSans = $stmtLoaiTaiSan->fetchAll(PDO::FETCH_ASSOC);

        $content = 'views/taisans/edit.php';
        include('views/layouts/base.php');
    }
}
?>
