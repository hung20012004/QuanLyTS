<?php
session_start();

require 'controllers/Controller.php';
require 'controllers/BaoTriController.php';
require 'controllers/HoaDonMuaController.php';
require 'controllers/LoaiTaiSanController.php';
require 'controllers/NhaCungCapController.php';
require 'controllers/ThanhLyController.php';
require 'controllers/ViTriController.php';
require 'controllers/UserController.php';
require 'controllers/AuthController.php';
require 'controllers/TaiSanController.php';
require 'controllers/KhauHaoController.php';


$controller = new Controller();

$model = isset($_GET['model']) ? $_GET['model'] : 'index';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!isset($_SESSION['user_id']) && !in_array($action, ['login', 'register'])) {
    header('Location: index.php?model=auth&action=login');
    exit();
}

switch ($model) {
    case 'baotri':
        $controller = new BaoTriController();
        break;
    case 'hoadonmua':
        $controller = new HoaDonMuaController();
        break;
    case 'thanhly':
        $controller = new ThanhLyController();
        break;
    case 'vitri':
        $controller = new ViTriController();
        break;
    case 'taisan':
        $controller = new TaiSanController();
        break;
    case 'user':
        $controller = new UserController();
        break;
    case 'auth':
        $controller = new AuthController();
        break;
    case 'nhacungcap':
        $controller = new NhaCungCapController();
        break;
    case 'loaitaisan':
        $controller = new LoaiTaiSanController();
        break;
    case 'khauhao':
        $controller = new KhauHaoController();
        break;
    default:
        $controller = new Controller();
        break;
}

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit($id);
        break;
    case 'viewedit':
        $controller->viewedit($id);
        break;
    case 'delete':
        $controller->delete($id);
        break;
    case 'login':
        $controller->login();
        break;
    case 'register':
        $controller->register();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'profile':
        $controller->profile();
        break;
    case 'export':
        $controller->export();
        break;
    case 'statistic':
        $controller->statistics();
        break;
    case 'viewcreate':
        $controller->viewcreate();
        break;
    case 'show':
        $controller->show($id);
        break;
    case 'detail':
        $controller->detail($id);
        break;
    case 'forgot_password':
        $controller->forgot_password_request();
        break;
    case 'reset_password':
        $controller->reset_password();
        break;
    case 'getQuantityInStock':
        $controller->getQuantityInStock();
        break;
    // case 'viewcreatekh':
    //     $controller->viewcreatekh($id); 
    //     break;
    default:
        $controller->index();
        break;
}
?>