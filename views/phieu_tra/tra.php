<div class="container-fluid">
<?php if (isset($_SESSION['message'])): ?>
        <div id="alert-message" class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message']; ?>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
        <script>
            setTimeout(function() {
                var alert = document.getElementById('alert-message');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 150); // Optional: wait for the fade-out transition to complete
                }
            }, 2000); // 2000 milliseconds = 2 seconds
        </script>
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Trả tài sản - Phiếu trả tài sản số #<?=$phieuTra['phieu_tra_id']?></h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Người tạo yêu cầu:</strong> <?= htmlspecialchars($nguoiNhan['ten']); ?>
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
                    <strong>Người kiểm tra:</strong> <?= htmlspecialchars($_SESSION['ten']); ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($phieuTra['ghi_chu'])); ?>
                </div>
            </div>

            <h5 class="mt-4">Danh sách tài sản yêu cầu:</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vị trí</th>
                        <th>Tên tài sản</th>
                        <th>Số lượng yêu cầu</th>
                        <th>Số lượng trong kho</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chiTietPhieuTra as $chiTiet): ?>
                        <tr>
                            <td><?= htmlspecialchars($chiTiet['ten_vi_tri']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['ten_tai_san']); ?></td>
                            <td><?= htmlspecialchars($chiTiet['so_luong']); ?></td>
                             <td><?= htmlspecialchars($chiTiet['so_luong_trong_phong_ban']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form action="index.php?model=phieutra&action=tra&id=<?= $phieuTra['phieu_tra_id']; ?>" method="POST" class="mt-4">
                <button type="submit" class="btn btn-primary">Xác nhận trả tài sản</button>
                <a href="index.php?model=phieutra&action=index" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
</div>