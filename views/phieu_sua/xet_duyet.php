<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieusua&action=index">Phiếu sửa</a></li>
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
        <script>
            setTimeout(function () {
                var alert = document.getElementById('alert-message');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(function () {
                        alert.style.display = 'none';
                    }, 150);
                }
            }, 2000); // 7000 milliseconds = 7 seconds
        </script>
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Xét duyệt phiếu sửa tài sản</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=phieusua&action=xet_duyet&id=<?= $phieuSua['phieu_sua_id'] ?>" id="phieuSuaForm">
                <input type="hidden" name="phieu_sua_id" value="<?= $phieuSua['phieu_sua_id'] ?>">
                
                <div class="form-group row">
                    <label for="nguoiYeuCau" class="col-sm-2 col-form-label">Người yêu cầu:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nguoiYeuCau" value="<?= htmlspecialchars($phieuSua['user_yeu_cau_id']); ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="ngayYeuCau" class="col-sm-2 col-form-label">Ngày yêu cầu:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngayYeuCau" name="ngay_yeu_cau" value="<?= $phieuSua['ngay_yeu_cau']; ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="nguoiSuaChua" class="col-sm-2 col-form-label">Người sửa chữa:</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="nguoiSuaChua" name="user_sua_chua_id">
                            <option value="">Chọn người sửa chữa</option>
                            <?php foreach ($kyThuatUsers as $user): ?>
                                <option value="<?= $user['user_id']; ?>">
                                    <?= htmlspecialchars($user['ten']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="ngaySuaChua" class="col-sm-2 col-form-label">Ngày sửa chữa:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="ngaySuaChua" name="ngay_sua_chua">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="moTa" class="col-sm-2 col-form-label">Mô tả:</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="moTa" name="mo_ta" rows="3" readonly><?= htmlspecialchars($phieuSua['mo_ta']); ?></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="index.php?model=phieusua&action=index" class="btn btn-secondary">Quay lại</a>
                            <div>
                                <button type="submit" name="action" value="approve" class="btn btn-success mr-2" onclick="return confirmApproval()">Phê duyệt</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirmRejection()">Không phê duyệt</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
<script>
    function confirmApproval() {
        if (confirm('Bạn có chắc muốn phê duyệt phiếu sửa này?')) {
            document.getElementById('nguoiSuaChua').required = true;
            document.getElementById('ngaySuaChua').required = true;
            return true; // proceed with form submission
        } else {
            return false; // cancel form submission
        }
    }

    function confirmRejection() {
        if (confirm('Bạn có chắc muốn không phê duyệt phiếu sửa này?')) {
            document.getElementById('nguoiSuaChua').required = false;
            document.getElementById('ngaySuaChua').required = false;
            return true; // proceed with form submission
        } else {
            return false; // cancel form submission
        }
    }
</script>