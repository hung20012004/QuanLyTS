<?php
include_once 'config/database.php';
include_once 'models/PhieuSua.php';
include_once 'models/ViTri.php';

class PhieuSuaController extends Controller
{
    private $db;
    private $phieuSuaModel;
    private $viTriModel;
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuSuaModel = new PhieuSua($this->db);
        $this->viTriModel = new ViTri($this->db);
        $this->userModel = new User($this->db);
    }

    public function index()
    {
        $phieuSuas = $this->phieuSuaModel->readAll();
        $content = 'views/phieu_sua/index.php';
        include 'views/layouts/base.php';
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
        $viTris=$this->viTriModel->read();
        $content = 'views/phieu_sua/create.php';
        include 'views/layouts/base.php';
    }

    private function processCreateForm()
    {
        $this->db->beginTransaction();
        $viTris=$this->viTriModel->read();
        try {
            $this->phieuSuaModel->user_yeu_cau_id = $_SESSION['user_id'];
            $this->phieuSuaModel->ngay_yeu_cau = $_POST['ngay_yeu_cau'];
            $this->phieuSuaModel->mo_ta = $_POST['mo_ta'];
            $this->phieuSuaModel->vi_tri_id = $_POST['vi_tri_id'];
            $this->phieuSuaModel->trang_thai = 'DaGui';
            
            if ($this->phieuSuaModel->create()){
                $this->db->commit();
                $_SESSION['message'] = 'Tạo phiếu sửa mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=phieusua&action=index");
                exit();
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=create");
            exit();
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
        $phieuSua = $this->phieuSuaModel->readById($id);
        if (!$phieuSua) {
            die('Phiếu sửa không tồn tại.');
        }

        $viTris = $this->viTriModel->read();
        $content = 'views/phieu_sua/edit.php';
        include 'views/layouts/base.php';
    }

    private function processEditForm($id)
    {
        $this->db->beginTransaction();
        try {
            $this->updatePhieuSua($id);
            
            $this->db->commit();
            $_SESSION['message'] = 'Cập nhật phiếu sửa thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=edit&id=" . $id);
            exit();
        }
    }

    private function updatePhieuSua($id)
    {
        $this->phieuSuaModel->phieu_sua_id = $id;
        $this->phieuSuaModel->vi_tri_id = $_POST['vi_tri_id'];
        $this->phieuSuaModel->mo_ta = $_POST['mo_ta'];
        $this->phieuSuaModel->update();
    }

    public function show($id)
    {
        $phieuSua = $this->phieuSuaModel->readById($id);
        if (!$phieuSua) {
            die('Phiếu sửa không tồn tại.');
        }
        $viTri = $this->viTriModel->read();
        $content = 'views/phieu_sua/show.php';
        include 'views/layouts/base.php';
    }

    public function delete($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
        }

        $this->db->beginTransaction();
        try {
            $this->viTriModel->read();
            $this->phieuSuaModel->delete($id);

            $this->db->commit();
            $_SESSION['message'] = 'Xóa phiếu sửa thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        }
    }

    public function xet_duyet($id)
    {
        $phieuSua = $this->phieuSuaModel->readById($id);
        if (!$phieuSua) {
            die('Phiếu sửa không tồn tại.');
        }
        
        $this->db->beginTransaction();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            
            if ($action == 'approve') {
                $this->phieuSuaModel->trang_thai = 'DaNhan';
                $this->phieuSuaModel->phieu_sua_id = $id;
                $this->phieuSuaModel->user_sua_chua_id = $_POST['user_sua_chua_id'];
                $this->phieuSuaModel->ngay_sua_chua = $_POST['ngay_sua_chua'];

                // Validate ngay_sua_chua > ngay_yeu_cau
                $ngayYeuCau = strtotime($phieuSua['ngay_yeu_cau']);
                $ngaySuaChua = strtotime($_POST['ngay_sua_chua']);
                
                if ($ngaySuaChua <= $ngayYeuCau) {
                    $_SESSION['message'] = 'Ngày sửa chữa phải lớn hơn ngày yêu cầu!';
                    $_SESSION['message_type'] = 'danger';
                    header("Location: index.php?model=phieusua&action=xet_duyet&id={$id}");
                    exit();
                }
            } elseif ($action == 'reject') {
                $this->phieuSuaModel->trang_thai = 'Huy';
            }
            
            // Thực hiện cập nhật dữ liệu vào cơ sở dữ liệu
            if ($this->phieuSuaModel->updateFix()) {
                $this->db->commit();
                $_SESSION['message'] = 'Cập nhật thông tin thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=phieusua&action=index");
                exit();
            } else {
                // Xử lý khi cập nhật không thành công
                $_SESSION['message'] = 'Cập nhật thông tin không thành công!';
                $_SESSION['message_type'] = 'danger';
                // Hoặc log lỗi để kiểm tra
                error_log('Lỗi: Cập nhật thông tin phiếu sửa không thành công.');
            }
        }
        
        // Đọc danh sách người dùng kỹ thuật và vị trí
        $kyThuatUsers = $this->userModel->readKyThuat();
        $viTri = $this->viTriModel->read();
        
        // Load view
        $content = 'views/phieu_sua/xet_duyet.php';
        include 'views/layouts/base.php';
    }


    public function hoan_thanh($id)
    {
        // Kiểm tra phiếu sửa tồn tại
        $phieuSua = $this->phieuSuaModel->readById($id);
        if (!$phieuSua) {
            $_SESSION['message'] = 'Phiếu sửa không tồn tại.';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        }
        $ngayHoanThanh = date('Y-m-d');
    
        // Kiểm tra ngày hoàn thành phải lớn hơn ngày yêu cầu
        if (strtotime($ngayHoanThanh) <= strtotime($phieuSua['ngay_yeu_cau'])) {
            $_SESSION['message'] = 'Ngày hoàn thành phải lớn hơn ngày yêu cầu!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        }
        
        // Thực hiện cập nhật trạng thái và ngày hoàn thành
        $this->phieuSuaModel->ngay_hoan_thanh = $ngayHoanThanh;
        $this->phieuSuaModel->trang_thai = 'DaHoanThanh';
        $this->phieuSuaModel->phieu_sua_id = $id;
        
        // Thực hiện cập nhật dữ liệu vào cơ sở dữ liệu
        if ($this->phieuSuaModel->updateStatusAndDay()) {
            $_SESSION['message'] = 'Hoàn thành yêu cầu thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        } else {
            $_SESSION['message'] = 'Hoàn thành yêu cầu không thành công!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        }
    }

    // Phương thức xử lý khi nhân viên gửi yêu cầu hủy
    public function cancellationrequest($id)
    {
        // Kiểm tra phiếu sửa tồn tại
        $phieuSua = $this->phieuSuaModel->readById($id);
        if (!$phieuSua) {
            $_SESSION['message'] = 'Phiếu sửa không tồn tại.';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        }
        
        // Thực hiện cập nhật trạng thái yêu cầu hủy
        $this->phieuSuaModel->trang_thai = 'YeuCauHuy';
        $this->phieuSuaModel->phieu_sua_id = $id;
        
        // Thực hiện cập nhật dữ liệu vào cơ sở dữ liệu
        if ($this->phieuSuaModel->updateStatus()) {
            $_SESSION['message'] = 'Gửi yêu cầu hủy thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        } else {
            $_SESSION['message'] = 'Gửi yêu cầu hủy không thành công!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        }
    }


    // Phương thức xử lý khi quản lý xác nhận hủy yêu cầu
    public function cancel($id)
    {    
        // Thực hiện cập nhật trạng thái thành 'Hủy'
        $this->phieuSuaModel->trang_thai = 'Huy';
        $this->phieuSuaModel->phieu_sua_id = $id;
        
        // Thực hiện cập nhật dữ liệu vào cơ sở dữ liệu
        if ($this->phieuSuaModel->updateStatus()) {
            $_SESSION['message'] = 'Xác nhận hủy yêu cầu thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        } else {
            $_SESSION['message'] = 'Xác nhận hủy yêu cầu không thành công!';
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieusua&action=index");
            exit();
        }
    }
    public function calendarView() {
        // Lấy user_sua_chua_id từ session hoặc request
        $user_sua_chua_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        // Nếu muốn cho phép xem lịch của người khác (ví dụ: admin)
        if (isset($_GET['user_id'])) {
            $user_sua_chua_id = intval($_GET['user_id']);
        }

        // Lấy danh sách người dùng (nếu cần cho dropdown chọn người dùng)
        $users = $this->phieuSuaModel->getAllUsers();

        // Chuẩn bị dữ liệu cho view
        $data = [
            'user_sua_chua_id' => $user_sua_chua_id,
            'users' => $users
        ];

        // Load view
        $content = 'views/phieu_sua/calendar.php';
        include 'views/layouts/base.php';
    }

    public function getRepairForms() {
        $user_sua_chua_id = isset($_GET['user_sua_chua_id']) ? intval($_GET['user_sua_chua_id']) : null;
        
        // Kiểm tra quyền truy cập (ví dụ: chỉ admin mới có thể xem tất cả)
        if ($user_sua_chua_id === null) {
            $user_sua_chua_id = $_SESSION['user_id'];
        }

        $forms = $this->phieuSuaModel->getRepairFormsByUser($user_sua_chua_id);

        // Xử lý dữ liệu nếu cần
        $events = array_map(function($form) {
            return [
                'id' => $form['phieu_sua_id'],
                'title' => $form['trang_thai'] . ' - ' . $form['user_sua_chua_name'],
                'start' => $form['ngay_sua_chua'] ?: $form['ngay_yeu_cau'],
                'end' => $form['ngay_hoan_thanh'] ?: $form['ngay_sua_chua'],
                'color' => $this->getEventColor($form['trang_thai'])
            ];
        }, $forms);

        header('Location: index.php?controller=phieusua&action=calendar');
        echo json_encode($events);
        exit;
    }

    private function getEventColor($trangThai) {
        $colors = [
            'DaNhan' => '#4e73df',
            'DangXuLy' => '#f6c23e',
            'DaHoanThanh' => '#1cc88a',
            'default' => '#858796'
        ];
        return isset($colors[$trangThai]) ? $colors[$trangThai] : $colors['default'];
    }

    public function statistics() {
        // Fetch statistics
        $statistics = [
            'totalProcessed' => $this->phieuSuaModel->getTotalProcessed(),
            'totalUnprocessed' => $this->phieuSuaModel->getTotalUnprocessed(),
            'recentCompleted' => $this->phieuSuaModel->getRecentCompleted(),
            'recentRequests' => $this->phieuSuaModel->getRecentRequests(),
            'recentReceives' => $this->phieuSuaModel->getRecentReceiveds(),
            'mostRequests' => $this->phieuSuaModel->getMostRequests(),
            'leastRequests' => $this->phieuSuaModel->getLeastRequests()
        ];

        // Render the view with the statistics
        $content = 'views/phieu_sua/statistics.php';
        include 'views/layouts/base.php';
    }

    public function export()
    {
        // Fetch the data
        $phieuSuas = $this->phieuSuaModel->readAll();

        // Set the header for CSV file
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=phieu_sua.csv');

        $output = fopen('php://output', 'w');

        // Add BOM to fix UTF-8 in Excel
        fputs($output, "\xEF\xBB\xBF");

        // Set CSV column headers
        fputcsv($output, array('phieu_sua_id', 'user_yeu_cau_name', 'ngay_yeu_cau', 'user_sua_chua_name', 'ngay_sua_chua', 'ngay_hoan_thanh', 'ten_vi_tri', 'trang_thai'));

        // Add rows to the CSV file
        foreach ($phieuSuas as $phieu) {
            fputcsv($output, $phieu);
        }

        fclose($output);
    }
}
