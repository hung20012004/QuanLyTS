<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=baotri&action=index">Lịch Bảo Trì</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Chỉnh Sửa Lịch Bảo Trì</h5>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?model=baotri&action=edit&id=<?= $schedule['schedule_id'] ?>" method="POST">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="vi_tri_id" class="form-label">Vị Trí ID</label>
                                <select name="vi_tri_id" id="vi_tri_id" class="form-control" required>
                                    <?php foreach ($viTris as $viTri): ?>
                                        <option value="<?= $viTri['vi_tri_id'] ?>" <?= ($schedule['vi_tri_id'] == $viTri['vi_tri_id']) ? 'selected' : '' ?>><?= htmlspecialchars($viTri['ten_vi_tri']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="ngay_bat_dau" class="form-label">Ngày Bắt Đầu</label>
                                <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" value="<?= htmlspecialchars($schedule['ngay_bat_dau']) ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="ngay_ket_thuc" class="form-label">Ngày Kết Thúc</label>
                                <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" value="<?= htmlspecialchars($schedule['ngay_ket_thuc']) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô Tả</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta"><?= htmlspecialchars($schedule['mo_ta']) ?></textarea>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=baotri&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
