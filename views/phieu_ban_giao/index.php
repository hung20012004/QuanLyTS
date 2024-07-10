<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active"><a href="index.php?model=phieubangiao&action=index">Bàn giao tài sản</a></li>
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
            setTimeout(function () {
                var alert = document.getElementById('alert-message');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(function () {
                        alert.style.display = 'none';
                    }, 150);
                }
            }, 2000);
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản lý phiếu bàn giao tài sản</h5>
                <div>
                    <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <?php if ($_SESSION['role'] == 'NhanVien'): ?>
                        <a href="index.php?model=phieubangiao&action=create" class="btn btn-primary">Tạo yêu cầu</a>
                    <?php endif; ?>
                    <a href="index.php?model=phieubangiao&action=export" class="btn btn-success">Xuất excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
        <div id="searchForm" class="mb-3" style="display: none;">
    <div class="row">
        <div class="col-md-3 mb-2">
            <label for="ngayTaoPhieu" class="form-label">Ngày tạo phiếu:</label>
            <div class="input-group">
                <input type="date" name="ngayTaoPhieuBatDau" id="ngayTaoPhieuBatDau" class="form-control" placeholder="Từ ngày">
                <input type="date" name="ngayTaoPhieuKetThuc" id="ngayTaoPhieuKetThuc" class="form-control" placeholder="Đến ngày">
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="ngayKiemTra" class="form-label">Ngày kiểm tra:</label>
            <div class="input-group">
                <input type="date" name="ngayKiemTraBatDau" id="ngayKiemTraBatDau" class="form-control" placeholder="Từ ngày">
                <input type="date" name="ngayKiemTraKetThuc" id="ngayKiemTraKetThuc" class="form-control" placeholder="Đến ngày">
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="ngayPheDuyet" class="form-label">Ngày phê duyệt:</label>
            <div class="input-group">
                <input type="date" name="ngayPheDuyetBatDau" id="ngayPheDuyetBatDau" class="form-control" placeholder="Từ ngày">
                <input type="date" name="ngayPheDuyetKetThuc" id="ngayPheDuyetKetThuc" class="form-control" placeholder="Đến ngày">
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="ngayBanGiao" class="form-label">Ngày bàn giao:</label>
            <div class="input-group">
                <input type="date" name="ngayBanGiaoBatDau" id="ngayBanGiaoBatDau" class="form-control" placeholder="Từ ngày">
                <input type="date" name="ngayBanGiaoKetThuc" id="ngayBanGiaoKetThuc" class="form-control" placeholder="Đến ngày">
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-3 mb-2">
            <label for="trangThai" class="form-label">Trạng thái:</label>
            <select name="trangThai" id="trangThai" class="form-select">
                <option value="">Tất cả</option>
                <option value="Đã gửi">Đã gửi</option>
                <option value="Đã kiểm tra">Đã kiểm tra</option>
                <option value="Đang chờ phê duyệt">Đang chờ phê duyệt</option>
                <option value="Đã phê duyệt">Đã phê duyệt</option>
                <option value="Đã giao">Đã giao</option>
                <option value="Không duyệt">Không duyệt</option>
            </select>
        </div>
        <div class="col-md-9 d-flex align-items-end justify-content-end">
            <button type="button" id="searchButton" class="btn btn-primary me-2">Tìm kiếm</button>
            <button type="button" id="resetButton" class="btn btn-secondary">Đặt lại</button>
        </div>
    </div>
