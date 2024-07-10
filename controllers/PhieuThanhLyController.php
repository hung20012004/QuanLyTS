<?php
include_once 'config/database.php';
include_once 'models/PhieuThanhLy.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ChiTietPhieuThanhLy.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTriChiTiet.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PhieuThanhLyController extends Controller
{
    private $db;
    private $phieuThanhLyModel;
    private $loaiTaiSanModel;
    private $chiTietPhieuThanhLyModel;
    private $taiSanModel;
    private $viTriChiTietModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuThanhLyModel = new PhieuThanhLy($this->db);
        // $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
        $this->chiTietPhieuThanhLyModel = new ChiTietPhieuThanhLy($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);
    }

    public function index()
    {
        $phieuThanhLy = $this->phieuThanhLyModel->readAll();
        $content = 'views/phieu_thanh_ly/index.php';
        include('views/layouts/base.php');
    }

     public function show($id)
    {
        $phieuThanhLy = $this->phieuThanhLyModel->readById($id);
        if (!$phieuThanhLy) {
            die('Phiếu thanh lý không tồn tại.');
        }

        $chitietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_thanh_ly/show.php';
        include('views/layouts/base.php');
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
    // $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
    $tai_san_list = $this->phieuThanhLyModel->readTai_San(); // Lấy tất cả tài sản và create thì không cần lấy trường khác
    $content = 'views/phieu_thanh_ly/create.php';
    include('views/layouts/base.php');
}

    private function processCreateForm()
    {
        $this->db->beginTransaction();
        try {
            $phieuThanhLyId = $this->createPhieuThanhLy();
            $this->createChiTietPhieuThanhLy($phieuThanhLyId);
            
            $this->db->commit();  // kết thúc giao dịch và lưu tất cả các thay đổi
            $_SESSION['message'] = 'Tạo phiếu nhập mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieuthanhly&action=create");
            exit();
        }
    }

    private function createPhieuThanhLy()
    {
        $this->phieuThanhLyModel->user_id = $_SESSION['user_id'];
        $this->phieuThanhLyModel->ngay_tao = $_POST['ngay_tao'];
        // $this->phieuThanhLyModel->ngay_xac_nhan = $_POST['ngay_xac_nhan'];
        $this->phieuThanhLyModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuThanhLyModel->trang_thai = 'DangChoPheDuyet';
        return $this->phieuThanhLyModel->create();
    }

    private function createChiTietPhieuThanhLy($phieuThanhLyId)
    {
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId)) continue;  // Bỏ qua nếu không có tài sản được chọn

            $this->chiTietPhieuThanhLyModel->phieu_thanh_ly_id = $phieuThanhLyId;
            $this->chiTietPhieuThanhLyModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuThanhLyModel->so_luong = $_POST['so_luong'][$index];
            $this->chiTietPhieuThanhLyModel->tinh_trang = $_POST['tinh_trang'][$index];
            $chiTietId = $this->chiTietPhieuThanhLyModel->create();

        }
    }

    // public function getByLoai()
    // {
    //     if (isset($_GET['loai_id'])) {
    //         $loai_id = $_GET['loai_id'];
    //         $taiSanList = $this->taiSanModel->readByLoaiId($loai_id);
    //         header('Content-Type: application/json');
    //         echo json_encode($taiSanList);
    //     } else {
    //         http_response_code(400);
    //         echo json_encode(array("message" => "Missing loai_id parameter"));
    //     }
    // }
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
        $phieuNhap = $this->phieuThanhLyModel->readById($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }

        // Sửa đổi truy vấn này để lấy thêm thông tin về loại tài sản và tên tài sản
        $chiTietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
        $tai_san_list = $this->phieuThanhLyModel->readTai_San();
        // var_dump($chiTietPhieuNhap);
        // exit();
        $content = 'views/phieu_thanh_ly/edit.php';
        include('views/layouts/base.php');
    }

    private function processEditForm($id)
    {
        $this->db->beginTransaction();
        try {
            $this->updatePhieuThanhLy($id);
            $this->updateChiTietPhieuThanhLy($id);
            
            $this->db->commit();
            $_SESSION['message'] = 'Cập nhật phiếu nhập thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieuthanhly&action=edit&id=" . $id);
            exit();
        }
    }

    private function updatePhieuThanhLy($id)
    {
        $this->phieuThanhLyModel->phieu_thanh_ly_id = $id;
        $this->phieuThanhLyModel->ngay_tao = $_POST['ngay_tao'];
        // $this->phieuThanhLyModel->ngay_xac_nhan = $_POST['ngay_xac_nhan'];
        $this->phieuThanhLyModel->ghi_chu = $_POST['ghi_chu'];
        $this->phieuThanhLyModel->trang_thai = 'DangChoPheDuyet';
        $this->phieuThanhLyModel->update();
    }

    private function updateChiTietPhieuThanhLy($phieuNhapId)
    {
        // Delete existing chi tiết
        $this->chiTietPhieuThanhLyModel->deleteByPhieuThanhLyId($phieuNhapId);
        
        // Create new chi tiết
        foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
            if (empty($taiSanId)) continue;

            $this->chiTietPhieuThanhLyModel->phieu_thanh_ly_id= $phieuNhapId;
            $this->chiTietPhieuThanhLyModel->tai_san_id = $taiSanId;
            $this->chiTietPhieuThanhLyModel->so_luong = $_POST['so_luong'][$index];
            $this->chiTietPhieuThanhLyModel->tinh_trang = $_POST['tinh_trang'][$index];
            $this->chiTietPhieuThanhLyModel->create();
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
            $this->chiTietPhieuThanhLyModel->deleteByPhieuThanhLyId($id);

            // Xóa phiếu nhập
            $this->phieuThanhLyModel->delete($id);

            $this->db->commit();
            $_SESSION['message'] = 'Xóa phiếu thanh lý thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        }
    }

