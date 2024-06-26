<?php
include_once 'config/database.php';
include_once 'models/NhaCungCap.php';

class NhaCungCapController extends Controller {
    private $db;
    private $nhaCungCap;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->nhaCungCap = new NhaCungCap($this->db);
    }

    public function index() {
        $stmt = $this->nhaCungCap->read();
        $nhaCungCaps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/nhacungcaps/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->nhaCungCap->ten_nha_cung_cap = $_POST['ten'];
            if ($this->nhaCungCap->create()) {
                header("Location: index.php?model=nhacungcap");
            }
            if ($this->nhaCungCap->create()) {
                $_SESSION['message'] = 'Tạo nhà cung cấp mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=nhacungcap");
            }else {
                $_SESSION['message'] = 'Tạo mới thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/nhacungcaps/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->nhaCungCap->nha_cung_cap_id = $id;
            $this->nhaCungCap->ten_nha_cung_cap = $_POST['ten_nha_cung_cap'];
            
            if ($this->nhaCungCap->update()) {
                $_SESSION['message'] = 'Sửa nhà cung cấp thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=nhacungcap");
            }
            else {
                $_SESSION['message'] = 'Sửa thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
            
        } else {
            $nhaCungCap = $this->nhaCungCap->readById($id);
            $content = 'views/nhacungcaps/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
        if ($this->nhaCungCap->delete($id)) {
            $_SESSION['message'] = 'Xóa nhà cung cấp thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=nhacungcap");
        }else {
            $_SESSION['message'] = 'Xóa thất bại!';
            $_SESSION['message_type'] = 'danger';
        }
    }
}
?>
