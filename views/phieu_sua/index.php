<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieusua&action=index">Phiếu sửa</a></li>
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
                <h5 class="card-title mb-0">Quản lý phiếu sửa</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <?php if ($_SESSION['role'] == 'NhanVien'): ?> 
                        <a href="index.php?model=phieusua&action=create" class="btn btn-primary">Tạo phiếu</a>
                    <?php elseif ($_SESSION['role'] == 'QuanLy' || $_SESSION['role'] == 'KyThuat'): ?>
                        <a href="index.php?model=phieusua&action=export" class="btn btn-success">Xuất excel</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3" style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayBatDau" class="mr-2 mb-0" style="white-space: nowrap;">Từ ngày:&nbsp;&nbsp;&nbsp;</label>
                            <input type="date" id="ngayBatDau" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayKetThuc" class="mr-2 mb-0" style="white-space: nowrap;">Đến ngày:</label>
                            <input type="date" id="ngayKetThuc" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="tenViTri" class="mr-2 mb-0" style="white-space: nowrap;">Tên vị trí:</label>
                            <input type="text" id="tenViTri" class="form-control" placeholder="Nhập tên vị trí">
                        </div>
                    </div>
                    <?php if ($_SESSION['role'] != 'NhanVien'): ?>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="tenNguoiYeuCau" class="mr-2 mb-0" style="white-space: nowrap;">Tên người yêu cầu:</label>
                            <input type="text" id="tenNguoiYeuCau" class="form-control" placeholder="Nhập tên người yêu cầu">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>Mã số phiếu</th>
                            <th>Ngày yêu cầu</th>
                            <?php if ($_SESSION['role'] != 'NhanVien'): ?>
                                <th>Người yêu cầu</th>
                            <?php endif; ?>
                            <th>Vị trí</th>
                            <th>Mô tả</th>
                            <th>Người sửa chữa</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phieuSuas as $phieu): ?>
                            <?php if ($phieu['user_yeu_cau_id'] == $_SESSION['user_id'] && $phieu['trang_thai'] != 'Huy' || $_SESSION['role'] == 'QuanLy' && $phieu['trang_thai'] == 'DaHoanThanh' || $_SESSION['role'] == 'KyThuat' && $phieu['user_sua_chua_id'] == $_SESSION['user_id'] || $_SESSION['role'] == 'NhanVienQuanLy'): ?>
                                <tr>
                                    <td class="text-center"><?php echo $phieu['phieu_sua_id']; ?></td>
                                    <td class="text-center"><?= date('d-m-Y', strtotime($phieu['ngay_yeu_cau'])) ?></td>
                                    <?php if ($_SESSION['role'] != 'NhanVien'): ?>
                                        <td class="text-center"> <?= $phieu['user_yeu_cau_name']; ?> </td>
                                    <?php endif; ?>
                                    <td class="text-center"><?= $phieu['ten_vi_tri']; ?></td>
                                    <td class="text-center"><?= $phieu['mo_ta']; ?></td>
                                    <td class="text-center"><?= $phieu['user_sua_chua_name']; ?></td>
                                    <td class="text-center">
                                        <?= $phieu['trang_thai'] == 'DaGui' ? 'Đã gửi' : ($phieu['trang_thai'] == 'DaNhan' ? 'Đã nhận' : ($phieu['trang_thai'] == 'DaHoanThanh' ? 'Đã hoàn thành' : ($phieu['trang_thai'] == 'YeuCauHuy' ? 'Đang yêu cầu hủy' : 'Đã hủy'))); ?>
                                    </td>
                                    <td class="d-flex justify-content-center">
                                        <a href="index.php?model=phieusua&action=show&id=<?php echo $phieu['phieu_sua_id']; ?>"
                                            class="btn btn-info btn-sm mx-2">Xem</a>
                                        <?php if ($_SESSION['role'] == 'NhanVien'): ?>
                                            <?php if ($phieu['trang_thai'] == 'DaGui'): ?>
                                                <a href="index.php?model=phieusua&action=edit&id=<?php echo $phieu['phieu_sua_id']; ?>"
                                                    class="btn btn-warning btn-sm mx-2">Sửa</a>
                                            <?php endif; ?>
                                            <?php if ($phieu['trang_thai'] == 'DaGui' || $phieu['trang_thai'] == 'DaNhan' ): ?>
                                            <a href="index.php?model=phieusua&action=cancellationrequest&id=<?php echo $phieu['phieu_sua_id']; ?>"
                                                class="btn btn-danger btn-sm mx-2"
                                                onclick="return confirm('Bạn có chắc muốn gửi yêu cầu hủy phiếu sửa này?')">Hủy</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] == 'KyThuat' && $phieu['trang_thai'] != 'DaHoanThanh'):?>
                                            <a href="index.php?model=phieusua&action=hoan_thanh&id=<?php echo $phieu['phieu_sua_id']; ?>"
                                                class="btn btn-success btn-sm mx-2">Hoàn thành</a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] == 'NhanVienQuanLy'): ?>
                                            <?php if ($phieu['trang_thai'] != 'DaNhan' && $phieu['trang_thai'] != 'Huy' || $phieu['trang_thai'] != 'YeuCauHuy'): ?>
                                                <a href="index.php?model=phieusua&action=xet_duyet&id=<?php echo $phieu['phieu_sua_id']; ?>"
                                                    class="btn btn-sm mx-2 btn-primary">Xét duyệt</a>
                                            <?php endif; ?>
                                            <?php if ($phieu['trang_thai'] == 'YeuCauHuy'): ?>
                                                <a href="index.php?model=phieusua&action=cancel&id=<?php echo $phieu['phieu_sua_id']; ?>"
                                                    class="btn btn-danger btn-sm mx-2"
                                                    onclick="return confirm('Bạn có chắc muốn xác nhận hủy yêu cầu này?')">Xác nhận hủy</a>
                                            <?php endif; ?>
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

        $('#ngayBatDau, #ngayKetThuc').on('change', function() {
            var ngayBatDau = $('#ngayBatDau').val();
            var ngayKetThuc = $('#ngayKetThuc').val();

            if (ngayBatDau) {
                ngayBatDau = new Date(ngayBatDau);
            }

            if (ngayKetThuc) {
                ngayKetThuc = new Date(ngayKetThuc);
            }

            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var dateStr = data[1]; // Assuming the date is in the 3rd column (index 2)
                    var date = parseDate(dateStr);

                    if (
                        (!ngayBatDau && !ngayKetThuc) ||
                        (!ngayBatDau && date <= ngayKetThuc) ||
                        (ngayBatDau <= date && !ngayKetThuc) ||
                        (ngayBatDau <= date && date <= ngayKetThuc)
                    ) {
                        return true;
                    }
                    return false;
                }
            );
            table.draw();
            $.fn.dataTable.ext.search.pop();
        });

        function parseDate(dateStr) {
            var parts = dateStr.split('-');
            return new Date(parts[2], parts[1] - 1, parts[0]); // Create a new Date object in the format yyyy-mm-dd
        }
        
        <?php if ($_SESSION['role'] != 'NhanVien'): ?>
            $('#tenNguoiYeuCau').on('input', function() {
                table.column(2).search(this.value).draw();
            });
            $('#tenViTri').on('input', function() {
                table.column(3).search(this.value).draw();
            });
        <?php else: ?>
            $('#tenViTri').on('input', function() {
                table.column(2).search(this.value).draw();
            }); 
        <?php endif; ?>

        var toggleButton = document.getElementById('toggleSearch');
        var searchForm = document.getElementById('searchForm');

        toggleButton.addEventListener('click', function() {
            if (searchForm.style.display === 'none') {
                searchForm.style.display = 'block';
                toggleButton.textContent = 'Ẩn tìm kiếm';
            } else {
                searchForm.style.display = 'none';
                toggleButton.textContent = 'Tìm kiếm';
            }
        });
    });

    function cancelRequest(event) {
        event.preventDefault(); // Ngăn chặn hành vi mặc định của nút

        var phieuSuaId = event.target.getAttribute('data-phieu-id'); // Lấy id của phiếu sửa từ data attribute

        // Gửi yêu cầu AJAX đến controller PHP
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php?model=phieusua&action=cancellationrequest&id=' + phieuSuaId, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Xử lý thành công
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('Gửi yêu cầu hủy thành công!');
                    window.location.reload(); // Tải lại trang sau khi thành công (hoặc cập nhật DOM tương ứng)
                } else {
                    alert('Gửi yêu cầu hủy không thành công!');
                }
            } else {
                // Xử lý lỗi
                alert('Đã xảy ra lỗi trong quá trình gửi yêu cầu hủy.');
            }
        };
        xhr.send();
    }
    
    function confirmDelete() {
        return confirm('Bạn có chắc muốn xóa phiếu này?');
    }
</script>
