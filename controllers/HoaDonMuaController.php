<?php
// controllers/HoaDonMuaController.php
include_once 'config/database.php';
include_once 'models/HoaDonMua.php';
include_once 'models/NhaCungCap.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ChiTietHoaDonMua.php';
include_once 'models/TaiSan.php';

class ValidationException extends Exception {}

class HoaDonMuaController extends Controller {
    private $db;
    private $hoaDonMuaModel;
    private $nhaCungCapModel;
    private $loaiTaiSanModel;
    private $chiTietHoaDonMuaModel;
    private $taiSanModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->hoaDonMuaModel = new HoaDonMua($this->db);
        $this->nhaCungCapModel = new NhaCungCap($this->db);
        $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
        $this->chiTietHoaDonMuaModel = new ChiTietHoaDonMua($this->db);
        $this->taiSanModel = new TaiSan($this->db);
    }

    public function index() {
        $invoices = $this->hoaDonMuaModel->readAll();
        $suppliers = $this->nhaCungCapModel->read();
        $stmtLoaiTaiSan = $this->loaiTaiSanModel->readAll();
        $content = 'views/hoa_don_mua/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->db->beginTransaction();

            try {
                $this->hoaDonMuaModel->ngay_mua = $_POST['ngay_mua'];
                $this->hoaDonMuaModel->tong_gia_tri = $_POST['tong_gia_tri'];
                $this->hoaDonMuaModel->nha_cung_cap_id = $_POST['nha_cung_cap_id'];

                if ($this->hoaDonMuaModel->create()) {
                    $hoaDonMuaId = $this->db->lastInsertId();

                    for ($i = 0; $i < count($_POST['ten_tai_san']); $i++) {
                        $taiSanData = array(
                            'ten_tai_san' => $_POST['ten_tai_san'][$i],
                            'mo_ta' => $_POST['mo_ta'][$i],
                            'so_luong' => $_POST['so_luong'][$i],
                            'loai_tai_san_id' => $_POST['loai_tai_san'][$i]
                        );

                        $taiSanId = $this->taiSanModel->createOrUpdate($taiSanData);

                        $this->chiTietHoaDonMuaModel->hoa_don_id = $hoaDonMuaId;
                        $this->chiTietHoaDonMuaModel->tai_san_id = $taiSanId;
                        $this->chiTietHoaDonMuaModel->so_luong = $_POST['so_luong'][$i];
                        $this->chiTietHoaDonMuaModel->don_gia = $_POST['don_gia'][$i];
                        $this->chiTietHoaDonMuaModel->create();
                    }

                    $this->db->commit();

                    $_SESSION['message'] = 'Tạo hóa đơn mới thành công!';
                    $_SESSION['message_type'] = 'success';
                    header("Location: index.php?model=hoadonmua");
                } else {
                    throw new Exception('Tạo mới hóa đơn thất bại!');
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }

        $stmtLoaiTaiSan = $this->loaiTaiSanModel->readAll();
        $suppliers = $this->nhaCungCapModel->read();
        $content = 'views/hoa_don_mua/create.php';
        include('views/layouts/base.php');
    }

    public function edit($id) {
        $invoice = $this->hoaDonMuaModel->readById($id);
        if (!$invoice) {
            $_SESSION['message'] = 'Không tìm thấy hóa đơn!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=hoadonmua");
            return;
        }

        if ($_POST) {
            $this->db->beginTransaction();

            try {
                $this->validateInput($_POST);

                $stmt = $this->db->prepare("UPDATE hoa_don_mua SET ngay_mua = ?, tong_gia_tri = ?, nha_cung_cap_id = ? WHERE hoa_don_id = ?");
                $stmt->execute([$_POST['ngay_mua'], $_POST['tong_gia_tri'], $_POST['nha_cung_cap_id'], $id]);

                // Xóa chi tiết hóa đơn cũ
                $this->chiTietHoaDonMuaModel->delete($id);

                // Thêm chi tiết hóa đơn mới
                for ($i = 0; $i < count($_POST['ten_tai_san']); $i++) {
                    $taiSanData = array(
                        'ten_tai_san' => $_POST['ten_tai_san'][$i],
                        'mo_ta' => $_POST['mo_ta'][$i],
                        'so_luong' => $_POST['so_luong'][$i],
                        'loai_tai_san_id' => $_POST['loai_tai_san'][$i]
                    );

                    $taiSanId = $this->taiSanModel->createOrUpdate($taiSanData);

                    $stmt = $this->db->prepare("INSERT INTO chi_tiet_hoa_don_mua (hoa_don_id, tai_san_id, so_luong, don_gia) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$id, $taiSanId, $_POST['so_luong'][$i], $_POST['don_gia'][$i]]);
                }

                $this->db->commit();

                $_SESSION['message'] = 'Sửa hóa đơn thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=hoadonmua");
                exit();
            } catch (ValidationException $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }

        $suppliers = $this->nhaCungCapModel->read();
        $invoice_details = $this->chiTietHoaDonMuaModel->readByHoaDonId($id);
        
        // Fetch loai_tai_san_id for each tai_san
        foreach ($invoice_details as &$detail) {
            $taiSan = $this->taiSanModel->readById($detail['tai_san_id']);
            $detail['loai_tai_san_id'] = $taiSan['loai_tai_san_id'];
        }
        
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $content = 'views/hoa_don_mua/edit.php';
        include('views/layouts/base.php');
    }

    public function delete($id) {
        if ($this->hoaDonMuaModel->delete($id)) {
            $_SESSION['message'] = 'Xóa hóa đơn thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Xóa hóa đơn thất bại!';
            $_SESSION['message_type'] = 'danger';
        }
        header("Location: index.php?model=hoadonmua");
    }

    private function validateInput($data) {
        if (empty($data['ngay_mua'])) {
            throw new ValidationException('Ngày mua không được để trống');
        }
        if (empty($data['nha_cung_cap_id'])) {
            throw new ValidationException('Vui lòng chọn nhà cung cấp');
        }
        if (empty($data['ten_tai_san']) || !is_array($data['ten_tai_san'])) {
            throw new ValidationException('Danh sách tài sản không hợp lệ');
        }
        // Add more validation as needed
    }

    public function export() {
        try {
            // Truy vấn dữ liệu
            $sql = "SELECT hm.hoa_don_id, hm.ngay_mua, hm.tong_gia_tri, ncc.ten_nha_cung_cap 
                    FROM hoa_don_mua hm
                    JOIN nha_cung_cap ncc ON hm.nha_cung_cap_id = ncc.nha_cung_cap_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Tính toán thống kê
            $totalInvoices = count($invoices);
            $totalValue = array_sum(array_column($invoices, 'tong_gia_tri'));
            $avgValue = $totalInvoices > 0 ? $totalValue / $totalInvoices : 0;
    
            // Tạo file Excel
            require_once 'vendor/autoload.php';
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
            // Đặt tiêu đề
            $sheet->setCellValue('A1', 'Báo cáo Hóa đơn mua');
            $sheet->mergeCells('A1:D1');
            $sheet->getStyle('A1')->getFont()->setBold(true);
    
            // Đặt tiêu đề cột
            $sheet->setCellValue('A3', 'ID');
            $sheet->setCellValue('B3', 'Ngày mua');
            $sheet->setCellValue('C3', 'Tổng giá trị');
            $sheet->setCellValue('D3', 'Nhà cung cấp');
            $sheet->getStyle('A3:D3')->getFont()->setBold(true);
    
            // Điền dữ liệu
            $row = 4;
            foreach ($invoices as $invoice) {
                $sheet->setCellValue('A' . $row, $invoice['hoa_don_id']);
                $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($invoice['ngay_mua'])));
                $sheet->setCellValue('C' . $row, round($invoice['tong_gia_tri']));
                $sheet->setCellValue('D' . $row, $invoice['ten_nha_cung_cap']);
                $row++;
            }
    
            // Format số tiền
            $lastRow = $row - 1;
            $sheet->getStyle('C4:C' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
    
            // Thêm thống kê
            $row += 2;
            $sheet->setCellValue('A' . $row, 'Tổng số hóa đơn:');
            $sheet->setCellValue('B' . $row, $totalInvoices);
            $row++;
            $sheet->setCellValue('A' . $row, 'Tổng giá trị:');
            $sheet->setCellValue('B' . $row, round($totalValue));
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
            $sheet->setCellValue('A' . $row, 'Trung bình giá trị:');
            $sheet->setCellValue('B' . $row, round($avgValue));
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0');
    
            // Tự động điều chỉnh độ rộng cột
            foreach(range('A','D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
    
            // Tạo writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
            // Đặt headers để tải xuống file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Bao_cao_hoa_don_mua.xlsx"');
            header('Cache-Control: max-age=0');
    
            // Đảm bảo tất cả output buffer đã được xóa
            ob_end_clean();
    
            // Lưu file
            $writer->save('php://output');
            exit;
    
        } catch (PDOException $e) {
            die("Lỗi khi xuất Excel: " . $e->getMessage());
        }
    }
}
?>
