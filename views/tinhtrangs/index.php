<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=tinhtrang&action=index">Tình Trạng</a></li>
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
                <h5 class="card-title mb-0">Quản Lý Tình Trạng</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <a href="index.php?model=tinhtrang&action=create" class="btn btn-primary">Thêm mới</a>
                    <a href="index.php?model=tinhtrang&action=export" class="btn btn-success">Xuất excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3 " style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayBatDau" class="mr-2 mb-0" style="white-space: nowrap;">Từ ngày:&nbsp&nbsp&nbsp </label>
                            <input type="date" id="ngayBatDau" class="form-control" placeholder="Từ ngày">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ViTriSearch" class="mr-2 mb-0" style="white-space: nowrap;">Vị trí:</label>
                            <select id="ViTriSearch" class="form-control">
                                <option value="">Chọn vị trí</option>
                                <?php foreach ($viTris as $viTri): ?>
                                    <option value="<?= htmlspecialchars($viTri['vi_tri_id']); ?>">
                                        <?= htmlspecialchars($viTri['ten_vi_tri']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="col-md-4 mb-2">
                        <button type="button" id="searchButton" class="btn btn-primary">Tìm</button>
                    </div> -->
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayKetThuc" class="mr-2 mb-0" style="white-space: nowrap;">Đến ngày:</label>
                            <input type="date" id="ngayKetThuc" class="form-control" placeholder="Đến ngày">
                        </div>
                    </div>
                </div>
                
            </form>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Vị trí bảo trì</th>
                            <th>Ngày bảo trì</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tinhTrangs as $tinhTrang): ?> 
                            <tr>
                                <td class="text-center"><?php echo $tinhTrang['tinh_trang_id']; ?></td>
                                <td class="text-center"><?= htmlspecialchars($tinhTrang['ten_vi_tri']) ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($tinhTrang['ngay_bat_dau'])) ?> - <?= date('d-m-Y', strtotime($tinhTrang['ngay_ket_thuc'])) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=tinhtrang&action=show&id=<?php echo $tinhTrang['tinh_trang_id']; ?>"
                                        class="btn btn-info btn-sm mx-2">Xem</a>
                                    <a href="index.php?model=tinhtrang&action=edit&id=<?php echo $tinhTrang['tinh_trang_id']; ?>"
                                        class="btn btn-warning btn-sm mx-2">Sửa</a>
                                    <a href="index.php?model=tinhtrang&action=delete&id=<?php echo $tinhTrang['tinh_trang_id']; ?>"
                                        class="btn btn-danger btn-sm mx-2" onclick="return confirmDelete(<?php echo $invoice['hoa_don_id']; ?>);">Xóa</a>
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
        function filterTable() {
            var ngayBatDau = document.getElementById('ngayBatDau').value;
            var ngayKetThuc = document.getElementById('ngayKetThuc').value;
            var viTriFilter = document.getElementById('ViTriSearch').value.toLowerCase();

            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var viTri = cells[0].textContent.trim().toLowerCase();
                var ngayMuaRange = cells[2].textContent.trim();
                
                //Tách ngày mua
                var [ngayMuaStart, ngayMuaEnd] = ngayMuaRange.split(' - ').map(dateStr => {
                    var [day, month, year] = dateStr.split('-').map(Number);
                    return new Date(year, month - 1, day);
                });

                // Chuyển đổi định dạng ngày bắt đầu và ngày kết thúc từ input
                var ngayBatDauDate = ngayBatDau ? new Date(ngayBatDau) : null;
                var ngayKetThucDate = ngayKetThuc ? new Date(ngayKetThuc) : null;
                // Kiểm tra điều kiện lọc
                var passNgay = (!ngayBatDauDate || (ngayMuaStart >= ngayBatDauDate )) && (!ngayKetThucDate || (ngayMuaEnd <= ngayKetThucDate));
                var passViTri = viTriFilter === '' || viTri.includes(viTriFilter);

                if (passNgay && passViTri) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
        // document.getElementById('searchButton').addEventListener('click', filterTable);

        document.getElementById('ngayBatDau').addEventListener('change', filterTable);
        document.getElementById('ngayKetThuc').addEventListener('change', filterTable);
        document.getElementById('ViTriSearch').addEventListener('change', filterTable);

        // Gọi filterTable ngay khi trang được tải để áp dụng bất kỳ giá trị mặc định nào
        filterTable();
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

    function confirmDelete(id) {
        if (confirm('Bạn có chắc muốn xóa tình trạng này?')) {
            fetch('index.php?model=tinhtrang&action=delete&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa tình trạng thành công!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa tình trạng.');
                });
        }
        return false;
    }
</script>