public function xet_duyet($id)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action == 'check_quantity') {
            $messages = []; // Mảng lưu trữ các thông điệp

            foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
                $chi_tiet_id = $_POST['chi_tiet_id'][$index];
                $required_quantity = $_POST['so_luong'][$index];
                $result = $this->check_sl_phe_duyet($chi_tiet_id);

                if ($result && $result['so_luong'] < $required_quantity) {
                    $messages[] = 'KHÔNG đủ số lượng của ' . $_POST['tai_san_ten'][$index] . ' để phê duyệt ! Còn lại: ' . $result['so_luong'] . '';
                } else {
                    $messages[] = 'Số lượng tài sản của ' . $_POST['tai_san_ten'][$index] . '  ĐỦ để phê duyệt. Còn lại: ' . $result['so_luong'] . '';
                }
            }

            // Thiết lập thông báo session
            if (!empty($messages)) {
                $_SESSION['message'] = implode("<br>", $messages); // Nối các thông điệp thành một chuỗi HTML
                $_SESSION['message_type'] = 'success'; // Hoặc 'danger' tùy theo logic của bạn
            }

            // Chuyển hướng người dùng đến trang hiện tại
             var_dump($_POST);
            header("Location: index.php?model=phieuthanhly&action=xet_duyet&id=$id");
            exit();
        }

        // Xử lý hành động approve và reject
        if ($action == 'approve') {

             $all_approved = true;
             $messages = [];
            foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
                $chi_tiet_id = $_POST['chi_tiet_id'][$index];
                $required_quantity = $_POST['so_luong'][$index];
                $result = $this->check_sl_phe_duyet($chi_tiet_id);

                if ($result && $result['so_luong'] < $required_quantity) {
                    $messages[] = 'Có tài sản không đủ số lượng để phê duyệt !';
                    $all_approved = false;
                }else {
                    // Cập nhật số lượng phê duyệt vào cơ sở dữ liệu
                    $this->chiTietPhieuThanhLyModel->updateSoluongPhieu($chi_tiet_id, $required_quantity);
    
                }
            }

            // Thiết lập thông báo session
            if ($all_approved) {
                // Cập nhật trạng thái của phiếu thành 'DaPheDuyet'
                $this->phieuThanhLyModel->trang_thai = 'DaPheDuyet';
                $this->phieuThanhLyModel->ngay_xac_nhan = date('Y-m-d');
                $this->phieuThanhLyModel->nguoi_duyet_id = $_POST['nguoi_phe_duyet_id'];
                $this->phieuThanhLyModel->phieu_thanh_ly_id = $id;
                $this->phieuThanhLyModel->ghi_chu = $_POST['ghi_chu'];
                $this->phieuThanhLyModel->updateStatusPheDuyet();

                // Thiết lập thông báo thành công
                $_SESSION['message'] = 'Tất cả các tài sản đã được phê duyệt thành công!';
                $_SESSION['message_type'] = 'success';

                // Chuyển hướng người dùng đến trang danh sách phiếu
                header("Location: index.php?model=phieuthanhly&action=index");
                exit();
            } else {
                // Nếu có tài sản không đủ số lượng để phê duyệt
                $_SESSION['message'] = implode("<br>", $messages); // Nối các thông điệp thành một chuỗi HTML
                $_SESSION['message_type'] = 'danger'; // Hoặc 'danger' tùy theo logic của bạn

                // Chuyển hướng người dùng trở lại trang xét duyệt với thông báo lỗi
                header("Location: index.php?model=phieuthanhly&action=xet_duyet&id=$id");
                exit();
            }

        } elseif ($action == 'reject') {
           $this->phieuThanhLyModel->trang_thai = 'KhongDuyet';

            // Cập nhật thông tin vào model
            $this->phieuThanhLyModel->ngay_xac_nhan = date('Y-m-d');
            $this->phieuThanhLyModel->nguoi_duyet_id = $_POST['nguoi_phe_duyet_id'];
            $this->phieuThanhLyModel->phieu_thanh_ly_id = $id;
            $this->phieuThanhLyModel->ghi_chu = $_POST['ghi_chu'];
            $this->phieuThanhLyModel->updateStatusPheDuyet();

            // Thiết lập thông báo thành công
            $_SESSION['message'] = 'Phiếu thanh lý đã bị từ chối!';
            $_SESSION['message_type'] = 'success';

            // Chuyển hướng người dùng đến trang danh sách phiếu
            header("Location: index.php?model=phieuthanhly&action=index");
            exit();
        }
       
    } else {
        // Nếu không phải POST request, hiển thị form xét duyệt
        $phieuNhap = $this->phieuThanhLyModel->readById($id);
        if (!$phieuNhap) {
            die('Phiếu nhập không tồn tại.');
        }

        $chitietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_thanh_ly/xet_duyet.php';
        include('views/layouts/base.php');
    }
}

