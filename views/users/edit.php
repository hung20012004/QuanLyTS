<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=user&action=index">Quản lý tài khoản người dùng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa thông tin người dùng</li>
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
                        <h5 class="card-title mb-0">Sửa thông tin người dùng</h5>
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
                        <div class="mb-3">
                            <label for="tenEmail" class="form-label">Tên và Email:</label>
                            <div class="input-group">
                                <input type="text" name="ten" id="ten" class="form-control" placeholder="Tên" value="<?php echo htmlspecialchars($user['ten']); ?>" required>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu:</label>
                            <div class="input-group">
                                <input type="password" id="password" class="form-control" value="********" readonly>
                                <input type="password" id="old_password" name="old_password" class="form-control" value="<?php echo $user['password']?>" hidden>
                                <button type="button" class="btn btn-warning" onclick="resetPassword()">
                                    <i class="fa-solid fa-arrows-rotate px-1"></i>Reset
                                </button>
                                <input type="hidden" name="password" id="password_hidden">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò:</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="NhanVien" <?php echo $user['role'] === 'NhanVien' ? 'selected' : ''; ?>>Cán bộ nhân viên nhà trường</option>
                                <option value="KyThuat" <?php echo $user['role'] === 'KyThuat' ? 'selected' : ''; ?>>Kỹ thuật viên</option>
                                <option value="NhanVienQuanLy" <?php echo $user['role'] === 'NhanVienQuanLy' ? 'selected' : ''; ?>>Nhân viên quản lý tài sản</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="khoa" class="form-label">Khoa:</label>
                            <select name="khoa" id="khoa" class="form-control" required>
                                <option value="HTTT" <?php echo $user['khoa'] === 'HTTT' ? 'selected' : ''; ?>>Hệ thống thông tin</option>
                                <option value="CNTT" <?php echo $user['khoa'] === 'CNTT' ? 'selected' : ''; ?>>Công nghệ thông tin</option>
                                <option value="KT" <?php echo $user['khoa'] === 'KT' ? 'selected' : ''; ?>>Kinh tế</option>
                                <option value="Co khi" <?php echo $user['khoa'] === 'Co khi' ? 'selected' : ''; ?>>Cơ khí</option>
                                <option value="Cong trinh" <?php echo $user['khoa'] === 'Cong trinh' ? 'selected' : ''; ?>>Công trình</option>
                                <option value="Moi truong-ATGT" <?php echo $user['khoa'] === 'Moi truong-ATGT' ? 'selected' : ''; ?>>Môi trường - An toàn giao thông</option>
                            </select>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=user&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
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

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const roleSelect = document.getElementById('role');

    form.addEventListener('submit', function(event) {
        if (roleSelect.value === '') {
            event.preventDefault();
            alert('Vui lòng chọn vai trò cho người dùng.');
        }
    });
});
</script>
