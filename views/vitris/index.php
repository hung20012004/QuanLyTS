<!-- views/vitris/index.php -->
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=vitri&action=index">Vị Trí</a></li>
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
                    }, 150); // Optional: wait for the fade-out transition to complete
                }
            }, 2000); // 2000 milliseconds = 2 seconds
        </script>
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản Lý Vị Trí</h5>
                <div>
                    <a href="index.php?model=vitri&action=create" class="btn btn-primary">Thêm Mới</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Vị Trí</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($viTris as $viTri): ?>
                            <?php if ($viTri['vi_tri_id'] != 0): ?>
                                <tr>
                                    <td class="text-center"><?= $viTri['vi_tri_id'] ?></td>
                                    <td><?= htmlspecialchars($viTri['ten_vi_tri']) ?></td>
                                    <td class="d-flex justify-content-center">
                                        <a href="index.php?model=vitri&action=show&id=<?= $viTri['vi_tri_id'] ?>"
                                            class="btn btn-info btn-sm mx-2">Xem</a>
                                        <a href="index.php?model=vitri&action=edit&id=<?= $viTri['vi_tri_id'] ?>"
                                            class="btn btn-warning btn-sm mx-2">Sửa</a>
                                        <form action="index.php?model=vitri&action=delete&id=<?= $viTri['vi_tri_id'] ?>"
                                            method="POST" style="display: inline-block;"
                                            onsubmit="return confirmDelete();">
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
    document.addEventListener('DOMContentLoaded', function () {
        window.confirmDelete = function() {
            return confirm('Bạn có chắc muốn xóa vị trí này?');
        };
    });
</script>
