<?php
include_once 'config/database.php';
include_once 'models/User.php';

class UserController extends Controller {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function index() {
        $stmt = $this->user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/users/index.php';
        include('views/layouts/base.php');
    }

    public function create() {
        $ten = '';
        $email = '';
        $role = '';
        $errors = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten = $_POST['ten'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $password = 'Utt@1234';
    
            // Kiểm tra email đã tồn tại chưa
            if ($this->user->emailExists($email)) {
                $errors[] = 'Email đã tồn tại.';
            }
    
            if (empty($errors)) {
                try {
                    $this->user->ten = $ten;
                    $this->user->email = $email;
                    $this->user->password = password_hash($password, PASSWORD_BCRYPT);
                    $this->user->role = $role;
    
                    if ($this->user->create()) {
                        $_SESSION['message'] = 'Tạo người dùng mới thành công!';
                        $_SESSION['message_type'] = 'success';
                        header("Location: index.php?model=user&action=index");
                        exit();
                    } else {
                        $errors[] = 'Tạo mới thất bại!';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Có lỗi xảy ra: ' . $e->getMessage();
                }
            }
        }
    
        $content = 'views/users/create.php';
        include('views/layouts/base.php');
    }
    public function edit($id) {
        if ($_POST) {
            
            $user = $this->user->readById($id);
            $this->user->user_id = $id;
            $this->user->email = $_POST['email'];
            $this->user->ten = $_POST['ten'];
            if($_POST['password']!=''){
                $pass='Utt@1234';
                $this->user->password = password_hash($pass, PASSWORD_BCRYPT);
            }
            $this->user->role = $_POST['role'];
            if ($this->user->update()) {
                // var_dump($_POST['ten']);
                $_SESSION['message'] = 'Sửa người dùng thành công!';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=user");
            }else {
                $_SESSION['message'] = 'Sửa người dùng thất bại!';
                $_SESSION['message_type'] = 'danger';
            }
        }else {
            $user = $this->user->readById($id);
            $content = 'views/users/edit.php';
            include('views/layouts/base.php');
        }
    }

    public function delete($id) {
        if ($this->user->delete($id)) {
            $_SESSION['message'] = 'Xóa thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Xóa thất bại';
            $_SESSION['message_type'] = 'danger';
        }
        header("Location: index.php?model=user");
    }

    public function statistics() {
    // Gọi phương thức từ model để lấy dữ liệu thống kê
   $data = $this->user->getUsersByRole();
    $usersByRole = $data['usersByRole'];
    $totalUsers = $data['totalUsers'];
    // Hiển thị view thống kê và truyền dữ liệu vào
    $content = 'views/thongke/user.php';
    include('views/layouts/base.php');
}

// Action để hiển thị chi tiết người dùng theo role
public function roleDetail($role) {
    // Gọi phương thức từ model để lấy danh sách người dùng theo role
    $users = $this->user->getUsersByRoleSortedByName($role);

    // Hiển thị view chi tiết người dùng và truyền dữ liệu vào
    include('views/user/role_detail.php');
}
}

    
?>
