<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=vitri&action=index">Vị Trí</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sửa Vị Trí</li>
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
                <h5 class="card-title mb-0">Sửa Vị Trí</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=vitri&action=edit&id=<?= $viTri['vi_tri_id']; ?>" id="vitriForm">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="ten_vi_tri">Vị trí</label>
                        <input type="text" class="form-control" id="ten_vi_tri" name="ten_vi_tri" value="<?= htmlspecialchars($viTri['ten_vi_tri']); ?>" required>
                    </div>
                </div>

                <h5 class="mt-4">Chi Tiết Vị Trí</h5>
                <div class="table-responsive">
                    <table id="tableChiTiet" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Loại tài sản</th>
                                <th>Tên Tài Sản</th>
                                <th>Ngày mua</th>
                                <th>Số Lượng</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($viTriChiTiets as $index => $detail): ?>
                            <tr id="row<?= $index; ?>">
                                <td>
                                    <select class="form-control loai-tai-san" name="loai_tai_san_id[]" required>
                                        <option value="">Chọn loại tài sản</option>
                                        <?php foreach ($loaiTaiSans as $loai): ?>
                                            <option value="<?= $loai['loai_tai_san_id']; ?>" <?= ($loai['loai_tai_san_id'] == $detail['loai_tai_san_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($loai['ten_loai_tai_san']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input list="taiSanList<?= $index; ?>" class="form-control select-tai-san" name="ten_tai_san[]" value="<?= htmlspecialchars($detail['ten_tai_san']) ?>" placeholder="Chọn hoặc nhập tên tài sản" required>
                                    <input type="hidden" name="chi_tiet_id[]" value="<?= $detail['chi_tiet_id'] ?>">
                                    <datalist id="taiSanList<?= $index; ?>">
                                        <?php foreach ($taiSansInStock as $tai_san): 
                                            if ($tai_san['loai_tai_san_id'] == $detail['loai_tai_san_id']): ?>
                                            <option value="<?= htmlspecialchars($tai_san['ten_tai_san']); ?>" data-loai-tai-san-id="<?= $tai_san['loai_tai_san_id']; ?>" data-tai-san-id="<?= $tai_san['tai_san_id']; ?>">
                                                <?= htmlspecialchars($tai_san['ten_tai_san']); ?>
                                            </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </datalist>
                                </td>
                                <td><input type="date" class="form-control ngay-mua" name="ngay_mua[]" value="<?= $detail['ngay_mua'] ?>" required></td>
                                <td><input type="number" class="form-control so-luong" name="so_luong[]" value="<?= $detail['so_luong'] ?>" required></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="xoaDong(this)">Xóa</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-primary mt-2" onclick="themDong()">Thêm Dòng</button>

                <div class="mt-3 d-flex justify-content-between">
                    <a href="index.php?model=vitri&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function initializeTaiSanListeners() {
        document.querySelectorAll('.select-tai-san').forEach(function(input) {
            input.addEventListener('input', handleTaiSanSelection);
        });
    }

    function handleTaiSanSelection() {
        var datalist = this.list;
        var option = Array.from(datalist.options).find(opt => opt.value === this.value);
        var row = this.closest('tr');
        var loaiTaiSanSelect = row.querySelector('.loai-tai-san');
        var taiSanIdInput = row.querySelector('.tai-san-id');
        
        if (option) {
            var loaiTaiSanId = option.getAttribute('data-loai-tai-san-id');
            var taiSanId = option.getAttribute('data-tai-san-id');
            loaiTaiSanSelect.value = loaiTaiSanId;
            loaiTaiSanSelect.disabled = true;
            taiSanIdInput.value = taiSanId;
        } else {
            loaiTaiSanSelect.disabled = false;
            loaiTaiSanSelect.value = '';
            taiSanIdInput.value = '';
        }
    }

    function themDong() {
        var tbody = document.querySelector("#tableChiTiet tbody");
        var rowCount = tbody.rows.length;

        var newRow = document.createElement('tr');
        newRow.id = 'row' + rowCount;

        var loaiTaiSanCell = document.createElement('td');
        var loaiTaiSanSelect = document.createElement('select');
        loaiTaiSanSelect.className = 'form-control loai-tai-san';
        loaiTaiSanSelect.name = 'loai_tai_san[]';
        loaiTaiSanSelect.innerHTML = '<option value="">Chọn loại tài sản</option>';
        // Thêm các tùy chọn loại tài sản
        <?php foreach ($loaiTaiSans as $loaiTaiSan): ?>
            loaiTaiSanSelect.innerHTML += '<option value="<?= $loaiTaiSan['loai_tai_san_id']; ?>"><?= htmlspecialchars($loaiTaiSan['ten_loai_tai_san']); ?></option>';
        <?php endforeach; ?>
        loaiTaiSanSelect.addEventListener('change', function() {
            updateTaiSanList(loaiTaiSanSelect);
        });
        loaiTaiSanCell.appendChild(loaiTaiSanSelect);

        var taiSanCell = document.createElement('td');
        var taiSanSelect = document.createElement('select');
        taiSanSelect.className = 'form-control tai-san';
        taiSanSelect.name = 'tai_san_id[]';
        taiSanSelect.innerHTML = '<option value="">Chọn tài sản</option>';
        taiSanCell.appendChild(taiSanSelect);

        var ngayMuaCell = document.createElement('td');
        var ngayMuaInput = document.createElement('input');
        ngayMuaInput.type = 'date';
        ngayMuaInput.className = 'form-control';
        ngayMuaInput.name = 'ngay_mua[]';
        ngayMuaInput.required = true;
        ngayMuaCell.appendChild(ngayMuaInput);

        var soLuongCell = document.createElement('td');
        var soLuongInput = document.createElement('input');
        soLuongInput.type = 'number';
        soLuongInput.className = 'form-control';
        soLuongInput.name = 'so_luong[]';
        soLuongInput.required = true;
        soLuongCell.appendChild(soLuongInput);

        var hanhDongCell = document.createElement('td');
        var xoaButton = document.createElement('button');
        xoaButton.type = 'button';
        xoaButton.className = 'btn btn-danger btn-sm';
        xoaButton.textContent = 'Xóa';
        xoaButton.addEventListener('click', function() {
            xoaDong(this);
        });
        hanhDongCell.appendChild(xoaButton);

        newRow.appendChild(loaiTaiSanCell);
        newRow.appendChild(taiSanCell);
        newRow.appendChild(ngayMuaCell);
        newRow.appendChild(soLuongCell);
        newRow.appendChild(hanhDongCell);

        tbody.appendChild(newRow);
        updateRowNumbers();
    }

    function updateTaiSanList(select) {
        var row = select.closest('tr');
        var loaiTaiSanId = select.value;
        var taiSanSelect = row.querySelector('.tai-san');

        // Xóa các tùy chọn hiện tại
        taiSanSelect.innerHTML = '<option value="">Chọn tài sản</option>';

        // Thêm các tùy chọn tài sản mới dựa trên loại tài sản đã chọn
        var taiSansInStock = <?php echo json_encode($taiSansInStock); ?>;
        taiSansInStock.forEach(function(taiSan) {
            if (taiSan.loai_tai_san_id == loaiTaiSanId) {
                var option = document.createElement('option');
                option.value = taiSan.tai_san_id;
                option.text = taiSan.ten_tai_san;
                taiSanSelect.appendChild(option);
            }
        });
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

    function updateRowNumbers() {
        document.querySelectorAll('#tableChiTiet tbody tr').forEach(function(row, index) {
            row.id = 'row' + index;
        });
    }

    document.querySelectorAll('.loai-tai-san').forEach(function(select) {
        select.addEventListener('change', function() {
            updateTaiSanList(select);
        });
    });

</script>
