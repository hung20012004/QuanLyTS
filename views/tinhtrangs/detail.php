
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=tinhtrang&action=index">Tình Trạng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết Tình Trạng</li>
                </ol>
            </nav>
        </div>
    </div>


    <div class="row mt-3">
        <div class="col">
            <h5 class="mb-3">Chi tiết tình trạng</h5>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">ID: <?= htmlspecialchars($tinhTrang['tinh_trang_id']) ?></h6>
                    <h6 class="mb-0"><?= htmlspecialchars($tinhTrang['ten_vi_tri']) ?></h6>
                </div>
                <div class="card-body">
                    <p><strong>Ngày Bắt Đầu:</strong> <?= htmlspecialchars($tinhTrang['ngay_bat_dau']) ?></p>
                    <p><strong>Ngày Kết Thúc:</strong> <?= htmlspecialchars($tinhTrang['ngay_ket_thuc']) ?></p>
                    <p><strong>Mô Tả:</strong> <?= htmlspecialchars($tinhTrang['mo_ta_tinh_trang']) ?></p> 
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=tinhtrang&action=index" class="btn btn-primary mt-3">Quay Lại</a> 
                    <a href="index.php?model=tinhtrang&action=export" class="btn btn-success">Xuất excel</a>
                </div>
            </div>
        </div>
    </div>
</div>
