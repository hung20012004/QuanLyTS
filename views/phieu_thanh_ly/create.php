<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tạo phiếu nhập tài sản</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=phieuthanhly&action=create" id="phieuNhapForm">
                <!-- Các trường thông tin chung của phiếu nhập -->
                <div class="form-group row">
                    <label for="nguoiNhap" class="col-sm-2 col-form-label">Người tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nguoiNhap" name="nguoi_nhap" value="<?= htmlspecialchars($_SESSION['ten']); ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="ngayNhap" class="col-sm-2 col-form-label">Ngày tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayNhap" name="ngay_tao" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <!-- <div class="form-group row">
                    <label for="ngayXacNhan" class="col-sm-2 col-form-label">Ngày phê duyệt:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayXacNhan" name="ngay_xac_nhan">
                    </div>
                </div> -->
                
                <h5 class="mt-4">Chi tiết phiếu thanh lý</h5>
                <table id="chiTietTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên tài sản</th>
                            <th>Số lượng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select class="form-control select-tai-san" name="tai_san_id[]" required>
                                    <option value="">Chọn tài sản</option>
                                    <?php foreach ($tai_san_list as $tai_san): ?>
                                        <option value="<?= $tai_san['tai_san_id']; ?>">
                                            <?= htmlspecialchars($tai_san['ten_tai_san']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="so_luong[]" required min="1">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeTaiSan(this)">Xóa</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary" onclick="addTaiSan()">Thêm tài sản</button>

                <div class="form-group mt-3">
                    <label for="ghiChu">Ghi chú:</label>
                    <textarea class="form-control" id="ghiChu" name="ghi_chu" rows="3"></textarea>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Lưu và gửi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addTaiSan() {
    var table = document.getElementById('chiTietTable').getElementsByTagName('tbody')[0];
    var newRow = table.insertRow(table.rows.length);
    var cell1 = newRow.insertCell(0);
    var cell2 = newRow.insertCell(1);
    var cell3 = newRow.insertCell(2);
    // var cell4 = newRow.insertCell(3);

    // cell1.innerHTML = document.querySelector('.loai-tai-san').outerHTML;
    cell1.innerHTML = document.querySelector('.select-tai-san').outerHTML;
    cell2.innerHTML = '<input type="number" class="form-control" name="so_luong[]" required min="1">';
    cell3.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="removeTaiSan(this)">Xóa</button>';

    newRow.querySelector('.loai-tai-san').addEventListener('change', function() {
        updateTaiSanOptions(this);
    });
}

function removeTaiSan(button) {
    var row = button.closest('tr');
    if (document.querySelectorAll('#chiTietTable tbody tr').length > 1) {
        row.parentNode.removeChild(row);
    } else {
        alert('Không thể xóa dòng cuối cùng.');
    }
}

function updateTaiSanOptions(selectLoai) {
    var loaiTaiSanId = selectLoai.value;
    var selectTaiSan = selectLoai.closest('tr').querySelector('.select-tai-san');
    
    Array.from(selectTaiSan.options).forEach(option => {
        if (option.value === "") {
            option.style.display = "";
        } else {
            option.style.display = option.dataset.loai === loaiTaiSanId ? "" : "none";
        }
    });

    selectTaiSan.value = ""; // Reset selection
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.loai-tai-san').forEach(function(select) {
        select.addEventListener('change', function() {
            updateTaiSanOptions(this);
        });
    });
});
</script>