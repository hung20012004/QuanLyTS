<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=hoadonmua&action=index">Hóa Đơn</a></li>
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
            <h5 class="card-title mb-0">Chỉnh Sửa Hóa Đơn</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=hoadonmua&action=edit&id=<?= $invoice['hoa_don_id'] ?>" id="editForm">
                <input type="hidden" name="hoa_don_id" value="<?= $invoice['hoa_don_id'] ?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="ngayMua">Ngày Mua</label>
                        <input type="date" class="form-control" id="ngayMua" name="ngay_mua" value="<?= $invoice['ngay_mua'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nhaCungCap">Nhà Cung Cấp</label>
                        <select class="form-control" id="nhaCungCap" name="nha_cung_cap_id" required>
                            <option value="">Chọn nhà cung cấp</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <?php if ($supplier['trang_thai'] != 0): ?>
                                    <option value="<?= $supplier['nha_cung_cap_id']; ?>" <?= ($supplier['nha_cung_cap_id'] == $invoice['nha_cung_cap_id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($supplier['ten_nha_cung_cap']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

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
                            <?php foreach ($invoice_details as $index => $detail): ?>
                                <tr id="row<?= $index ?>">
                                    <td>
                                        <select class="form-control loai-tai-san" name="loai_tai_san[]" required>
                                            <option value="">Chọn loại tài sản</option>
                                            <?php foreach ($loai_tai_san_list as $loai_tai_san): ?>
                                                <option value="<?= $loai_tai_san['loai_tai_san_id']; ?>" <?= ($loai_tai_san['loai_tai_san_id'] == $detail['loai_tai_san_id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($loai_tai_san['ten_loai_tai_san']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="ten_tai_san[]" value="<?= htmlspecialchars($detail['ten_tai_san']) ?>" required></td>
                                    <td><input type="number" class="form-control so-luong" name="so_luong[]" value="<?= $detail['so_luong'] ?>" required onchange="tinhThanhTien(this)" oninput="tinhThanhTien(this)"></td>
                                    <td><input type="number" class="form-control don-gia" name="don_gia[]" value="<?= $detail['don_gia'] ?>" required onchange="tinhThanhTien(this)" oninput="tinhThanhTien(this)"></td>
                                    <td><input type="text" class="form-control thanh-tien" name="thanh_tien[]" readonly></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="xoaDong(this)">Xóa</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-primary mt-2" onclick="themDong()">Thêm Dòng</button>

                <div class="form-group mt-3">
                    <label for="tongGiaTri">Tổng Giá Trị</label>
                    <input type="text" class="form-control" id="tongGiaTri" name="tong_gia_tri" value="<?= $invoice['tong_gia_tri'] ?>" readonly>
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
    newRow.id = 'row' + tbody.rows.length;
    newRow.querySelectorAll('input').forEach(function(input) {
        input.value = '';
    });
    newRow.querySelectorAll('select').forEach(function(select) {
        select.selectedIndex = 0;
    });
    tbody.appendChild(newRow);
    updateRowNumbers();
}

function xoaDong(button) {
    if (confirm('Bạn có chắc chắn muốn xóa dòng này?')) {
        var row = button.closest('tr');
        if (document.querySelectorAll('#tableChiTiet tbody tr').length > 1) {
            row.remove();
            updateRowNumbers();
        } else {
            alert('Không thể xóa dòng cuối cùng.');
        }
    }
}

// Tính toán ban đầu khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#tableChiTiet tbody tr').forEach(function(row) {
        tinhThanhTien(row.querySelector('.so-luong'));
    });
});
</script>
