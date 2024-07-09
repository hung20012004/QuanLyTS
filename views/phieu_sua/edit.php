<div class="container-fluid">
        <div class="row mt-3">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?model=phieusua&action=index">Phiếu sửa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa phiếu sửa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa phiếu sửa</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?model=phieusua&action=edit&id=<?= $phieuSua['phieu_sua_id'] ?>" id="phieuSuaForm">
                    <input type="hidden" name="phieu_sua_id" value="<?= $phieuSua['phieu_sua_id'] ?>">

                    <div class="form-group row">
                        <label for="nguoiYeuCau" class="col-sm-2 col-form-label">Người yêu cầu:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nguoiYeuCau" value="<?= htmlspecialchars($_SESSION['ten']); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ngayYeuCau" class="col-sm-2 col-form-label">Ngày yêu cầu:</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="ngayYeuCau" name="ngay_yeu_cau" value="<?= $phieuSua['ngay_yeu_cau'] ?>" readonly>
                        </div>
                    </div>
                    <!-- <div class="form-group row">
                        <label for="khoa" class="col-sm-2 col-form-label">Khoa:</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="khoa" name="khoa">
                                <option value="">Chọn khoa</option>
                                <option value="HTTT" >HTTT</option>
                                <option value="CNTT" >CNTT</option>
                                <option value="KT" >KT</option>
                                <option value="Co khi" >Cơ khí</option>
                                <option value="Cong trinh" >Công trình</option>
                                <option value="Moi truong-ATGT" >Môi trường-ATGT</option>
                            </select>
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <label for="viTriId" class="col-sm-2 col-form-label">Vị trí:</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="viTriId" name="vi_tri_id" required>
                                <option value="">Chọn vị trí</option>
                                <?php foreach ($viTris as $viTri): ?>
                                    <option value="<?= $viTri['vi_tri_id']; ?>" data-khoa="<?= $viTri['khoa']; ?>" <?= $viTri['vi_tri_id'] == $phieuSua['vi_tri_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($viTri['ten_vi_tri']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="moTa" class="col-sm-2 col-form-label">Mô tả:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="moTa" name="mo_ta" rows="3"><?= htmlspecialchars($phieuSua['mo_ta']) ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="index.php?model=phieusua&action=index" class="btn btn-secondary">Hủy</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const khoaSelect = document.getElementById('khoa');
        const viTriSelect = document.getElementById('viTriId');
        
        // Lưu trữ tất cả các options vị trí khi tải trang
        let allViTriOptions = Array.from(viTriSelect.options);
        
        khoaSelect.addEventListener('change', function() {
            const selectedKhoa = khoaSelect.value;
            
            // Lọc danh sách vị trí dựa trên khoa đã chọn
            const filteredOptions = allViTriOptions.filter(option => {
                return option.dataset.khoa === selectedKhoa || option.value === '';
            });
            
            // Xóa tất cả các option hiện có và thêm lại các option đã lọc
            viTriSelect.innerHTML = '';
            filteredOptions.forEach(option => {
                viTriSelect.appendChild(option.cloneNode(true));
            });
            
            // Kiểm tra nếu đã chọn vị trí có giá trị khoa thì vô hiệu hóa dropdown khoa
            const selectedViTri = viTriSelect.options[viTriSelect.selectedIndex];
            if (selectedViTri.dataset.khoa !== undefined && selectedViTri.dataset.khoa !== '') {
                khoaSelect.disabled = true;
            } else {
                khoaSelect.disabled = false;
            }
        });
        
        // Bắt sự kiện khi thay đổi vị trí để kiểm tra giá trị khoa
        viTriSelect.addEventListener('change', function() {
            const selectedViTri = viTriSelect.options[viTriSelect.selectedIndex];
            if (selectedViTri.dataset.khoa !== undefined && selectedViTri.dataset.khoa !== '') {
                khoaSelect.disabled = true;
            } else {
                khoaSelect.disabled = false;
            }
        });
        
        // Kiểm tra ngay khi tải trang nếu đã chọn vị trí có giá trị khoa
        const selectedViTri = viTriSelect.options[viTriSelect.selectedIndex];
        if (selectedViTri.dataset.khoa !== undefined && selectedViTri.dataset.khoa !== '') {
            khoaSelect.disabled = true;
        }
    });
    </script> -->