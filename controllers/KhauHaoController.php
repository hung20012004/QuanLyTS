<?php
include_once 'config/database.php';
include_once 'models/TaiSan.php';
include_once 'models/KhauHao.php';

class KhauHaoController extends Controller {
    private $db;
    private $taiSan;
    private $loaiTaiSan;
    private $khauhao;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->taiSan = new TaiSan($this->db);
        $this->loaiTaiSan = new LoaiTaiSan($this->db);
        $this->khauhao = new KhauHao($this->db);
    }

    public function index() {
        // Assuming $this->taiSan->read() returns a valid PDO statement
        $stmt = $this->taiSan->read();
        
        // Check if $stmt is a valid PDO statement
        if ($stmt) {
            // Fetch all rows as associative array
            $taiSans = $stmt;
            $content = 'views/khauhao/index.php';
            include('views/layouts/base.php');
        } else {
            // Handle error if query execution fails
            echo "Error executing query.";
        }
    }

    public function show($id)
    {
    
        $stmt = $this->khauhao->readById($id);
        $KhauHaos = $stmt;
       // Thêm dòng này để kiểm tra dữ liệu
        $ts = $id;
        $content = 'views/khauhao/show.php';
        include('views/layouts/base.php');

    }
    public function viewcreatekh($id) {
        $ts = $this->khauhao->readtaisan($id);
        $content = 'views/khauhao/create.php';
        include('views/layouts/base.php');
    }

    public function create() {

        if ($_POST) {
            $this->khauhao->tai_san_id = $_POST['tai_san_id'];
            $this->khauhao->ngay_khau_hao = $_POST['ngay_khau_hao'];
            $this->khauhao->so_tien = $_POST['so_tien'];

            if ($this->khauhao->create()) {
                $_SESSION['message'] = 'Tạo khấu hao mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=khauhao&action=index");
                exit();
            } else {
                $_SESSION['message'] = 'Tạo khấu hao thất bại!';
                $_SESSION['message_type'] = 'danger';
                header("Location: index.php?model=khauhao&action=viewcreatekh&id=".$this->khauhao->tai_san_id);
                exit();
            }
        }

        $content = 'views/khauhao/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->khauhao->khau_hao_id = $id;
            $this->khauhao->tai_san_id = $_POST['tai_san_id'];
            $this->khauhao->ngay_khau_hao = $_POST['ngay_khau_hao'];
            $this->khauhao->so_tien = $_POST['so_tien'];

            if ($this->khauhao->update()) {
                $_SESSION['message'] = 'Sửa khấu hao thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=khauhao&action=index");
                exit();
            } else {
                $_SESSION['message'] = 'Sửa khấu hao thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }

        $khau_hao = $this->khauhao->readKhAll($id);
        $khid = $id;
        $content = 'views/khauhao/edit.php';
        include('views/layouts/base.php');
    }

     public function delete($id)
     {
         if ($this->khauhao->delete($id)) {
                $_SESSION['message'] = 'Xóa thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=khauhao&action=index");
                exit();
            } else {
                $_SESSION['message'] = 'Xóa khấu hao thất bại!';
                $_SESSION['message_type'] = 'danger';
                header("Location: index.php?model=khauhao&action=viewcreatekh&id=".$this->khauhao->tai_san_id);
                exit();
            }
     }
}
?>
