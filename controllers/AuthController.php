<?php
// controllers/AuthController.php

include_once 'config/database.php';
require 'models/Auth.php';

class AuthController extends Controller {
    private $db;
    private $authModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $authModel = new Auth($this->db);
            $user = $authModel->getUserByEmail($email);
    
            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công, lưu session và chuyển hướng đến trang chính
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['ten'] = $user['ten'];
                header('Location: dashboard.php');
                exit();
            } else {
                // Đăng nhập thất bại, xử lý thông báo lỗi hoặc tái hiện form đăng nhập
                echo "Email hoặc mật khẩu không chính xác.";
            }
        } else {
            include('views/auth/login.php');
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
}
?>
