<?php
include_once 'config/database.php';
include_once 'models/PhieuTra.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/PhieuTraChiTiet.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTri.php';
include_once 'models/User.php';
include_once 'models/ViTriChiTiet.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class PhieuTraController extends Controller
 {
    private $db;
    private $taiSanModel;
    private $phieuTraModel;
    private $phieuTraChiTietModel;
    private $viTriModel;
    private $userModel;
    private $viTriChiTietModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuTraModel = new PhieuTra($this->db);
        $this->phieuTraChiTietModel = new PhieuTraChiTiet($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriModel = new ViTri($this->db);
        $this->userModel = new User($this->db);
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);

    }

    public function index()
    {
        $phieuTra = $this->phieuTraModel->getAll();
        $content = 'views/phieu_tra/index.php';
        include('views/layouts/base.php') ;
    }

    public function search()
    {
        if (isset($_POST['btn_tim_kiem'])) {
        $ngay_tao_tk = $_POST['ngay_tao_tk'];
        $ngay_pd_tk = $_POST['ngay_pd_tk'];

        // Câu truy vấn SQL
         $query = "SELECT * 
                  FROM phieu_tra
                  WHERE ngay_gui LIKE '%".$ngay_tao_tk."%' AND ngay_duyet LIKE '%".$ngay_pd_tk."%'";
                
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $phieuTra = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content = 'views/phieu_tra/index.php';
        include('views/layouts/base.php');
    }
    }

    public function show($id){
        $phieuTra = $this->phieuTraModel->getAllByID($id);
        $nguoiTra = $this->phieuTraModel->getUserName($phieuTra['user_tra_id']);
        $nguoiNhan = $this->userModel->readById($phieuTra['user_nhan_id']);
        $nguoiDuyet = $this->userModel->readById($phieuTra['user_duyet_id']);
        $chiTietPhieuTra = $this->phieuTraChiTietModel->readDetailById($id);
        $content = 'views/phieu_tra/show.php';
        include('views/layouts/base.php');
    }

    public function create()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->createphieutra();
        }
        else{
            $this->showcreate();
        }
    }

    public function showcreate(){
        $user_nhan_id = $_SESSION['user_id'] ?? $_GET['user_id'];
        $nguoiNhan = $this->phieuTraModel->getUserName($user_nhan_id);
        // $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $vi_tri_list = $this->viTriModel->readByKhoa($nguoiNhan['khoa']);
        // exit();
        $tai_san_list = $this->phieuTraModel->readAllTaiSanbyKhoa($nguoiNhan['khoa']);
        $content = "views/phieu_tra/create.php";
        include "views/layouts/base.php";
    }

    public function createphieutra()
    {
         $this->db->beginTransaction();
        try {
            $this->phieuTraModel->user_tra_id =  $_POST['user_nhan_id'];
            $this->phieuTraModel->ghi_chu = $_POST['ghi_chu'];
            $this->phieuTraModel->ngay_gui = date('Y-m-d');
            $this->phieuTraModel->trang_thai = 'DaGui';

            $phieuTraId = $this->phieuTraModel->create();

            foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId)) continue;

            $this->phieuTraChiTietModel->phieu_tra_id = $phieuTraId;
            $this->phieuTraChiTietModel->tai_san_id = $taiSanId;
            $this->phieuTraChiTietModel->so_luong = $_POST['so_luong'][$index];
            $this->phieuTraChiTietModel->vi_tri_id=$_POST['vi_tri_id'][$index];
            $this->phieuTraChiTietModel->tinh_trang = 'Moi';
            $this->phieuTraChiTietModel->create();
        }
            
            $this->db->commit();
            $_SESSION['message'] = 'Tạo phiếu bàn giao mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieutra&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieutra&action=create");
            exit();
        }
    }

     public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEditForm($id);
        } else {
            $this->showEditForm($id);
        }
    }

    private function showEditForm($id)
    {
        $phieuTra = $this->phieuTraModel->readById($id);
        if (!$phieuTra) {
            die('Phiếu trả không tồn tại.');
        }

        $chiTietPhieuTra = $this->phieuTraChiTietModel->readDetailById($id);
        $user_tra_id = $phieuTra['user_tra_id'];
        $nguoiNhan = $this->phieuTraModel->getUserName($user_tra_id);
        // $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $vi_tri_list = $this->viTriModel->readByKhoa($nguoiNhan['khoa']);
        // exit();
        $tai_san_list = $this->phieuTraModel->readAllTaiSanbyKhoa($nguoiNhan['khoa']);

        $content = 'views/phieu_tra/edit.php';
        include ('views/layouts/base.php');
    }

    private function processEditForm($id)
    {
        $this->db->beginTransaction();
        try {
            $this->updatePhieuTra($id);
            $this->updateChiTietPhieuTra($id);

            $this->db->commit();
            $_SESSION['message'] = 'Cập nhật phiếu trả thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieutra&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieutra&action=edit&id=$id");
            exit();
        }
    }

    private function updatePhieuTra($id)
    {
        $this->phieuTraModel->user_tra_id = $_POST['user_nhan_id'];
        // $this->phieuTraModel->vi_tri_id = $_POST['vi_tri_id'];
        $this->phieuTraModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuTraModel->phieu_tra_id = $id;
        $this->phieuTraModel->update();
    }

   private function updateChiTietPhieuTra($id)
{
    // Xóa chi tiết cũ
    $this->phieuTraChiTietModel->deleteByPhieutraId($id);

    // Thêm chi tiết mới
    foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
        if (empty($taiSanId))
            continue;

        $this->phieuTraChiTietModel->phieu_tra_id = $id;
        $this->phieuTraChiTietModel->tai_san_id = $taiSanId;
        $this->phieuTraChiTietModel->so_luong = $_POST['so_luong'][$index];
        $this->phieuTraChiTietModel->vi_tri_id = $_POST['vi_tri_id'][$index];
        $this->phieuTraChiTietModel->tinh_trang = $_POST['tinh_trang'][$index]; // Sửa tên trường này
        $this->phieuTraChiTietModel->create();
    }
}

     public function delete($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        $this->db->beginTransaction();
        try {
            // Xóa chi tiết phiếu nhập trước
            $this->phieuTraChiTietModel->deleteByPhieuTraId($id);

            // Xóa phiếu nhập
            $this->phieuTraModel->delete($id);

            $this->db->commit();
            $_SESSION['message'] = 'Xóa phiếu trả thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieutra&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieutra&action=index");
            exit();
        }
    }

