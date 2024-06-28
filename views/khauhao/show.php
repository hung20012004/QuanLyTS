<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=khauhao&action=index">Khấu hao</a></li>
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
                <h5 class="card-title mb-0">Chi tiết khấu hao</h5>
                <div>
                    <!-- <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a> -->
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <a href="index.php?model=khauhao&action=viewcreatekh&id=<?= $ts ?>" class="btn btn-primary">Thêm mới</a>
                    <a href="index.php?model=hoadonmua&action=export" class="btn btn-success">Xuất excel</a>
               </div>
            </div>
            
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayKhauhao" class="mr-2 mb-0" style="white-space: nowrap;">Ngày khấu hao:</label>
                            <input type="date" id="ngayKhauhao" class="form-control" placeholder="Ngày khấu hao">
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
                            <th>Tên Tài Sản</th>
                            <th>Ngày Khấu Hao</th>
                            <th>Số Tiền</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ((is_array($KhauHaos))) {
                         foreach ($KhauHaos as $kh): ?>
                            <tr>
                                <td class="text-center"><?= $kh['khau_hao_id'] ?></td>
                                <td class="text-center"><?= htmlspecialchars($kh['ten_tai_san']) ?></td>
                                <td class="text-center"> <?= htmlspecialchars(date('d-m-Y', strtotime($kh['ngay_khau_hao']))) ?></td>
                                <td class="text-center"><?= number_format($kh['so_tien'], 0, ',', '.') ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=khauhao&action=edit&id=<?= $kh['khau_hao_id'] ?>"
                                        class="btn btn-info btn-sm mx-2">Sửa</a>
                                  <form action="index.php?model=thanhly&action=delete&id=<?= $kh['khau_hao_id'] ?>"
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirmDelete();">
                                    <button type="submit" class="btn btn-danger btn-sm mx-2">Xóa</button>
                                     </form>
                                </td>
                            </tr>
                        <?php endforeach; }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
     document.addEventListener('DOMContentLoaded', function () {
        function filterTable() {
            var ngayKhauhaoFilter = document.getElementById('ngayKhauhao').value;
            var giaTriMinFilter = parseFloat(document.getElementById('giaTriMinSearch').value) || 0;

            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var ngayKhauhao = cells[2].textContent.trim();
                var giaTriText = cells[3].textContent.trim(); // Lấy giá trị số tiền từ cells[3]
                var giaTri = parseFloat(giaTriText.replace(/\./g, '')); // Chuyển đổi giá trị số tiền thành số, loại bỏ dấu chấm

                // Kiểm tra điều kiện lọc
                var passNgayThanhLy = !ngayKhauhaoFilter || ngayKhauhao.includes(ngayKhauhaoFilter);
                var passGiaTri = (giaTri > giaTriMinFilter , giaTri= giaTriMinFilter);

                if (passNgayThanhLy && passGiaTri) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        document.getElementById('ngayKhauhao').addEventListener('input', filterTable);
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
        return confirm('Bạn có chắc muốn xóa?');
    }
</script>