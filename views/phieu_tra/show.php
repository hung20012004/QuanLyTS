<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Chi tiết phiếu trả tài sản</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Người tạo yêu cầu:</strong> <?= $nguoiTra['ten']; ?>
                </div>
                <div class="col-md-6">
                    <strong>Ngày tạo phiếu:</strong> <?= date('d/m/Y', strtotime($phieuTra['ngay_gui'])); ?>
                </div>
            </div>
            <div class="row mb-3">
                <!-- <div class="col-md-6">
                    <strong>Vị trí:</strong> <?= htmlspecialchars($viTri['ten_vi_tri']); ?>
                </div> -->
                <div class="col-md-6">
                    <strong>Trạng thái:</strong>
                    <?php
                    $statusMap = [
                        'DaGui' => 'Đã gửi',
                        'DaHuy' => 'Đã hủy',
                        'DangChoPheDuyet' => 'Đang chờ phê duyệt',
                        'DaPheDuyet' => 'Đã phê duyệt',
                        'DaTra' => 'Đã trả',
                        'KhongDuyet' => 'Không duyệt'
                    ];
                    echo htmlspecialchars($statusMap[$phieuTra['trang_thai']]);
                    ?>
                </div>

                <div class="col-md-6">
                    <strong>Ngày kiểm tra:</strong>
                    <?= $phieuTra['ngay_kiem_tra'] ? date('d/m/Y', strtotime($phieuTra['ngay_kiem_tra'])) : 'Chưa kiểm tra'; ?>
                </div>

            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Ghi chú:</strong> <?= (htmlspecialchars($phieuTra['ghi_chu'])); ?>
                </div>
            </div>

            <h5 class="mt-4">Danh sách tài sản yêu cầu:</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vị trí</th>
                        <th>Tên tài sản</th>
                        <th>Số lượng</th>
                        <th>Tình trạng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chiTietPhieuTra as $chiTiet): ?>
                        <tr>
                            <td><?= htmlspecialchars($chiTiet['ten_vi_tri']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['ten_tai_san']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['so_luong']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['tinh_trang']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row mt-4">
                <div class="col-md-6">
                    <strong>Người trả:</strong> <?= htmlspecialchars($nguoiTra['ten'] ?? 'Chưa trả'); ?>
                </div>
                <div class="col-md-6">
                    <strong>Người duyệt:</strong> <?= htmlspecialchars($nguoiDuyet['ten'] ?? 'Chưa duyệt'); ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Ngày trả:</strong>
                    <?= $phieuTra['ngay_tra'] ? date('d/m/Y', strtotime($phieuTra['ngay_tra'])) : 'Chưa trả'; ?>
                </div>
                <div class="col-md-6">
                    <strong>Ngày duyệt:</strong>
                    <?= $phieuTra['ngay_duyet'] ? date('d/m/Y', strtotime($phieuTra['ngay_duyet'])) : 'Chưa duyệt'; ?>
                </div>
                
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <a href="index.php?model=phieutra&action=index" class="btn btn-secondary">Quay lại </a>
                    <!-- <?php if ($phieuTra['trang_thai'] == 'DaGui'): ?>
                        <a href="index.php?model=phieutra&action=kiem_tra&id=<?= $phieuTra['phieu_tra_id']; ?>"
                            class="btn btn-primary">Kiểm tra</a>
                    <?php elseif ($phieuTra['trang_thai'] == 'DaKiemTra'): ?>
                        <a href="index.php?model=phieutra&action=xet_duyet&id=<?= $phieuTra['phieu_tra_id']; ?>"
                            class="btn btn-success">Xét duyệt</a>
                    <?php elseif ($phieuTra['trang_thai'] == 'DaPheDuyet'): ?>
                        <a href="index.php?model=phieutra&action=tra&id=<?= $phieuTra['phieu_tra_id']; ?>"
                            class="btn btn-info">Bàn giao</a>
                    <?php endif; ?> -->
                </div>
            </div>
        </div>
    </div>
</div>