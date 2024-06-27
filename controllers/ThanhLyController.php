<?php
include_once 'config/database.php';
include_once 'models/ThanhLy.php';
include_once 'models/ChiTietThanhLy.php';

class ThanhLyController {
    private $db;
    private $thanhly;
    private $chitietThanhLy;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->thanhly = new ThanhLy($this->db);
        $this->chitietThanhLy = new ChiTietThanhLy($this->db);
    }

    public function index() {
       $stmt = $this->thanhly->readAllWithDetails();
        $hoa_don_thanh_lys = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/thanhly/index.php';
        include('views/layouts/base.php');
    }

    public function viewcreate() {
        $stmt = $this->thanhly->viewcreate();
        $taisans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/thanhly/create.php';
        include('views/layouts/base.php');
    }
     
    public function create()
    {
      if (isset($_POST['btnThem'])) {
        $ngay_thanh_ly = $_POST['ngay_thanh_ly'];
        $taisanadds = json_decode($_POST['hidden_taisans'], true);

        // Kiểm tra và xử lý dữ liệu từ form và JSON
        if (empty($ngay_thanh_ly) || empty($taisanadds) || !is_array($taisanadds)) {
            // Xử lý lỗi dữ liệu không hợp lệ
            echo "Dữ liệu không hợp lệ";
            return;
        }
    
        // Gán giá trị cho đối tượng
        $this->thanhly->ngay_thanh_ly = $ngay_thanh_ly;
        $this->thanhly->taisans = $taisanadds;


        // Tính toán tổng tiền
        $this->thanhly->tong_tien = 0;
        foreach ($taisanadds as $taisan) {
            if (isset($taisan['quantity']) && isset($taisan['price'])) {
                $this->thanhly->tong_tien += $taisan['quantity'] * $taisan['price'];
            }
        }


        // Gọi hàm create để lưu vào cơ sở dữ liệu
        if ($this->thanhly->create()) {
            echo"Thành công";
            var_dump($this->thanhly->taisans);
            header("Location: index.php?model=thanhly");
            $content = 'views/thanhly/index.php';
            include('views/layouts/base.php');
        } else {
            echo"Thất bại";
            $stmt = $this->thanhly->viewcreate();
            $taisans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $content = 'views/thanhly/create.php';
            include('views/layouts/base.php');
        }
    }
                
    }

    public function viewedit($id) {

    $ds = $this->thanhly->viewcreate();
    $taisans = $ds->fetchAll(PDO::FETCH_ASSOC);

    // Lấy thông tin hóa đơn thanh lý cần chỉnh sửa
    $tl = $this->thanhly->viewedit($id);
    $dstl = $tl->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn bị dữ liệu cho các tài sản đã thanh lý
    $taisansData = [];
    foreach ($dstl as $taisan) {
        $taisansData[] = [
            'id' => $taisan['tai_san_id'],
            'name' => $taisan['ten_tai_san'],
            'quantity' => $taisan['so_luong'],
            'price' => $taisan['gia_thanh_ly'],
            'total' => $taisan['so_luong'] * $taisan['gia_thanh_ly']
        ];
    }

    // Đưa dữ liệu vào view để hiển thị và chỉnh sửa
    $content = 'views/thanhly/edit.php';
    include('views/layouts/base.php');
    }


     public function show($id) {
        $tl = $this->thanhly->viewedit($id);
        $dstl = $tl->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/thanhly/show.php';
        include('views/layouts/base.php');
    }


