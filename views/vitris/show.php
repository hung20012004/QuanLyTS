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
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>ID Vị trí</th>
                        <td><?= $viTri['vi_tri_id'] ?></td>
                    </tr>
                    <tr>
                        <th>Tên vị trí</th>
                        <td><?= htmlspecialchars($viTri['ten_vi_tri']) ?></td>
                    </tr>
                </table>
            </div>

            <h6 class="mt-4 mb-3 font-weight-bold text-primary">Danh sách tài sản tại vị trí</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên tài sản</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($viTriChiTiet) && is_array($viTriChiTiet) && !empty($viTriChiTiet)): ?>
                            <?php foreach ($viTriChiTiet as $chiTiet): ?>
                            <tr>
                                <td><?= isset($chiTiet['tai_san_id']) ? htmlspecialchars($chiTiet['tai_san_id']) : 'N/A' ?></td>
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
            </div>
        </div>
    </div>
</div>