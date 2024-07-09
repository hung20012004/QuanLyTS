<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

// Lấy dữ liệu phiếu bàn giao từ database (giả sử bạn đã có các hàm để lấy dữ liệu)
$phieuBanGiao = getPhieuBanGiao($_GET['id']);
$nguoiNhan = getNguoiNhan($phieuBanGiao['nguoi_nhan_id']);
$viTri = getViTri($phieuBanGiao['vi_tri_id']);
$chiTietWithAdditionalData = getChiTietPhieuBanGiao($phieuBanGiao['phieu_ban_giao_id']);
$nguoiBanGiao = getNguoiBanGiao($phieuBanGiao['nguoi_ban_giao_id']);
$nguoiDuyet = getNguoiDuyet($phieuBanGiao['nguoi_duyet_id']);

// Tạo đối tượng PHPWord
$phpWord = new PhpWord();

// Thêm một section mới
$section = $phpWord->addSection();

// Thêm tiêu đề
$section->addText('CHI TIẾT PHIẾU BÀN GIAO TÀI SẢN', ['bold' => true, 'size' => 16], ['alignment' => 'center']);

// Thêm thông tin chung
$section->addText('Người tạo yêu cầu: ' . $nguoiNhan['ten']);
$section->addText('Ngày tạo phiếu: ' . date('d/m/Y', strtotime($phieuBanGiao['ngay_gui'])));
$section->addText('Vị trí: ' . $viTri['ten_vi_tri']);
$section->addText('Trạng thái: ' . $statusMap[$phieuBanGiao['trang_thai']]);
$section->addText('Ghi chú: ' . $phieuBanGiao['ghi_chu']);

// Thêm bảng danh sách tài sản
$table = $section->addTable();
$table->addRow();
$table->addCell(2000)->addText('Loại tài sản', ['bold' => true]);
$table->addCell(2000)->addText('Tên tài sản', ['bold' => true]);
$table->addCell(1000)->addText('Số lượng', ['bold' => true]);
$table->addCell(2000)->addText('Tình trạng', ['bold' => true]);

foreach ($chiTietWithAdditionalData as $chiTiet) {
    $table->addRow();
    $table->addCell(2000)->addText($chiTiet['ten_loai_tai_san']);
    $table->addCell(2000)->addText($chiTiet['ten_tai_san']);
    $table->addCell(1000)->addText($chiTiet['so_luong']);
    $table->addCell(2000)->addText($chiTiet['tinh_trang']);
}

// Thêm thông tin người bàn giao và người duyệt
$section->addText('Người bàn giao: ' . ($nguoiBanGiao['ten'] ?? 'Chưa bàn giao'));
$section->addText('Người duyệt: ' . ($nguoiDuyet['ten'] ?? 'Chưa duyệt'));
$section->addText('Ngày kiểm tra: ' . ($phieuBanGiao['ngay_kiem_tra'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_kiem_tra'])) : 'Chưa kiểm tra'));
$section->addText('Ngày duyệt: ' . ($phieuBanGiao['ngay_duyet'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_duyet'])) : 'Chưa duyệt'));
$section->addText('Ngày bàn giao: ' . ($phieuBanGiao['ngay_ban_giao'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_ban_giao'])) : 'Chưa bàn giao'));

// Lưu file
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$fileName = 'PhieuBanGiao_' . $phieuBanGiao['phieu_ban_giao_id'] . '.docx';
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header('Content-Disposition: attachment; filename="' . $fileName . '"');
$objWriter->save("php://output");
exit;