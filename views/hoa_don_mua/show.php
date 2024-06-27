<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=hoadonmua&action=index">Hóa Đơn</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi Tiết Hóa Đơn</li>
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
            }, 7000);
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Chi Tiết Hóa Đơn</h5>
                <a href="index.php?model=hoadonmua&action=export&id=<?= $hoadon['hoa_don_id']; ?>" class="btn btn-success">Xuất Hóa Đơn</a>
            </div>
        </div>
        <div class="card-body">
            <!-- Thông tin chung của hóa đơn -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Ngày Mua:</strong> <?= date('d/m/Y', strtotime($hoadon['ngay_mua'])); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Nhà Cung Cấp:</strong> <?= htmlspecialchars($hoadon['ten_nha_cung_cap']); ?></p>
                </div>
            </div>

            <!-- Bảng Chi Tiết Hóa Đơn -->
            <h5 class="mt-4">Chi Tiết Hóa Đơn</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="chiTietTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Loại Tài Sản</th>
                            <th>Tên Tài Sản</th>
                            <th>Số Lượng</th>
                            <th>Đơn Giá (VNĐ)</th>
                            <th>Thành Tiền (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chitiethoadon as $index => $chitiet): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($chitiet['ten_loai_tai_san']); ?></td>
                                <td><?= htmlspecialchars($chitiet['ten_tai_san']); ?></td>
                                <td class="so-luong"><?= number_format($chitiet['so_luong'], 0, ',', '.'); ?></td>
                                <td class="don-gia"><?= number_format($chitiet['don_gia'], 0, ',', '.'); ?></td>
                                <td class="thanh-tien"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tổng Giá Trị -->
            <div class="form-group mt-3">
                <h5><strong>Tổng Giá Trị: <span id="tongGiaTri"></span> VNĐ</strong></h5>
            </div>

            <div class="mt-3 d-flex justify-content-between">
                <a href="index.php?model=hoadonmua&action=index" class="btn btn-secondary">Quay Lại</a>
                <a href="index.php?model=hoadonmua&action=edit&id=<?= $hoadon['hoa_don_id']; ?>" class="btn btn-primary">Sửa Hóa Đơn</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }

    function tinhThanhTien() {
        let tongGiaTri = 0;
        document.querySelectorAll('#chiTietTable tbody tr').forEach(function(row) {
            let soLuong = parseFloat(row.querySelector('.so-luong').textContent.replace(/\./g, '').replace(',', '.')) || 0;
            let donGia = parseFloat(row.querySelector('.don-gia').textContent.replace(/\./g, '').replace(',', '.')) || 0;
            let thanhTien = soLuong * donGia;
            row.querySelector('.thanh-tien').textContent = formatNumber(Math.round(thanhTien));
            tongGiaTri += thanhTien;
        });
        document.getElementById('tongGiaTri').textContent = formatNumber(Math.round(tongGiaTri));
    }

    tinhThanhTien();
});
</script>