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
                    <a href="index.php?model=user&action=create" class="btn btn-primary">Thêm Mới</a>
                    <a href="index.php?model=user&action=export" class="btn btn-success">Xuất Excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="form-inline mb-3 justify-content-end">
                <div class="form-group mb-2 ">
                    <label for="emailSearch" class="sr-only">Email:</label>
                    <input type="text" id="emailSearch" class="form-control" placeholder="Tìm theo email">
                </div>
                <div class="form-group mx-1 mb-2">
                    <label for="tenSearch" class="sr-only">Tên:</label>
                    <input type="text" id="tenSearch" class="form-control" placeholder="Tìm theo tên">
                </div>
                <div class="form-group accordion mb-2">
                    <label for="vaitroSearch" class="sr-only">Vai trò:</label>
                    <input type="text" id="vaitroSearch" class="form-control" placeholder="Tìm theo vai trò">
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
                            <tr>
                                <td class="text-center"><?= $user['user_id'] ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['ten']) ?></td>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=user&action=show&id=<?= $user['user_id'] ?>"
                                        class="btn btn-info btn-sm mx-2">Xem</a>
                                    <?php if ($user['role'] !== 'Admin'): ?>
                                        <a href="index.php?model=user&action=edit&id=<?= $user['user_id'] ?>"
                                            class="btn btn-warning btn-sm mx-2">Sửa</a>
                                        <form action="index.php?model=user&action=delete&id=<?= $user['user_id'] ?>"
                                            method="POST" style="display: inline-block;"
                                            onsubmit="return confirmDelete();">
                                            <button type="submit" class="btn btn-danger btn-sm mx-2">Xóa</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
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
            var roleFilter = document.getElementById('vaitroSearch').value.toLowerCase();

            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');
            
            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var email = cells[1].textContent.toLowerCase();
                var name = cells[2].textContent.toLowerCase();
                var role = cells[3].textContent.toLowerCase();
                
                if (name.includes(nameFilter) && email.includes(emailFilter) && role.includes(roleFilter)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        document.getElementById('tenSearch').addEventListener('keyup', filterTable);
        document.getElementById('emailSearch').addEventListener('keyup', filterTable);
        document.getElementById('vaitroSearch').addEventListener('keyup', filterTable);
    });
    
    function confirmDelete() {
        return confirm('Bạn có chắc muốn xóa người dùng này?');
    }
</script>
