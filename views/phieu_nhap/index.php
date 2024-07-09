<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieunhap&action=index">Phiếu nhập</a></li>
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
            }, 7000); // 7000 milliseconds = 7 seconds
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản lý phiếu nhập</h5>
                <div>
                    <?php if ($_SESSION['role'] == 'NhanVienQuanLy'): ?>
                        <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                        <a href="index.php?model=phieunhap&action=create" class="btn btn-primary">Thêm mới</a>
                        <a href="index.php?model=phieunhap&action=export" class="btn btn-success">Xuất excel</a>
                    <?php elseif ($_SESSION['role'] == 'QuanLy'): ?>
                        
                        <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                        <a href="index.php?model=phieunhap&action=export" class="btn btn-success">Xuất excel</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3" style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayBatDau" class="mr-2 mb-0" style="white-space: nowrap;">Từ
                                ngày:&nbsp&nbsp&nbsp</label>
                            <input type="date" id="ngayBatDau" class="form-control" placeholder="Từ ngày">
                        </div>
                    </div>
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
                            <th>Mã số phiếu</th>
                            <th>Ngày tạo phiếu</th>
                            <th>Ngày phê duyệt</th>
                            <th>Ngày nhập tài sản</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phieuNhap as $phieu): ?>
                            <?php if (($phieu['user_id'] == $_SESSION['user_id']&&$_SESSION['role']=='NhanVienQuanLy')||($_SESSION['role']=='QuanLy')): ?>
                                <tr>
                                    <td class="text-center"><?php echo $phieu['phieu_nhap_tai_san_id']; ?></td>
                                    <td class="text-center"><?= date('d-m-Y', strtotime($phieu['ngay_tao'])) ?></td>
                                    <td class="text-center">
                                        <?php if (in_array($phieu['trang_thai'], ['DaPheDuyet', 'DaNhap','KhongDuyet'])): ?>
                                            <?= !empty($phieu['ngay_xac_nhan']) ? date('d-m-Y', strtotime($phieu['ngay_xac_nhan'])) : ''; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $phieu['trang_thai'] == 'DaNhap' ? (!empty($phieu['ngay_nhap']) ? date('d-m-Y', strtotime($phieu['ngay_nhap'])) : '') : ''; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $phieu['trang_thai'] == 'DangChoPheDuyet' ? 'Đang chờ phê duyệt' : ($phieu['trang_thai'] == 'KhongDuyet' ? 'Không phê duyệt' : ($phieu['trang_thai'] == 'DaNhap' ? 'Đã nhập tài sản' : 'Đã phê duyệt')); ?>
                                    </td>
                                    <td class="d-flex justify-content-center">
                                        <a href="index.php?model=phieunhap&action=show&id=<?php echo $phieu['phieu_nhap_tai_san_id']; ?>"
                                            class="btn btn-info btn-sm mx-2">Xem</a>
                                        <?php if ($phieu['trang_thai'] == 'DangChoPheDuyet'&&$_SESSION['role'] == 'NhanVienQuanLy'): ?>
                                            <a href="index.php?model=phieunhap&action=edit&id=<?php echo $phieu['phieu_nhap_tai_san_id']; ?>"
                                                class="btn btn-warning btn-sm mx-2">Sửa</a>
                                            <?php if ($_SESSION['role'] == 'NhanVienQuanLy'): ?>
                                                <a href="index.php?model=phieunhap&action=delete&id=<?= $phieu['phieu_nhap_tai_san_id']; ?>"
                                                    onclick="return confirmDelete();" class="btn btn-danger btn-sm mx-2">Xóa</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($phieu['trang_thai'] == 'DaPheDuyet'): ?>
                                            <?php if ($_SESSION['role'] == 'NhanVienQuanLy'): ?>
                                                <a href="index.php?model=phieunhap&action=nhap_tai_san&id=<?php echo $phieu['phieu_nhap_tai_san_id']; ?>"
                                                    class="btn btn-success btn-sm mx-2">Nhập tài sản</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] == 'QuanLy' &&$phieu['trang_thai'] == 'DangChoPheDuyet'): ?>
                                            <a href="index.php?model=phieunhap&action=xet_duyet&id=<?php echo $phieu['phieu_nhap_tai_san_id']; ?>"
                                                class="btn btn-sm mx-2 btn-primary">Xét duyệt</a>
                                        <?php endif; ?>
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
    $(document).ready(function () {
        var table = $('#dataTable').DataTable({
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

            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var ngayNhap = cells[1].textContent.trim();
                var ngayXacNhan = cells[2].textContent.trim();

                // Kiểm tra điều kiện lọc
                var passNgay = (!ngayBatDau || ngayNhap >= ngayBatDau) && (!ngayKetThuc || ngayNhap <= ngayKetThuc) && (!ngayKetThuc || ngayXacNhan <= ngayKetThuc);

                if (passNgay) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        document.getElementById('ngayBatDau').addEventListener('change', filterTable);
        document.getElementById('ngayKetThuc').addEventListener('change', filterTable);

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
        return confirm('Bạn có chắc muốn xóa phiếu này?');
    }
</script>