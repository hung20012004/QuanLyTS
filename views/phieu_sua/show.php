<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieusua&action=index">Phiếu sửa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Phiếu sửa</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=phieusua&action=edit&id=<?= $phieuSua['phieu_sua_id'] ?>" id="phieuSuaForm">
                <input type="hidden" name="phieu_sua_id" value="<?= $phieuSua['phieu_sua_id'] ?>">

                <div class="form-group row">
                    <label for="nguoiYeuCau" class="col-sm-2 col-form-label">Người yêu cầu:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="nguoiYeuCau" value="<?= htmlspecialchars($phieuSua['user_yeu_cau_name']); ?>" readonly>
                    </div>
                    <label for="ngayYeuCau" class="col-sm-2 col-form-label">Ngày yêu cầu:</label>
                    <div class="col-sm-2">
                        <?php $ngayYeuCauFormatted = date('d-m-Y', strtotime($phieuSua['ngay_yeu_cau'])); ?>
                        <input type="text" class="form-control" id="ngayYeuCau" value="<?= $ngayYeuCauFormatted ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="viTriId" class="col-sm-2 col-form-label">Vị trí:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="viTriId" name="vi_tri_id" value="<?= htmlspecialchars($phieuSua['ten_vi_tri']); ?>" readonly>
                    </div>
                    <label for="khoa" class="col-sm-2 col-form-label">Khoa:</label>
                    <div class="col-sm-2">
                        <?php
                            $khoaText = '';
                            switch ($phieuSua['khoa']) {
                                case 'HTTT':
                                    $khoaText = 'Hệ thống thông tin';
                                    break;
                                case 'CNTT':
                                    $khoaText = 'Công nghệ thông tin';
                                    break;
                                case 'KT':
                                    $khoaText = 'Kỹ thuật';
                                    break;
                                case 'Co khi':
                                    $khoaText = 'Cơ khí';
                                    break;
                                case 'Cong trinh':
                                    $khoaText = 'Công trình';
                                    break;    
                                case 'Moi truong-ATGT':
                                    $khoaText = 'Môi trường - ATGT';
                                    break;
                                default:
                                    $khoaText = '';
                                    break;
                            }
                            ?>
                        <input type="text" class="form-control" id="khoa" name="khoa" value="<?= $khoaText; ?>" readonly>
                    </div>
                </div>
                
                <?php if (!empty($phieuSua['user_sua_chua_name'] && !empty($phieuSua['ngay_sua_chua']) && $phieuSua['ngay_sua_chua'] != '0000-00-00')): ?>
                <div class="form-group row">
                    <label for="nguoiSuaChuaId" class="col-sm-2 col-form-label">Người sửa chữa:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="nguoiSuaChuaId" name="user_sua_chua_id" value="<?= htmlspecialchars($phieuSua['user_sua_chua_name']); ?>" readonly ?>
                    </div>
                    <label for="ngaySuaChua" class="col-sm-2 col-form-label">Ngày sửa chữa:</label>
                    <div class="col-sm-2">
                        <?php $ngaySuaChuaFormatted = date('d-m-Y', strtotime($phieuSua['ngay_sua_chua'])); ?>
                        <input type="text" class="form-control" id="ngaySuaChua" value="<?= $ngaySuaChuaFormatted ?>" readonly>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($phieuSua['ngay_hoan_thanh']) && $phieuSua['ngay_hoan_thanh'] != '0000-00-00'): ?>
                <div class="form-group row">
                    <label for="ngayHoanThanh" class="col-sm-2 col-form-label">Ngày hoàn thành:</label>
                    <div class="col-sm-2">
                        <?php $ngayHoanThanhFormatted = date('d-m-Y', strtotime($phieuSua['ngay_hoan_thanh'])); ?>
                        <input type="text" class="form-control" id="ngayHoanThanh" value="<?= $ngayHoanThanhFormatted ?>" readonly>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="form-group row">
                    <label for="moTa" class="col-sm-2 col-form-label">Mô tả:</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="moTa" name="mo_ta" rows="3" readonly><?= htmlspecialchars($phieuSua['mo_ta']) ?></textarea>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="trangThai" class="col-sm-2 col-form-label">Trạng thái:</label>
                    <div class="col-sm-2">
                        <?php
                        $trangThaiText = '';
                        switch ($phieuSua['trang_thai']) {
                            case 'DaGui':
                                $trangThaiText = 'Đã gửi';
                                break;
                            case 'DaNhan':
                                $trangThaiText = 'Đã nhận';
                                break;
                            case 'DaHoanThanh':
                                $trangThaiText = 'Đã hoàn thành';
                                break;
                            case 'Huy':
                                $trangThaiText = 'Đã hủy';
                                break;
                            default:
                                $trangThaiText = '';
                                break;
                        }
                        ?>
                        <input type="text" class="form-control" id="trangThai" name="trang_thai" value="<?= $trangThaiText ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <a href="index.php?model=phieusua&action=index" class="btn btn-secondary">Quay lại</a>
                        <?php if ($_SESSION['role'] == 'KyThuat' && $phieuSua['trang_thai'] != 'DaHoanThanh'): ?>
                            <a href="index.php?model=phieusua&action=hoan_thanh&id=<?php echo $phieu['phieu_sua_id']; ?>"
                                class="btn btn-success">Hoàn thành</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
