<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tạo phiếu trả tài sản</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=phieutra&action=create" id="phieuTaoForm">
                <!-- Các trường thông tin chung của phiếu trả -->
                <div class="form-group row">
                    <label for="nguoiNhan" class="col-sm-2 col-form-label">Người nhận yêu cầu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nguoiNhan" name="nguoi_nhan" value="<?= $nguoiNhan['ten']; ?>" readonly>
                        <input type="hidden" name="user_nhan_id" value="<?= $nguoiNhan['user_id']; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="ngayTao" class="col-sm-2 col-form-label">Ngày tạo phiếu:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayTao" name="ngay_tao" value="<?= date('Y-m-d'); ?>" readonly>
                    </div>
                </div>

                <h5 class="mt-4">Yêu cầu trả các tài sản:</h5>
                <table id="chiTietTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Vị trí</th>
                            <th>Tên tài sản</th>
                            <th>Số lượng</th>
                            <th>Tình trạng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select class="form-control select-vi-tri" name="vi_tri_id[]" required onchange="updateTaiSanOptions(this)">
                                    <option value="">Chọn vị trí</option>
                                    <?php foreach ($vi_tri_list as $vi_tri): ?>
                                        <option value="<?= $vi_tri['vi_tri_id']; ?>">
                                            <?= htmlspecialchars($vi_tri['ten_vi_tri']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control select-tai-san" name="tai_san_id[]" required>
                                    <option value="">Chọn tài sản</option>
                                    <?php foreach ($tai_san_list as $tai_san): ?>
                                        <option value="<?= $tai_san['tai_san_id']; ?>" data-vi_tri="<?= $tai_san['vi_tri_id']; ?>">
                                            <?= htmlspecialchars($tai_san['ten_tai_san']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="so_luong[]" required min="1">
                            </td>
                            <td>
                                <select class="form-control select-tinh-trang" name="tinh_trang[]" required>
                                    <option value="Moi">Mới</option>
                                    <option value="Tot">Tốt</option>
                                    <option value="Kha">Khá</option>
                                    <option value="TrungBinh">Trung bình</option>
                                    <option value="Kem">Kém</option>
                                    <option value="Hong">Hỏng</option>
                                </select>
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

                <div class="form-group row mt-3">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Lưu và gửi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateTaiSanOptions(selectElement) {
        // Tìm dòng cha của phần tử select vị trí và select tài sản
        var row = selectElement.closest('tr');
        
        // Tìm phần tử select tài sản trong dòng cha
        var taiSanSelect = row.querySelector('.select-tai-san');
        var taiSanOptions = taiSanSelect.querySelectorAll('option');

        // Lấy giá trị vị trí đã chọn
        var selectedViTri = selectElement.value;

        // Duyệt qua từng tùy chọn của select tài sản
        taiSanOptions.forEach(function(option) {
            if (option.value === "") {
                option.style.display = ""; // Hiển thị tùy chọn mặc định
            } else {
                // So sánh giá trị data-vi_tri của tùy chọn với vị trí đã chọn
                option.style.display = option.getAttribute('data-vi_tri') === selectedViTri ? "" : "none";
            }
        });

        // Đặt giá trị của select tài sản về rỗng
        taiSanSelect.value = "";
    }

    // Gắn sự kiện 'change' cho tất cả các phần tử select vị trí ban đầu
    document.querySelectorAll('.select-vi-tri').forEach(function(select) {
        select.addEventListener('change', function() {
            updateTaiSanOptions(select); // Gọi hàm cập nhật tùy chọn tài sản
        });
    });

    // Hàm thêm dòng mới cho bảng chi tiết
    function addTaiSan() {
        var table = document.getElementById('chiTietTable').getElementsByTagName('tbody')[0];
        var newRow = table.insertRow(table.rows.length);
        
        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);
        var cell4 = newRow.insertCell(3);
        var cell5 = newRow.insertCell(4);

        // Tạo select box cho vị trí
        var selectViTri = document.createElement('select');
        selectViTri.className = 'form-control select-vi-tri';
        selectViTri.name = 'vi_tri_id[]';
        selectViTri.required = true;
        var viTriOptions = document.querySelector('.select-vi-tri').innerHTML;
        selectViTri.innerHTML = viTriOptions;
        cell1.appendChild(selectViTri);

        // Tạo select box cho tài sản
        var selectTaiSan = document.createElement('select');
        selectTaiSan.className = 'form-control select-tai-san';
        selectTaiSan.name = 'tai_san_id[]';
        selectTaiSan.required = true;
        var taiSanOptions = document.querySelector('.select-tai-san').innerHTML;
        selectTaiSan.innerHTML = taiSanOptions;
        cell2.appendChild(selectTaiSan);

        // Tạo input cho số lượng
        var inputSoLuong = document.createElement('input');
        inputSoLuong.type = 'number';
        inputSoLuong.className = 'form-control';
        inputSoLuong.name = 'so_luong[]';
        inputSoLuong.required = true;
        inputSoLuong.min = '1';
        cell3.appendChild(inputSoLuong);

        // Tạo select box cho tình trạng
        var selectTinhTrang = document.createElement('select');
        selectTinhTrang.className = 'form-control select-tinh-trang';
        selectTinhTrang.name = 'tinh_trang[]';
        selectTinhTrang.required = true;
        var tinhTrangOptions = document.querySelector('.select-tinh-trang').innerHTML;
        selectTinhTrang.innerHTML = tinhTrangOptions;
        cell4.appendChild(selectTinhTrang);

        // Tạo nút xóa
        var buttonXoa = document.createElement('button');
        buttonXoa.type = 'button';
        buttonXoa.className = 'btn btn-danger btn-sm';
        buttonXoa.textContent = 'Xóa';
        buttonXoa.onclick = function() {
            removeTaiSan(buttonXoa);
        };
        cell5.appendChild(buttonXoa);

        // Thêm sự kiện cập nhật tài sản khi vị trí thay đổi
        selectViTri.addEventListener('change', function() {
            updateTaiSanOptions(selectViTri);
        });
    }

    // Hàm xóa dòng trong bảng chi tiết
    function removeTaiSan(button) {
        var row = button.closest('tr');
        if (document.querySelectorAll('#chiTietTable tbody tr').length > 1) {
            row.parentNode.removeChild(row);
        } else {
            alert('Không thể xóa dòng cuối cùng.');
        }
    }

    // Gắn sự kiện 'click' cho nút thêm tài sản
    document.querySelector('.btn-primary').addEventListener('click', function() {
        addTaiSan();
    });
});
</script>