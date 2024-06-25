<?php
include_once 'config/database.php';
include_once 'models/LoaiTaiSan.php';

class LoaiTaiSanController extends Controller {
    private $db;
    private $loaiTaiSan;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->loaiTaiSan = new LoaiTaiSan($this->db);
    }

    public function index() {
        $stmt = $this->loaiTaiSan->read();
        $loaiTaiSans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/loaitaisans/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->loaiTaiSan->ten_loai_tai_san = $_POST['ten_loai_tai_san'];
            if ($this->loaiTaiSan->create()) {
                $_SESSION['message'] = 'Tạo loại tài sản mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=loaitaisan");
            } else {
                $_SESSION['message'] = 'Tạo mới thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/loaitaisans/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->loaiTaiSan->loai_tai_san_id = $id;
            $this->loaiTaiSan->ten_loai_tai_san = $_POST['ten_loai_tai_san'];
            
            if ($this->loaiTaiSan->update()) {
                $_SESSION['message'] = 'Sửa loại tài sản thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=loaitaisan");
            } else {
                $_SESSION['message'] = 'Sửa thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
            
        } else {
            $loaiTaiSan = $this->loaiTaiSan->readById($id);
            $content = 'views/loaitaisans/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
        if ($this->loaiTaiSan->delete($id)) {
            $_SESSION['message'] = 'Xóa loại tài sản thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=loaitaisan");
        } else {
            $_SESSION['message'] = 'Xóa thất bại!';
            $_SESSION['message_type'] = 'danger';
        }
    }
}
?>