</div>  
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>Mã số phiếu</th>
                            <th>Ngày tạo phiếu</th>
                            <th>Ngày kiểm tra</th>
                            <th>Ngày phê duyệt</th>
                            <th>Ngày bàn giao</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phieuBanGiao as $phieu): ?>
                           
                            <?php if (($phieu['user_nhan_id'] == $_SESSION['user_id'] && $_SESSION['role']=='NhanVien')
                              || ($phieu['user_ban_giao_id'] == $_SESSION['user_id'] && $_SESSION['role']=='NhanVienQuanLy')
                              || ($phieu['user_ban_giao_id'] == '' && $_SESSION['role']=='NhanVienQuanLy')
                              ||($_SESSION['role']=='QuanLy'&& ($phieu['trang_thai']=='DaBanGiao'||$phieu['trang_thai']=='DangChoPheDuyet'||$phieu['trang_thai']=='KhongDuyet'||$phieu['trang_thai']=='DaPheDuyet'))): ?>
                                <tr>
                                    <td class="text-center"><?php echo $phieu['phieu_ban_giao_id']; ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($phieu['ngay_gui'])) ?></td>
                                    <td class="text-center"><?= !empty($phieu['ngay_kiem_tra']) ? date('d/m/Y', strtotime($phieu['ngay_kiem_tra'])) : ''; ?></td>
                                    <td class="text-center"><?= !empty($phieu['ngay_duyet']) ? date('d/m/Y', strtotime($phieu['ngay_duyet'])) : ''; ?></td>
                                    <td class="text-center"><?= !empty($phieu['ngay_ban_giao']) ? date('d/m/Y', strtotime($phieu['ngay_ban_giao'])) : ''; ?></td>
                                    <td class="text-center">
                                        <?php
                                        switch ($phieu['trang_thai']) {
                                            case 'DaGui':
                                                echo 'Đã gửi';
                                                break;
                                            case 'DaKiemTra':
                                                echo 'Đã kiểm tra';
                                                break;
                                            case 'DangChoPheDuyet':
                                                echo 'Đang chờ phê duyệt';
                                                break;
                                            case 'DaPheDuyet':
                                                echo 'Đã phê duyệt';
                                                break;
                                            case 'DaGiao':
                                                echo 'Đã giao';
                                                break;
                                            case 'KhongDuyet':
                                                echo 'Không duyệt';
                                                break;
                                            default:
                                                echo $phieu['trang_thai'];
                                        }
                                        ?>
                                    </td>
                                    <td class="d-flex justify-content-center">
                                        <a href="index.php?model=phieubangiao&action=show&id=<?php echo $phieu['phieu_ban_giao_id']; ?>" class="btn btn-info btn-sm mx-2">Xem</a>
                                        <?php if ($_SESSION['role'] == 'NhanVien' && $phieu['trang_thai'] == 'DaGui'): ?>
                                            <a href="index.php?model=phieubangiao&action=edit&id=<?php echo $phieu['phieu_ban_giao_id']; ?>" class="btn btn-warning btn-sm mx-2">Sửa</a>
                                            <a href="index.php?model=phieubangiao&action=delete&id=<?php echo $phieu['phieu_ban_giao_id']; ?>" class="btn btn-danger btn-sm mx-2" onclick="return confirm('Bạn có chắc chắn muốn xóa phiếu này không?');">Xóa</a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] == 'QuanLy' && $phieu['trang_thai'] == 'DangChoPheDuyet'): ?>
                                            <a href="index.php?model=phieubangiao&action=xet_duyet&id=<?php echo $phieu['phieu_ban_giao_id']; ?>" class="btn btn-primary btn-sm mx-2">Xét duyệt</a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] == 'NhanVienQuanLy' && $phieu['trang_thai'] == 'DaGui'): ?>
                                            <a href="index.php?model=phieubangiao&action=kiem_tra&id=<?php echo $phieu['phieu_ban_giao_id']; ?>" class="btn btn-primary btn-sm mx-2">Tạo phiếu</a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] == 'NhanVienQuanLy' && $phieu['trang_thai'] == 'DaPheDuyet'): ?>
                                            <a href="index.php?model=phieubangiao&action=ban_giao&id=<?php echo $phieu['phieu_ban_giao_id']; ?>" class="btn btn-primary btn-sm mx-2">Bàn giao</a>
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
    var toggleButton = document.getElementById('toggleSearch');
    var searchForm = document.getElementById('searchForm');
    var searchButton = document.getElementById('searchButton');
    var resetButton = document.getElementById('resetButton');
    var table = document.getElementById('dataTable');

    toggleButton.addEventListener('click', function () {
        searchForm.style.display = searchForm.style.display === 'none' ? 'block' : 'none';
        toggleButton.textContent = searchForm.style.display === 'none' ? 'Tìm kiếm' : 'Ẩn tìm kiếm';
    });
    function parseDate(dateString) {
    if (!dateString) return null;
    var parts = dateString.split('-');
    return new Date(parts[0], parts[1] - 1, parts[2]);
}

