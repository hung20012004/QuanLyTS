<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=nhacungcap&action=index">Nhà Cung Cấp</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo Nhà Cung Cấp</li>
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
                        <h5 class="card-title mb-0">Thêm nhà cung cấp</h5>
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
                    <form action="index.php?model=nhacungcap&action=create" method="POST">
                        <div class="mb-3">
                            <label for="ten" class="form-label">Tên nhà cung cấp</label>
                            <div class="input-group">
                                <input type="text" name="ten" id="ten" class="form-control" placeholder="Tên" required>
                            </div>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=nhacungcap&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Tạo</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
