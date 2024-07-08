<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieunhap&action=index">phiếu bàn giao</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nhập tài sản</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Nhập tài sản - phiếu bàn giao #<?= $phieuNhap['phieu_nhap_tai_san_id'] ?></h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=phieunhap&action=nhap_tai_san&id=<?= $phieuNhap['phieu_nhap_tai_san_id'] ?>">
                <!-- Hiển thị thông tin phiếu bàn giao và chi tiết -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Ngày tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($phieuNhap['ngay_tao'])) ?>" readonly>
                    </div>
                </div>
                <?php if ($phieuNhap['trang_thai'] != 'DangXetDuyet'): ?>
                <div class="form-group row">
                    <label for="nguoiDuyet" class="col-sm-2 col-form-label">Người phê duyệt phiếu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nguoiDuyet"
                            value="<?= htmlspecialchars($phieuNhap['user_duyet_id']); ?>" readonly>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Ngày phê duyệt:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?= date('d-m-Y', strtotime($phieuNhap['ngay_xac_nhan'])) ?>" readonly>
                    </div>
                </div>

                <h5 class="mt-4">Chi tiết phiếu bàn giao</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Loại tài sản</th>
                            <th>Tên tài sản</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chiTietPhieuNhap as $chiTiet): ?>
                            <tr>
                                <td><?= htmlspecialchars($chiTiet['ten_loai_tai_san']) ?></td>
                                <td><?= htmlspecialchars($chiTiet['ten_tai_san']) ?></td>
                                <td><?= $chiTiet['so_luong'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="form-group mt-3">
                    <label for="ghiChu">Ghi chú:</label>
                    <textarea class="form-control" id="ghiChu" name="ghi_chu" rows="3" readonly><?= htmlspecialchars($phieuNhap['ghi_chu']) ?></textarea>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Xác nhận nhập tài sản</button>
                        <a href="index.php?model=phieunhap&action=index" class="btn btn-secondary">Quay lại</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>