function isDateInRange(dateString, startDateString, endDateString) {
    if (!dateString) return false; // Thay đổi ở đây
    var date = parseDate(dateString);
    var startDate = startDateString ? parseDate(startDateString) : null;
    var endDate = endDateString ? parseDate(endDateString) : null;

    if (startDate && endDate) {
        return date >= startDate && date <= endDate;
    } else if (startDate) {
        return date >= startDate;
    } else if (endDate) {
        return date <= endDate;
    }
    return true; // Nếu không có ngày bắt đầu và kết thúc, hiển thị tất cả ngày không null
}

function filterTable() {
    var rows = table.getElementsByTagName('tr');
    for (var i = 1; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var showRow = true;

        // Kiểm tra ngày tạo phiếu
        var ngayTaoPhieuBatDau = document.getElementById('ngayTaoPhieuBatDau').value;
        var ngayTaoPhieuKetThuc = document.getElementById('ngayTaoPhieuKetThuc').value;
        if (ngayTaoPhieuBatDau || ngayTaoPhieuKetThuc) {
            showRow = showRow && isDateInRange(
                cells[1].textContent.trim(),
                ngayTaoPhieuBatDau,
                ngayTaoPhieuKetThuc
            );
        }

        // Kiểm tra ngày kiểm tra
        var ngayKiemTraBatDau = document.getElementById('ngayKiemTraBatDau').value;
        var ngayKiemTraKetThuc = document.getElementById('ngayKiemTraKetThuc').value;
        if (ngayKiemTraBatDau || ngayKiemTraKetThuc) {
            showRow = showRow && isDateInRange(
                cells[2].textContent.trim(),
                ngayKiemTraBatDau,
                ngayKiemTraKetThuc
            );
        }

        // Kiểm tra ngày phê duyệt
        var ngayPheDuyetBatDau = document.getElementById('ngayPheDuyetBatDau').value;
        var ngayPheDuyetKetThuc = document.getElementById('ngayPheDuyetKetThuc').value;
        if (ngayPheDuyetBatDau || ngayPheDuyetKetThuc) {
            showRow = showRow && isDateInRange(
                cells[3].textContent.trim(),
                ngayPheDuyetBatDau,
                ngayPheDuyetKetThuc
            );
        }

        // Kiểm tra ngày bàn giao
        var ngayBanGiaoBatDau = document.getElementById('ngayBanGiaoBatDau').value;
        var ngayBanGiaoKetThuc = document.getElementById('ngayBanGiaoKetThuc').value;
        if (ngayBanGiaoBatDau || ngayBanGiaoKetThuc) {
            showRow = showRow && isDateInRange(
                cells[4].textContent.trim(),
                ngayBanGiaoBatDau,
                ngayBanGiaoKetThuc
            );
        }

        // Kiểm tra trạng thái
        var trangThai = document.getElementById('trangThai').value;
        showRow = showRow && (!trangThai || cells[5].textContent.trim() === trangThai);

        rows[i].style.display = showRow ? '' : 'none';
    }
}

    function resetForm() {
        var inputs = searchForm.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = '';
        }
        document.getElementById('trangThai').value = '';
        var rows = table.getElementsByTagName('tr');
        for (var i = 1; i < rows.length; i++) {
            rows[i].style.display = '';
        }
    }

    searchButton.addEventListener('click', filterTable);
    resetButton.addEventListener('click', resetForm);

    // Logging for debugging
    searchButton.addEventListener('click', function() {
        console.log('Search activated');
        var inputs = searchForm.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            console.log(inputs[i].id + ': ' + inputs[i].value);
        }
        console.log('Status: ' + document.getElementById('trangThai').value);
    });

    resetButton.addEventListener('click', function() {
        console.log('Reset activated');
    });
});
</script>
