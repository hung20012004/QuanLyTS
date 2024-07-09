<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieunhap&action=index">Phiếu thanh lý</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thanh lý tài sản</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

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
        <!-- <script>
            setTimeout(function () {
                var alert = document.getElementById('alert-message');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(function () {
                        alert.style.display = 'none';
                    }, 150);
                }
            }, 7000); // 7000 milliseconds = 7 seconds
        </script> -->
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thanh lý tài sản - Phiếu thanh lý #<?= $phieuThanhLy['phieu_thanh_ly_id'] ?></h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=phieuthanhly&action=thanh_ly&id=<?= $phieuThanhLy['phieu_thanh_ly_id'] ?>">
                <!-- Hiển thị thông tin phiếu nhập và chi tiết -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Ngày tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($phieuThanhLy['ngay_tao'])) ?>" readonly>
                    </div>
                </div>
                <?php if ($phieuThanhLy['trang_thai'] != 'DangXetDuyet'): ?>
                <div class="form-group row">
                    <label for="nguoiDuyet" class="col-sm-2 col-form-label">Người phê duyệt phiếu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nguoiDuyet"
                            value="<?= htmlspecialchars($phieuThanhLy['nguoi_duyet_name']); ?>" readonly>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Ngày phê duyệt:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($phieuThanhLy['ngay_xac_nhan'])) ?>" readonly>
                    </div>
                </div>

                <h5 class="mt-4">Chi tiết phiếu nhập</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên tài sản</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chiTietPhieuThanhLy as $chiTiet): ?>
                            <tr>
                                <td><?= htmlspecialchars($chiTiet['ten_tai_san']) ?>
                              <input type="hidden" class="form-control" name="tai_san_ten[]" value="<?= htmlspecialchars($chiTiet['ten_tai_san']) ?>" readonly></td>
                                <td><?= $chiTiet['so_luong'] ?></td>
                                <input type="hidden" name="nguoi_phe_duyet_id" value="<?= $_SESSION['user_id']?>">
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="form-group mt-3">
                    <label for="ghiChu">Ghi chú:</label>
                    <textarea class="form-control" id="ghiChu" name="ghi_chu" rows="3" readonly><?= htmlspecialchars($phieuThanhLy['ghi_chu']) ?></textarea>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Xác nhận thanh lý tài sản</button>
                        <a href="index.php?model=phieunhap&action=index" class="btn btn-secondary">Quay lại</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>