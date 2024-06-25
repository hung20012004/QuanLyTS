<?php
require_once '../src/controllers/Controller.php';
require 'src/controllers/BaoTriController.php';
require 'src/controllers/HoaDonMuaController.php';
require 'src/controllers/LoaiTaiSanController.php';
require 'src/controllers/NhaCungCapController.php';
require 'src/controllers/ThanhLyController.php';
require 'src/controllers/ViTriController.php';
require 'src/controllers/UserController.php';
require 'src/controllers/TaiSanController.php';

$controller = new Controller();

$model = isset($_GET['model'])? $_GET['model'] : 'index';

$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($model){
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
    case 'nhacungcap':
        $controller = new NhaCungCapController();
        break;
    case 'loaitaisan':
        $controller = new LoaiTaiSanController();
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
    case 'delete':
        $controller->delete($id);
        break;
    default:
        $controller->index();
        break;
}


?>
