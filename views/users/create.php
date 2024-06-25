<div class="container">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=user&action=index">Người Dùng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo Người Dùng</li>
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
                        <h5 class="card-title mb-0">Thêm người dùng</h5>
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
                    <form action="index.php?model=user&action=create" method="POST">
                        <div class="mb-3">
                            <label for="ten" class="form-label">Tên:</label>
                            <input type="text" name="ten" id="ten" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò:</label>
                            <select name="role" id="role" class="form-control" required>
                                <option >>--Chọn vai trò--<</option>
                                <option value="NhanVien">Nhân viên quản lý tài sản</option>
                                <option value="Admin">Quản trị</option>
                                <option value="KyThuat">Kỹ thuật viên</option>
                                <option value="KeToan">Kế toán</option>
                            </select>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=user&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Tạo</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>