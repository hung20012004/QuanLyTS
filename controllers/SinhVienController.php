<?php
include_once 'config/database.php';
include_once 'models/SinhVien.php';

class SinhVienController extends Controller{
    private $db;
    private $sinhvien;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->sinhvien = new SinhVien($this->db);
    }

    public function index() {
        $stmt = $this->sinhvien->read();
        $sinhviens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content = 'views/sinhviens/index.php';
        include('views/layouts/auth.php');
    }

    public function create() {
        if($_POST) {
            $this->sinhvien->NgaySinh= $_POST['NgaySinh'];
            $this->sinhvien->MaSV = $_POST['MaSV'];
            $this->sinhvien->Ten = $_POST['Ten'];
            $this->sinhvien->DiemChuyenCan= $_POST['DiemChuyenCan'];
            $this->sinhvien->DiemCuoiKy = $_POST['DiemCuoiKy'];
            $this->sinhvien->DiemGiuaKy = $_POST['DiemGiuaKy'];
            if($this->sinhvien->create()) {
                header("Location: route.php?model=sinhvien");
            }
        }
        $content = 'views/sinhviens/create.php';
        include('views/layouts/auth.php');
    }

    public function edit($id) {
        if($_POST) {
            $this->sinhvien->ID = $id;
            $this->sinhvien->NgaySinh= $_POST['NgaySinh'];
            $this->sinhvien->MaSV = $_POST['MaSV'];
            $this->sinhvien->Ten = $_POST['Ten'];
            $this->sinhvien->DiemChuyenCan= $_POST['DiemChuyenCan'];
            $this->sinhvien->DiemCuoiKy = $_POST['DiemCuoiKy'];
            $this->sinhvien->DiemGiuaKy = $_POST['DiemGiuaKy'];
            if($this->sinhvien->update()) {
                header("Location: route.php?model=sinhvien");
            }
        }  
        $content = 'views/sinhviens/edit.php';
        include('views/layouts/auth.php');
    }

    public function delete($id) {
        $this->sinhvien->ID = $id;
        if($this->sinhvien->delete()) {
            header("Location: route.php?model=sinhvien");
        }
    }
}
?>
