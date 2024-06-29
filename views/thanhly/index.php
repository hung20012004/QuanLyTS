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
                <h5 class="card-title mb-0">Quản Lý Hóa Đơn</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary" type="button">Tìm kiếm</a>
                    <a href="index.php?model=thanhly&action=viewcreate" class="btn btn-primary">Thêm Mới</a>
                    <a href="index.php?model=thanhly&action=export" class="btn btn-success">Xuất Excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayThanhLy" class="mr-2 mb-0" style="white-space: nowrap;">Ngày thanh lý:</label>
                            <input type="text" id="ngayThanhLy" class="form-control" placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="giaTriMinSearch" class="mr-2 mb-0" style="white-space: nowrap;">Giá trị từ:</label>
                            <input type="number" id="giaTriMinSearch" class="form-control" placeholder="Giá trị thấp nhất">
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Ngày thanh lý</th>
                            <th>Tổng cộng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($hoa_don_thanh_lys as $hoadon): ?>
                        <tr>
                            <td class="text-center"><?= $hoadon['hoa_don_id'] ?></td>
                            <td class="text-center"><?= htmlspecialchars(date('d/m/Y', strtotime($hoadon['ngay_thanh_ly']))) ?></td>
                            <td class="text-center"><?= number_format($hoadon['tong_gia_tri'], 0, ',', '.') ?></td>
                            <td class="d-flex justify-content-center">
                                <a href="index.php?model=thanhly&action=show&id=<?= $hoadon['hoa_don_id'] ?>"
                                   class="btn btn-info btn-sm mx-2">Xem</a>
                                <a href="index.php?model=thanhly&action=viewedit&id=<?= $hoadon['hoa_don_id'] ?>"
                                   class="btn btn-warning btn-sm mx-2">Sửa</a>
                                <form action="index.php?model=thanhly&action=delete&id=<?= $hoadon['hoa_don_id'] ?>"
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
    document.addEventListener('DOMContentLoaded', function () {
        function formatDate(date) {
            var d = new Date(date);
            var day = d.getDate();
            var month = d.getMonth() + 1;
            var year = d.getFullYear();
            return day + '/' + month + '/' + year;
        }

        function filterTable() {
            var ngayThanhLyFilter = document.getElementById('ngayThanhLy').value;
            var giaTriMinFilter = parseFloat(document.getElementById('giaTriMinSearch').value) || 0;

            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var ngayThanhLy = cells[1].textContent.trim(); // Lấy giá trị ngày tháng từ cột thứ 2
                var giaTriText = cells[2].textContent.trim(); // Lấy giá trị số tiền từ cột thứ 3
                var giaTri = parseFloat(giaTriText.replace(/\./g, '')); // Chuyển đổi giá trị số tiền thành số, loại bỏ dấu chấm

                // Kiểm tra điều kiện lọc
                var passNgayThanhLy = true; // Mặc định cho phép đi qua nếu không có điều kiện lọc ngày
                if (ngayThanhLyFilter) {
                    var formattedNgayThanhLy = formatDate(ngayThanhLy); // Chuyển đổi định dạng ngày tháng
                    passNgayThanhLy = formattedNgayThanhLy === ngayThanhLyFilter;
                }

                var passGiaTri = giaTri >= giaTriMinFilter;

                if (passNgayThanhLy && passGiaTri) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        document.getElementById('ngayThanhLy').addEventListener('input', filterTable);
        document.getElementById('giaTriMinSearch').addEventListener('input', filterTable);

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

    function confirmDelete() {
        return confirm('Bạn có chắc muốn xóa hóa đơn này?');
    }
</script>