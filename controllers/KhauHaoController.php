<?php
include_once 'config/database.php';
include_once 'models/TaiSan.php';
include_once 'models/KhauHao.php';

class KhauHaoController extends Controller
{
    private $db;
    private $taiSan;
    private $loaiTaiSan;
    private $khauhao;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->taiSan = new TaiSan($this->db);
        $this->loaiTaiSan = new LoaiTaiSan($this->db);
        $this->khauhao = new KhauHao($this->db);
    }

    public function index()
    {
        $loaitss = $this->khauhao->readloaits();
        $khau_hao_all = $this->khauhao->readKhAll();
        $content = 'views/khauhao/index.php';

        include ('views/layouts/base.php');
    }

    public function show($id)
    {

        $stmt = $this->khauhao->readById($id);
        $KhauHaos = $stmt;
        // Thêm dòng này để kiểm tra dữ liệu
        $ts = $id;
        $content = 'views/khauhao/show.php';
        include ('views/layouts/base.php');

    }
    public function viewcreatekh($id)
    {
        $ts = $this->khauhao->readtaisan($id);
        $content = 'views/khauhao/create.php';
        include ('views/layouts/base.php');
    }

    public function create()
    {

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
                header("Location: index.php?model=khauhao&action=viewcreatekh&id=" . $this->khauhao->tai_san_id);
                exit();
            }
        }

        $content = 'views/khauhao/create.php';
        include ('views/layouts/base.php');
    }

    public function edit($id)
    {
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
        include ('views/layouts/base.php');
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
            header("Location: index.php?model=khauhao&action=viewcreatekh&id=" . $this->khauhao->tai_san_id);
            exit();
        }
    }
    public function detail($id)
{
    // Lấy thông tin tài sản và loại tài sản
    $queryTaiSan = "SELECT tai_san.*, loai_tai_san.ten_loai_tai_san
                    FROM tai_san
                    INNER JOIN loai_tai_san ON tai_san.loai_tai_san_id = loai_tai_san.loai_tai_san_id
                    WHERE tai_san.tai_san_id = ?";
    $stmtTaiSan = $this->db->prepare($queryTaiSan);
    $stmtTaiSan->execute([$id]);
    $taiSan = $stmtTaiSan->fetch(PDO::FETCH_ASSOC);

    // Truy vấn dữ liệu chi tiết từ bảng chi tiết hóa đơn mua
    $query = "SELECT cthd.chi_tiet_id, cthd.so_luong, cthd.don_gia, 
                     hd.ngay_mua, vt.ten_vi_tri, vtct.so_luong as so_luong_vi_tri,
                     COALESCE(kh.thoi_gian_khau_hao, 0) as thoi_gian_khau_hao
              FROM chi_tiet_hoa_don_mua cthd
              INNER JOIN hoa_don_mua hd ON hd.hoa_don_id = cthd.hoa_don_id
              LEFT JOIN vi_tri_chi_tiet vtct ON vtct.chi_tiet_id = cthd.chi_tiet_id
              LEFT JOIN vi_tri vt ON vt.vi_tri_id = vtct.vi_tri_id
              LEFT JOIN khau_hao kh ON kh.chi_tiet_id = cthd.chi_tiet_id
              WHERE cthd.tai_san_id = ?";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute([$id]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Xử lý POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $thoiGianKhauHao = $_POST['thoi_gian_khau_hao'];

        foreach ($thoiGianKhauHao as $chiTietId => $thoiGian) {
            // Tính số tiền khấu hao
            $chiTiet = array_filter($details, function($detail) use ($chiTietId) {
                return $detail['chi_tiet_id'] == $chiTietId;
            });
            $chiTiet = reset($chiTiet);
            $soTien = $chiTiet['don_gia'] / $thoiGian;
            
            // Kiểm tra xem đã có bản ghi khấu hao chưa
            $checkQuery = "SELECT COUNT(*) FROM khau_hao WHERE chi_tiet_id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([$chiTietId]);
            $exists = $checkStmt->fetchColumn();

            if ($exists) {
                // Cập nhật bản ghi khấu hao hiện có
                $updateQuery = "UPDATE khau_hao 
                                SET thoi_gian_khau_hao = ?, so_tien = ?
                                WHERE chi_tiet_id = ?";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->execute([$thoiGian, $soTien, $chiTietId]);
            } else {
                // Thêm bản ghi khấu hao mới
                $insertQuery = "INSERT INTO khau_hao (chi_tiet_id, thoi_gian_khau_hao, so_tien)
                                VALUES (?, ?, ?)";
                $insertStmt = $this->db->prepare($insertQuery);
                $insertStmt->execute([$chiTietId, $thoiGian, $soTien]);
            }
        }
        $_SESSION['message'] = 'Cập nhật thông tin khấu hao thành công!';
        $_SESSION['message_type'] = 'success';
        // Redirect để tránh gửi lại form khi refresh
        header("Location: index.php?model=khauhao&action=detail&id=" . $id);
        exit();
    }

    // Load view để hiển thị dữ liệu và form
    $content = 'views/khauhao/detail.php';
    include('views/layouts/base.php');
}

public function search()
{
    if($_POST)
    {
        $tenTaiSan = isset($_POST['ten_tai_san']) ? $_POST['ten_tai_san'] : '';
    $loaiTaiSan = isset($_POST['loai_tai_san']) ? $_POST['loai_tai_san'] : '';
    
    // Gọi phương thức tìm kiếm khấu hao từ model
    $loaitss = $this->khauhao->readloaits();
    $khau_hao_all = $this->khauhao->search($tenTaiSan, $loaiTaiSan);
    
    // Load view để hiển thị kết quả tìm kiếm
    $content = 'views/khauhao/index.php';
    include('views/layouts/base.php');
    }
    
}
}
?>