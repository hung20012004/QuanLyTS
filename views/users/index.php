<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=user&action=index">Người Dùng</a></li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
<?php if (isset($_SESSION['message'])): ?>
    <div id="alert-message" class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?>
    </div>
    <?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
    <script>
        setTimeout(function() {
            var alert = document.getElementById('alert-message');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 150); 
            }
        }, 2000); // 2000 milliseconds = 2 seconds
    </script>
<?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản Lý Người Dùng</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <a href="index.php?model=user&action=create" class="btn btn-primary">Thêm Mới</a>
                    <a href="index.php?model=user&action=export" class="btn btn-success">Xuất Excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3" style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="emailSearch" class="mr-2 mb-0" style="white-space: nowrap;">Email:&nbsp;&nbsp;&nbsp;</label>
                            <input type="text" id="emailSearch" class="form-control" placeholder="Tìm theo email">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="tenSearch" class="mr-2 mb-0" style="white-space: nowrap;">Tên:&nbsp;&nbsp;&nbsp;</label>
                            <input type="text" id="tenSearch" class="form-control" placeholder="Tìm theo tên">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="vaitroSearch" class="mr-2 mb-0" style="white-space: nowrap;">Vai trò:&nbsp;&nbsp;&nbsp;</label>
                            <select id="vaitroSearch" class="form-control">
                                <option value="">Chọn vai trò</option>
                                <option value="NhanVien">Nhân viên</option>
                                <option value="KyThuat">Kỹ thuật viên</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Tên</th>
                            <th>Vai Trò</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php if ($user['role'] !== 'Admin'): ?>
                                <tr>
                                    <td class="text-center"><?= $user['user_id'] ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['ten']) ?></td>
                                    <td><?= $user['role'] === 'NhanVien' ? 'Nhân viên' : ($user['role'] === 'KyThuat' ? 'Kỹ thuật viên' : $user['role']) ?></td>
                                    <td class="d-flex justify-content-center">
                                        <a href="index.php?model=user&action=edit&id=<?= $user['user_id'] ?>" class="btn btn-warning btn-sm mx-2">Sửa</a>
                                        <form action="index.php?model=user&action=delete&id=<?= $user['user_id'] ?>" method="POST" style="display: inline-block;" onsubmit="return confirmDelete();">
                                            <button type="submit" class="btn btn-danger btn-sm mx-2">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function filterTable() {
        var nameFilter = document.getElementById('tenSearch').value.toLowerCase();
        var emailFilter = document.getElementById('emailSearch').value.toLowerCase();
        var roleFilter = document.getElementById('vaitroSearch').value;

        var table = document.getElementById('dataTable');
        var rows = table.getElementsByTagName('tr');

        for (var i = 1; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName('td');
            var email = cells[1].textContent.toLowerCase();
            var name = cells[2].textContent.toLowerCase();
            var role = cells[3].textContent;

            var roleMatch = roleFilter === '' || 
                            (roleFilter === 'NhanVien' && role === 'Nhân viên') || 
                            (roleFilter === 'KyThuat' && role === 'Kỹ thuật viên');

            if (name.includes(nameFilter) && email.includes(emailFilter) && roleMatch) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    document.getElementById('tenSearch').addEventListener('keyup', filterTable);
    document.getElementById('emailSearch').addEventListener('keyup', filterTable);
    document.getElementById('vaitroSearch').addEventListener('change', filterTable);

    var toggleButton = document.getElementById('toggleSearch');
    var searchForm = document.getElementById('searchForm');

    toggleButton.addEventListener('click', function() {
        if (searchForm.style.display === 'none') {
            searchForm.style.display = 'block';
            toggleButton.textContent = 'Ẩn tìm kiếm';
        } else {
            searchForm.style.display = 'none';
            toggleButton.textContent = 'Tìm kiếm';
        }
    });
});

function confirmDelete() {
    return confirm('Bạn có chắc muốn xóa người dùng này?');
}
</script>