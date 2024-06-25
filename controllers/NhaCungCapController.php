<?php
include_once 'config/database.php';
include_once 'models/NhaCungCap.php';

class NhaCungCapController extends Controller {
    private $db;
    private $nhaCungCap;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->nhaCungCap = new NhaCungCap($this->db);
    }

    public function index() {
        $stmt = $this->nhaCungCap->read();
        $nhaCungCaps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/nhacungcaps/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        if ($_POST) {
            $this->nhaCungCap->ten_nha_cung_cap = $_POST['ten'];
            
            // Kiểm tra xem nhà cung cấp đã tồn tại và trạng thái = 1 hay không
            if ($this->nhaCungCap->checkExist($_POST['ten']) && $this->nhaCungCap->isActive($_POST['ten'])) {
                $_SESSION['message'] = 'Nhà cung cấp đã tồn tại trong cơ sở dữ liệu!';
                $_SESSION['message_type'] = 'danger';
                $content = 'views/nhacungcaps/create.php'; // Hiển thị lại form tạo mới
                include('views/layouts/base.php');
                return; // Dừng hàm để ngăn không chuyển hướng
            }
            
            // Kiểm tra nếu nhà cung cấp đã tồn tại nhưng có trạng thái = 0, thì cập nhật lại trạng thái thành 1
            if ($this->nhaCungCap->checkExist($_POST['ten']) && !$this->nhaCungCap->isActive($_POST['ten'])) {
                $this->nhaCungCap->updateStatusToActive($_POST['ten']);
                
                $_SESSION['message'] = 'Tạo nhà cung cấp mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=nhacungcap");
                return; // Dừng hàm sau khi cập nhật thành công để ngăn render lại form
            }

            if ($this->nhaCungCap->create()) {
                $_SESSION['message'] = 'Tạo nhà cung cấp mới thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=nhacungcap");
                return; // Dừng hàm sau khi thực hiện thành công để ngăn render lại form
            } else {
                $_SESSION['message'] = 'Tạo mới thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }
        $content = 'views/nhacungcaps/create.php';
        include('views/layouts/base.php');
    }
    

    public function edit($id) {
        if ($_POST) {
            $this->nhaCungCap->nha_cung_cap_id = $id;
            $this->nhaCungCap->ten_nha_cung_cap = $_POST['ten_nha_cung_cap'];
            
            // Kiểm tra xem tên mới đã tồn tại và trạng thái = 1 hay không
            if ($this->nhaCungCap->checkExist($_POST['ten_nha_cung_cap'], $id)) {
                $_SESSION['message'] = 'Nhà cung cấp đã tồn tại trong cơ sở dữ liệu!';
                $_SESSION['message_type'] = 'danger';
                $nhaCungCap = $this->nhaCungCap->readById($id); // Lấy lại thông tin nhà cung cấp
                $content = 'views/nhacungcaps/edit.php'; // Hiển thị lại form sửa
                include('views/layouts/base.php');
                return; // Dừng hàm để ngăn không chuyển hướng
            }
            
            // Kiểm tra nếu nhà cung cấp đã tồn tại nhưng có trạng thái = 0
            if ($this->nhaCungCap->checkExist($_POST['ten_nha_cung_cap'], $id) && !$this->nhaCungCap->isActive($_POST['ten_nha_cung_cap'])) {
                // Đổi trạng thái từ 0 sang 1
                $this->nhaCungCap->updateStatusToActive($_POST['ten_nha_cung_cap']);
                
                $_SESSION['message'] = 'Đã cập nhật lại trạng thái nhà cung cấp thành hoạt động!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=nhacungcap");
                return; // Dừng hàm sau khi cập nhật thành công để ngăn render lại form
            }
    
            // Nếu nhà cung cấp chưa tồn tại, thực hiện sửa tên nhà cung cấp
            if ($this->nhaCungCap->update()) {
                $_SESSION['message'] = 'Sửa nhà cung cấp thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=nhacungcap");
                return; // Dừng hàm sau khi thực hiện thành công để ngăn render lại form
            } else {
                $_SESSION['message'] = 'Sửa thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
            
        } else {
            $nhaCungCap = $this->nhaCungCap->readById($id);
            $content = 'views/nhacungcaps/edit.php';
            include('views/layouts/base.php');
        }
    }
    
    

    public function delete($id) {
        if ($this->nhaCungCap->delete($id)) {
            $_SESSION['message'] = 'Xóa nhà cung cấp thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=nhacungcap");
        }else {
            $_SESSION['message'] = 'Xóa thất bại!';
            $_SESSION['message_type'] = 'danger';
        }
    }
}
?>
