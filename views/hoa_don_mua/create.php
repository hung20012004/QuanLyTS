<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Thêm Mới Hóa Đơn</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=hoadonmua&action=create" id="hoadonForm">
                <!-- Ngày Mua và Nhà Cung Cấp -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="ngayMua">Ngày Mua</label>
                        <input type="date" class="form-control" id="ngayMua" name="ngay_mua" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nhaCungCap">Nhà Cung Cấp</label>
                        <select class="form-control" id="nhaCungCap" name="nha_cung_cap_id" required>
                            <option value="">Chọn nhà cung cấp</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['nha_cung_cap_id']; ?>"><?= htmlspecialchars($supplier['ten_nha_cung_cap']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Bảng Chi Tiết -->
                <h5 class="mt-4">Chi Tiết Hóa Đơn</h5>
                <div class="table-responsive">
                    <table id="tableChiTiet" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Loại Tài Sản</th>
                                <th>Tên Tài Sản</th>
                                <th>Số Lượng</th>
                                <th>Đơn Giá</th>
                                <th>Thành Tiền</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="row0">
                                <td>
                                    <select class="form-control loai-tai-san" name="loai_tai_san_id[]" required>
                                        <option value="">Chọn loại tài sản</option>
                                        <?php foreach ($loai_tai_san_list as $loai): ?>
                                            <option value="<?= $loai['loai_tai_san_id']; ?>"><?= htmlspecialchars($loai['ten_loai_tai_san']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input list="taiSanList" class="form-control select-tai-san" name="ten_tai_san[]" placeholder="Chọn hoặc nhập tên tài sản" required>
                                    <input type="hidden" class="tai-san-id" name="tai_san_id[]" value="">
                                    <datalist id="taiSanList">
                                        <?php foreach ($tai_san_list as $tai_san): ?>
                                            <option 
                                                data-loai-tai-san-id="<?= $tai_san['loai_tai_san_id']; ?>"
                                                data-tai-san-id="<?= $tai_san['tai_san_id']; ?>"
                                                value="<?= htmlspecialchars($tai_san['ten_tai_san']); ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </td>
                                <td><input type="number" class="form-control so-luong" name="so_luong[]" required></td>
                                <td><input type="number" class="form-control don-gia" name="don_gia[]" required></td>
                                <td><input type="text" class="form-control thanh-tien" name="thanh_tien[]" readonly></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="xoaDong(this)">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-primary mt-2" onclick="themDong()">Thêm Dòng</button>

                <!-- Tổng Giá Trị -->
                <div class="form-group mt-3">
                    <label for="tongGiaTri">Tổng Giá Trị</label>
                    <input type="text" class="form-control" id="tongGiaTri" name="tong_gia_tri" readonly>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="index.php?model=hoadonmua&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    initializeTaiSanListeners();
    document.querySelectorAll('.so-luong, .don-gia').forEach(input => {
        input.addEventListener('input', function() {
            tinhThanhTien(this);
        });
    });
});

function initializeTaiSanListeners() {
    document.querySelectorAll('.select-tai-san').forEach(function(input) {
        input.addEventListener('input', handleTaiSanSelection);
    });
}

function handleTaiSanSelection() {
    var datalist = document.getElementById('taiSanList');
    var option = Array.from(datalist.options).find(opt => opt.value === this.value);
    var row = this.closest('tr');
    var loaiTaiSanSelect = row.querySelector('.loai-tai-san');
    var taiSanIdInput = row.querySelector('.tai-san-id');
    
    if (option) {
        var loaiTaiSanId = option.dataset.loaiTaiSanId;
        var taiSanId = option.dataset.taiSanId;
        loaiTaiSanSelect.value = loaiTaiSanId;
        loaiTaiSanSelect.disabled = true;
        taiSanIdInput.value = taiSanId;
    } else {
        loaiTaiSanSelect.disabled = false;
        loaiTaiSanSelect.value = '';
        taiSanIdInput.value = '';
    }
}

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

function updateRowNumbers() {
    document.querySelectorAll('#tableChiTiet tbody tr').forEach(function(row, index) {
        row.id = 'row' + index;
    });
    tinhTongGiaTri();
}

function themDong() {
    var tbody = document.querySelector("#tableChiTiet tbody");
    var newRow = tbody.rows[0].cloneNode(true);
    var rowCount = tbody.rows.length;
    newRow.id = 'row' + rowCount;
    newRow.querySelectorAll('input').forEach(function(input) {
        input.value = '';
    });
    newRow.querySelectorAll('select').forEach(function(select) {
        select.selectedIndex = 0;
        select.disabled = false;
    });

    var newTaiSanInput = newRow.querySelector('.select-tai-san');
    newTaiSanInput.addEventListener('input', handleTaiSanSelection);

    newRow.querySelectorAll('.so-luong, .don-gia').forEach(input => {
        input.addEventListener('input', function() {
            tinhThanhTien(this);
        });
    });

    tbody.appendChild(newRow);
    updateRowNumbers();
}

function xoaDong(button) {
    var row = button.closest('tr');
    if (document.querySelectorAll('#tableChiTiet tbody tr').length > 1) {
        row.remove();
        updateRowNumbers();
    } else {
        alert('Không thể xóa dòng cuối cùng.');
    }
}

document.getElementById('hoadonForm').addEventListener('submit', function(e) {
    var disabledInputs = this.querySelectorAll('select:disabled, input:disabled');
    disabledInputs.forEach(function(input) {
        input.disabled = false;
    });
});
</script>