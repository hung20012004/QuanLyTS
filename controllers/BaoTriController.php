<?php
include_once 'config/database.php';
include_once 'models/BaoTri.php';
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BaoTriController extends Controller {
    private $db;
    private $schedule;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->schedule = new BaoTri($this->db);
    }

    public function index() {
        $stmt = $this->schedule->read();
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/baotri/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->schedule->tai_san_id = $_POST['tai_san_id'];
            $this->schedule->ngay_bat_dau = $_POST['ngay_bat_dau'];
            $this->schedule->ngay_ket_thuc = $_POST['ngay_ket_thuc'];
            $this->schedule->mo_ta = $_POST['mo_ta'];
            if ($this->schedule->create()) {
                $_SESSION['message'] = 'Lịch bảo trì đã được tạo thành công.';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=baotri");
                exit();
            } else {
                $_SESSION['message'] = 'Tạo lịch bảo trì thất bại.';
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/baotri/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        if ($_POST) {
            $this->schedule->schedule_id = $id;
            $this->schedule->tai_san_id = $_POST['tai_san_id'];
            $this->schedule->ngay_bat_dau = $_POST['ngay_bat_dau'];
            $this->schedule->ngay_ket_thuc = $_POST['ngay_ket_thuc'];
            $this->schedule->mo_ta = $_POST['mo_ta'];
            if ($this->schedule->update()) {
                $_SESSION['message'] = 'Lịch bảo trì đã được cập nhật thành công.';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=baotri");
                exit();
            } else {
                $_SESSION['message'] = 'Cập nhật lịch bảo trì thất bại.';
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            $schedule = $this->schedule->readById($id);
            $content = 'views/baotri/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
        if ($this->schedule->delete($id)) {
            $_SESSION['message'] = 'Lịch bảo trì đã được xóa thành công.';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=baotri");
            exit();
        } else {
            $_SESSION['message'] = 'Xóa lịch bảo trì thất bại.';
            $_SESSION['message_type'] = 'danger';
        }
    }

    public function export(){
        try {
            // Tạo một đối tượng Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
            // Đặt tiêu đề cho các cột và thiết lập style cho header
            $sheet->setCellValue('A1', 'ID')->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]
            ]);
            $sheet->setCellValue('B1', 'Tài Sản ID')->getStyle('B1')->applyFromArray([
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
            $sheet->setCellValue('E1', 'Mô Tả')->getStyle('E1')->applyFromArray([
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
            $sql = "SELECT schedule_id, tai_san_id, ngay_bat_dau, ngay_ket_thuc, mo_ta FROM maintenance_schedule";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Nếu có dữ liệu
            if (!empty($rows)) {
                $rowNumber = 2; // Bắt đầu từ hàng thứ 2 vì hàng 1 là tiêu đề
                foreach ($rows as $row) {
                    $sheet->setCellValue('A' . $rowNumber, $row['schedule_id'])->getStyle('A' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('B' . $rowNumber, $row['tai_san_id'])->getStyle('B' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    // Định dạng ngày tháng cho các ô ngày
                    $sheet->setCellValue('C' . $rowNumber, date('Y-m-d', strtotime($row['ngay_bat_dau'])))->getStyle('C' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('D' . $rowNumber, date('Y-m-d', strtotime($row['ngay_ket_thuc'])))->getStyle('D' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->setCellValue('E' . $rowNumber, $row['mo_ta'])->getStyle('E' . $rowNumber)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $rowNumber++;
                }
            } else {
                echo "Không có dữ liệu để xuất.";
                exit();
            }
    
            // Lưu file Excel
            $fileName = 'maintenance_schedules.xlsx';
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
    public function statistics() {
        try {
            // Tính tổng số lịch bảo trì
            $sql = "SELECT COUNT(*) AS totalSchedules FROM maintenance_schedule";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalSchedules = $result['totalSchedules'];
    
            // Tính tổng thời gian bảo trì
            $sql = "SELECT SUM(DATEDIFF(ngay_ket_thuc, ngay_bat_dau)) AS totalMaintenanceDays FROM maintenance_schedule";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalMaintenanceDays = $result['totalMaintenanceDays'];
    
            // Tính trung bình thời gian bảo trì
            $sql = "SELECT AVG(DATEDIFF(ngay_ket_thuc, ngay_bat_dau)) AS avgMaintenanceDays FROM maintenance_schedule";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $avgMaintenanceDays = $result['avgMaintenanceDays'];
    
            // Hiển thị thông tin thống kê
            $content = 'views/baotri/statistics.php'; // Đây là file hiển thị thông tin thống kê
            include('views/layouts/base.php');
    
        } catch (PDOException $e) {
            die("Lỗi khi tính toán thống kê: " . $e->getMessage());
        }
    }
    
    
    
}
?>
