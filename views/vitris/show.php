<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=vitri&action=index">Vị trí</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết vị trí</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Chi tiết vị trí</h6>
        </div>
        <div class="card-body">
        <div class="form-group col-md-1" style="padding: 0px">
            <p><strong>ID:</strong> <?= htmlspecialchars($viTri['vi_tri_id']); ?></p>
        </div>
        <form method="POST" action="index.php?model=vitri&action=show&id=<?= $viTri['vi_tri_id']; ?>" id="vitriForm">
            <div class="row">
                    <div class="form-group col-md-4">
                        <label for="ten_vi_tri">Tên Vị Trí</label>
                        <input type="text" class="form-control" id="ten_vi_tri" name="ten_vi_tri" value="<?= htmlspecialchars($viTri['ten_vi_tri']); ?>" readonly>
                    </div>
                    <?php if ($viTri['vi_tri_id'] != 1): ?>
                    <div class="form-group col-md-4">
                        <label for="khoa">Tên Khoa</label>
                        <select class="form-control" id="khoa" name="khoa" disabled>
                            <option value="HTTT" <?= $viTri['khoa'] == 'HTTT' ? 'selected' : '' ?>>Hệ thống thông tin</option>
                            <option value="CNTT" <?= $viTri['khoa'] == 'CNTT' ? 'selected' : '' ?>>Công nghệ thông tin</option>
                            <option value="KT" <?= $viTri['khoa'] == 'KT' ? 'selected' : '' ?>>Kỹ Thuật</option>
                            <option value="Co khi" <?= $viTri['khoa'] == 'Co khi' ? 'selected' : '' ?>>Cơ khí</option>
                            <option value="Cong trinh" <?= $viTri['khoa'] == 'Cong trinh' ? 'selected' : '' ?>>Công trình</option>
                            <option value="Moi truong-ATGT" <?= $viTri['khoa'] == 'Moi truong-ATGT' ? 'selected' : '' ?>>Môi trường-ATGT</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($viTri['vi_tri_id'] != 1): ?>
                    <div>
                        <button type="button" id="editButton" class="btn btn-primary mt-2" onclick="enableEdit()">Sửa</button>
                        <button type="submit" id="saveButton" class="btn btn-success mt-2" style="display:none;" onclick="saveEdit()">Lưu</button>
                        <button type="button" id="cancelButton" class="btn btn-secondary mt-2" style="display:none;" onclick="cancelEdit()">Hủy</button>
                    </div>
                <?php endif; ?>
            </form>
            
            
            <h6 class="mt-4 mb-3 font-weight-bold text-primary">Danh sách tài sản tại vị trí</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><strong>Loại tài sản</strong></th>
                            <th><strong>Tên tài sản</strong></th>
                            <th><strong>Số lượng</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($viTriChiTiets) && is_array($viTriChiTiets) && !empty($viTriChiTiets)): ?>
                            <?php foreach ($viTriChiTiets as $chiTiet): ?>
                            <tr>
                                <td><?= isset($chiTiet['ten_loai_tai_san']) ? htmlspecialchars($chiTiet['ten_loai_tai_san']) : 'N/A' ?></td>
                                <td><?= isset($chiTiet['ten_tai_san']) ? htmlspecialchars($chiTiet['ten_tai_san']) : 'N/A' ?></td>
                                <td><?= isset($chiTiet['so_luong']) ? $chiTiet['so_luong'] : 'N/A' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">Không có dữ liệu chi tiết vị trí.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <a href="index.php?model=vitri&action=index" class="btn btn-secondary">Quay Lại</a>
                </div>
            </div>
            
        </div>
    </div>
</div>
<script>
    function enableEdit() {
        document.getElementById('ten_vi_tri').readOnly = false;
        document.getElementById('khoa').disabled = false;
        document.getElementById('editButton').style.display = 'none';
        document.getElementById('saveButton').style.display = 'inline-block';
        document.getElementById('cancelButton').style.display = 'inline-block';
    }
    function cancelEdit() {
        document.getElementById('ten_vi_tri').readOnly = true;
        document.getElementById('khoa').disabled = true;
        document.getElementById('editButton').style.display = 'inline-block';
        document.getElementById('saveButton').style.display = 'none';
        document.getElementById('cancelButton').style.display = 'none';
        document.getElementById('ten_vi_tri').value = "<?= htmlspecialchars($viTri['ten_vi_tri']); ?>";
        document.getElementById('khoa').value = "<?= htmlspecialchars($viTri['khoa']); ?>";
    }
</script>