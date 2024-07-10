<div class="container-fluid">
    <div class="row mt-3">  
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieuthanhly&action=index">Phiếu Thanh Lý</a></li>
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
            }, 7000); // 7000 milliseconds = 7 seconds
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản lý phiếu thanh lý</h5>
                <div>
                    <?php if ($_SESSION['role'] == 'NhanVienQuanLy'): ?>
                        <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                        <a href="index.php?model=phieuthanhly&action=create" class="btn btn-primary">Thêm mới</a>
                       
                    <?php elseif ($_SESSION['role'] == 'QuanLy'): ?>
                        <a id="toggleSearch" class="btn btn-secondary">Tìm kiếm</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-body">
             <!-- <form id="searchForm" class="mb-3" style="display: none;">
               <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayBatDau" class="mr-2 mb-0" style="white-space: nowrap;">Từ
                                ngày:&nbsp&nbsp&nbsp</label>
                            <input type="date" id="ngayBatDau" class="form-control" placeholder="Từ ngày">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="ngayKetThuc" class="mr-2 mb-0" style="white-space: nowrap;">Đến ngày:</label>
                            <input type="date" id="ngayKetThuc" class="form-control" placeholder="Đến ngày">
                        </div>
                    </div> -->
                   <div id="searchForm" class="mb-3">
                        <form action="index.php?model=phieuthanhly&action=search" method="post" class="form-inline">
                            <div class="form-group mr-3">
                                <label for="ngay_tao_tk" class="mr-2">Ngày tạo phiếu</label>
                                <input type="date" class="form-control" id="ngay_tao_tk" name="ngay_tao_tk" placeholder="Ngày tạo phiếu">
                            </div>
                            <div class="form-group mr-3">
                                <label for="ngay_pd_tk" class="mr-2">Ngày phê duyệt</label>
                                <input type="date" class="form-control" id="ngay_pd_tk" name="ngay_pd_tk" placeholder="Ngày phê duyệt phiếu">
                            </div>
                            <button type="submit" class="btn btn-success" name = "btn_tim_kiem">Tìm kiếm</button>
                        </form>
                    </div>
                <!-- </div>
            </form> -->
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>Mã số phiếu</th>
                            <th>Ngày tạo phiếu</th>
                            <th>Ngày phê duyệt</th>
                            <th>Trạng thái</th>
                            <th>Ngày thanh lý</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phieuThanhLy as $phieu): ?>
                                <tr>
                                    <td class="text-center"><?php echo $phieu['phieu_thanh_ly_id']; ?></td>
                                    <td class="text-center"><?= date('d-m-Y', strtotime($phieu['ngay_tao'])) ?></td>
                                    <td class="text-center">
                                        <?= $phieu['trang_thai'] != 'DangChoPheDuyet' ? (!empty($phieu['ngay_xac_nhan']) ? date('d-m-Y', strtotime($phieu['ngay_xac_nhan'])) : '') : ''; ?>
                                    </td>
                                    <td class="text-center">
                                     <?php
                                            if ($phieu['trang_thai'] == 'DangChoPheDuyet') {
                                                echo 'Đang chờ phê duyệt';
                                            } elseif ($phieu['trang_thai'] == 'KhongDuyet') {
                                                echo 'Không phê duyệt';
                                            } elseif ($phieu['trang_thai'] == 'DaThanhLy') {
                                                echo 'Đã thanh lý';
                                            } else {
                                                echo 'Đã phê duyệt';
                                            }
                                            ?>
                                    </td>
                                    <td class="text-center">
                                       <?= $phieu['trang_thai'] == 'DaThanhLy' ? (!empty($phieu['ngay_thanh_ly']) ? date('d-m-Y', strtotime($phieu['ngay_thanh_ly'])) : '') : '' ?>
                                    </td>
                                    <td class="d-flex justify-content-center">
                                        <a href="index.php?model=phieuthanhly&action=show&id=<?php echo $phieu['phieu_thanh_ly_id']; ?>"
                                            class="btn btn-info btn-sm mx-2">Xem</a>
                                        <?php if ($phieu['trang_thai'] == 'DangChoPheDuyet' && $_SESSION['role']=='NhanVienQuanLy'): ?>
                                            <a href="index.php?model=phieuthanhly&action=edit&id=<?php echo $phieu['phieu_thanh_ly_id']; ?>"
                                                class="btn btn-warning btn-sm mx-2">Sửa</a>
                                            <?php if ($_SESSION['role'] == 'NhanVienQuanLy'): ?>
                                                <a href="index.php?model=phieuthanhly&action=delete&id=<?= $phieu['phieu_thanh_ly_id']; ?>" onclick="return confirmDelete();"
                                                    class="btn btn-danger btn-sm mx-2">Xóa</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                         <?php if ($phieu['trang_thai'] == 'DaPheDuyet' ): ?>
                                            <?php if ($_SESSION['role'] == 'NhanVienQuanLy'): ?>
                                                <a href="index.php?model=phieuthanhly&action=thanh_ly&id=<?php echo $phieu['phieu_thanh_ly_id']; ?>"
                                                    class="btn btn-success btn-sm mx-2">Thanh lý</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                         <?php if ($phieu['trang_thai'] == 'DaPheDuyet' || $phieu['trang_thai'] == 'DaThanhLy' ): ?>
                                              <a href="index.php?model=phieuthanhly&action=xuatphieu&id=<?php echo $phieu['phieu_thanh_ly_id']; ?>" class="btn btn-success">Xuất excel</a>
                                        <?php endif; ?>

                                        <?php if ($_SESSION['role'] == 'QuanLy' && $phieu['trang_thai']== 'DangChoPheDuyet'): ?>
                                            <a href="index.php?model=phieuthanhly&action=xet_duyet&id=<?php echo $phieu['phieu_thanh_ly_id']; ?>"
                                            class="btn btn-sm mx-2 btn-primary">Xét duyệt</a>
                                            <input type="hidden" name="nguoi_phe_duyet_id" value="<?= $_SESSION['user_id']?>">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var table = $('#dataTable').DataTable({
            dom: 'rtip',
            language: {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // function filterTable() {
        //     var ngayBatDau = document.getElementById('ngayBatDau').value;
        //     var ngayKetThuc = document.getElementById('ngayKetThuc').value;

        //     var table = document.getElementById('dataTable');
        //     var rows = table.getElementsByTagName('tr');

        //     for (var i = 1; i < rows.length; i++) {
        //         var cells = rows[i].getElementsByTagName('td');
        //         var ngayNhap = cells[1].textContent.trim();
        //         var ngayXacNhan = cells[2].textContent.trim();

        //         // Kiểm tra điều kiện lọc
        //         var passNgay = (!ngayBatDau || ngayNhap >= ngayBatDau) && (!ngayKetThuc || ngayNhap <= ngayKetThuc) && (!ngayKetThuc || ngayXacNhan <= ngayKetThuc);

        //         if (passNgay) {
        //             rows[i].style.display = '';
        //         } else {
        //             rows[i].style.display = 'none';
        //         }
        //     }
        // }

        // document.getElementById('ngayBatDau').addEventListener('change', filterTable);
        // document.getElementById('ngayKetThuc').addEventListener('change', filterTable);

        // // Gọi filterTable ngay khi trang được tải để áp dụng bất kỳ giá trị mặc định nào
        // filterTable();

        var toggleButton = document.getElementById('toggleSearch');
        var searchForm = document.getElementById('searchForm');

        toggleButton.addEventListener('click', function () {
            if (searchForm.style.display === 'none') {
                searchForm.style.display = 'block';
                toggleButton.textContent = 'Ẩn tìm kiếm';
            } else {
                searchForm.style.display = 'none';
                toggleButton.textContent = 'Tìm kiếm';
            }
        });
    });
    function confirmDelete() {
        return confirm('Bạn có chắc muốn xóa phiếu này?');
    }
</script>