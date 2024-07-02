<?php
// controllers/PhieuNhapController.php
include_once 'config/database.php';
include_once 'models/PhieuNhap.php';
include_once 'models/LoaiTaiSan.php';
include_once 'models/ChiTietPhieuNhap.php';
include_once 'models/TaiSan.php';
include_once 'models/ViTriChiTiet.php';

class PhieuNhapController extends Controller
{
    private $db;
    private $phieuNhapModel;
    private $loaiTaiSanModel;
    private $chiTietPhieuNhapModel;
    private $taiSanModel;
    private $viTriChiTietModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->phieuNhapModel = new PhieuNhap($this->db);
        $this->loaiTaiSanModel = new LoaiTaiSan($this->db);
        $this->chiTietPhieuNhapModel = new ChiTietPhieuNhap($this->db);
        $this->taiSanModel = new TaiSan($this->db);
        $this->viTriChiTietModel = new ViTriChiTiet($this->db);
    }

    public function index()
    {
        $phieuNhap = $this->phieuNhapModel->readAll();
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $content = 'views/phieu_nhap/index.php';
        include('views/layouts/base.php');
    }

    public function create()
{
    if ($_POST) {
        $this->db->beginTransaction();
        try {
            // Tạo mới phiếu nhập
            $this->phieuNhapModel->ngay_nhap = $_POST['ngay_nhap'];
            $this->phieuNhapModel->create();
            $phieuNhapId = $this->db->lastInsertId();

            // Lặp qua từng chi tiết phiếu nhập
            for ($i = 0; $i < count($_POST['ten_tai_san']); $i++) {
                // Kiểm tra và tạo mới tài sản nếu chưa tồn tại
                $taiSanId = $_POST['tai_san_id'][$i];
                if (empty($taiSanId)) {
                    $taiSanData = array(
                        'ten_tai_san' => $_POST['ten_tai_san'][$i],
                        'mo_ta' => '',
                        'loai_tai_san_id' => $_POST['loai_tai_san_id'][$i]
                    );
                    $taiSanId = $this->taiSanModel->createOrUpdate($taiSanData);
                }

                // Tạo mới chi tiết phiếu nhập
                $this->chiTietPhieuNhapModel->phieu_nhap_tai_san_id = $phieuNhapId;
                $this->chiTietPhieuNhapModel->tai_san_id = $taiSanId;
                $this->chiTietPhieuNhapModel->so_luong = $_POST['so_luong'][$i];
                $this->chiTietPhieuNhapModel->create();
                $chiTietID = $this->db->lastInsertId();

                // Tạo mới vị trí chi tiết
                $viTri = new ViTriChiTiet($this->db);
                $viTri->chi_tiet_id = $chiTietID;
                $viTri->vi_tri_id = 1; // Giả sử mặc định là vị trí ID 1
                $viTri->so_luong = $_POST['so_luong'][$i];
                $viTri->create();
            }

            $this->db->commit();

            $_SESSION['message'] = 'Tạo phiếu nhập mới thành công!';
            $_SESSION['message_type'] = 'success';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header("Location: index.php?model=phieunhap&action=index");
            exit();
        }
    }

    // Đọc danh sách tài sản để hiển thị trong form
    $tai_san_list = $this->taiSanModel->read();
    $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
    // Đặt nội dung view và layout
    $content = 'views/phieu_nhap/create.php';
    include('views/layouts/base.php');
}


    public function edit($id)
    {
        $invoice = $this->phieuNhapModel->readById($id);
        $invoice_details = $this->chiTietPhieuNhapModel->readByPhieuNhapId($id);
        $quantityMismatch = false;

        // Kiểm tra số lượng trước khi xử lý POST
        foreach ($invoice_details as $detail) {
            if (!$this->checkQuantityMatch($detail['chi_tiet_id'], $detail['so_luong'])) {
                $quantityMismatch = true;
                break;
            }
        }

        if ($_POST) {
            if ($quantityMismatch) {
                $_SESSION['message'] = 'Không thể sửa phiếu nhập do số lượng không khớp với vị trí chi tiết!';
                $_SESSION['message_type'] = 'danger';
            } else {
                $this->db->beginTransaction();
                try {
                    $this->phieuNhapModel->phieu_nhap_id = $id;
                    $this->phieuNhapModel->ngay_nhap = $_POST['ngay_nhap'];
                    $this->phieuNhapModel->tong_gia_tri = $_POST['tong_gia_tri'];
                    $this->phieuNhapModel->nha_cung_cap_id = $_POST['nha_cung_cap_id'];
                    $this->phieuNhapModel->update();

                    for ($i = 0; $i < count($_POST['ten_tai_san']); $i++) {
                        $taiSanId = $_POST['tai_san_id'][$i];
                        if (empty($taiSanId)) {
                            $taiSanData = array(
                                'ten_tai_san' => $_POST['ten_tai_san'][$i],
                                'mo_ta' => '',
                                'loai_tai_san_id' => $_POST['loai_tai_san_id'][$i]
                            );
                            $taiSanId = $this->taiSanModel->createOrUpdate($taiSanData);
                        }

                        $this->chiTietPhieuNhapModel->phieu_nhap_id = $id;
                        $this->chiTietPhieuNhapModel->tai_san_id = $taiSanId;
                        $this->chiTietPhieuNhapModel->so_luong = $_POST['so_luong'][$i];
                        $this->chiTietPhieuNhapModel->don_gia = $_POST['don_gia'][$i];

                        if (empty($_POST['chi_tiet_id'][$i])) {
                            $this->chiTietPhieuNhapModel->create();
                            $chiTietID = $this->db->lastInsertId();
                            $this->viTriChiTietModel->chi_tiet_id = $chiTietID;
                            $this->viTriChiTietModel->vi_tri_id = 1;
                            $this->viTriChiTietModel->so_luong = $_POST['so_luong'][$i];
                            $this->viTriChiTietModel->create();
                        } else {
                            $this->chiTietPhieuNhapModel->chi_tiet_id = $_POST['chi_tiet_id'][$i];
                            $this->chiTietPhieuNhapModel->update();
                            $this->viTriChiTietModel->update($_POST['chi_tiet_id'][$i], 1, $_POST['so_luong'][$i]);
                        }
                    }

                    if (!empty($_POST['deleted_chi_tiet_id'])) {
                        foreach ($_POST['deleted_chi_tiet_id'] as $chi_tiet_id) {
                            $this->chiTietPhieuNhapModel->delete($chi_tiet_id);
                            $this->viTriChiTietModel->delete($chi_tiet_id);
                        }
                    }

                    $this->db->commit();
                    $_SESSION['message'] = 'Sửa phiếu nhập thành công!';
                    $_SESSION['message_type'] = 'success';
                    header('Location: index.php?model=phieuNhap&action=index');
                    exit();
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $_SESSION['message'] = $e->getMessage();
                    $_SESSION['message_type'] = 'danger';
                    header('Location: index.php?model=phieuNhap&action=index');
                    exit();
                }
            }
        }

        $suppliers = $this->nhaCungCapModel->read();
        $loai_tai_san_list = $this->loaiTaiSanModel->readAll();
        $tai_san_list = $this->taiSanModel->read();
        $content = 'views/phieu_nhap/edit.php';
        include('views/layouts/base.php');
    }

    private function checkQuantityMatch($chi_tiet_id, $so_luong)
    {
        $viTriChiTiet = $this->viTriChiTietModel->readByChiTietId($chi_tiet_id);
        $totalQuantity = array_reduce($viTriChiTiet, function ($sum, $item) {
            return $sum + $item['so_luong'];
        }, 0);
        return $totalQuantity == $so_luong;
    }
}
