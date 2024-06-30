<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=hoadonmua&action=index">Hóa Đơn</a></li>
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
            }, 7000); // 2000 milliseconds = 2 seconds
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản Lý Hóa Đơn</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <a href="index.php?model=hoadonmua&action=create" class="btn btn-primary">Thêm mới</a>
                    <a href="index.php?model=hoadonmua&action=export" class="btn btn-success">Xuất excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3 " style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayBatDau" class="mr-2 mb-0" style="white-space: nowrap;">Từ
                                ngày:&nbsp&nbsp&nbsp </label>
                            <input type="date" id="ngayBatDau" class="form-control" placeholder="Từ ngày">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="giaTriMinSearch" class="mr-2 mb-0" style="white-space: nowrap;">Giá trị
                                từ:&nbsp&nbsp&nbsp</label>
                            <input type="number" id="giaTriMinSearch" class="form-control"
                                placeholder="Giá trị thấp nhất">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="nhaCungCapSearch" class="mr-2 mb-0" style="white-space: nowrap;">Nhà cung
                                cấp:</label>
                            <select id="nhaCungCapSearch" class="form-control">
                                <option value="">Chọn nhà cung cấp</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <?php if ($supplier['trang_thai'] != 0): ?>
                                    <option value="<?= htmlspecialchars($supplier['ten_nha_cung_cap']); ?>">
                                        <?= htmlspecialchars($supplier['ten_nha_cung_cap']); ?></option>
                                    <?php endif;?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayKetThuc" class="mr-2 mb-0" style="white-space: nowrap;">Đến ngày:</label>
                            <input type="date" id="ngayKetThuc" class="form-control" placeholder="Đến ngày">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="giaTriMaxSearch" class="mr-2 mb-0" style="white-space: nowrap;">Giá trị
                                đến:</label>
                            <input type="number" id="giaTriMaxSearch" class="form-control"
                                placeholder="Giá trị cao nhất">
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Ngày mua</th>
                            <th>Tổng giá trị(VNĐ)</th>
                            <th>Nhà cung cấp</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?> 
                            <tr>
                                <td class="text-center"><?php echo $invoice['hoa_don_id']; ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($invoice['ngay_mua'])) ?></td>
                                <td class="text-right"><?= number_format($invoice['tong_gia_tri'], 0, ',', '.') ?></td>
                                <td class="text-center"><?= htmlspecialchars($invoice['ten_nha_cung_cap']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=hoadonmua&action=show&id=<?php echo $invoice['hoa_don_id']; ?>"
                                        class="btn btn-info btn-sm mx-2">Xem</a>
                                    <a href="index.php?model=hoadonmua&action=edit&id=<?php echo $invoice['hoa_don_id']; ?>"
                                        class="btn btn-warning btn-sm mx-2">Sửa</a>
                                    <a href="index.php?model=hoadonmua&action=delete&id=<?php echo $invoice['hoa_don_id']; ?>"
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
    $(document).ready(function() {
        var table=$('#dataTable').DataTable({
            dom: 'rtip',
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
                    }
            });
  });
    document.addEventListener('DOMContentLoaded', function () {
        function filterTable() {
            var ngayBatDau = document.getElementById('ngayBatDau').value;
            var ngayKetThuc = document.getElementById('ngayKetThuc').value;
            var giaTriMinFilter = parseFloat(document.getElementById('giaTriMinSearch').value) || 0;
            var giaTriMaxFilter = parseFloat(document.getElementById('giaTriMaxSearch').value) || Infinity;
            var nhaCungCapFilter = document.getElementById('nhaCungCapSearch').value.toLowerCase();

            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var ngayMua = cells[1].textContent.trim();
                var giaTri = parseFloat(cells[2].textContent.trim().replace(/\./g, '').replace(',', '.'));
                var nhaCungCap = cells[3].textContent.trim().toLowerCase();

                // Kiểm tra điều kiện lọc
                var passNgay = (!ngayBatDau || ngayMua >= ngayBatDau) && (!ngayKetThuc || ngayMua <= ngayKetThuc);
                var passGiaTri = giaTri >= giaTriMinFilter && giaTri <= giaTriMaxFilter;
                var passNhaCungCap = nhaCungCapFilter === '' || nhaCungCap.includes(nhaCungCapFilter);

                if (passNgay && passGiaTri && passNhaCungCap) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        document.getElementById('ngayBatDau').addEventListener('change', filterTable);
        document.getElementById('ngayKetThuc').addEventListener('change', filterTable);
        document.getElementById('giaTriMinSearch').addEventListener('input', filterTable);
        document.getElementById('giaTriMaxSearch').addEventListener('input', filterTable);
        document.getElementById('nhaCungCapSearch').addEventListener('change', filterTable);

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
        if (confirm('Bạn có chắc muốn xóa hóa đơn này?')) {
            fetch('index.php?model=hoadonmua&action=delete&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa hóa đơn thành công!');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa hóa đơn.');
                });
        }
        return false;
    }
</script>