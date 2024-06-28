<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=khauhao&action=index">Khấu hao</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Khấu Hao</li>
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
                        <h5 class="card-title mb-0">Thêm khấu hao</h5>
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
                    <form action="index.php?model=khauhao&action=create" method="POST">
                        <div class="mb-3">
                            <label for="tai_san" class="form-label">Tên tài sản</label>
                            <?php foreach($ts as $taiSan): ?>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($taiSan['ten_tai_san']) ?>" disabled>
                                <input type="hidden" name = 'tai_san_id'class="form-control" value="<?= htmlspecialchars($taiSan['tai_san_id']) ?>">
                            <?php endforeach; ?>    
                        </div>
                        <div class="mb-3">
                            <label for="ngay_khau_hao" class="form-label">Ngày Khấu Hao</label>
                            <input type="date" id="ngay_khau_hao" name="ngay_khau_hao" class="form-control" placeholder="Ngày khấu hao"></input>
                        </div>
                        <div class="mb-3">
                            <label for="so_tien" class="form-label">Số tiền</label>
                            <input type="number" name="so_tien" id="so_tien" class="form-control" placeholder="Số tiền" required>
                        </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=khauhao&action=index" class="btn btn-secondary">Trở về</a>
                    <button type="submit" class="btn btn-primary" name="btnThem">Tạo</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
