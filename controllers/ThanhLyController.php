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
        $stmt = $this->db->prepare(
                "SELECT cthd.tai_san_id, ts.ten_tai_san
                FROM vi_tri vt
                JOIN vi_tri_chi_tiet vtct ON vt.vi_tri_id = vtct.vi_tri_id
                JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id= cthd.chi_tiet_id
                JOIN tai_san ts ON ts.tai_san_id= cthd.tai_san_id
                WHERE vt.vi_tri_id=1 AND vtct.so_luong > 0
                GROUP BY cthd.tai_san_id   
                ORDER BY ts.ten_tai_san ASC");
        $stmt->execute();
        $taisans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/thanhly/create.php';
        include('views/layouts/base.php');
    }
     
    public function create()
    {
        try {
            
            $this->db->beginTransaction();
            $tong_cong = 0;

            // Tính tổng cộng
            for ($i = 0; $i < count($_POST['tai_san_id']); $i++) {
                $tong_cong += $_POST['so_luong'][$i] * $_POST['gia_thanh_ly'][$i];
            }

            // Lấy dữ liệu từ form
            $ngay_thanh_ly = $_POST['ngay_thanh_ly'];

            // Thêm vào bảng hoa_don_thanh_ly
            $stmt = $this->db->prepare("INSERT INTO hoa_don_thanh_ly (ngay_thanh_ly, tong_gia_tri) VALUES (:ngay_thanh_ly, :tong_gia_tri)");
            $stmt->bindParam(':ngay_thanh_ly', $ngay_thanh_ly);
            $stmt->bindParam(':tong_gia_tri', $tong_cong);
            $stmt->execute();

            // Lấy ID của hóa đơn thanh lý vừa tạo
            $hoa_don_thanh_ly_id = $this->db->lastInsertId();

            // Thêm chi tiết hóa đơn vào bảng chi_tiet_hoa_don_thanh_ly
            $tai_san_ids = $_POST['tai_san_id'];
            $so_luongs = $_POST['so_luong'];
            $gia_thanh_lys = $_POST['gia_thanh_ly'];
            $ngaymuas = $_POST['ngay_mua'];
            for ($i = 0; $i < count($tai_san_ids); $i++) {
                $tai_san_id = $tai_san_ids[$i];
                $so_luong = $so_luongs[$i];
                $gia_thanh_ly = $gia_thanh_lys[$i];
                $ngay_mua = $ngaymuas[$i];

                // Kiểm tra số lượng tài sản
                $sql = "SELECT vtct.so_luong
                        FROM vi_tri vt
                        JOIN vi_tri_chi_tiet vtct ON vt.vi_tri_id = vtct.vi_tri_id
                        JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id = cthd.chi_tiet_id
                        JOIN hoa_don_mua hdm ON hdm.hoa_don_id = cthd.hoa_don_id
                        JOIN tai_san ts ON ts.tai_san_id = cthd.tai_san_id
                        WHERE vt.vi_tri_id = 1 AND cthd.tai_san_id = ? AND hdm.ngay_mua = ? ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$tai_san_id, $ngay_mua]);
                $current_so_luong = $stmt->fetchColumn();

                if ($current_so_luong < $so_luong) {
                    // Thông báo lỗi và hủy bỏ phiên giao dịch
                    $_SESSION['message'] = "Số lượng tài sản không đủ để thanh lý.";
                    $_SESSION['message_type'] = "danger";
                    $this->db->rollBack();
                    header("Location: index.php?model=thanhly&action=viewcreate");
                    return;
                }

                // Trừ số lượng tài sản trong bảng vi_tri_chi_tiet
                $sql = "UPDATE vi_tri_chi_tiet 
                        SET so_luong = so_luong - :so_luong 
                        WHERE chi_tiet_id IN (
                            SELECT vtct.chi_tiet_id
                            FROM vi_tri vt
                            JOIN vi_tri_chi_tiet vtct ON vt.vi_tri_id = vtct.vi_tri_id
                            JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id = cthd.chi_tiet_id
                            JOIN hoa_don_mua hdm ON hdm.hoa_don_id = cthd.hoa_don_id
                            WHERE vt.vi_tri_id = 1 AND cthd.tai_san_id = :tai_san_id AND hdm.ngay_mua= :ngay_mua)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['so_luong' => $so_luong, 'tai_san_id' => $tai_san_id , 'ngay_mua' => $ngay_mua ]);

                // Thêm chi tiết hóa đơn thanh lý
                $stmt = $this->db->prepare("INSERT INTO chi_tiet_hoa_don_thanh_ly (hoa_don_id, tai_san_id, so_luong, gia_thanh_ly) VALUES (:hoa_don_thanh_ly_id, :tai_san_id, :so_luong, :gia_thanh_ly)");
                $stmt->bindParam(':hoa_don_thanh_ly_id', $hoa_don_thanh_ly_id);
                $stmt->bindParam(':tai_san_id', $tai_san_id);
                $stmt->bindParam(':so_luong', $so_luong);
                $stmt->bindParam(':gia_thanh_ly', $gia_thanh_ly);
                $stmt->execute();
            }

            $this->db->commit();

            // Chuyển hướng đến trang danh sách hóa đơn với thông báo thành công
            $_SESSION['message'] = 'Tạo hóa đơn thành công!';
            $_SESSION['message_type'] = 'success';
            header('Location: index.php?model=thanhly&action=index');
            exit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            $_SESSION['message'] = 'Lỗi khi tạo hóa đơn: ' . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php?model=thanhly&action=create');
            exit();
        }
    }


    public function viewedit($id) {
        $stmt = $this->thanhly->viewcreate();
        $taisans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy thông tin hóa đơn thanh lý cần chỉnh sửa
    $tl = $this->thanhly->viewedit($id);
    $dstl = $tl->fetchAll(PDO::FETCH_ASSOC);

    // // Chuẩn bị dữ liệu cho các tài sản đã thanh lý
    // $taisansData = [];
    // foreach ($dstl as $taisan) {
    //     $taisansData[] = [
    //         'id' => $taisan['tai_san_id'],
    //         'name' => $taisan['ten_tai_san'],
    //         'quantity' => $taisan['so_luong'],
    //         'price' => $taisan['gia_thanh_ly'],
    //         'total' => $taisan['so_luong'] * $taisan['gia_thanh_ly']
    //     ];
    // }

    // Đưa dữ liệu vào view để hiển thị và chỉnh sửa
    $content = 'views/thanhly/edit.php';
    include('views/layouts/base.php');
    }


     public function show($id) {
        $stmt = $this->db->prepare(
                "SELECT *
                FROM hoa_don_thanh_ly hd
                JOIN chi_tiet_hoa_don_thanh_ly cthd ON hd.hoa_don_id= cthd.hoa_don_id
                JOIN tai_san ts ON ts.tai_san_id= cthd.tai_san_id
                WHERE hd.hoa_don_id = ?");
        $stmt->execute([$id]);
        $dstl = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/thanhly/show.php';
        include('views/layouts/base.php');
    }

public function edit($id) {
    if ($_POST) {
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
            // $soluonghientai = $currentDetails['so_luong']

            // Duyệt qua các chi tiết từ form
            foreach ($_POST['chi_tiet_id'] as $index => $chi_tiet_id) {
                $so_luong = $_POST['so_luong'][$index];
                $gia_thanh_ly = $_POST['gia_thanh_ly'][$index];
                $taisan = $_POST['tai_san_id'][$index];
                $ngaymua = $_POST['ngay_mua'][$index];
                // Tìm chi tiết hóa đơn trong danh sách hiện tại
                $found = false;
                foreach ($currentDetails as $detail) {
                    if ($detail['chi_tiet_id'] == $chi_tiet_id) {
                        // Cập nhật chi tiết nếu tồn tại
                        $this->chitietThanhLy->chi_tiet_id = $chi_tiet_id;
                        $this->chitietThanhLy->so_luong = $so_luong;
                        $this->chitietThanhLy->gia_thanh_ly = $gia_thanh_ly;
                        $this->chitietThanhLy->tai_san_id = $taisan;
                        $this->chitietThanhLy->update();
                        $this->updateQuantity($id,$so_luong- $detail['so_luong'],$taisan, $ngaymua);
                        // Xóa chi tiết khỏi danh sách hiện tại để loại bỏ sau khi cập nhật
                        unset($currentDetails[array_search($detail, $currentDetails)]);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    // Thêm mới chi tiết nếu không tồn tại trong danh sách hiện tại
                    $this->chitietThanhLy->hoa_don_id = $id;
                    $this->chitietThanhLy->tai_san_id = $taisan;
                    $this->chitietThanhLy->so_luong = $so_luong;
                    $this->chitietThanhLy->gia_thanh_ly = $gia_thanh_ly;
                    $this->chitietThanhLy->create_for_update($id, $ngaymua);
                    $this->updateQuantity($id,$so_luong, $taisan, $ngaymua);
                }
            }

            // Xóa các chi tiết không còn trong danh sách mới
            foreach ($currentDetails as $detail) {
                $this->chitietThanhLy->delete($detail['chi_tiet_id']);
                 $this->updateQuantity($id,-$detail['so_luong'] , $detail['tai_san_id'],$detail['ngay_mua'] );
            }

            // Commit giao dịch
            $this->db->commit();

            // Thông báo thành công và chuyển hướng về trang danh sách
            $_SESSION['message'] = 'Sửa hóa đơn thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=thanhly&action=index");
            exit();
        } catch (Exception $e) {
            // Xử lý ngoại lệ và roll back giao dịch nếu có lỗi
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=thanhly&action=viewedit&id=$id");
            exit();
        }
    }
}
public function updateQuantity($id, $so_luong_change, $tai_san_id, $ngay_mua) {
   
        // $stmt1 = $this->db->prepare("SELECT vtct.chi_tiet_id, vtct.so_luong 
        //         FROM vi_tri vt
        //         JOIN vi_tri_chi_tiet vtct ON vt.vi_tri_id = vtct.vi_tri_id
        //         JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id= cthd.chi_tiet_id
        //         WHERE vt.vi_tri_id=1 AND cthd.tai_san_id = ? AND vtct.so_luong > 0 ");
        //         $stmt1->execute([$tai_san_id]);
        //         $vitri= $stmt1->fetchAll(PDO::FETCH_ASSOC);
        //         $firstChi_tiet = isset($vitri['chi_tiet_id']) ? $vitri['chi_tiet_id'] : null;


                $stmt = $this->db->prepare(
                "SELECT vtct.so_luong 
                FROM vi_tri vt
                JOIN vi_tri_chi_tiet vtct ON vt.vi_tri_id = vtct.vi_tri_id
                JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id= cthd.chi_tiet_id
                WHERE vt.vi_tri_id=1 AND cthd.tai_san_id = ? AND vtct.so_luong>0");
            $stmt->execute([$tai_san_id]);
            $currentQuantity = $stmt->fetchColumn();


    $stmt1 = $this->db->prepare("SELECT ten_tai_san FROM tai_san WHERE tai_san_id = ?");
    $stmt1->execute([$tai_san_id]);
    $Tents = $stmt1->fetchColumn();
    
    $newQuantity = $currentQuantity;
    if($so_luong_change>0 && $so_luong_change<$currentQuantity)
    {
        $newQuantity = $currentQuantity - $so_luong_change;
        //  $_SESSION['message'] = " số lượng của ".$Tents." hiện tại là ".$currentQuantity."  newquantity la ".$newQuantity."";
        //     $_SESSION['message_type'] = 'danger';
        //     header("Location: index.php?model=thanhly&action=viewedit&id=".$id."");
        //     exit();
    }
    elseif($so_luong_change<0)
    {
        $newQuantity = $currentQuantity - $so_luong_change;
        //  $_SESSION['message'] = "Số lượng giảm đi quá nhiều, số lượng của ".$Tents." hiện tại là ".$currentQuantity."  newquantity la ".$newQuantity."";
        //     $_SESSION['message_type'] = 'danger';
        //     header("Location: index.php?model=thanhly&action=viewedit&id=".$id."");
        //     exit();

    }
    // Kiểm tra và cập nhật lại số lượng

    // Kiểm tra số lượng mới có hợp lệ không
    if ($newQuantity < 0 ) {
             $_SESSION['message'] = "Số lượng không đủ để cập nhật, số lượng của ".$Tents." hiện tại là ".$currentQuantity." so luong change la".$so_luong_change."";
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=thanhly&action=viewedit&id=".$id."");
            exit();
    }

    //  elseif ($newQuantity < 0 && $so_luong_change < 0 && $newQuantity > $so_luong_change) {
    //     $_SESSION['message'] = "Số lượng giảm đi quá nhiều, số lượng của ".$Tents." hiện tại là ".$currentQuantity."  so luong change la".$so_luong_change."";
    //         $_SESSION['message_type'] = 'danger';
    //         header("Location: index.php?model=thanhly&action=viewedit&id=".$id."");
    //         exit();
    // }

    $updateStmt = $this->db->prepare("UPDATE vi_tri_chi_tiet vtct 
                    JOIN chi_tiet_hoa_don_mua cthd ON vtct.chi_tiet_id= cthd.chi_tiet_id
                    JOIN vi_tri vt ON vt.vi_tri_id = vtct.vi_tri_id
                    JOIN hoa_don_mua hdm ON cthd.hoa_don_id = hdm.hoa_don_id
                    SET vtct.so_luong = ? WHERE tai_san_id = ? AND vt.vi_tri_id = 1 AND ngay_mua = ? ");
    $updateStmt->execute([$newQuantity, $tai_san_id, $ngay_mua]);
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

public function statistics()
    {
        // Lấy dữ liệu thống kê
        $totalInvoices = $this->thanhly->getTotalInvoices();
        $totalValue = $this->thanhly->getTotalValue();
        $avgValue = $totalInvoices > 0 ? $totalValue / $totalInvoices : 0;
    
        // Thống kê theo tháng
        $monthlyInvoices = $this->thanhly->getMonthlyInvoices();
    
    
        // Lấy top 5 tài sản được mua nhiều nhất
        $topAssets = $this->thanhly->getTopAssets(5);
    
        // Chuẩn bị dữ liệu cho biểu đồ
        $chartData = [
            'monthlyLabels' => array_column($monthlyInvoices, 'month'),
            'monthlyData' => array_column($monthlyInvoices, 'count'),
        ];
    
        // Truyền dữ liệu vào view
        $data = [
            'totalInvoices' => $totalInvoices,
            'totalValue' => $totalValue,
            'avgValue' => $avgValue,
            'monthlyInvoices' => $monthlyInvoices,
            'topAssets' => $topAssets,
            'chartData' => json_encode($chartData),
        ];
    
        $content = 'views/thanhly/statistics_thanhly.php';
        include('views/layouts/base.php');
    }

   public function getNgayMua()
    {
        $database = new Database();
        $db = $database->getConnection();

        $thanhLyModel = new ThanhLy($db);

        // Kiểm tra xem có tồn tại tham số tài sản ID từ yêu cầu GET không
        if (isset($_GET['tai_san_id'])) {
            $taiSanId = $_GET['tai_san_id'];

            // Gọi phương thức từ model để lấy danh sách ngày mua
            $ngayMua = $thanhLyModel->getNgayMuaByTaiSanId($taiSanId);

            // Trả về dữ liệu dưới dạng JSON
            echo json_encode($ngayMua);
        } else {
            echo json_encode([]); // Trả về mảng rỗng nếu không có dữ liệu
        }
    }


}
?>



