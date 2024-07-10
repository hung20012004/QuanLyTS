<?php
include_once 'config/database.php';
include_once 'models/PhieuNhap.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ChiTietPhieuNhap.php';
include_once 'models/TaiSan.php';
include_once 'models/User.php';
include_once 'models/ViTriChiTiet.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PhieuNhapController extends Controller
{
    private $db;
    private $phieuNhapModel;
    private $loaiTaiSanModel;
    private $userModel;
    private $chiTietPhieuNhapModel;
    private $taiSanModel;
    private $viTriChiTietModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuNhapModel = new PhieuNhap($this->db);
        $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
        $this->chiTietPhieuNhapModel = new ChiTietPhieuNhap($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);
        $this->userModel= new User($this->db);
    }

    public function index()
    {
        $phieuNhap = $this->phieuNhapModel->readAll();
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $content = 'views/phieu_nhap/index.php';
        include ('views/layouts/base.php');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreateForm();
        } else {
            $this->showCreateForm();
        }
    }

    private function showCreateForm()
    {
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read(); // Lấy tất cả tài sản
        $content = 'views/phieu_nhap/create.php';
        include ('views/layouts/base.php');
    }

    private function processCreateForm()
    {
        $this->db->beginTransaction();
        try {
            $phieuNhapId = $this->createPhieuNhap();
            $this->createChiTietPhieuNhap($phieuNhapId);

            $this->db->commit();
            $_SESSION['message'] = 'Tạo phiếu nhập mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=create");
            exit();
        }
    }

    private function createPhieuNhap()
    {
        $this->phieuNhapModel->user_id = $_SESSION['user_id'];
        $this->phieuNhapModel->ngay_tao = $_POST['ngay_tao'];
        $this->phieuNhapModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuNhapModel->trang_thai = 'DangChoPheDuyet';
        return $this->phieuNhapModel->create();
    }

    private function createChiTietPhieuNhap($phieuNhapId)
    {
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId))
                continue;  // Bỏ qua nếu không có tài sản được chọn

            $this->chiTietPhieuNhapModel->phieu_nhap_tai_san_id = $phieuNhapId;
            $this->chiTietPhieuNhapModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuNhapModel->so_luong = $_POST['so_luong'][$index];
            $chiTietId = $this->chiTietPhieuNhapModel->create();

            $this->viTriChiTietModel->vi_tri_id = 1;
            $this->viTriChiTietModel->so_luong = 0;
            $this->viTriChiTietModel->tai_san_id = $taiSanId;
            $this->viTriChiTietModel->create();
        }
    }

    public function getByLoai()
    {
        if (isset($_GET['loai_id'])) {
            $loai_id = $_GET['loai_id'];
            $taiSanList = $this->taiSanModel->readByLoaiId($loai_id);
            header('Content-Type: application/json');
            echo json_encode($taiSanList);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Missing loai_id parameter"));
        }
    }
    public function edit($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEditForm($id);
        } else {
            $this->showEditForm($id);
        }
    }

    public function showEditForm($id)
    {
        $phieuNhap = $this->phieuNhapModel->readById($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }

        // Sửa đổi truy vấn này để lấy thêm thông tin về loại tài sản và tên tài sản
        $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);

        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        // var_dump($chiTietPhieuNhap);
        // exit();
        $content = 'views/phieu_nhap/edit.php';
        include ('views/layouts/base.php');
    }

    private function processEditForm($id)
    {
        $this->db->beginTransaction();
        try {
            $this->updatePhieuNhap($id);
            $this->updateChiTietPhieuNhap($id);

            $this->db->commit();
            $_SESSION['message'] = 'Cập nhật phiếu nhập thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=edit&id=" . $id);
            exit();
        }
    }

    private function updatePhieuNhap($id)
    {
        $this->phieuNhapModel->phieu_nhap_tai_san_id = $id;
        $this->phieuNhapModel->ngay_nhap = $_POST['ngay_nhap'];
        $this->phieuNhapModel->ngay_xac_nhan = $_POST['ngay_xac_nhan'];
        $this->phieuNhapModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuNhapModel->trang_thai = 'DangChoPheDuyet';
        $this->phieuNhapModel->update();
    }

    private function updateChiTietPhieuNhap($phieuNhapId)
    {
        // Delete existing chi tiết
        $this->chiTietPhieuNhapModel->deleteByPhieuNhapId($phieuNhapId);

        // Create new chi tiết
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId))
                continue;

            $this->chiTietPhieuNhapModel->phieu_nhap_tai_san_id = $phieuNhapId;
            $this->chiTietPhieuNhapModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuNhapModel->so_luong = $_POST['so_luong'][$index];
            $this->chiTietPhieuNhapModel->create();

            $this->viTriChiTietModel->vi_tri_id = 1;
            $this->viTriChiTietModel->so_luong = 0;
            $this->viTriChiTietModel->tai_san_id = $taiSanId;
            $this->viTriChiTietModel->create();
        }
    }
    public function show($id)
    {
        $phieuNhap = $this->phieuNhapModel->readByIdWithUserInfo($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }
        $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);

        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_nhap/show.php';
        include ('views/layouts/base.php');
    }
    public function delete($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        $this->db->beginTransaction();
        try {
            // Xóa chi tiết phiếu nhập trước
            $this->chiTietPhieuNhapModel->deleteByPhieuNhapId($id);

            // Xóa phiếu nhập
            $this->phieuNhapModel->delete($id);

            $this->db->commit();
            $_SESSION['message'] = 'Xóa phiếu nhập thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        }
    }
    public function xet_duyet($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = isset($_POST['action']) ? $_POST['action'] : null;
            if ($action == 'approve') {
                $this->phieuNhapModel->trang_thai = 'DaPheDuyet';
            } elseif ($action == 'reject') {
                $this->phieuNhapModel->trang_thai = 'KhongDuyet';
            }
            $this->phieuNhapModel->user_duyet_id = $_SESSION['user_id'];
            $this->phieuNhapModel->ngay_xac_nhan = date('Y-m-d');
            $this->phieuNhapModel->phieu_nhap_tai_san_id = $id;
            $this->phieuNhapModel->updateStatus();
            $_SESSION['message'] = 'Cập nhật thông tin thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } else {
            $phieuNhap = $this->phieuNhapModel->readByIdWithUserInfo($id);
            if (!$phieuNhap) {
                die('Phiếu nhập không tồn tại.');
            }
            $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);
            $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
            $tai_san_list = $this->taiSanModel->read();
            $content = 'views/phieu_nhap/xet_duyet.php';
            include ('views/layouts/base.php');
        }
    }
    public function nhap_tai_san($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->phieuNhapModel->phieu_nhap_tai_san_id = $id;
            $this->processNhapTaiSan($id);
        } else {
            $this->showNhapTaiSanForm($id);
        }
    }

    private function showNhapTaiSanForm($id)
    {
        $phieuNhap = $this->phieuNhapModel->readByIdWithUserInfo($id);

        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }
        $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readDetailedByPhieuNhapId($id);
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_nhap/nhap_tai_san.php';
        include ('views/layouts/base.php');
    }

    private function processNhapTaiSan($id)
    {
        $this->db->beginTransaction();
        try {
            $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readByPhieuNhapId($id);
            foreach ($chiTietPhieuNhap as $chiTiet) {

                // Cập nhật số lượng trong bảng vi_tri_chi_tiet
                $viTriChiTiet = $this->viTriChiTietModel->readByTaiSanAndViTri($chiTiet['tai_san_id'], 1);
                if ($viTriChiTiet) {
                    $newViTriQuantity = $viTriChiTiet['so_luong'] + $chiTiet['so_luong'];
                    $this->viTriChiTietModel->updateQuantity($viTriChiTiet['vi_tri_chi_tiet_id'], $newViTriQuantity);
                } else {
                    $this->viTriChiTietModel->vi_tri_id = 1;
                    $this->viTriChiTietModel->tai_san_id = $chiTiet['tai_san_id'];
                    $this->viTriChiTietModel->so_luong = $chiTiet['so_luong'];
                    $this->viTriChiTietModel->create();
                }
            }
            $this->phieuNhapModel->ngay_nhap = date('Y-m-d');
            $this->phieuNhapModel->trang_thai = 'DaNhap';
            // var_dump($this->phieuNhapModel);
            // exit();
            $this->phieuNhapModel->updateStatus2();

            $this->db->commit();
            $_SESSION['message'] = 'Nhập tài sản thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=nhap_tai_san&id=" . $id);
            exit();
        }
    }
    public function export()
    {
        // Fetch data to be exported
        $phieuNhap = $this->phieuNhapModel->readAll();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Merge cells for the title and set the title
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'Danh sách Phiếu nhập tài sản');
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ]);

        // Set the header of the columns
        $headers = ['Mã số phiếu', 'Ngày tạo phiếu', 'Ngày phê duyệt', 'Ngày nhập tài sản', 'Trạng thái'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '2', $header);
            $column++;
        }

        // Populate the spreadsheet with data
        $row = 3;
        foreach ($phieuNhap as $phieu) {
            $sheet->setCellValue('A' . $row, $phieu['phieu_nhap_tai_san_id']);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($phieu['ngay_tao'])));
            $sheet->setCellValue('C' . $row, in_array($phieu['trang_thai'], ['DaPheDuyet', 'DaNhap', 'KhongDuyet']) ? date('d-m-Y', strtotime($phieu['ngay_xac_nhan'])) : '');
            $sheet->setCellValue('D' . $row, $phieu['trang_thai'] == 'DaNhap' ? date('d-m-Y', strtotime($phieu['ngay_nhap'])) : '');
            $sheet->setCellValue('E' . $row, $phieu['trang_thai']);
            $row++;
        }

        // Set auto width for columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Create a writer and save the file to the server temporarily
        $fileName = 'phieu_nhap.xlsx';
        $filePath = __DIR__ . '/' . $fileName; // Save in the current directory of the project
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Return the file as a download
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            unlink($filePath); // Delete temporary file after download
            exit;
        } else {
            echo "File không tồn tại.";
        }
    }
    public function exportWord($id)
    {
        require 'vendor/autoload.php';
    
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
    
        // Fetch data
        $phieuNhap = $this->phieuNhapModel->readById($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }
    
        $nguoiTao = $this->userModel->readById($phieuNhap['user_id']);
        $nguoiDuyet = $this->userModel->readById($phieuNhap['user_duyet_id']);
        $chiTietPhieuNhap = $this->chiTietPhieuNhapModel->readByPhieuNhapId($id);
    
        // Add a section to the document
        $section = $phpWord->addSection();
    
        // Add title
        $section->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('Độc lập - Tự do - Hạnh Phúc', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('---***---', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);
        $section->addText('PHIẾU NHẬP TÀI SẢN', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);
    
        // Add general information
        $section->addText('Ngày tạo phiếu: ' . date('d/m/Y', strtotime($phieuNhap['ngay_tao'])));
        $section->addText('Người tạo phiếu: ' . $nguoiTao['ten'] . ', MSNV: ' . $nguoiTao['user_id']);
        $section->addText('Người phê duyệt: ' . ($nguoiDuyet ? $nguoiDuyet['ten'] . ', MSNV: ' . $nguoiDuyet['user_id'] : 'Chưa duyệt'));
        $section->addText('Ngày phê duyệt: ' . ($phieuNhap['ngay_xac_nhan'] ? date('d/m/Y', strtotime($phieuNhap['ngay_xac_nhan'])) : 'Chưa duyệt'));
        $section->addTextBreak(1);
        $section->addText('Chi tiết tài sản nhập:');
    
        // Add table of assets
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        $table->addRow();
        $table->addCell(500)->addText('STT', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table->addCell(2000)->addText('Loại tài sản', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table->addCell(3000)->addText('Tên tài sản', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table->addCell(1000)->addText('Số lượng', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
    
        foreach ($chiTietPhieuNhap as $index => $chiTiet) {
            $taiSan = $this->taiSanModel->readById($chiTiet['tai_san_id']);
            $loaiTaiSan = $this->loaiTaiSanModel->readById($taiSan['loai_tai_san_id']);
            $table->addRow();
            $table->addCell(500)->addText($index + 1, [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(2000)->addText($loaiTaiSan['ten_loai_tai_san']);
            $table->addCell(3000)->addText($taiSan['ten_tai_san']);
            $table->addCell(1000)->addText($chiTiet['so_luong'], [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        }
    
        $section->addTextBreak(1);
        $section->addText('Ghi chú: ' . $phieuNhap['ghi_chu']);
    
        $section->addTextBreak(2);
        $section->addText('Người tạo phiếu', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('(Ký, họ tên)', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(3);
        $section->addText($nguoiTao['ten'], [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
    
        $section->addTextBreak(2);
        $section->addText('Người phê duyệt', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('(Ký, họ tên)', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(3);
        $section->addText($nguoiDuyet ? $nguoiDuyet['ten'] : '...................................', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
    
        // Save file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'PhieuNhapTaiSan_' . $id . '.docx';
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $objWriter->save("php://output");
        exit;
    }



}