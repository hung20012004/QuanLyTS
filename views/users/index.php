<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=user&action=index">Quản lý tài khoản người dùng</a></li>
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
            }, 2000);
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản lý tài khoản người dùng</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <a href="index.php?model=user&action=create" class="btn btn-primary">Thêm mới</a>
                    <!-- <a href="index.php?model=user&action=export" class="btn btn-success">Xuất Excel</a> -->
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
                            <select id="vaitroSearch" class="form-control" >
                                <option value="">Chọn vai trò</option>
                                <option value="NhanVien">Nhân viên</option>
                                <option value="KyThuat">Kỹ thuật viên</option>
                                <option value="NhanVienQuanLy">Nhân viên quản lý tài sản</option>
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
                            <?php if ($user['role'] !== 'Quanly'): ?>
                                <tr>
                                    <td class="text-center"><?= $user['user_id'] ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['ten']) ?></td>
                                    <td><?= $user['role'] === 'NhanVien' ? 'Nhân viên' : ($user['role'] === 'KyThuat' ? 'Kỹ thuật viên' : ($user['role'] === 'NhanVienQuanLy' ? 'Nhân viên quản lý tài sản' : $user['role'])) ?></td>
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
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        dom: 'rtip',
        language: {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
        }
    });

    function filterTable() {
        var nameFilter = $('#tenSearch').val().toLowerCase();
        var emailFilter = $('#emailSearch').val().toLowerCase();
        var roleFilter = $('#vaitroSearch').val();

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var email = data[1].toLowerCase();
            var name = data[2].toLowerCase();
            var role = data[3];

            var roleMatch = roleFilter === '' || 
                            (roleFilter === 'NhanVien' && role === 'Nhân viên') || 
                            (roleFilter === 'KyThuat' && role === 'Kỹ thuật viên');

            return name.includes(nameFilter) && email.includes(emailFilter) && roleMatch;
        });

        table.draw();

        $.fn.dataTable.ext.search.pop();
    }

    $('#tenSearch, #emailSearch').on('keyup', filterTable);
    $('#vaitroSearch').on('change', filterTable);

    $('#toggleSearch').on('click', function() {
        var searchForm = $('#searchForm');
        if (searchForm.is(':hidden')) {
            searchForm.show();
            $(this).text('Ẩn tìm kiếm');
        } else {
            searchForm.hide();
            $(this).text('Tìm kiếm');
        }
    });
});

function confirmDelete() {
    return confirm('Bạn có chắc muốn xóa người dùng này?');
}
</script>