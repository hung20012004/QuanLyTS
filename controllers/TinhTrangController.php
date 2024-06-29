<?php
include_once 'config/database.php';
include_once 'models/TinhTrang.php';
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TinhTrangController extends Controller {
    private $db;
    private $tinhTrang;
    private $baoTri;
    private $viTri;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->tinhTrang = new TinhTrang($this->db);
        $this->baoTri = new BaoTri($this->db);
        $this->viTri = new ViTri($this->db);
    }

    public function index() {
        $stmt = $this->tinhTrang->read();
        $stmtviTris = $this->viTri->readNotKho();
        $viTris = $stmtviTris->fetchAll(PDO::FETCH_ASSOC);
        $tinhTrangs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/tinhtrangs/index.php';
        include('views/layouts/base.php');
    }

    public function show($id) {
        $tinhTrang = $this->tinhTrang->readById($id);
        if (!$tinhTrang) {
            // Handle the case where no vi_tri is found with the given id
            $_SESSION['message'] = 'Không tìm thấy tình trạng!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=tinhtrang");
            exit();
        }
        
        $content = 'views/tinhtrangs/detail.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->tinhTrang->schedule_id = $_POST['schedule_id'];
            $this->tinhTrang->mo_ta_tinh_trang = $_POST['mo_ta_tinh_trang'];

            if ($this->tinhTrang->create()) {
                $_SESSION['message'] = 'Tình trạng đã được tạo thành công.';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=tinhtrang");
                exit();
            } else {
                $_SESSION['message'] = 'Tạo tình trạng thất bại.';
                $_SESSION['message_type'] = 'danger';
            }
        }
        $stmt = $this->baoTri->read();
        $baoTris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/tinhtrangs/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->tinhTrang->tinh_trang_id = $id;
            $this->tinhTrang->schedule_id = $_POST['schedule_id'];
            $this->tinhTrang->mo_ta_tinh_trang = $_POST['mo_ta_tinh_trang'];

            if ($this->tinhTrang->update()) {
                $_SESSION['message'] = 'Tình trạng đã được cập nhật thành công.';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=tinhtrang");
                exit();
            } else {
                $_SESSION['message'] = 'Cập nhật tình trạng thất bại.';
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            $stmt = $this->baoTri->read();
            $baoTris = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $tinhTrang = $this->tinhTrang->readById($id);
            $content = 'views/tinhtrangs/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
        if ($this->tinhTrang->delete($id)) {
            $_SESSION['message'] = 'Tình trạng đã được xóa thành công.';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=tinhtrang");
            exit();
        } else {
            $_SESSION['message'] = 'Xóa tình trạng thất bại.';
            $_SESSION['message_type'] = 'danger';
        }
    }

    public function export() {
        try {
            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Đặt tiêu đề cho các cột và thiết lập style cho header
            $sheet->setCellValue('A1', 'ID')->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
            ]);
            $sheet->setCellValue('B1', 'Vị Trí')->getStyle('B1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
            ]);
            $sheet->setCellValue('C1', 'Ngày Bắt Đầu')->getStyle('C1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
            ]);
            $sheet->setCellValue('D1', 'Ngày Kết Thúc')->getStyle('D1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
            ]);
            $sheet->setCellValue('E1', 'Mô Tả Tình Trạng')->getStyle('E1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
            ]);

            // Đặt chiều rộng cho các cột
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(50);

            // Lấy dữ liệu từ cơ sở dữ liệu
            $sql = "SELECT tt.tinh_trang_id, vt.ten_vi_tri, bt.*, tt.mo_ta_tinh_trang FROM tinh_trang tt JOIN maintenance_schedule bt ON bt.schedule_id = tt.schedule_id
                  JOIN vi_tri vt ON vt.vi_tri_id = bt.vi_tri_id ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Nếu có dữ liệu
            if (!empty($rows)) {
                $rowNumber = 2; // Bắt đầu từ hàng thứ 2 vì hàng 1 là tiêu đề
                foreach ($rows as $row) {
                    $sheet->setCellValue('A' . $rowNumber, $row['tinh_trang_id'])->getStyle('A' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('B' . $rowNumber, $row['ten_vi_tri'])->getStyle('B' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('C' . $rowNumber, $row['ngay_bat_dau'])->getStyle('C' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('D' . $rowNumber, $row['ngay_ket_thuc'])->getStyle('D' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('E' . $rowNumber, $row['mo_ta_tinh_trang'])->getStyle('E' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $rowNumber++;
                }
            } else {
                echo "Không có dữ liệu để xuất.";
                exit();
            }

            // Lưu file Excel
            $fileName = 'tinh_trang.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileName);

            // Thiết lập header để tải file về
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($fileName));
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);

            // Xóa file tạm sau khi đã tải xuống
            unlink($fileName);
            exit();

        } catch (PDOException $e) {
            die("Lỗi khi lấy dữ liệu từ cơ sở dữ liệu: " . $e->getMessage());
        }
    }
}
?>
