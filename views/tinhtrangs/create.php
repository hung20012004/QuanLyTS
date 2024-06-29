<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=tinhtrang&action=index">Tình Trạng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tạo Tình Trạng</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container">
    <div id="alert-container"></div>
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Thêm tình trạng</h5>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <form action="index.php?model=tinhtrang&action=create" method="POST">
                    <div class=" row mb-3">
                        <div class="col">
                            <label for="vi_tri_id" class="mr-2 mb-0" style="white-space: nowrap;">Tên Vị trí:</label>
                            <select id="vi_tri_id" class="form-control">
                                <option value="">Chọn vị trí</option>
                                <?php foreach ($baoTris as $baoTri): ?>
                                    <option value="<?= htmlspecialchars($baoTri['vi_tri_id']); ?>">
                                        <?= htmlspecialchars($baoTri['ten_vi_tri']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="schedule_id" class="mr-2 mb-0" style="white-space: nowrap;">Chọn lịch:</label>
                            <select id="schedule_id" name="schedule_id" class="form-control">
                                <option value="">Chọn lịch</option>
                                <?php foreach ($baoTris as $lich): ?>
                                    <option value="<?= htmlspecialchars($lich['schedule_id']); ?>" data-vi-tri-id="<?= htmlspecialchars($lich['vi_tri_id']); ?>">
                                        <?= htmlspecialchars($lich['ngay_bat_dau']); ?> - <?= htmlspecialchars($lich['ngay_ket_thuc']); ?>
                                    </option>
                                <?php endforeach; ?>  
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="mo_ta_tinh_trang" class="form-label">Mô tả tình trạng</label>
                            <textarea name="mo_ta_tinh_trang" id="mo_ta_tinh_trang" class="form-control" rows="3" placeholder="Mô tả tình trạng"></textarea>
                        </div>
                    </div>
                
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?model=tinhtrang&action=index" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Tạo</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viTriSelect = document.getElementById('vi_tri_id');
        const scheduleSelect = document.getElementById('schedule_id');
        const scheduleOptions = scheduleSelect.querySelectorAll('option');
        const alertContainer = document.getElementById('alert-container');

        function showAlert(message, type = 'danger') {
            const alertDiv = document.createElement('div');
            alertDiv.id = 'alert-message';
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = message;

            // Xóa thông báo cũ nếu có
            while (alertContainer.firstChild) {
                alertContainer.removeChild(alertContainer.firstChild);
            }

            // Thêm thông báo mới
            alertContainer.appendChild(alertDiv);

            // Tự động ẩn thông báo sau 2 giây
            setTimeout(function () {
                alertDiv.classList.remove('show');
                alertDiv.classList.add('fade');
                setTimeout(function () {
                    alertDiv.remove();
                }, 150);
            }, 2000);
        }

        viTriSelect.addEventListener('change', function() {
            const selectedViTriId = this.value;
            
            scheduleOptions.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else if (option.getAttribute('data-vi-tri-id') === selectedViTriId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });

            scheduleSelect.value = '';
            // Xóa thông báo khi vị trí được chọn
            while (alertContainer.firstChild) {
                alertContainer.removeChild(alertContainer.firstChild);
            }
        });

        scheduleSelect.addEventListener('mousedown', function(event) {
            if (!viTriSelect.value) {
                event.preventDefault();
                showAlert('Vui lòng chọn vị trí trước khi chọn lịch.');
                viTriSelect.focus();
            }
        });
    });
</script>