<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=taisan&action=index">Tài Sản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa Tài Sản</li>
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
                        <h5 class="card-title mb-0">Sửa Tài Sản</h5>
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
                    <form action="index.php?model=taisan&action=edit&id=<?php echo $taiSan['tai_san_id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="ten_tai_san" class="form-label">Tên Tài Sản:</label>
                            <input type="text" name="ten_tai_san" id="ten_tai_san" class="form-control" value="<?= htmlspecialchars($taiSan['ten_tai_san']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô Tả:</label>
                            <textarea name="mo_ta" id="mo_ta" class="form-control" rows="3"><?= htmlspecialchars($taiSan['mo_ta']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="loai_tai_san_id" class="form-label">Loại Tài Sản:</label>
                            <select name="loai_tai_san_id" id="loai_tai_san_id" class="form-control" required>
                                <?php foreach ($loaiTaiSans as $loaiTaiSan): ?>
                                    <option value="<?= $loaiTaiSan['loai_tai_san_id'] ?>" <?= ($taiSan['loai_tai_san_id'] == $loaiTaiSan['loai_tai_san_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loaiTaiSan['ten_loai_tai_san']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>    
                     
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=taisan&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                </div>
                </form>  
            </div>
        </div>
    </div>
</div>
