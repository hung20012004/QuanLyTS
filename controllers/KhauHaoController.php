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
        $taiSans = $this->taiSan->read();
        $loaiTS=new LoaiTaiSan($this->db);
        $loaiTaiSans=$loaiTS->read();
        $content = 'views/khauhao/index.php';
        
        include('views/layouts/base.php');
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
     public function detail($id)
     {
         $viTriId = 1; // ID của vị trí chi tiết
     
         // Lấy thông tin tài sản và loại tài sản
         $queryTaiSan = "SELECT tai_san.*, loai_tai_san.ten_loai_tai_san
                         FROM tai_san
                         INNER JOIN loai_tai_san ON tai_san.loai_tai_san_id = loai_tai_san.loai_tai_san_id
                         WHERE tai_san.tai_san_id = ?";
         $stmtTaiSan = $this->db->prepare($queryTaiSan);
         $stmtTaiSan->execute([$id]);
         $taiSan = $stmtTaiSan->fetch(PDO::FETCH_ASSOC);
     
         // Truy vấn dữ liệu chi tiết từ bảng chi tiết hóa đơn mua
         $query = "SELECT chi_tiet_hoa_don_mua.*, hoa_don_mua.ngay_mua, vi_tri_chi_tiet.so_luong 
                   FROM chi_tiet_hoa_don_mua 
                   INNER JOIN hoa_don_mua ON hoa_don_mua.hoa_don_id = chi_tiet_hoa_don_mua.hoa_don_id
                   INNER JOIN vi_tri_chi_tiet ON vi_tri_chi_tiet.chi_tiet_id = chi_tiet_hoa_don_mua.chi_tiet_id
                   WHERE chi_tiet_hoa_don_mua.tai_san_id = ? AND vi_tri_chi_tiet.vi_tri_id = ?";
         
         $stmt = $this->db->prepare($query);
         $stmt->execute([$id, $viTriId]);
         $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
     
         // Truy vấn thông tin khấu hao
         $queryKhauHao = "SELECT ngay_khau_hao, so_tien
                          FROM khau_hao 
                          INNER JOIN chi_tiet_hoa_don_mua ON khau_hao.chi_tiet_id = chi_tiet_hoa_don_mua.chi_tiet_id
                          WHERE chi_tiet_hoa_don_mua.tai_san_id = ?";
         
         $stmtKhauHao = $this->db->prepare($queryKhauHao);
         $stmtKhauHao->execute([$id]);
         $khauHaos = $stmtKhauHao->fetchAll(PDO::FETCH_ASSOC);
     
         // Thực hiện các thao tác thêm hoặc sửa ở đây
     
         // Ví dụ thêm mới hoặc cập nhật thông tin
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Xử lý dữ liệu từ form POST
             // Ví dụ: 
             // $ngayMua = $_POST['ngay_mua'];
             // $soLuong = $_POST['so_luong'];
     
             // Thực hiện thêm hoặc cập nhật vào cơ sở dữ liệu
     
             // Ví dụ cập nhật thông tin ngày mua và số lượng
             /*
             $updateQuery = "UPDATE chi_tiet_hoa_don_mua 
                             SET ngay_mua = :ngay_mua, so_luong = :so_luong
                             WHERE tai_san_id = :tai_san_id AND vi_tri_id = :vi_tri_id";
     
             $updateStmt = $this->db->prepare($updateQuery);
             $updateStmt->bindParam(':ngay_mua', $ngayMua);
             $updateStmt->bindParam(':so_luong', $soLuong);
             $updateStmt->bindParam(':tai_san_id', $id);
             $updateStmt->bindParam(':vi_tri_id', $viTriId);
             $updateStmt->execute();
             */
             // Sau khi thêm hoặc cập nhật, bạn có thể chuyển hướng hoặc thông báo thành công
         }
     
         // Load view để hiển thị dữ liệu và form
         $content = 'views/khauhao/detail.php';
         include('views/layouts/base.php');
     }
     
     
}
?>
