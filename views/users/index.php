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
                                    <a href="index.php?model=user&action=edit&id=<?= $user['user_id'] ?>"
                                        class="btn btn-warning btn-sm mx-2">Sửa</a>
                                    <?php if ($user['role'] !== 'Admin'): ?>
                                        <form action="index.php?model=user&action=delete&id=<?= $user['user_id'] ?>"
                                            method="POST" style="display: inline-block;" onsubmit="return confirmDelete();">
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