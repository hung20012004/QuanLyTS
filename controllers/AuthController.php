<?php
// controllers/AuthController.php

include_once 'config/database.php';
require 'models/Auth.php';
include_once 'models/User.php';


class AuthController extends Controller {
    private $db;
    private $authModel;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->authModel = new Auth($this->db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($this->authModel->register($email, $password)) {
                header('Location: index.php?model=auth&action=login');
                exit();
            } else {
                echo "Đăng ký không thành công.";
            }
        } else {
            include('views/auth/register.php');
        }
    }

    public function login() {
        $error_msg = '';
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $authModel = new Auth($this->db);
            $user = $authModel->getUserByEmail($email);
            // var_dump($user);
            // exit();
            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['ten'] = $user['ten'];
                $_SESSION['avatar'] = $user['avatar'];
                // $_SESSION['password'] = $user['password'];
                header('Location: dashboard.php');
                exit();
            } else {
                // Đăng nhập thất bại
                $error_msg = "Email hoặc mật khẩu không chính xác.";
            }
        }
    
        include('views/auth/login.php');
    }
    public function sendEmail($to, $subject, $message) {
        $headers = "From: no-reply@yourwebsite.com\r\n";
        $headers .= "Reply-To: no-reply@yourwebsite.com\r\n";
        $headers .= "Content-type: text/html\r\n";
        mail($to, $subject, $message, $headers);
    }
    public function forgot_password_request() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            
            // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
            $user = $this->authModel->getUserByEmail($email);
            if ($user) {
                $token = bin2hex(random_bytes(50)); // Tạo token ngẫu nhiên
                $this->authModel->setResetToken($email, $token);

                $resetLink = "http://yourwebsite.com/index.php?model=auth&action=reset_password&token=$token";
                $subject = "Đặt lại mật khẩu của bạn";
                $message = "Nhấp vào liên kết sau để đặt lại mật khẩu của bạn: <a href='$resetLink'>$resetLink</a>";
                $this->sendEmail($email, $subject, $message); // Gửi email

                echo "Một liên kết đặt lại mật khẩu đã được gửi đến email của bạn.";
            } else {
                echo "Email không tồn tại trong hệ thống.";
            }
        }
    }
    
    public function reset_password() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

            // Xác thực token và cập nhật mật khẩu mới
            if ($this->authModel->resetPassword($token, $newPassword)) {
                echo "Mật khẩu của bạn đã được cập nhật thành công.";
            } else {
                echo "Liên kết không hợp lệ hoặc đã hết hạn.";
            }
        } else {
            $token = $_GET['token'];
            include('views/auth/reset-password.php');
        }
    }
    public function logout() {
        // session_start();
        session_destroy();
        header('Location: index.php?model=auth&action=login');
        exit();
    }
    public function profile() {
        if (!isset($_SESSION['email'])) {
            // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập
            header('Location: index.php?model=auth&action=login');
            exit();
        }

        // Lấy thông tin người dùng từ CSDL dựa trên $_SESSION['user_id']
        $user = $this->authModel->getUserByEmail($_SESSION['email']);

        // Nếu không tìm thấy người dùng, xử lý lỗi hoặc thông báo không tìm thấy
        if (!$user) {
            echo "Không tìm thấy thông tin người dùng.";
            exit();
        }

        // Include view để hiển thị thông tin người dùng
        $content = 'views/auth/profile.php';
        include('views/layouts/base.php');
    }
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $ten = $_POST['ten'];
            $avatarPath = $_SESSION['avatar'];
            $password = null;
            $current_password = $_POST['current_password'];
            
            
            if (!empty($_POST['new_password'])) {
                if (!$this->authModel->checkCurrentPassword($id, $current_password)) {
                    $_SESSION['message'] = 'Mật khẩu không chính xác.';
                    $_SESSION['message_type'] = 'danger';
                    header("Location: index.php?model=auth&action=profile");
                    exit();
                }
                $password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            }
    
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                
                $uploadDir = 'uploads/avatars/';
                $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                    $avatarPath = $uploadFile;
                } else {
                    echo "Không thể tải lên tệp. Vui lòng kiểm tra quyền thư mục.";
                    return;
                }
            }
    
            if ($this->authModel->updateUserProfile($id, $email, $ten, $avatarPath, $password)) {
                $_SESSION['email'] = $email;
                $_SESSION['ten'] = $ten;
                if ($avatarPath) {
                    $_SESSION['avatar'] = $avatarPath;
                }
                if ($password) {
                    $_SESSION['password'] = $password;
                }
                $_SESSION['message'] = 'Cập nhật thông tin thành công.';
                $_SESSION['message_type'] = 'success';
                header("Location: index.php?model=auth&action=profile");
                exit();
            } else {
                $_SESSION['message'] = 'Cập nhật thông tin không thành công.';
                $_SESSION['message_type'] = 'danger';
                header("Location: index.php?model=auth&action=profile");
            }
        } else {
            $user = $this->authModel->getUserByID($id);
            if (!$user) {
                echo "Không tìm thấy thông tin người dùng.";
                exit();
            }
    
            $content = 'views/auth/edit_profile.php';
            include('views/layouts/base.php');
        }
    }
    
}
?>