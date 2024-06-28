<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=khauhao&action=index">Khấu hao</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết khấu hao</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col">
            <h5 class="mb-3">Chi tiết khấu hao</h5>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><?= htmlspecialchars($taiSan['ten_tai_san']) ?></h6>
                </div>
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
                                }, 150); // Optional: wait for the fade-out transition to complete
                            }
                        }, 2000); // 2000 milliseconds = 2 seconds
                    </script>
                <?php endif; ?>
                <div class="card-body">
                    <p><strong>ID:</strong> <?= htmlspecialchars($taiSan['tai_san_id']) ?></p>
                    <p><strong>Mô Tả:</strong> <?= htmlspecialchars($taiSan['mo_ta']) ?></p>
                    <p><strong>Loại Tài Sản:</strong> <?= htmlspecialchars($taiSan['ten_loai_tai_san']) ?></p>
                    
                    <form id="khauHaoForm" method="post" action="index.php?model=khauhao&action=detail&id=<?= $taiSan['tai_san_id'] ?>">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Ngày nhập</th>
                                    <th>Vị trí</th>
                                    <th>Số lượng trong kho</th>
                                    <th>Nguyên giá</th>
                                    <th>Thời gian trích khấu hao (tháng)</th>
                                    <th>Mức trích khấu hao</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $detail): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($detail['ngay_mua']) ?></td>
                                        <td><?= htmlspecialchars($detail['ten_vi_tri']) ?></td>
                                        <td><?= htmlspecialchars($detail['so_luong_vi_tri']) ?></td>
                                        <td><?= htmlspecialchars(number_format($detail['don_gia'], 0, ',', '.')) ?> VNĐ</td>
                                        <td>
                                            <input type="number" class="form-control thoi-gian-khau-hao" 
                                                   name="thoi_gian_khau_hao[<?= $detail['chi_tiet_id'] ?>]" 
                                                   value="<?= htmlspecialchars($detail['thoi_gian_khau_hao']) ?>" 
                                                   min="1" required>
                                        </td>
                                        <td class="muc-trich-khau-hao" data-row="<?= $detail['chi_tiet_id'] ?>">
                                            <?= htmlspecialchars(number_format($detail['don_gia'] / ($detail['thoi_gian_khau_hao'] ?: 1), 0, ',', '.')) ?> VNĐ
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <button type="submit" class="btn btn-success mt-3">Lưu thay đổi</button>
                        <a href="index.php?model=khauhao&action=index" class="btn btn-primary mt-3">Quay Lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.thoi-gian-khau-hao').forEach(element => {
    element.addEventListener('input', updateMucTrichKhauHao);
});

function updateMucTrichKhauHao() {
    const row = this.closest('tr');
    const nguyenGia = parseFloat(row.querySelector('td:nth-child(4)').textContent.replace(/[^\d]/g, ''));
    const thoiGianKhauHao = parseInt(this.value) || 1;
    const mucTrichKhauHaoElement = row.querySelector('.muc-trich-khau-hao');
    
    let mucTrichKhauHao = nguyenGia / thoiGianKhauHao;
    
    mucTrichKhauHaoElement.textContent = mucTrichKhauHao.toLocaleString('vi-VN') + ' VNĐ';
}
</script>