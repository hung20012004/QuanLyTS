<div class="container">
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
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Quản Lý Người Dùng</h5>
                        <div>
                            <a href="index.php?model=user&action=create" class="btn btn-primary">Thêm Mới</a>
                            <a href="index.php?model=user&action=export" class="btn btn-success">Xuất Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?= $_SESSION['success'] ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <form action="index.php" method="GET" class="mb-3">
                        <input type="hidden" name="model" value="user">
                        <input type="hidden" name="action" value="index">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Tìm kiếm..."
                                aria-label="Tìm kiếm"
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            <div class="input-group-append">
                                <button class="btn btn-outline-success" type="submit"><i
                                        class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="users-table" class="table table-bordered mt-3">
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
                                            <a href="index.php?model=user&action=edit&id=<?= $user['user_id'] ?>"
                                                class="btn btn-warning btn-sm mx-2">Sửa</a>
                                            <form action="index.php?model=user&action=delete&id=<?= $user['user_id'] ?>"
                                                method="POST" style="display: inline-block;"
                                                onsubmit="return confirmDelete();">
                                                <button type="submit" class="btn btn-danger btn-sm mx-2">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#users-table').DataTable({
            dom: 'rtip',
            language: {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
            }
        });
    });

    function confirmDelete() {
        return confirm('Bạn có chắc muốn xóa người dùng này?');
    }
</script>