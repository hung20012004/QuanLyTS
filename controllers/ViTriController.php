<?php
include_once 'config/database.php';
include_once 'models/ViTri.php';

class ViTriController extends Controller {
    private $db;
    private $viTri;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->viTri = new ViTri($this->db);
    }

    public function index() {
        $stmt = $this->viTri->read();
        $viTris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/vitris/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
            if ($this->viTri->create()) {
                $_SESSION['message'] = 'Tạo vị trí mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=vitri");
                exit();
            } else {
                $_SESSION['message'] = 'Tạo mới thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/vitris/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->viTri->vi_tri_id = $id;
            $this->viTri->ten_vi_tri = $_POST['ten_vi_tri'];
            
            if ($this->viTri->update()) {
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
            $content = 'views/vitris/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
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
