<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieubangiao&action=index">Bàn giao tài sản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Xét duyệt phiếu bàn giao tài sản</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Xét duyệt phiếu bàn giao tài sản</h6>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type']; ?>">
                    <?= $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
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
                        'DaTra' => 'Đã trả',
                        'KhongDuyet' => 'Không duyệt'
                    ];
                    echo htmlspecialchars($statusMap[$phieuBanGiao['trang_thai']]);
                    ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($phieuBanGiao['ghi_chu'])); ?>
                </div>
            </div>

            <h5 class="mt-4">Danh sách tài sản yêu cầu:</h5>
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
                            <?php
                            $tinhTrangLabels = [
                                'Moi' => 'Mới',
                                'Tot' => 'Tốt',
                                'Kha' => 'Khá',
                                'TrungBinh' => 'Trung bình',
                                'Kem' => 'Kém',
                                'Hong' => 'Hỏng'
                            ];
                            $tinhTrangLabel = $tinhTrangLabels[$chiTiet['tinh_trang']] ?? 'Không xác định';
                            ?>
                            <td><?= htmlspecialchars($tinhTrangLabel); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row mt-4">
                <div class="col-md-6">
                    <strong>Người bàn giao:</strong> <?= htmlspecialchars($nguoiBanGiao['ten'] ?? 'Chưa kiểm tra'); ?>
                </div>
                <div class="col-md-6">
                    <strong>Người duyệt:</strong> <?= htmlspecialchars($user_duyet['ten']); ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Ngày kiểm tra:</strong>
                    <?= $phieuBanGiao['ngay_kiem_tra'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_kiem_tra'])) : 'Chưa kiểm tra'; ?>
                </div>
                <div class="col-md-6">
                    <strong>Ngày duyệt:</strong>
                    <?= $phieuBanGiao['ngay_duyet'] ? date('d/m/Y', strtotime($phieuBanGiao['ngay_duyet'])) : 'Chưa duyệt'; ?>
                </div>
            </div>

            <form action="index.php?model=phieubangiao&action=xet_duyet&id=<?= $phieuBanGiao['phieu_ban_giao_id']; ?>" method="POST" class="mt-4">
        </div>
        <div class="card-footer d-flex justify-content-between">
        <a href="index.php?model=phieubangiao&action=index" class="btn btn-secondary">Quay lại</a>
                <input type="hidden" name="nguoiBanGiao" value="<?php echo $nguoiBanGiao['user_id']; ?>">
                <input type="hidden" name="ngayKiemTra" value="<?= htmlspecialchars($phieuBanGiao['ngay_kiem_tra']); ?>">
                <button type="submit" name="action" value="approve" class="btn btn-success">Phê duyệt</button>
                <button type="submit" name="action" value="reject" class="btn btn-danger">Từ chối</button>
        </div>
        </form>
    </div>
</div>
