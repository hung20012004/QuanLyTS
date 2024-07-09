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
        <div id="alert-message" class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show"
            role="alert">
            <?= $_SESSION['message']; ?>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
        <script>
            setTimeout(function () {
                var alert = document.getElementById('alert-message');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(function () {
                        alert.style.display = 'none';
                    }, 150);
                }
            }, 2000); // 2000 milliseconds = 2 seconds
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản Lý Tài Sản</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3 " style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="tenTaiSan" class="mr-2 mb-0" style="white-space: nowrap;">Tên tài sản:</label>
                            <input type="text" id="tenTaiSan" class="form-control" placeholder="Nhập tên tài sản">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="loaiTaiSan" class="mr-2 mb-0" style="white-space: nowrap;">Loại tài sản:</label>
                            <select id="loaiTaiSan" class="form-control">
                                <option value="">Chọn loại tài sản</option>
                                <?php foreach ($loaiTaiSans as $loaiTaiSan): ?>
                                    <option value="<?= htmlspecialchars($loaiTaiSan['ten_loai_tai_san']); ?>">
                                        <?= htmlspecialchars($loaiTaiSan['ten_loai_tai_san']); ?></option>
                                <?php endforeach; ?>
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
                            <th>Tên tài sản</th>
                            <th>Mô tả</th>
                            <th>Loại tài sản</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($taiSans as $taisan): ?> 
                            <tr>
                                <td class="text-center"><?php echo $taisan['tai_san_id']; ?></td>
                                <td class="text-center"><?= htmlspecialchars($taisan['ten_tai_san']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($taisan['mo_ta']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($taisan['ten_loai_tai_san']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=taisan&action=detail&id=<?php echo $taisan['tai_san_id']; ?>"
                                        class="btn btn-info btn-sm mx-2">Chi tiết</a>
                                    <a href="index.php?model=taisan&action=edit&id=<?php echo $taisan['tai_san_id']; ?>"
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var table = $('#dataTable').DataTable({
            dom: 'rtip',
            language: {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
            }
        });

        // Thêm sự kiện tìm kiếm cho input
        $('#tenTaiSan').on('input', function(){
            table.column(1).search(this.value).draw();
        });
        $('#loaiTaiSan').on('change', function(){
            table.column(3).search(this.value).draw();
        });
        var toggleButton = document.getElementById('toggleSearch');
        var searchForm = document.getElementById('searchForm');

        toggleButton.addEventListener('click', function () {
            if (searchForm.style.display === 'none') {
                searchForm.style.display = 'block';
                toggleButton.textContent = 'Ẩn tìm kiếm';
            } else {
                searchForm.style.display = 'none';
                toggleButton.textContent = 'Tìm kiếm';
            }
        });
    });

    function showDetail(taiSanId) {
        var detailRow = document.getElementById('detail-' + taiSanId);
        if (detailRow.style.display === 'none') {
            fetch('index.php?model=taisan&action=detail&id=' + taiSanId)
                .then(response => response.text())
                .then(data => {
                    detailRow.querySelector('.detail-content').innerHTML = data;
                    detailRow.style.display = 'table-row';
                });
        } else {
            detailRow.style.display = 'none';
        }
    }

    function confirmDelete(id) {
        if (confirm('Bạn có chắc muốn xóa tài sản này?')) {
            fetch('index.php?model=taisan&action=delete&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa tài sản thành công!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa tài sản.');
                });
        }
        return false;
    }
</script>
