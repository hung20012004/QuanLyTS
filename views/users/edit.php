<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=user&action=index">Người Dùng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa Người Dùng</li>
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
                        <h5 class="card-title mb-0">Sửa Người Dùng</h5>
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

                    <form action="index.php?model=user&action=update&id=<?echo $user['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label for="ten" class="form-label">Tên:</label>
                            <input type="text" name="ten" id="ten" class="form-control" value="<?= htmlspecialchars($user['ten']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật Khẩu:</label>
                            <input type="password" name="password" id="password" class="form-control">
                            <small class="text-muted">Để trống nếu bạn không muốn thay đổi mật khẩu.</small>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai Trò:</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="Asset Manager" <?= ($user['role'] === 'Asset Manager') ? 'selected' : '' ?>>Quản Lý Tài Sản</option>
                                <option value="Admin" <?= ($user['role'] === 'Admin') ? 'selected' : '' ?>>Quản Trị</option>
                                <option value="Technician" <?= ($user['role'] === 'Technician') ? 'selected' : '' ?>>Kỹ Thuật Viên</option>
                            </select>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=user&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
