<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=user&action=index">Quản lý tài khoản người dùng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo tài khoản</li>
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
                        <h5 class="card-title mb-0">Tạo tài khoản</h5>
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
                            <label for="tenEmail" class="form-label">Tên và Email:</label>
                            <div class="input-group">
                                <input type="text" name="ten" id="ten" class="form-control" placeholder="Tên" value="<?php echo htmlspecialchars($ten); ?>" required>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu:</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" value="Utt@1234" disabled required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò:</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="NhanVien" <?php echo $role === 'NhanVien' ? 'selected' : ''; ?>>Cán bộ nhân viên nhà trường</option>
                                <option value="KyThuat" <?php echo $role === 'KyThuat' ? 'selected' : ''; ?>>Kỹ thuật viên</option>
                                <option value="NhanVienQuanLy" <?php echo $role === 'NhanVienQuanLy' ? 'selected' : ''; ?>>Nhân viên quản lý tài sản</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="khoa" class="form-label">Khoa:</label>
                            <select name="khoa" id="khoa" class="form-control" required>
                                <option value="HTTT" >Hệ thống thông tin</option>
                                <option value="CNTT" >Công nghệ thông tin</option>
                                <option value="KT" >Kinh tế</option>
                                <option value="Co khi" >Cơ khí</option>
                                <option value="Cong trinh" >Công trình</option>
                                <option value="Moi truong-ATGT" >Môi trường - An toàn giao thông</option>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
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