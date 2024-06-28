<!-- views/taisan/detail.php -->
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=taisan&action=index">Tài Sản</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi Tiết Tài Sản</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h5 class="mb-3">Chi Tiết Tài Sản</h5>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><?= htmlspecialchars($taiSan['ten_tai_san']) ?></h6>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> <?= htmlspecialchars($taiSan['tai_san_id']) ?></p>
                    <p><strong>Mô Tả:</strong> <?= htmlspecialchars($taiSan['mo_ta']) ?></p>
                    <p><strong>Loại Tài Sản:</strong> <?= htmlspecialchars($taiSan['ten_loai_tai_san']) ?></p>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ngày nhập</th>
                                <th>Số lượng trong kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $detail): ?>
                                <tr>
                                    <td><?= htmlspecialchars($detail['ngay_mua']) ?></td>
                                    <td><?= htmlspecialchars($detail['so_luong']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <a href="index.php?model=taisan&action=index" class="btn btn-primary mt-3">Quay Lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
