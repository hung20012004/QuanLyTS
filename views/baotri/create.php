<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=baotri&action=index">Lịch Bảo Trì</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Mới</li>
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
                        <h5 class="card-title mb-0">Thêm Mới Lịch Bảo Trì</h5>
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
                    <form action="index.php?model=baotri&action=create" method="POST">
                        <div class="mb-3">
                            <label for="tai_san_id" class="form-label">Tài Sản ID:</label>
                            <input type="number" name="tai_san_id" id="tai_san_id" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="ngay_bat_dau" class="form-label">Ngày Bắt Đầu:</label>
                                <input type="date" name="ngay_bat_dau" id="ngay_bat_dau" class="form-control" required>
                            </div>
                            <div class="col">
                                <label for="ngay_ket_thuc" class="form-label">Ngày Kết Thúc:</label>
                                <input type="date" name="ngay_ket_thuc" id="ngay_ket_thuc" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô Tả:</label>
                            <textarea name="mo_ta" id="mo_ta" class="form-control"></textarea>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=baotri&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
