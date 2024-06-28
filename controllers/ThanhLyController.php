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
        $stmt = $this->thanhly->readAll();
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
    if ($_POST) {
        $ngay_thanh_ly = $_POST['ngay_thanh_ly'];
        $taisanadds = json_decode($_POST['hidden_taisans'], true);

        // Kiểm tra và xử lý dữ liệu từ form và JSON
        if (empty($ngay_thanh_ly) || empty($taisanadds) || !is_array($taisanadds)) {
            $_SESSION['message'] = "Dữ liệu không hợp lệ";
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=thanhly&action=create");
            exit();
        }
        var_dump($taisanadds);
        // Tính toán tổng tiền từ các chi tiết tài sản
        $tong_tien = 0;
        foreach ($taisanadds as $taisan) {
            $tong_tien += $taisan['quantity'] * $taisan['price'];
        }

        // Bắt đầu giao dịch
        $this->db->beginTransaction();

        try {
            // Lấy danh sách tài sản từ bảng vi_tri_chi_tiet và kiểm tra số lượng
            $taisanIds = array_column($taisanadds, 'id');
            $taisanIdsPlaceholder = implode(',', array_fill(0, count($taisanIds), '?'));

            $stmt = $this->db->prepare("SELECT tai_san_id, SUM(so_luong) AS total_quantity FROM vi_tri_chi_tiet WHERE tai_san_id IN ($taisanIdsPlaceholder) GROUP BY tai_san_id");
            $stmt->execute($taisanIds);
            $currentQuantities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $currentQuantitiesMap = [];
            foreach ($currentQuantities as $quantity) {
                $currentQuantitiesMap[$quantity['tai_san_id']] = $quantity['total_quantity'];
            }

            // Kiểm tra số lượng yêu cầu có vượt quá số lượng hiện có không
            $errors = [];
            foreach ($taisanadds as $taisan) {
                if (!isset($currentQuantitiesMap[$taisan['id']]) || $taisan['quantity'] > $currentQuantitiesMap[$taisan['id']]) {
                    $errors[] = "Không đủ số lượng cho tài sản ID: " . $taisan['id'] . ". Số lượng hiện có: " . ($currentQuantitiesMap[$taisan['id']] ?? 0);
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['message_type'] = 'danger';
                header("Location: index.php?model=thanhly&action=create");
                exit();
            }

            // Chèn dữ liệu vào bảng thanh lý và lấy ID của hóa đơn vừa chèn
            $query = "INSERT INTO hoa_don_thanh_ly (ngay_thanh_ly, tong_gia_tri) VALUES (:ngay_thanh_ly, :tong_tien)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':ngay_thanh_ly', $ngay_thanh_ly);
            $stmt->bindParam(':tong_tien', $tong_tien);
            $stmt->execute();
            $hoa_don_thanh_ly_id = $this->db->lastInsertId();

            // Cập nhật số lượng tài sản trong bảng vi_tri_chi_tiet và chèn dữ liệu vào bảng chi tiết thanh lý
            foreach ($taisanadds as $taisan) {
                $remainingQuantity = $taisan['quantity'];
                // $stmt1 = $this->db->prepare("SELECT vi_tri_id FROM vi_tri_chi_tiet WHERE tai_san_id = ? AND so_luong > 0");
                // $stmt1->execute([$taisan['id']]);
                // $vi_tri_id = $stmt1->fetchColumn();

                $stmt = $this->db->prepare("SELECT vi_tri_id, so_luong FROM vi_tri_chi_tiet WHERE tai_san_id = ? AND so_luong > 0 ");
                $stmt->execute([$taisan['id']]);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($remainingQuantity <= 0) break;
                    $updateQuantity = min($row['so_luong'], $remainingQuantity);
                    $newQuantity = $row['so_luong'] - $updateQuantity;
                    $updateStmt = $this->db->prepare("UPDATE vi_tri_chi_tiet SET so_luong = ? WHERE tai_san_id = ? AND vi_tri_id = ?");
                    $updateStmt->execute([$newQuantity, $taisan['id'], $row['vi_tri_id']]);
                    $remainingQuantity -= $updateQuantity;
                }

                // Tạo chi tiết hóa đơn thanh lý
                $query_detail = "INSERT INTO chi_tiet_hoa_don_thanh_ly (hoa_don_id, tai_san_id, so_luong, gia_thanh_ly) VALUES (:hoa_don_id, :tai_san_id, :so_luong, :gia_thanh_ly)";
                $stmt_detail = $this->db->prepare($query_detail);
                $stmt_detail->bindParam(':hoa_don_id', $hoa_don_thanh_ly_id);
                $stmt_detail->bindParam(':tai_san_id', $taisan['id']);
                $stmt_detail->bindParam(':so_luong', $taisan['quantity']);
                $stmt_detail->bindParam(':gia_thanh_ly', $taisan['price']);
                $stmt_detail->execute();
            }

            // Commit giao dịch
            $this->db->commit();

            $_SESSION['message'] = 'Tạo hóa đơn thanh lý mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=thanhly");
            exit();
        } catch (PDOException $e) {
            // Xử lý lỗi và rollback giao dịch
            $this->db->rollBack();
            $_SESSION['message'] = 'Tạo hóa đơn thanh lý mới thất bại: ' . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=thanhly&action=create");
            exit();
        }
    }

    // Load lại dữ liệu cần thiết để hiển thị lại form
    $stmt = $this->thanhly->viewcreate();
    $taisans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $content = 'views/thanhly/create.php';
    include('views/layouts/base.php');
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
  if($_POST){
      try {
        // Bắt đầu giao dịch
        $this->db->beginTransaction();

        // Thực hiện các thao tác cập nhật thông tin chung của hóa đơn
        $this->thanhly->hoa_don_id = $id;
        $this->thanhly->ngay_thanh_ly = $_POST['ngay_thanh_ly'];
        $this->thanhly->tong_tien = $_POST['tong_tien'];
        $this->thanhly->update($id);

        // Lấy danh sách chi tiết hiện tại trong DB
        $currentDetails = $this->chitietThanhLy->getByHoaDonId($id);
        $currentDetailIds = array_column($currentDetails, 'chi_tiet_id');

        // Duyệt qua các chi tiết từ form
        $newDetailIds = [];

        for ($i = 0; $i < count($_POST['tai_san']); $i++) {
            $chi_tiet_id = $_POST['chi_tiet_id'][$i];
            $so_luong = $_POST['so_luong'][$i];
            $gia_thanh_ly = $_POST['gia_thanh_ly'][$i];
            $taisan = $_POST['tai_san'][$i];

            if (!empty($chi_tiet_id)) {
                // Cập nhật chi tiết nếu tồn tại
                $this->chitietThanhLy->chi_tiet_id = $chi_tiet_id;
                $this->chitietThanhLy->so_luong = $so_luong;
                $this->chitietThanhLy->gia_thanh_ly = $gia_thanh_ly;
                $this->chitietThanhLy->update();

                // Kiểm tra số lượng và cập nhật lại bảng vi_tri_chi_tiet
                $this->updateQuantity($chi_tiet_id, $so_luong-$currentDetails['so_luong']);
            } else {
                // Thêm mới chi tiết nếu chưa tồn tại
                $this->chitietThanhLy->hoa_don_id = $id;
                $this->chitietThanhLy->tai_san_id = $taisan;
                $this->chitietThanhLy->so_luong = $so_luong;
                $this->chitietThanhLy->gia_thanh_ly = $gia_thanh_ly;
                $this->chitietThanhLy->create();

                // Giảm đi số lượng trong bảng vi_tri_chi_tiet
                $this->updateQuantity($taisan, -$so_luong, $taisan);
            }

            // Lưu lại ID của chi tiết mới hoặc được cập nhật
            $newDetailIds[] = $chi_tiet_id;
        }

        // Xóa các chi tiết không còn trong danh sách mới
        foreach ($currentDetailIds as $currentId) {
            if (!in_array($currentId, $newDetailIds)) {
                $detailToDelete = $this->chitietThanhLy->getById($currentId);
                if ($detailToDelete) {
                    // Giảm số lượng trong bảng vi_tri_chi_tiet
                    $this->updateQuantity(null, $detailToDelete['so_luong'], $detailToDelete['tai_san_id']);
                    // Xóa chi tiết thanh lý
                    $this->chitietThanhLy->delete($currentId);
                }
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
        header("Location: index.php?model=thanhly&action=edit&id=$id");
        exit();
    }
  }
}

private function updateQuantity($chi_tiet_id = null, $so_luong_change=0, $tai_san_id = null) {
    if (!is_null($chi_tiet_id)) {
        // Lấy thông tin chi tiết cần cập nhật
        $detail = $this->chitietThanhLy->getById($chi_tiet_id); // Phương thức này cần phải tồn tại trong model ChiTietThanhLy
        $so_luong = $detail['so_luong'];
        $tai_san_id = $detail['tai_san_id'];
        $hdid = $detail['hoa_don_id'];
    } else {
        $so_luong = 0;
    }
    
    // Lấy số lượng hiện có từ bảng vi_tri_chi_tiet
    $stmt = $this->db->prepare("SELECT so_luong FROM vi_tri_chi_tiet WHERE tai_san_id = ?");
    $stmt->execute([$tai_san_id]);
    $stmt1 = $this->db->prepare("SELECT ten_tai_san FROM tai_san WHERE tai_san_id = ?");
    $stmt1->execute([$tai_san_id]);
    $Tents = $stmt1->fetchColumn();
    $currentQuantity = $stmt->fetchColumn();

    if($so_luong_change>0)
    {
        $newQuantity = $currentQuantity-$so_luong_change;
    }
    elseif($so_luong_change<0)
    {
        $newQuantity = $currentQuantity + $so_luong_change;
    }
    // Kiểm tra và cập nhật lại số lượng

    // Kiểm tra số lượng mới có hợp lệ không
    if ($newQuantity < 0) {
        $_SESSION['message'] = "Số lượng không đủ để cập nhật, số lượng của ".$Tents." hiện tại là ".$currentQuantity."";
     
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=thanhly&action=viewedit&id=".$hdid."");
            exit();

    } elseif ($newQuantity > 0 && $so_luong_change < 0 && $newQuantity > $so_luong_change) {
        $_SESSION['message'] = "Số lượng giảm đi quá nhiều, số lượng của ".$Tents." hiện tại là ".$currentQuantity."";
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=thanhly&action=viewedit&id=".$hdid."");
            exit();
    }

    $updateStmt = $this->db->prepare("UPDATE vi_tri_chi_tiet SET so_luong = ? WHERE tai_san_id = ?");
    $updateStmt->execute([$newQuantity, $tai_san_id]);
}
    
    
        public function delete($id) {
        if ($this->thanhly->delete($id)) {
            $_SESSION['message'] = 'Xóa hóa đơn thành công!';
            $_SESSION['message_type'] = 'success';
             header("Location: index.php?model=thanhly");
        } else {
            $_SESSION['message'] = 'Xóa hóa đơn thất bại!';
            $_SESSION['message_type'] = 'danger';
             header("Location: index.php?model=thanhly");
        }
    }

    public function saveMultiple() {
        
    }

     public function export() {
    try {
        // Truy vấn dữ liệu
        $sql = "SELECT tl.hoa_don_id, tl.ngay_thanh_ly, tl.tong_gia_tri, ts.ten_tai_san, tlct.gia_thanh_ly, tlct.so_luong
                FROM hoa_don_thanh_ly tl
                JOIN chi_tiet_hoa_don_thanh_ly tlct ON tl.hoa_don_id = tlct.hoa_don_id
                JOIN tai_san ts ON ts.tai_san_id = tlct.tai_san_id"; 

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính toán thống kê
        $totalInvoices = count($orders);
        $totalValue = array_sum(array_column($orders, 'tong_gia_tri'));
        $avgValue = $totalInvoices > 0 ? $totalValue / $totalInvoices : 0;

        // Tạo đối tượng Spreadsheet và sheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        // Đặt tiêu đề
        $sheet->setCellValue('A1', 'Báo cáo Hóa đơn thanh lý');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCEEFF');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Đặt tiêu đề cột
        $sheet->setCellValue('A2', 'ID');
        $sheet->setCellValue('B2', 'Ngày thanh lý');
        $sheet->setCellValue('C2', 'Tài sản');
        $sheet->setCellValue('D2', 'Giá thanh lý');
        $sheet->setCellValue('E2', 'Số lượng');
        $sheet->setCellValue('F2', 'Tổng giá trị');
        $sheet->getStyle('A2:F2')->getFont()->setBold(true);

        // Điền dữ liệu từ mảng vào sheet
        $row = 3;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $order['hoa_don_id']);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($order['ngay_thanh_ly'])));
            $sheet->setCellValue('C' . $row, $order['ten_tai_san']);
            $sheet->setCellValue('D' . $row, $order['gia_thanh_ly']);
            $sheet->setCellValue('E' . $row, $order['so_luong']);
            $sheet->setCellValue('F' . $row, $order['gia_thanh_ly'] * $order['so_luong']);
            $row++;
        }

        // Định dạng số và viền cho các ô
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A3:F' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A1:F' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Tự động điều chỉnh độ rộng cột
        foreach(range('A','F') as $col) {
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