public function check_sl_phe_duyet($id)
{
    $sql = "SELECT vt.so_luong
            FROM vi_tri_chi_tiet vt
            INNER JOIN chi_tiet_phieu_thanh_ly ct ON vt.tai_san_id = ct.tai_san_id
            WHERE vt.vi_tri_id = 1 AND ct.chi_tiet_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function search()
{
    if (isset($_POST['btn_tim_kiem'])) {
        $ngay_tao_tk = $_POST['ngay_tao_tk'];
        $ngay_pd_tk = $_POST['ngay_pd_tk'];

        // Câu truy vấn SQL
         $query = "SELECT ptl.*, u.ten AS user_name 
                  FROM phieu_thanh_ly ptl
                  LEFT JOIN users u ON ptl.user_id = u.user_id 
                  WHERE ptl.ngay_tao LIKE '%".$ngay_tao_tk."%' AND ptl.ngay_xac_nhan LIKE '%".$ngay_pd_tk."%'";
                
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $phieuThanhLy = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content = 'views/phieu_thanh_ly/index.php';
        include('views/layouts/base.php');
    }
}

      public function thanh_ly($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->phieuThanhLyModel->phieu_thanh_ly_id=$id;
            $this->processThanhLyTaiSan($id);
        } else {
            $this->showThanhLyTaiSanForm($id);
        }
    }

    private function showThanhLyTaiSanForm($id)
    {
        $phieuThanhLy = $this->phieuThanhLyModel->readById($id);
        
        if (!$phieuThanhLy) {
            die('Phiếu thanh lý không tồn tại.');
        }
        $chiTietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_thanh_ly/thanh_ly.php';
        include('views/layouts/base.php');
    }

