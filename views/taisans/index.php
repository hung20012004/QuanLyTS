<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=taisan&action=index">Tài Sản</a></li>
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
                <h5 class="card-title mb-0">Quản Lý Tài Sản</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Tên Tài Sản</th>
                            <th>Mô Tả</th>
                            <th>Số Lượng</th>
                            <th>Loại Tài Sản ID</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($taiSans as $taiSan): ?>
                            <tr>
                                <td class="text-center"><?= $taiSan['tai_san_id'] ?></td>
                                <td><?= htmlspecialchars($taiSan['ten_tai_san']) ?></td>
                                <td><?= htmlspecialchars($taiSan['mo_ta']) ?></td>
                                <td><?= $taiSan['so_luong'] ?></td>
                                <td><?= htmlspecialchars($taiSan['ten_loai_tai_san']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=taisan&action=show&id=<?= $taiSan['tai_san_id'] ?>"
                                        class="btn btn-info btn-sm mx-2">Xem</a>
                                    <a href="index.php?model=taisan&action=edit&id=<?= $taiSan['tai_san_id'] ?>"
                                        class="btn btn-warning btn-sm mx-2">Sửa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
