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
            $avatarPath = null;
            $password = null;
            
            if (!empty($_POST['new_password'])) {
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
                header("Location: index.php?model=auth&action=profile");
                exit();
            } else {
                echo "Cập nhật thông tin không thành công.";
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