public function kiem_tra($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processKiemTraForm($id);
        } else {
            $this->showKiemTraForm($id);
        }
    }

    private function showKiemTraForm($id)
    {
        $phieuTra = $this->phieuTraModel->readById($id);
        if (!$phieuTra || $phieuTra['trang_thai'] !== 'DaGui') {
            die('Phiếu trả không tồn tại hoặc không ở trạng thái chờ kiểm tra.');
        }

        $user_tra_id = $phieuTra['user_tra_id'];
        $nguoiNhan = $this->phieuTraModel->getUserName($user_tra_id);
        $chiTietPhieuTra = $this->phieuTraChiTietModel->readDetailById($id);

        $content = 'views/phieu_tra/kiem_tra.php';
        include ('views/layouts/base.php');
    }

    private function processKiemTraForm($id)
    {
        // $this->phieuTraModel->user_tra_id = $_SESSION['user_id'];
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'gui') {
                // Kiểm tra số lượng tài sản
                $chiTietPhieuTra = $this->phieuTraChiTietModel->readDetailById($id);
                foreach ($chiTietPhieuTra as $chiTiet) {
                    $soLuongTrongKho = $this->phieuTraModel->getSoLuongTSPhongBan($chiTiet['vi_tri_id'], $chiTiet['tai_san_id']);
                    if ($chiTiet['so_luong'] > $soLuongTrongKho) {
                        $_SESSION['message'] = 'Phiếu gửi không hợp lệ. Số lượng yêu cầu vượt quá số lượng trong kho.';
                        $_SESSION['message_type'] = 'danger';
                        header("Location: index.php?model=phieutra&action=kiem_tra&id=$id");
                        exit();
                    }
                }

                // Cập nhật trạng thái phiếu
                $this->phieuTraModel->phieu_tra_id = $id;
                $this->phieuTraModel->trang_thai = 'DangChoPheDuyet';
                $this->phieuTraModel->ngay_kiem_tra = date('Y-m-d');
                $this->phieuTraModel->user_nhan_id = $_SESSION['user_id'];
                $this->phieuTraModel->updateStatusPhieuKiemTra();

                $_SESSION['message'] = 'Kiểm tra phiếu thành công. Phiếu đã chuyển sang trạng thái chờ phê duyệt.';
                $_SESSION['message_type'] = 'success';
            } elseif ($_POST['action'] === 'huy') {
                // Cập nhật trạng thái phiếu thành 'DaHuy'
                $this->phieuTraModel->phieu_tra_id = $id;
                $this->phieuTraModel->trang_thai = 'DaHuy';
                $this->phieuTraModel->user_nhan_id = $_SESSION['user_id'];
                $this->phieuTraModel->ngay_kiem_tra = date('Y-m-d');
                $this->phieuTraModel->updateStatusPhieuKiemTra();

                $_SESSION['message'] = 'Phiếu đã được hủy.';
                $_SESSION['message_type'] = 'warning';
            }
        }

        header("Location: index.php?model=phieutra&action=index");
        exit();
    }

      public function xet_duyet($id)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : null;
        $ghi_chu_duyet = isset($_POST['ghi_chu_duyet']) ? $_POST['ghi_chu_duyet'] : '';

        if ($action == 'approve') {
            $this->phieuTraModel->trang_thai = 'DaPheDuyet';
            $this->phieuTraModel->ngay_duyet = date('Y-m-d');
            $this->phieuTraModel->user_duyet_id = $_SESSION['user_id'];
            $this->phieuTraModel->phieu_tra_id = $id;
            $this->phieuTraModel->updateStatusXetDuyet();
            $_SESSION['message'] = 'Phê duyệt thành công.';
            $_SESSION['message_type'] = 'success';
        } elseif ($action == 'reject') {
            $this->phieuTraModel->trang_thai = 'KhongDuyet';
             $this->phieuTraModel->ngay_duyet = date('Y-m-d');
            $this->phieuTraModel->user_duyet_id = $_SESSION['user_id'];
            $this->phieuTraModel->phieu_tra_id = $id;
            $this->phieuTraModel->updateStatusXetDuyet();
            $_SESSION['message'] = 'Đã hủy phiếu yêu cầu.';
            $_SESSION['message_type'] = 'danger';
        }
        
        header("Location: index.php?model=phieutra&action=index");
        exit();
    } else {
        $phieuTra = $this->phieuTraModel->readById($id);
        if (!$phieuTra) {
            die('Phiếu trả không tồn tại.');
        }

        $phieuTra = $this->phieuTraModel->readById($id);
        $user_tra_id = $phieuTra['user_tra_id'];
        $nguoiNhan = $this->phieuTraModel->getUserName($user_tra_id);
        $chiTietPhieuTra = $this->phieuTraChiTietModel->readDetailById($id);

        $content = 'views/phieu_tra/xet_duyet.php';
        include('views/layouts/base.php');
    }
}

 public function tra($id)
 {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       
        $chiTietPhieuTra = $this->phieuTraChiTietModel->readDetailById($id);
                foreach ($chiTietPhieuTra as $chiTiet) {
                    $soLuongTrongKho = $this->phieuTraModel->getSoLuongTSPhongBan($chiTiet['vi_tri_id'], $chiTiet['tai_san_id']);
                    if ($chiTiet['so_luong'] < $soLuongTrongKho) {
                       $newQuantity = $soLuongTrongKho - $chiTiet['so_luong']; 
                    }
                    else
                    {
                        $newQuantity=0;
                    }

                    $this->phieuTraModel->updateSoluongVitri($chiTiet['vi_tri_id'],$chiTiet['tai_san_id'], $newQuantity );

                }

        $this->phieuTraModel->ngay_tra = date('Y-m-d');
        $this->phieuTraModel->user_tra_id = $_SESSION['user_id'];
        $this->phieuTraModel->trang_thai = 'DaTra';
        $this->phieuTraModel->phieu_tra_id = $id;
        $this->phieuTraModel->updateStatusTra();
         $_SESSION['message'] = 'Trả tài sản thành công.';
         $_SESSION['message_type'] = 'success';

        header("Location: index.php?model=phieutra&action=index");
        exit();
    } else {
        $phieuTra = $this->phieuTraModel->readById($id);
        if (!$phieuTra) {
            die('Phiếu trả không tồn tại.');
        }

        $phieuTra = $this->phieuTraModel->readById($id);
        $user_tra_id = $phieuTra['user_tra_id'];
        $nguoiNhan = $this->phieuTraModel->getUserName($user_tra_id);
        $chiTietPhieuTra = $this->phieuTraChiTietModel->readDetailById($id);

        $content = 'views/phieu_tra/tra.php';
        include('views/layouts/base.php');
    }
 }

    public function exportphieu($id)
{
    // Fetch data from the database
    $sql = "SELECT pt.*, ptct.*, ts.ten_tai_san, vt.*
            FROM phieu_tra pt
            INNER JOIN phieu_tra_chi_tiet ptct ON pt.phieu_tra_id = ptct.phieu_tra_id
            INNER JOIN tai_san ts ON ptct.tai_san_id = ts.tai_san_id
            INNER JOIN vi_tri vt ON ptct.vi_tri_id = vt.vi_tri_id
            WHERE pt.phieu_tra_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
    $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

    $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // Set headers
    $sheet->mergeCells('B1:E1')->setCellValue('B1', 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM');
    $sheet->mergeCells('B2:E2')->setCellValue('B2', 'Độc lập - Tự do - Hạnh phúc');
    $sheet->mergeCells('B3:E3')->setCellValue('B3', 'GIẤY ĐỀ NGHỊ TRẢ SẢN CỐ ĐỊNH')->getStyle('B3')->getFont()->setBold(true)->setSize(13);
    $sheet->mergeCells('B4:E4')->setCellValue('B4', 'Kính gửi: ');
    $sheet->mergeCells('B5:E5')->setCellValue('B5', 'Phòng/Ban:');
    $sheet->mergeCells('B6:E6')->setCellValue('B6', 'Danh Mục TSCĐ Đề Nghị Trả')->getStyle('B6')->getFont()->setBold(true)->setSize(13);
    $sheet->getStyle('B6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCEEFF');
    $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('B4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('B5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // Column headers
    $sheet->setCellValue('B7', 'STT');
    $sheet->setCellValue('C7', 'Tên TSCĐ');
    $sheet->setCellValue('D7', 'Số lượng');
    $sheet->setCellValue('E7', 'Vị trí');

     // Set borders for headers
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

    $sheet->getStyle('B7:E7')->applyFromArray($styleArray);
    $sheet->getStyle('B6:E6')->applyFromArray($styleArray);

    // Data rows
    $stt = 1;
    $row = 8;
    foreach ($results as $data) {
        $sheet->setCellValue('B' . $row, $stt);
        $sheet->setCellValue('C' . $row, $data['ten_tai_san']);
        $sheet->setCellValue('D' . $row, $data['so_luong']);
        $sheet->setCellValue('E' . $row, $data['ten_vi_tri']);

        // Set styles for the row
        $sheet->getStyle('B' . $row . ':E' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B' . $row . ':E' . $row)->getFont()->setSize(13);

         $sheet->getStyle('B' . $row . ':E' . $row)->applyFromArray($styleArray);

        $stt++;
        $row++;
    }

    foreach(range('B','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

    // Output the Excel file
    $writer = new Xlsx($spreadsheet);
    $fileName = 'Phieu_Yeu_Cau_Tra_Tai_San_Co_Dinh.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    ob_end_clean();
    $writer->save('php://output');
    exit;
}
 }
?>