private function processThanhLyTaiSan($id)
{
    $this->db->beginTransaction();
        $chiTietPhieuThanhLy = $this->chiTietPhieuThanhLyModel->readDetailedByPhieuThanhLyId($id);
        
          $messages = []; // Mảng lưu trữ các thông điệp\
          
        foreach ($chiTietPhieuThanhLy as $index =>  $chiTiet) {
            // Cập nhật số lượng trong bảng vi_tri_chi_tiet
            $viTriChiTiet = $this->viTriChiTietModel->readByTaiSanAndViTri($chiTiet['tai_san_id'], 1);
            
            if ($viTriChiTiet) {
                $newViTriQuantity = $viTriChiTiet['so_luong'] - $chiTiet['so_luong'];
                
                if ($newViTriQuantity < 0) {
                    // Nếu số lượng cần thanh lý lớn hơn số lượng hiện có
                    $messages[] = 'Không đủ số lượng của ' . $_POST['tai_san_ten'][$index] . ' để phê duyệt ! Còn lại: '.$viTriChiTiet['so_luong'].'';
                }

            } 
        }
        
         if (!empty($messages)) {
                $this->db->rollBack();
                $_SESSION['message'] = implode("<br>", $messages); // Nối các thông điệp thành một chuỗi HTML
                $_SESSION['message_type'] = 'danger'; // Hoặc 'danger' tùy theo logic của bạn  
                header("Location: index.php?model=phieuthanhly&action=thanh_ly&id=$id");
                exit();
            }
       
            foreach($chiTietPhieuThanhLy as $index => $chitiet)
            {
                $viTriChiTiet = $this->viTriChiTietModel->readByTaiSanAndViTri($chitiet['tai_san_id'], 1);
                if($viTriChiTiet)
                {
                    $newQuantity = $viTriChiTiet['so_luong'] - $chitiet['so_luong'];
                    $this->viTriChiTietModel->updateQuantity($viTriChiTiet['vi_tri_chi_tiet_id'], $newQuantity);
                }else {
                // Nếu không tìm thấy vị trí chi tiết, thêm mới
                $this->viTriChiTietModel->vi_tri_id = 1;
                $this->viTriChiTietModel->tai_san_id = $chiTiet['tai_san_id'];
                $this->viTriChiTietModel->so_luong = $chiTiet['so_luong'];
                $this->viTriChiTietModel->create();
            }
  
            }


        // Cập nhật phiếu thanh lý và commit transaction
        $this->phieuThanhLyModel->nguoi_duyet_id = $_POST['nguoi_phe_duyet_id'];
        $this->phieuThanhLyModel->trang_thai = 'DaThanhLy';
        $this->phieuThanhLyModel->ngay_thanh_ly = date('Y-m-d');
        $this->phieuThanhLyModel->updateStatusThanhLy();
        
        $this->db->commit();
        
        // Chuyển hướng về trang danh sách phiếu thanh lý và thông báo thành công
        $_SESSION['message'] = 'Thanh lý tài sản thành công!';
        $_SESSION['message_type'] = 'success';
        header("Location: index.php?model=phieuthanhly&action=index");
        exit();
        
  
}
   public function exportphieu($id)
{
    // Fetch data from the database
    $sql = "SELECT ptl.*, ctptl.*, ts.ten_tai_san
            FROM phieu_thanh_ly ptl
            INNER JOIN chi_tiet_phieu_thanh_ly ctptl ON ptl.phieu_thanh_ly_id = ctptl.phieu_thanh_ly_id
            INNER JOIN tai_san ts ON ctptl.tai_san_id = ts.tai_san_id
            WHERE ptl.phieu_thanh_ly_id = ?";
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
    $sheet->mergeCells('B3:E3')->setCellValue('B3', 'GIẤY ĐỀ NGHỊ THANH LÝ TÀI SẢN CỐ ĐỊNH')->getStyle('B3')->getFont()->setBold(true)->setSize(13);
    $sheet->mergeCells('B4:E4')->setCellValue('B4', 'Kính gửi: Ban Giám Đốc');
    $sheet->mergeCells('B5:E5')->setCellValue('B5', 'Phòng/Ban:');
    $sheet->mergeCells('B6:E6')->setCellValue('B6', 'Danh Mục TSCĐ Đề Nghị Thanh Lý')->getStyle('B6')->getFont()->setBold(true)->setSize(13);
    $sheet->getStyle('B6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCEEFF');
    $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('B4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('B5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // Column headers
    $sheet->setCellValue('B7', 'STT');
    $sheet->setCellValue('C7', 'Tên TSCĐ');
    $sheet->setCellValue('D7', 'Số lượng');
    $sheet->setCellValue('E7', 'Ghi chú');

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
        $sheet->setCellValue('E' . $row, $data['ghi_chu']);

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
    $fileName = 'Phieu_Yeu_Cau_Thanh_Ly_Tai_San_Co_Dinh.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    ob_end_clean();
    $writer->save('php://output');
    exit;
}
}

