<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieuthanhly&action=index">Phiếu thanh lý</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Xét duyệt</li>
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
        <!-- <script>
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
        </script> -->
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Xét duyệt phiếu thanh lý tài sản</h6>
        </div>
        <div class="card-body">
            <form method="POST"
                action="index.php?model=phieuthanhly&action=xet_duyet&id=<?= $phieuNhap['phieu_thanh_ly_id'] ?>"
                id="phieuNhapForm">
                <div class="form-group row">
                    <label for="nguoiNhap" class="col-sm-2 col-form-label">Người tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nguoiNhap" value="<?= $phieuNhap['user_name'] ?>"
                            readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="ngayNhap" class="col-sm-2 col-form-label">Ngày tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayNhap" name="ngay_tao"
                            value="<?= $phieuNhap['ngay_tao'] ?>" readonly>
                    </div>
                </div>
                <!-- <div class="form-group row">
                    <label for="ngayXacNhan" class="col-sm-2 col-form-label">Ngày phê duyệt:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayXacNhan" name="ngay_xac_nhan"
                            value="<?= $phieuNhap['ngay_xac_nhan'] ?>" readonly>
                    </div>
                </div> -->

                <h5 class="mt-4">Chi tiết phiếu nhập</h5>
                <table id="chiTietTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên tài sản</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chitietPhieuThanhLy as $chiTiet): ?>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" name="tai_san_ten[]" value="<?= htmlspecialchars($chiTiet['ten_tai_san']) ?>" readonly>
                                     <input type="hidden" name="tai_san_id[]" value="<?= $chiTiet['tai_san_id'] ?>">
                                     <input type="hidden" name="chi_tiet_id[]" value="<?= $chiTiet['chi_tiet_id'] ?>">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="so_luong[]"
                                        value="<?= $chiTiet['so_luong'] ?>" min="1">
                                </td>
                                <input type="hidden" name="nguoi_phe_duyet_id" value="<?= $_SESSION['user_id']?>">
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="form-group mt-3">
                    <label for="ghiChu">Ghi chú:</label>
                    <textarea class="form-control" id="ghiChu" name="ghi_chu_duyet"
                        rows="3"><?= htmlspecialchars($phieuNhap['ghi_chu']) ?></textarea>
                </div>

                  <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="index.php?model=phieuthanhly&action=index" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" name="action" value="check_quantity" class="btn btn-warning mr-2">Kiểm tra số lượng</button>
                                <button type="submit" name="action" value="approve" class="btn btn-success mr-2"
                                    onclick="return confirm('Bạn có chắc muốn phê duyệt phiếu thanh lý này?')">Phê
                                    duyệt</button>

                                <button type="submit" name="action" value="reject" class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn không phê duyệt phiếu thanh lý này?')">Không
                                    phê duyệt</button>
                        </div>
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

    //     cell1.innerHTML = `
    //     <?php $loai_tai_san_list = $this->loaiTaiSanModel->readAll(); ?>
    //     <select class="form-control loai-tai-san" name="loai_tai_san_id[]" required>
    //         <option value="">Chọn loại tài sản</option>
    //         <?php foreach ($loai_tai_san_list as $loai): ?>
    //                         <option value="<?= $loai['loai_tai_san_id']; ?>">
    //                             <?= htmlspecialchars($loai['ten_loai_tai_san']); ?>
    //                         </option>
    //         <?php endforeach; ?>
    //     </select>
    // `;
        
        cell1.innerHTML = '<input type="text" class="form-control" name="tai_san_ten[]" required>';
        cell2.innerHTML = '<input type="number" class="form-control" name="so_luong[]" required min="1">';
        cell3.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="removeTaiSan(this)">Xóa</button>';

        newRow.querySelector('.loai-tai-san').addEventListener('change', function () {
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
        var selectedTaiSanId = selectTaiSan.dataset.selected;

        var hasValidSelection = false;

        Array.from(selectTaiSan.options).forEach(option => {
            if (option.value === "") {
                option.style.display = "";
            } else {
                var isMatch = option.dataset.loai === loaiTaiSanId;
                option.style.display = isMatch ? "" : "none";
                if (isMatch && option.value === selectedTaiSanId) {
                    option.selected = true;
                    hasValidSelection = true;
                }
            }
        });

        if (!hasValidSelection) {
            selectTaiSan.value = "";
        }

        // Cập nhật giá trị đã chọn
        selectTaiSan.dataset.selected = selectTaiSan.value;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.loai-tai-san').forEach(function (select) {
            select.addEventListener('change', function () {
                updateTaiSanOptions(this);
            });
            // Trigger the change event to update tai san options on page load
            updateTaiSanOptions(select);
        });
    });
    // Form validation
    document.getElementById('phieuNhapForm').addEventListener('submit', function (event) {
        var isValid = true;
        var taiSanInputs = document.querySelectorAll('input[name="tai_san_ten[]"]');
        var soLuongInputs = document.querySelectorAll('input[name="so_luong[]"]');

        taiSanInputs.forEach(function (input, index) {
            if (select.value === "") {
                isValid = false;
                select.classList.add('is-invalid');
            } else {
                select.classList.remove('is-invalid');
            }

            var soLuong = soLuongInputs[index].value;
            if (soLuong === "" || parseInt(soLuong) < 1) {
                isValid = false;
                soLuongInputs[index].classList.add('is-invalid');
            } else {
                soLuongInputs[index].classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Vui lòng kiểm tra lại thông tin nhập.');
        }
    });
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.loai-tai-san').forEach(function (select) {
            select.addEventListener('change', function () {
                updateTaiSanOptions(this);
            });
            // Kích hoạt sự kiện change để cập nhật tùy chọn tài sản khi tải trang
            updateTaiSanOptions(select);
        });

        // Thêm sự kiện lắng nghe cho việc thay đổi tài sản
        document.querySelectorAll('.select-tai-san').forEach(function (select) {
            select.addEventListener('change', function () {
                this.dataset.selected = this.value;
            });
        });
    });
</script>