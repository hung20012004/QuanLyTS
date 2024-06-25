<div class="container">
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
                        <button id="editBtn" class="btn btn-info">Sửa Thông Tin</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="editForm" action="index.php?model=user&action=edit&id=<?= $user['user_id']; ?>" method="POST">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="ten" class="form-label">Tên:</label>
                                <input type="text" name="ten" id="ten" class="form-control" value="<?= htmlspecialchars($user['ten']); ?>" required disabled>
                            </div>
                            <div class="col">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật Khẩu Mới:</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" disabled>
                            <small class="text-muted">Nhập lại mật khẩu nếu bạn muốn thay đổi.</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác Nhận Mật Khẩu:</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai Trò:</label>
                            <select name="role" id="role" class="form-control" required disabled>
                                <option value="NhanVien" <?= ($user['role'] === 'NhanVien') ? 'selected' : ''; ?>>Nhân viên quản lý tài sản</option>
                                <option value="Admin" <?= ($user['role'] === 'Admin') ? 'selected' : ''; ?>>Quản trị</option>
                                <option value="KyThuat" <?= ($user['role'] === 'KyThuat') ? 'selected' : ''; ?>>Kỹ thuật viên</option>
                                <option value="KeToan" <?= ($user['role'] === 'KeToan') ? 'selected' : ''; ?>>Kế toán</option>
                            </select>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=user&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="submit" class="btn btn-primary" disabled>Lưu Thay Đổi</button>
                    <button id="cancelBtn" class="btn btn-secondary d-none">Hủy</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editBtn = document.getElementById('editBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const editForm = document.getElementById('editForm');
        const inputs = editForm.querySelectorAll('input, select');

        editBtn.addEventListener('click', function () {
            inputs.forEach(input => {
                input.disabled = !input.disabled;
            });

            const saveBtn = editForm.querySelector('button[type="submit"]');
            saveBtn.disabled = !saveBtn.disabled;

            if (editBtn.textContent === 'Sửa Thông Tin') {
                editBtn.textContent = 'Hủy';
                cancelBtn.classList.remove('d-none');
            } else {
                editBtn.textContent = 'Sửa Thông Tin';
                cancelBtn.classList.add('d-none');
            }
        });

        cancelBtn.addEventListener('click', function () {
            inputs.forEach(input => {
                input.disabled = true;
            });

            saveBtn.disabled = true;

            editBtn.textContent = 'Sửa Thông Tin';
            cancelBtn.classList.add('d-none');
        });
    });
</script>
