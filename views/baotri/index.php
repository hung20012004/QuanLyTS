<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=baotri&action=index">Lịch Bảo Trì</a></li>
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
                <h5 class="card-title mb-0">Quản Lý Lịch Bảo Trì</h5>
                <div>
                    <a href="index.php?model=baotri&action=create" class="btn btn-primary">Thêm mới</a>
                    <a href="index.php?model=baotri&action=export" class="btn btn-success">Xuất Excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Vị Trí ID</th>
                            <th>Ngày Bắt Đầu</th>
                            <th>Ngày Kết Thúc</th>
                            <th>Mô Tả</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td class="text-center"><?= $schedule['schedule_id'] ?></td>
                                <td><?= htmlspecialchars($schedule['ten_vi_tri']) ?></td>
                                <td><?= htmlspecialchars($schedule['ngay_bat_dau']) ?></td>
                                <td><?= htmlspecialchars($schedule['ngay_ket_thuc']) ?></td>
                                <td><?= htmlspecialchars($schedule['mo_ta']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=baotri&action=edit&id=<?= $schedule['schedule_id'] ?>" class="btn btn-warning btn-sm mx-2">Sửa</a>
                                    <form action="index.php?model=baotri&action=delete&id=<?= $schedule['schedule_id'] ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
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
