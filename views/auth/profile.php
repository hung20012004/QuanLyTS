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

                    <form id="editForm" action="index.php?model=auth&action=edit&id=<?= $user['user_id']; ?>"
                        method="POST">
                        <div class="mb-3 row">
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="rounded-circle"
                                        alt="Avatar" style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                                <label for="avatar" class="form-label mt-2">Hình ảnh đại diện:</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" disabled>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="ten" class="form-label">Tên:</label>
                                    <input type="text" name="ten" id="ten" class="form-control"
                                        value="<?= htmlspecialchars($user['ten']); ?>" required disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="<?= htmlspecialchars($user['email']); ?>" required disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật Khẩu Mới:</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control"
                                        disabled>
                                    <small class="text-muted">Nhập lại mật khẩu nếu bạn muốn thay đổi.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác Nhận Mật Khẩu:</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Vai Trò:</label>
                                    <select name="role" id="role" class="form-control" required disabled>
                                        <option value="NhanVien" <?= ($user['role'] === 'NhanVien') ? 'selected' : ''; ?>>
                                            Nhân viên quản lý tài sản</option>
                                        <option value="Admin" <?= ($user['role'] === 'Admin') ? 'selected' : ''; ?>>Quản
                                            trị</option>
                                        <option value="KyThuat" <?= ($user['role'] === 'KyThuat') ? 'selected' : ''; ?>>Kỹ
                                            thuật viên</option>
                                        <option value="KeToan" <?= ($user['role'] === 'KeToan') ? 'selected' : ''; ?>>Kế
                                            toán</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=user&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="submit" class="btn btn-primary d-none" id="saveBtn">Lưu Thay Đổi</button>
                    <button type="button" style="display: none;" class="btn btn-secondary d-none"
                        id="cancelBtn">Hủy</button>
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
        const saveBtn = document.getElementById('saveBtn');
        const editForm = document.getElementById('editForm');
        const inputs = editForm.querySelectorAll('input, select');

        editBtn.addEventListener('click', function () {
            inputs.forEach(input => {
                input.disabled = !input.disabled;
            });

            saveBtn.classList.toggle('d-none');
            cancelBtn.classList.toggle('d-none');

            if (editBtn.textContent === 'Sửa Thông Tin') {
                editBtn.textContent = 'Hủy';
            } else {
                editBtn.textContent = 'Sửa Thông Tin';
            }
        });

        cancelBtn.addEventListener('click', function () {
            inputs.forEach(input => {
                input.disabled = true;
            });

            saveBtn.classList.add('d-none');
            cancelBtn.classList.add('d-none');
            editBtn.textContent = 'Sửa Thông Tin';
        });

        editForm.addEventListener('submit', function () {
            // Validate password if new password is entered
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
        });
    });
</script>