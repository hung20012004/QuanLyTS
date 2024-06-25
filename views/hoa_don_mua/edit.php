<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=hoa_don&action=index">Hóa Đơn</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <h5 class="card-title mb-0">Sửa Thông Tin Hóa Đơn</h5>
        </div>
    </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="hoa_don_id" value="<?= $invoice['hoa_don_id'] ?>">
                <div class="form-group">
                    <label for="ngayMua">Ngày Mua:</label>
                    <input type="date" class="form-control" id="ngayMua" name="ngay_mua" value="<?= $invoice['ngay_mua'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="tongGiaTri">Tổng Giá Trị:</label>
                    <input type="number" class="form-control" id="tongGiaTri" name="tong_gia_tri" value="<?= $invoice['tong_gia_tri'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="nhaCungCap">Nhà Cung Cấp:</label>
                    <select class="form-control" id="nhaCungCap" name="nha_cung_cap_id" required>
                        <option value="">Chọn nhà cung cấp</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['nha_cung_cap_id'] ?>" <?= ($supplier['nha_cung_cap_id'] == $invoice['nha_cung_cap_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($supplier['ten_nha_cung_cap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="index.php?model=hoa_don&action=index" class="btn btn-secondary">Quay Lại</a>
            </form>
        </div>
    </div>
</div>