// private function showCreateForm()
// {
//     // $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
//     $tai_san_list = $this->phieuThanhLyModel->readTai_San(); // Lấy tất cả tài sản và create thì không cần lấy trường khác
//     $content = 'views/phieu_thanh_ly/create.php';
//     include('views/layouts/base.php');
// }

//     private function processCreateForm()
//     {
//         $this->db->beginTransaction();
//         try {
//             $phieuThanhLyId = $this->createPhieuThanhLy();
//             $this->createChiTietPhieuThanhLy($phieuThanhLyId);
            
//             $this->db->commit();  // kết thúc giao dịch và lưu tất cả các thay đổi
//             $_SESSION['message'] = 'Tạo phiếu nhập mới thành công!';
//             $_SESSION['message_type'] = 'success';
//             header("Location: index.php?model=phieuthanhly&action=index");
//             exit();
//         } catch (Exception $e) {
//             $this->db->rollBack();
//             $_SESSION['message'] = $e->getMessage();
//             $_SESSION['message_type'] = 'danger';
//             header("Location: index.php?model=phieuthanhly&action=create");
//             exit();
//         }
//     }

//     private function createPhieuThanhLy()
//     {
//         $this->phieuThanhLyModel->user_id = $_SESSION['user_id'];
//         $this->phieuThanhLyModel->ngay_tao = $_POST['ngay_tao'];
//         // $this->phieuThanhLyModel->ngay_xac_nhan = $_POST['ngay_xac_nhan'];
//         $this->phieuThanhLyModel->ghi_chu = $_POST['ghi_chu'];
//         $this->phieuThanhLyModel->trang_thai = 'DangChoPheDuyet';
//         return $this->phieuThanhLyModel->create();
//     }

//     private function createChiTietPhieuThanhLy($phieuThanhLyId)
//     {
//         foreach ($_POST['tai_san_id'] as $index => $taiSanId) {
//             if (empty($taiSanId)) continue;  // Bỏ qua nếu không có tài sản được chọn

//             $this->chiTietPhieuThanhLyModel->phieu_thanh_ly_id = $phieuThanhLyId;
//             $this->chiTietPhieuThanhLyModel->tai_san_id = $taiSanId;
//             $this->chiTietPhieuThanhLyModel->so_luong = $_POST['so_luong'][$index];
//             $this->chiTietPhieuThanhLyModel->tinh_trang = $_POST['tinh_trang'][$index];
//             $chiTietId = $this->chiTietPhieuThanhLyModel->create();

//         }
//     }
