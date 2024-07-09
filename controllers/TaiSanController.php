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
        $loaiTaiSans=$this->loaiTaiSan->read();
        $content = 'views/taisans/index.php';

        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->taiSan->ten_tai_san = $_POST['ten_tai_san'];
            $this->taiSan->mo_ta = $_POST['mo_ta'];
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
        $loaiTaiSans=$this->loaiTaiSan->read();
        $content = 'views/taisans/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->taiSan->tai_san_id = $id;
            $this->taiSan->ten_tai_san = $_POST['ten_tai_san'];
            $this->taiSan->mo_ta = $_POST['mo_ta'];
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

    public function detail($id)
    {

        // Lấy thông tin tài sản
        $queryTaiSan = "SELECT * 
        FROM tai_san
        INNER JOIN loai_tai_san ON tai_san.loai_tai_san_id = loai_tai_san.loai_tai_san_id
        WHERE tai_san_id = ?";
        $stmtTaiSan = $this->db->prepare($queryTaiSan);
        $stmtTaiSan->execute([$id]);
        $taiSan = $stmtTaiSan->fetch(PDO::FETCH_ASSOC);

        // Truy vấn dữ liệu chi tiết
        $query = "SELECT ct.so_luong, vt.ten_vi_tri 
        FROM tai_san ts 
        INNER JOIN vi_tri_chi_tiet ct ON ts.tai_san_id = ct.tai_san_id 
        INNER JOIN vi_tri vt ON ct.vi_tri_id = vt.vi_tri_id 
        WHERE ts.tai_san_id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content = 'views/taisans/detail.php';
        include('views/layouts/base.php');
    }

    public function delete($id) {
        if ($this->taiSan->delete($id)) {
            $_SESSION['message'] = 'Xóa thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Xóa thất bại';
            $_SESSION['message_type'] = 'danger';
        }
        header("Location: index.php?model=taisan");
    }

    public function statistics() {
        // Example of data retrieval (replace with actual database queries)
        $totalAssets = $this->taiSan->getTotalAssets();
        $totalAssetTypes = $this->loaiTaiSan->getTotalAssetTypes();
        $assetTypes = $this->taiSan->getAssetTypeStatistics();
    
        // Prepare data for chart.js
        $chartData = [
            'labels' => array_column($assetTypes, 'loai_tai_san'),
            'data' => array_column($assetTypes, 'so_luong'),
        ];
    
        // Prepare data to pass to the view
        $data = [
            'totalAssets' => $totalAssets,
            'totalAssetTypes' => $totalAssetTypes,
            'assetTypes' => $assetTypes,
            'chartData' => json_encode($chartData),
        ];
        // Load the view
        $content = 'views/taisans/statistic.php'; // Adjust the path as per your application structure
        include('views/layouts/base.php'); // Assuming base.php is your main layout template
    }

    public function search() {
        if (isset($_GET['tenTaiSan']) && isset($_GET['loaiTaiSan'])) {
            $tenTaiSan = $_GET['tenTaiSan'];
            $loaiTaiSan = $_GET['loaiTaiSan'];
            $searchResults = $this->taiSan->searchTaisan($tenTaiSan, $loaiTaiSan);
            header('Content-Type: application/json');
            echo json_encode($searchResults);
            exit;
        }
    }
}
?>
