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

                    <form action="index.php?model=user&action=edit&id=<?php echo $user['user_id']; ?>" method="POST">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="ten" class="form-label">Tên:</label>
                                <input type="text" name="ten" id="ten" class="form-control" value="<?= htmlspecialchars($user['ten']) ?>" required>
                            </div>
                            <div class="col">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật Khẩu:</label>
                            <div class="input-group">
                                <input type="password" id="password" class="form-control" value="********" readonly>
                                <button type="button" class="btn btn-warning" onclick="resetPassword()"><i class="fa-solid fa-arrows-rotate px-1"></i>Reset</button>
                                <input type="hidden" name="password" id="password_hidden">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai Trò:</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="NhanVien" <?= ($user['role'] === 'NhanVien') ? 'selected' : '' ?>>Nhân Viên Quản Lý Tài Sản</option>
                                <option value="KyThuat" <?= ($user['role'] === 'KyThuat') ? 'selected' : '' ?>>Kỹ Thuật Viên</option>
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

<script>
function resetPassword() {
    document.getElementById('password_hidden').value = 'Utt@1234';
    alert('Mật khẩu đã được reset thành Utt@1234');
}
</script>
