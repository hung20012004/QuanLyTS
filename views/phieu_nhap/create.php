<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Thêm Mới Phiếu Nhập Tài Sản</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=phieunhaptaisan&action=create" id="phieuNhapForm">
                <div class="form-group row">
                    <label for="nguoiNhap" class="col-sm-1 pr-0 col-form-label">Người nhập:</label>
                    <div class="col-sm-4 pl-0">
                        <input type="text" class="form-control bg-light border-0 bg-white" id="nguoiNhap" name="nguoi_nhap" value="<?= htmlspecialchars($_SESSION['ten']); ?>" readonly>
                    </div>
                </div>

                <!-- Ngày Nhập -->
                <div class="form-group row">
                    <label for="ngayNhap" class="col-sm-1 pr-0 col-form-label">Ngày tạo:</label>
                    <div class="col-sm-4 pl-0">
                        <input type="date" class="form-control bg-light border-0 bg-white" id="ngayNhap" name="ngay_nhap" value="<?= date('Y-m-d'); ?>" readonly>
                    </div>
                </div>

                <!-- Ngày Xác Nhận -->
                <div class="form-group d-flex align-items-baseline">
                    <label for="ngayXacNhan" class="mr-2 ">Ngày xác nhận:</label>
                    <input type="date" class="form-control bg-light border-0 bg-white" id="ngayXacNhan" name="ngay_xac_nhan" readonly style="width: 200px;">
                </div>

                <!-- Bảng Chi Tiết -->
                <h5 class="mt-4">Chi Tiết Phiếu Nhập</h5>
                <div class="table-responsive">
                    <table id="tableChiTiet" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Loại Tài Sản</th>
                                <th>Tên Tài Sản</th>
                                <th>Số Lượng</th>
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
                                            <option value="<?= htmlspecialchars($tai_san['ten_tai_san']); ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </td>
                                <td><input type="number" class="form-control so-luong" name="so_luong[]" required></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="xoaDong(this)">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-primary mt-2" onclick="themDong()">Thêm Dòng</button>

                <!-- Ghi Chú -->
                <div class="form-group mt-3">
                    <label for="ghiChu">Ghi Chú</label>
                    <textarea class="form-control" id="ghiChu" name="ghi_chu" rows="2"></textarea>
                </div>

                <div class="mt-3 d-flex justify-content-between">
                    <a href="index.php?model=phieunhaptaisan&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
        });
        tbody.appendChild(newRow);
    }

    function xoaDong(button) {
        var row = button.closest('tr');
        if (document.querySelectorAll('#tableChiTiet tbody tr').length > 1) {
            row.remove();
        } else {
            alert('Không thể xóa dòng cuối cùng.');
        }
    }
</script>
