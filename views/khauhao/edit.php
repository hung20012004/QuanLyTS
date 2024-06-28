<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=khauhao&action=index">Khấu hao</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa Khấu Hao</li>
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
                        <h5 class="card-title mb-0">Sửa khấu hao</h5>
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
                    <form action="index.php?model=khauhao&action=edit&id=<?=$khid?>" method="POST">
                        <?php foreach($khau_hao as $kh): ?>
                        <div class="mb-3">
                            <label for="tai_san" class="form-label">Tên tài sản</label>
                                <input type="text" class="form-control" value="<?= $kh['ten_tai_san'] ?>" disabled>
                                <input type="hidden" name = 'tai_san_id'class="form-control" value="<?= htmlspecialchars($kh['tai_san_id']) ?>">   
                        </div>
                        <div class="mb-3">
                            <label for="ngay_khau_hao" class="form-label">Ngày Khấu Hao</label>
                            <input type="date" id="ngay_khau_hao" name="ngay_khau_hao" class="form-control"  value="<?= $kh['ngay_khau_hao'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="so_tien" class="form-label">Số tiền</label>
                             <input type="text" id="so_tien" name="so_tien" class="form-control"  value="<?= $kh['so_tien'] ?>" required>
                        </div>
                        <?php endforeach;?>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=khauhao&action=index" class="btn btn-secondary">Trở về</a>
                    <button type="submit" class="btn btn-primary" name="btnThem">Lưu</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
