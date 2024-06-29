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
        <!-- <script>
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
        </script> -->
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><strong>Chỉnh Sửa Hóa Đơn</strong></h5>
            </div>
        </div>
        <div class="card-body">
        
            <form id="editForm" method="POST" action="index.php?model=thanhly&action=create">
                <!-- Ngày Mua và Nhà Cung Cấp -->
                <input type="hidden" name="hoa_don_id" value="<?= $ds['hoa_don_id'] ?>">
                <!-- Bảng Chi Tiết -->
                <h5 class="mt-4">Chi Tiết Hóa Đơn</h5>
                <div class="table-responsive">
                    <table id="tableChiTiet" class="table table-bordered" style="text-align: center;">
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
                                <tr id="row<?= $index ?>">
                                    <td>
                                        <select class="form-control" name="tai_san_id[]" required>
                                            <option value="">Chọn tài sản</option>
                                            <?php foreach ($taisans as $ts): ?>
                                                <option value="<?= $ts['tai_san_id']; ?>"  style="text-align: center;">
                                                    <?= htmlspecialchars($ts['ten_tai_san']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <!-- <input type="hidden" class="form-control" name="tai_san_id[]" value="<?= isset($detail['tai_san_id']) ? htmlspecialchars($detail['tai_san_id']) : 0 ?>" required> -->
                                            <!-- <input type="hidden" class="form-control" name="chi_tiet_id[]" value="" required> -->
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control so-luong" name="so_luong[]" value="" required onchange="tinhThanhTien(this)" oninput="tinhThanhTien(this)" style="text-align: center;"></td>
                                    <td><input type="number" step="0.01" class="form-control don-gia" name="gia_thanh_ly[]" value="" required onchange="tinhThanhTien(this)" oninput="tinhThanhTien(this)" style="text-align: center;"></td>
                                    <td><input type="text" class="form-control thanh-tien" name="thanh_tien[]" value="" readonly style="text-align: center;"></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="editRow(this)">Sửa</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">Xóa</button>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tổng Giá Trị -->
                 <div class="form-row">
                    <div class="form-group col-md-5"></div>
                    <div class="form-group col-md-4 ">
                        <label for="ngayThanhLy">Ngày Mua</label>
                        <input type="date" class="form-control" id="ngayThanhLy" name="ngay_thanh_ly" value="<?= $ds['ngay_thanh_ly'] ?>" style="text-align: center;" required>
                    </div>
                        <div class="form-group col-md-3">
                            <label for="tongGiaTri"><strong>Tổng Giá Trị</strong></label>
                            <input type="text" class="form-control" id="tongGiaTri" name="tong_tien" value="" style="text-align: center;" readonly>
                        </div>
                        
                 </div>
                
                <div class="mt-3 d-flex justify-content-between">
                    <a href="index.php?model=thanhly&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="button" class="btn btn-primary col-ms-2" style="margin-left: 770px;" onclick="addRow()">Thêm Dòng</button>
                    <button type="submit" class="btn btn-success col-ms-2" >Tạo</button>
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
    row.querySelector('.thanh-tien').value = thanhTien.toLocaleString('vi-VN', { minimumFractionDigits: 0 });
    tinhTongGiaTri();
}

function tinhTongGiaTri() {
    var total = 0;
    document.querySelectorAll('.thanh-tien').forEach(function(input) {
        total += parseFloat(input.value.replace(/\./g, '').replace(',', '.')) || 0;
    });
    document.getElementById('tongGiaTri').value = total.toLocaleString('vi-VN', { minimumFractionDigits: 0 });
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
        tinhTongGiaTri(); // Cập nhật lại tổng giá trị sau khi xóa
    } else {
        alert('Không thể xóa dòng cuối cùng.');
    }
}

</script>