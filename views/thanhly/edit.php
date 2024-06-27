<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=thanhly&action=index">Hóa Đơn</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa</li>
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
            }, 2000);
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Chỉnh Sửa Hóa Đơn</h5>
            </div>
        </div>
        <div class="card-body">
            <?php $tontai = null;
                    foreach($dstl as $ds):
                    if($tontai != $ds['hoa_don_id']) {?>
            <form id="editForm" method="POST" action="index.php?model=thanhly&action=edit&id=<?= $ds['hoa_don_id'] ?>">
                    <?php } $tontai = $ds['hoa_don_id'];
                endforeach;?>
                <!-- Ngày Mua và Nhà Cung Cấp -->
                 <?php $tontai = null;
                    foreach($dstl as $ds):
                    if($tontai != $ds['hoa_don_id']) {?>
                <div class="form-row">
                     <div class="form-group col-md-6">
                        <label for="ID">Mã Hóa Đơn</label>
                        <input type="text" class="form-control" id="maHoaDon" name="ma_hoa_don" value="<?= $ds['hoa_don_id'] ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ngayMua">Ngày Mua</label>
                        <input type="date" class="form-control" id="ngayMua" name="ngay_mua" value="<?= $ds['ngay_thanh_ly'] ?>" required>
                    </div>
                    
                </div>

                 <?php } $tontai = $ds['hoa_don_id'];
                endforeach;?>
                <input type="hidden" name="hoa_don_id" value="<?= $ds['hoa_don_id'] ?>">
                <!-- Bảng Chi Tiết -->
                <h5 class="mt-4">Chi Tiết Hóa Đơn</h5>
                <div class="table-responsive">
                    <table id="tableChiTiet" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tài Sản</th>
                                <th>Số Lượng</th>
                                <th>Đơn Giá</th>
                                <th>Thành Tiền</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dstl as $index => $detail): ?>
                                <tr id="row<?= $index ?>">
                                    <td>
                                        <select class="form-control" name="tai_san[]" required>
                                            <option value="">Chọn tài sản</option>
                                            <?php foreach ($taisans as $ts): ?>
                                                <option value="<?= $ts['tai_san_id']; ?>" <?= ($ts['tai_san_id'] == $detail['tai_san_id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($ts['ten_tai_san']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <input type="hidden" class="form-control" name="tai_san_id[]" value="<?= isset($detail['tai_san_id']) ? htmlspecialchars($detail['tai_san_id']) : 0 ?>" required>
                                            <input type="hidden" class="form-control" name="chi_tiet_id[]" value="<?= isset($detail['chi_tiet_id']) ? htmlspecialchars($detail['chi_tiet_id']) : 0 ?>" required>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control so-luong" name="so_luong[]" value="<?= $detail['so_luong'] ?>" required onchange="tinhThanhTien(this)" oninput="tinhThanhTien(this)"></td>
                                    <td><input type="number" step="0.01" class="form-control don-gia" name="gia_thanh_ly[]" value="<?= $detail['gia_thanh_ly'] ?>" required onchange="tinhThanhTien(this)" oninput="tinhThanhTien(this)"></td>
                                    <td><input type="text" class="form-control thanh-tien" name="thanh_tien[]" value="<?= number_format($detail['so_luong'] * $detail['gia_thanh_ly'], 0, ',', '.')  ?>" readonly></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="editRow(this)">Sửa</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">Xóa</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-primary mt-2" onclick="addRow()">Thêm Dòng</button>

                <!-- Tổng Giá Trị -->
                <div class="form-group mt-3">
                    <?php $tontai=0; foreach($dstl as $ds):
                    if($tontai != $ds['hoa_don_id']) {?>
                    <label for="tongGiaTri">Tổng Giá Trị</label>
                    <input type="text" class="form-control" id="tongGiaTri" name="tong_gia_tri" value="<?= number_format($ds['tong_gia_tri'], 0, ',', '.') ?>" readonly>
                    <?php } $tontai = $ds['hoa_don_id'];
                endforeach;?>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="index.php?model=thanhly&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function tinhThanhTien(input) {
    var row = input.closest('tr');
    var soLuong = parseFloat(row.querySelector('.so-luong').value) || 0;
    var donGia = parseFloat(row.querySelector('.don-gia').value) || 0;
    var thanhTien = soLuong * donGia;
    row.querySelector('.thanh-tien').value = thanhTien.toFixed(2);
    tinhTongGiaTri();
}

function tinhTongGiaTri() {
    var total = 0;
    document.querySelectorAll('.thanh-tien').forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('tongGiaTri').value = total.toFixed(2);
}

function addRow() {
    var tbody = document.querySelector("#tableChiTiet tbody");
    var newRow = tbody.rows[0].cloneNode(true);
    var rowCount = tbody.rows.length;
    newRow.id = 'row' + rowCount;
    newRow.querySelectorAll('input').forEach(function(input) {
        input.value = '';
    });
    newRow.querySelectorAll('select').forEach(function(select) {
        select.selectedIndex = 0;
    });
    tbody.appendChild(newRow);
    tinhTongGiaTri();
}

function editRow(button) {
    var row = button.closest('tr');
    // Implement edit functionality here, if required
}

function deleteRow(button) {
    var row = button.closest('tr');
    if (document.querySelectorAll('#tableChiTiet tbody tr').length > 1) {
        row.remove();
        tinhTongGiaTri();
    } else {
        alert('Không thể xóa dòng cuối cùng.');
    }
}
</script>