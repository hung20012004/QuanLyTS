<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieusua&action=index">Phiếu sửa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo phiếu sửa</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tạo phiếu sửa</h6>
        </div>
        <div class="card-body">
            <h5>Thông tin yêu cầu</h5>
            <form method="POST" action="index.php?model=phieusua&action=create" id="phieuSuaForm">
                <!-- Các trường thông tin chung của phiếu sửa -->
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="nguoiYeuCau" class="row-md-4 row-form-label">Người yêu cầu:</label>
                        <div class="row-md-2">
                            <input type="text" class="form-control" id="nguoiYeuCau" name="nguoi_yeu_cau" value="<?= htmlspecialchars($_SESSION['ten']); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="ngayYeuCau" class="row-md-4 row-form-label">Ngày yêu cầu:</label>
                        <div class="row-sm-2">
                            <input type="date" class="form-control" id="ngayYeuCau" name="ngay_yeu_cau" value="<?= date('Y-m-d'); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="vi_tri_id[]">Vị trí:</label>
                    <select class="form-control vi-tri col-sm-2" name="vi_tri_id" required>
                        <option value="">Chọn vị trí</option>
                        <?php foreach ($viTris as $vi_tri): ?>
                            <option value="<?= $vi_tri['vi_tri_id']; ?>">
                                <?= htmlspecialchars($vi_tri['ten_vi_tri']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mt-3">
                    <label for="mo_ta">Mô tả:</label>
                    <textarea class="form-control" id="mo_ta" name="mo_ta" rows="3" required></textarea>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Lưu và gửi</button>
                        <a href="index.php?model=phieusua&action=index" class="btn btn-secondary">Hủy</a>
                    </div>
                </div>
                </div>
            </form>
        </div>

    </div>
</div>