public function edit($id) {
    try {
        // Bắt đầu giao dịch
        $this->db->beginTransaction();

        // Thực hiện các thao tác cập nhật thông tin chung của hóa đơn
        $this->thanhly->hoa_don_id = $id;
        $this->thanhly->ngay_thanh_ly = $_POST['ngay_mua'];
        $this->thanhly->tong_tien = $_POST['tong_gia_tri'];
        $this->thanhly->update($id);

        var_dump($_POST['tai_san']);
        // Xử lý cập nhật hoặc thêm mới chi tiết thanh lý (tài sản)
         for ($i = 0; $i < count($_POST['tai_san']); $i++) {
            $chi_tiet_id = $_POST['chi_tiet_id'][$i];
            $so_luong = $_POST['so_luong'][$i];
            $gia_thanh_ly = $_POST['gia_thanh_ly'][$i];
            $taisan = $_POST['tai_san'][$i];
            // Nếu chi_tiet_id đã tồn tại, cập nhật lại chi tiết
            if($_POST['tai_san_id'][$i]!=''){
                $this->chitietThanhLy->chi_tiet_id = $chi_tiet_id;
                $this->chitietThanhLy->so_luong = $so_luong;
                $this->chitietThanhLy->gia_thanh_ly = $gia_thanh_ly;
                $this->chitietThanhLy->update();
            } else {
                // Nếu chi_tiet_id chưa tồn tại, thêm mới chi tiết
                $this->chitietThanhLy->hoa_don_id = $id;
                $this->chitietThanhLy->tai_san_id = $taisan;
                $this->chitietThanhLy->so_luong = $so_luong;
                $this->chitietThanhLy->gia_thanh_ly = $gia_thanh_ly;
                $this->chitietThanhLy->create();
            }
        }

        // Commit giao dịch
        $this->db->commit();

        // Thông báo thành công và chuyển hướng về trang danh sách
        $_SESSION['message'] = 'Sửa hóa đơn thành công!';
        $_SESSION['message_type'] = 'success';
        header("Location: index.php?model=thanhly");
        exit();
    } catch (Exception $e) {
        // Xử lý ngoại lệ và roll back giao dịch nếu có lỗi
        $this->db->rollBack();
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }

    // Load lại dữ liệu cũ để hiển thị lại form nếu có lỗi hoặc cần chỉnh sửa
    $ds = $this->thanhly->viewcreate();
    $taisans = $ds->fetchAll(PDO::FETCH_ASSOC);

    $tl = $this->thanhly->viewedit($id);
    $dstl = $tl->fetchAll(PDO::FETCH_ASSOC);

    // Load view để hiển thị form chỉnh sửa
    $content = 'views/thanhly/edit.php';
    include('views/layouts/base.php');
}
    public function delete($id) {
        if ($this->thanhly->delete($id)) {
            $_SESSION['message'] = 'Xóa hóa đơn thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Xóa hóa đơn thất bại!';
            $_SESSION['message_type'] = 'danger';
        }
        header("Location: index.php?model=thanhly");
    }

    public function saveMultiple() {
        
    }

     public function export() {
        try {
            // Truy vấn dữ liệu
            $sql = "SELECT tl.hoa_don_id, tl.ngay_thanh_ly, tl.tong_gia_tri, ts.ten_tai_san, tlct.gia_thanh_ly, tlct.so_luong
        FROM hoa_don_thanh_ly tl
        JOIN chi_tiet_hoa_don_thanh_ly tlct ON tl.hoa_don_id = tlct.hoa_don_id
        JOIN tai_san ts ON ts.tai_san_id = tlct.tai_san_id";  // Chỉnh sửa tên bảng chi tiết hóa đơn thành lý và tài sản

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Tính toán thống kê
            $totalInvoices = count($orders);
            $totalValue = array_sum(array_column($orders, 'tong_gia_tri'));
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
            $sheet->getStyle('A3:C3')->getFont()->setBold(true);
    
            // Điền dữ liệu
            $row = 3;
            foreach ($orders as $order) {
                $sheet->setCellValue('A' . $row, $order['hoa_don_id']);
                $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($order['ngay_thanh_ly'])));
                $sheet->setCellValue('C' . $row, round($order['tong_gia_tri']));
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
            header('Content-Disposition: attachment; filename="Bao_cao_hoa_don_thanh_ly.xlsx"');
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



