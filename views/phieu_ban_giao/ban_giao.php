<div class="container-fluid">
<?php if (isset($_SESSION['message'])): ?>
        <div id="alert-message" class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show"
            role="alert">
            <?= $_SESSION['message']; ?>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
        <script>
            setTimeout(function () {
                var alert = document.getElementById('alert-message');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(function () {
                        alert.style.display = 'none';
                    }, 150);
                }
            }, 7000);
        </script>
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 pt-3 font-weight-bold text-primary">Chi tiết phiếu bàn giao tài sản</h6>
            <?php if ($phieuBanGiao['trang_thai'] == 'DaGiao'): ?>
            <a href="index.php?model=phieubangiao&action=exportWord&id=<?= $phieuBanGiao['phieu_ban_giao_id']; ?>" class="btn btn-primary">Xuất Word</a>
            <?php endif;?>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Người tạo yêu cầu:</strong> <?= htmlspecialchars($nguoiNhan['ten']); ?>
                </div>
                <div class="col-md-6">
                    <strong>Ngày tạo phiếu:</strong> <?= date('d/m/Y', strtotime($phieuBanGiao['ngay_gui'])); ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Vị trí:</strong> <?= htmlspecialchars($viTri['ten_vi_tri']); ?>
                </div>
                <div class="col-md-6">
                    <strong>Trạng thái:</strong>
                    <?php
                    $statusMap = [
                        'DaGui' => 'Đã gửi',
                        'DaKiemTra' => 'Đã kiểm tra',
                        'DangChoPheDuyet' => 'Đang chờ phê duyệt',
                        'DaPheDuyet' => 'Đã phê duyệt',
                        'DaGiao' => 'Đã bàn giao',
                        'KhongDuyet' => 'Không duyệt'
                    ];
                    echo htmlspecialchars($statusMap[$phieuBanGiao['trang_thai']] ?? $phieuBanGiao['trang_thai']);
                    ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($phieuBanGiao['ghi_chu'])); ?>
                </div>
            </div>

            <h5 class="mt-4">Danh sách tài sản bàn giao:</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Loại tài sản</th>
                        <th>Tên tài sản</th>
                        <th>Số lượng</th>
                        <th>Tình trạng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chiTietWithAdditionalData as $chiTiet): ?>
                        <tr>
                            <td><?= htmlspecialchars($chiTiet['ten_loai_tai_san']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['ten_tai_san']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['so_luong']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['tinh_trang']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row mt-4">
                <div class="col-md-6">
                    <strong>Người bàn giao:</strong> <?= htmlspecialchars($nguoiBanGiao['ten'] ?? 'Chưa bàn giao'); ?>
                </div>
                <div class="col-md-6">
                    <strong>Người duyệt:</strong> <?= htmlspecialchars($nguoiDuyet['ten'] ?? 'Chưa duyệt'); ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <strong>Ngày kiểm tra:</strong>
                    <?= $phieuBanGiao['ngay_kiem_tra'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_kiem_tra'])) : 'Chưa kiểm tra'; ?>
                </div>
                <div class="col-md-4">
                    <strong>Ngày duyệt:</strong>
                    <?= $phieuBanGiao['ngay_duyet'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_duyet'])) : 'Chưa duyệt'; ?>
                </div>
                <div class="col-md-4">
                    <strong>Ngày bàn giao:</strong>
                    <?= $phieuBanGiao['ngay_ban_giao'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_ban_giao'])) : 'Chưa bàn giao'; ?>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="index.php?model=phieubangiao&action=index" class="btn btn-secondary">Quay lại</a>
            <?php if ($phieuBanGiao['trang_thai'] == 'DaPheDuyet'): ?>
                <form method="POST" action="index.php?model=phieubangiao&action=ban_giao&id=<?= $phieuBanGiao['phieu_ban_giao_id']; ?>" id="phieuBanGiaoForm">
                    <button type="submit" class="btn btn-primary">Xác nhận bàn giao</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>