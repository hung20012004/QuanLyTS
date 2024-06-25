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
                <h5 class="card-title mb-0">Quản Lý Hóa Đơn</h5>
                <div>
                    <a href="index.php?model=hoadonmua&action=create" class="btn btn-primary">Thêm Mới</a>
                    <a href="index.php?model=hoadonmua&action=export" class="btn btn-success">Xuất Excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="form-inline mb-3 justify-content-end">
                <div class="form-group mb-2 ">
                    <label for="ngayMuaSearch" class="sr-only">Ngày Mua:</label>
                    <input type="date" id="ngayMuaSearch" class="form-control" placeholder="Tìm theo ngày mua">
                </div>
                <div class="form-group mx-1 mb-2">
                    <label for="giaTriSearch" class="sr-only">Giá Trị:</label>
                    <input type="number" id="giaTriSearch" class="form-control" placeholder="Tìm theo giá trị">
                </div>
            </form>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Ngày Mua</th>
                            <th>Tổng Giá Trị</th>
                            <th>Nhà Cung Cấp</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td class="text-center"><?= $invoice['hoa_don_mua_id'] ?></td>
                                <td><?= $invoice['ngay_mua'] ?></td>
                                <td><?= number_format($invoice['tong_gia_tri'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($invoice['ten_nha_cung_cap']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=hoadonmua&action=show&id=<?= $invoice['hoa_don_mua_id'] ?>"
                                        class="btn btn-info btn-sm mx-2">Xem</a>
                                    <a href="index.php?model=hoadonmua&action=edit&id=<?= $invoice['hoa_don_mua_id'] ?>"
                                        class="btn btn-warning btn-sm mx-2">Sửa</a>
                                    <form action="index.php?model=hoadonmua&action=delete&id=<?= $invoice['hoa_don_mua_id'] ?>"
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function filterTable() {
            var ngayMuaFilter = document.getElementById('ngayMuaSearch').value;
            var giaTriFilter = document.getElementById('giaTriSearch').value.trim();

            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');
            
            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var ngayMua = cells[1].textContent.trim();
                var giaTri = cells[2].textContent.trim().replace(/\./g, '').replace(',', '.');

                if (ngayMua.includes(ngayMuaFilter) && giaTri.includes(giaTriFilter)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        document.getElementById('ngayMuaSearch').addEventListener('change', filterTable);
        document.getElementById('giaTriSearch').addEventListener('keyup', filterTable);
    });
    
    function confirmDelete() {
        return confirm('Bạn có chắc muốn xóa hóa đơn này?');
    }
</script>
