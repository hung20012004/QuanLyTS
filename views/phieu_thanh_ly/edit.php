<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieuthanhly&action=index">Phiếu nhập</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa phiếu nhập</li>
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
            <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa phiếu nhập tài sản</h6>
        </div>
        
        <div class="card-body">
            <form method="POST"
                action="index.php?model=phieuthanhly&action=edit&id=<?= $phieuNhap['phieu_thanh_ly_id'] ?>"
                id="phieuThanhLyForm">
                <!-- <input type="hidden" name="phieu_nhap_id" value="<?= $phieuNhap['phieu_thanh_ly_id'] ?>"> -->

                <div class="form-group row">
                    <label for="nguoiNhap" class="col-sm-2 col-form-label">Người tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nguoiNhap"
                            value="<?= htmlspecialchars($_SESSION['ten']); ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="ngayNhap" class="col-sm-2 col-form-label">Ngày tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayNhap" name="ngay_tao"
                            value="<?= $phieuNhap['ngay_tao'] ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="ngayXacNhan" class="col-sm-2 col-form-label">Ngày phê duyệt:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayXacNhan" name="ngay_xac_nhan"
                            value="<?= $phieuNhap['ngay_xac_nhan'] ?>" readonly>
                    </div>
                </div>

                <h5 class="mt-4">Chi tiết phiếu nhập</h5>
                <table id="chiTietTable" class="table table-bordered">
                    <thead>
                        <tr>
                           <th>Tên tài sản</th>
                            <th>Số lượng</th>
                            <th>Tình trạng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chiTietPhieuThanhLy as $chiTiet): ?>
                            <tr>
                                <td>
                                    <select class="form-control select-tai-san" name="tai_san_id[]" required
                                        data-selected="<?= $chiTiet['tai_san_id'] ?>">
                                        <option value="">Chọn tài sản</option>
                                        <?php foreach ($tai_san_list as $tai_san): ?>
                                            <option value="<?= $tai_san['tai_san_id']; ?>"
                                                data-loai="<?= $tai_san['loai_tai_san_id']; ?>"
                                                <?= $tai_san['tai_san_id'] == $chiTiet['tai_san_id'] ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($tai_san['ten_tai_san']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="so_luong[]"
                                        value="<?= $chiTiet['so_luong'] ?>" required min="1">
                                </td>
                                 <td>
                                <select class="form-control select-tinh-trang" name="tinh_trang[]" required>
                                    <option value="Moi" <?= ($chiTiet['tinh_trang'] == 'Moi') ? 'selected' : ''; ?>>Mới</option>
                                    <option value="Tot" <?= ($chiTiet['tinh_trang'] == 'Tot') ? 'selected' : ''; ?>>Tốt</option>
                                    <option value="Kha" <?= ($chiTiet['tinh_trang'] == 'Kha') ? 'selected' : ''; ?>>Khá</option>
                                    <option value="TrungBinh" <?= ($chiTiet['tinh_trang'] == 'TrungBinh') ? 'selected' : ''; ?>>Trung bình</option>
                                    <option value="Kem" <?= ($chiTiet['tinh_trang'] == 'Kem') ? 'selected' : ''; ?>>Kém</option>
                                    <option value="Hong" <?= ($chiTiet['tinh_trang'] == 'Hong') ? 'selected' : ''; ?>>Hỏng</option>
                                </select>
                               </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="removeTaiSan(this)">Xóa</button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary" onclick="addTaiSan()">Thêm tài sản</button>

                <div class="form-group mt-3">
                    <label for="ghiChu">Ghi chú:</label>
                    <textarea class="form-control" id="ghiChu" name="ghi_chu"
                        rows="3"><?= htmlspecialchars($phieuNhap['ghi_chu']) ?></textarea>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="index.php?model=phieuthanhly&action=index" class="btn btn-secondary">Hủy</a>
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
        var cell4 = newRow.insertCell(3);

         //  <option value="Hong" <?= ($chiTiet['tinh_trang'] == 'Hong') ? 'selected' : ''; ?>>Hỏng</option>

       cell1.innerHTML = `
        <select class="form-control select-tai-san" name="tai_san_id[]" required>
            <option value=""  >Chọn tài sản</option>
                <?php foreach ($tai_san_list as $tai_san): ?>
                    <option value="<?= $tai_san['tai_san_id']; ?>">
                         <?= htmlspecialchars($tai_san['ten_tai_san']); ?>
                     </option>
                <?php endforeach; ?>
                </select>
    `;

        cell2.innerHTML = '<input type="number" class="form-control" name="so_luong[]" required min="1">';
        cell3.innerHTML = `<select class="form-control select-tinh-trang" name="tinh_trang[]" required>
                                    <option value="Moi" >Mới</option>
                                    <option value="Tot">Tốt</option>
                                    <option value="Kha">Khá</option>
                                    <option value="TrungBinh" >Trung bình</option>
                                    <option value="Kem" >Kém</option>
                                    <option value="Hong">Hỏng</option>
                                </select>

                               
        `;
        cell4.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="removeTaiSan(this)">Xóa</button>';

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
    document.getElementById('phieuThanhLyForm').addEventListener('submit', function (event) {
        var isValid = true;
        var taiSanSelects = document.querySelectorAll('.select-tai-san');
        var soLuongInputs = document.querySelectorAll('input[name="so_luong[]"]');

        taiSanSelects.forEach(function (select, index) {
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