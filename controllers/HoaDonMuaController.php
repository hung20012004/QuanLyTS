<?php
// controllers/HoaDonMuaController.php
include_once 'config/database.php';
include_once 'models/HoaDonMua.php';
include_once 'models/NhaCungCap.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ChiTietHoaDonMua.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTriChiTiet.php';

class HoaDonMuaController extends Controller
{
    private $db;
    private $hoaDonMuaModel;
    private $nhaCungCapModel;
    private $loaiTaiSanModel;
    private $chiTietHoaDonMuaModel;
    private $taiSanModel;
    private $viTriChiTietModel;


    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getconnection();
        $this->hoaDonMuaModel = new HoaDonMua($this->db);
        $this->nhaCungCapModel = new NhaCungCap($this->db);
        $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
        $this->chiTietHoaDonMuaModel = new ChiTietHoaDonMua($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);
    }

    public function index()
    {
        $invoices = $this->hoaDonMuaModel->readAll();
        $suppliers = $this->nhaCungCapModel->read();
        $stmtLoaiTaiSan = $this->loaiTaiSanModel->readAll();
        $content = 'views/hoa_don_mua/index.php';
        include ('views/layouts/base.php');
    }

    public function create()
    {
        if ($_POST) {
            // echo '<pre>' , var_dump($_POST) , '</pre>';
            //     exit();
            $this->db->beginTransaction();

            try {
                $this->hoaDonMuaModel->ngay_mua = $_POST['ngay_mua'];
                $this->hoaDonMuaModel->tong_gia_tri = $_POST['tong_gia_tri'];
                $this->hoaDonMuaModel->nha_cung_cap_id = $_POST['nha_cung_cap_id'];

                if ($this->hoaDonMuaModel->create()) {
                    $hoaDonMuaId = $this->db->lastInsertId();

                    for ($i = 0; $i < count($_POST['ten_tai_san']); $i++) {
                        $taiSanId = $_POST['tai_san_id'][$i];
                        if ($_POST['tai_san_id'][$i] == "") {

                            $taiSanData = array(
                                'ten_tai_san' => $_POST['ten_tai_san'][$i],
                                'mo_ta' => '',
                                'so_luong' => $_POST['so_luong'][$i],
                                'loai_tai_san_id' => $_POST['loai_tai_san_id'][$i]
                            );
                            $taiSanId = $this->taiSanModel->createOrUpdate($taiSanData);
                        }
                        // echo '<pre>' , var_dump($_POST) , '</pre>';
                        //     exit();
                        $this->chiTietHoaDonMuaModel->hoa_don_id = $hoaDonMuaId;
                        $this->chiTietHoaDonMuaModel->tai_san_id = $taiSanId;
                        $this->chiTietHoaDonMuaModel->so_luong = $_POST['so_luong'][$i];
                        $this->chiTietHoaDonMuaModel->don_gia = $_POST['don_gia'][$i];
                        $this->chiTietHoaDonMuaModel->create();
                        $chiTietID = $this->db->lastInsertId();
                        ;

                        $viTri = new ViTriChiTiet($this->db);
                        $viTri->chi_tiet_id = $chiTietID;
                        $viTri->vi_tri_id = 1;
                        $viTri->so_luong = $_POST['so_luong'][$i];
                        $viTri->create();

                    }

                    $this->db->commit();

                    $_SESSION['message'] = 'Tạo hóa đơn mới thành công!';
                    $_SESSION['message_type'] = 'success';
                    header("Location: index.php?model=hoadonmua");
                } else {
                    $_SESSION['message'] = 'Tạo hóa đơn mới thất bại!';
                    $_SESSION['message_type'] = 'danger';
                    header("Location: index.php?model=hoadonmua");
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        }

        $stmtLoaiTaiSan = $this->loaiTaiSanModel->readAll();
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        $suppliers = $this->nhaCungCapModel->read();
        $content = 'views/hoa_don_mua/create.php';
        include ('views/layouts/base.php');
    }
    public function edit($id)
    {
        $invoice = $this->hoaDonMuaModel->readById($id);
        $invoice_details = $this->chiTietHoaDonMuaModel->readByHoaDonId($id);
        $quantityMismatch = false;

        // Kiểm tra số lượng trước khi xử lý POST
        foreach ($invoice_details as $detail) {
            if (!$this->checkQuantityMatch($detail['chi_tiet_id'], $detail['so_luong'])) {
                $quantityMismatch = true;
                break;
            }
        }

        if ($_POST) {
            if ($quantityMismatch) {
                $_SESSION['message'] = 'Không thể sửa hóa đơn do số lượng không khớp với vị trí chi tiết!';
                $_SESSION['message_type'] = 'danger';
            } else {
                $this->db->beginTransaction();
                try {
                    $this->hoaDonMuaModel->hoa_don_id = $id;
                    $this->hoaDonMuaModel->ngay_mua = $_POST['ngay_mua'];
                    $this->hoaDonMuaModel->tong_gia_tri = $_POST['tong_gia_tri'];
                    $this->hoaDonMuaModel->nha_cung_cap_id = $_POST['nha_cung_cap_id'];
                    $this->hoaDonMuaModel->update();

                    for ($i = 0; $i < count($_POST['ten_tai_san']); $i++) {
                        $taiSanId = $_POST['tai_san_id'][$i];
                        if (empty($taiSanId)) {
                            $taiSanData = array(
                                'ten_tai_san' => $_POST['ten_tai_san'][$i],
                                'mo_ta' => '',
                                'so_luong' => $_POST['so_luong'][$i],
                                'loai_tai_san_id' => $_POST['loai_tai_san_id'][$i]
                            );
                            $taiSanId = $this->taiSanModel->createOrUpdate($taiSanData);
                        }

                        $this->chiTietHoaDonMuaModel->hoa_don_id = $id;
                        $this->chiTietHoaDonMuaModel->tai_san_id = $taiSanId;
                        $this->chiTietHoaDonMuaModel->so_luong = $_POST['so_luong'][$i];
                        $this->chiTietHoaDonMuaModel->don_gia = $_POST['don_gia'][$i];

                        if (empty($_POST['chi_tiet_id'][$i])) {
                            $this->chiTietHoaDonMuaModel->create();
                            $chiTietID = $this->db->lastInsertId();
                            $this->viTriChiTietModel->create($chiTietID, 1, $_POST['so_luong'][$i]);
                        } else {
                            $this->chiTietHoaDonMuaModel->chi_tiet_id = $_POST['chi_tiet_id'][$i];
                            $this->chiTietHoaDonMuaModel->update();
                            $this->viTriChiTietModel->update($_POST['chi_tiet_id'][$i], 1, $_POST['so_luong'][$i]);
                        }
                    }

                    if (!empty($_POST['deleted_chi_tiet_id'])) {
                        foreach ($_POST['deleted_chi_tiet_id'] as $chi_tiet_id) {
                            $this->chiTietHoaDonMuaModel->delete($chi_tiet_id);
                            $this->viTriChiTietModel->delete($chi_tiet_id);
                        }
                    }

                    $this->db->commit();
                    $_SESSION['message'] = 'Sửa hóa đơn thành công!';
                    $_SESSION['message_type'] = 'success';
                    header('Location: index.php?model=hoadonmua&action=index');
                    exit();
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $_SESSION['message'] = $e->getMessage();
                    $_SESSION['message_type'] = 'danger';
                }
            }
        }

        $suppliers = $this->nhaCungCapModel->read();
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/hoa_don_mua/edit.php';
        include('views/layouts/base.php');
    }

    public function delete($id)
    {
        $invoice_details = $this->chiTietHoaDonMuaModel->readByHoaDonId($id);
        $quantityMismatch = false;
    
        foreach ($invoice_details as $detail) {
            if (!$this->checkQuantityMatch($detail['chi_tiet_id'], $detail['so_luong'])) {
                $quantityMismatch = true;
                break;
            }
        }
        $response = [];
    
        if ($quantityMismatch) {
            $response['success'] = false;
            $response['message'] = 'Không thể xóa hóa đơn do số lượng không khớp với vị trí chi tiết!';
        } else {
            if ($this->hoaDonMuaModel->delete($id)) {
                $response['success'] = true;
                $response['message'] = 'Xóa hóa đơn thành công!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Xóa hóa đơn thất bại!';
            }
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function checkQuantityMatch($chiTietId, $soLuong)
    {
        $viTriQuantity = $this->viTriChiTietModel->getQuantityByChiTietIdAndViTriId($chiTietId, 1);
        return $viTriQuantity == $soLuong;
    }
    public function export()
    {
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
            foreach (range('A', 'D') as $col) {
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
    public function show($id)
    {
        $hoadon = $this->hoaDonMuaModel->readById($id);
        $suppliers = $this->nhaCungCapModel->read();
        $chitiethoadon = $this->chiTietHoaDonMuaModel->readByHoaDonId($id);
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();

        $content = 'views/hoa_don_mua/show.php';
        include ('views/layouts/base.php');
    }
}
?>