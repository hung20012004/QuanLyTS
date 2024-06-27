<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=thanhly&action=index">Thanh Lý</a></li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản Lý Hóa Đơn</h5>
                <div>
                    <a href="index.php?model=thanhly&action=viewcreate" class="btn btn-primary">Thêm Mới</a>
                    <a href="index.php?model=thanhly&action=export" class="btn btn-success">Xuất Excel</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Ngày thanh lý</th>
                            <!-- <th>Tài sản</th>
                            <th>Số lượng</th>
                            <th>Giá bán</th> -->
                            <th>Tổng cộng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                     <?php
                        $previous_hoadon_id = null;
                        foreach ($hoa_don_thanh_lys as $hoadon):
                            if ($hoadon['hoa_don_id'] != $previous_hoadon_id) {
                                // Đầu mỗi nhóm mới, hiển thị thông tin chung của hoa_don_id này
                                ?>
                                <tr>
                                    <td class="text-center"><?= $hoadon['hoa_don_id'] ?></td>
                                    <td style="text-align: center;"><?= htmlspecialchars(date('d-m-Y', strtotime($hoadon['ngay_thanh_ly']))) ?></td>
                                    <td text-align: center;><?= number_format($hoadon['tong_gia_tri'], 0, ',', '.') ?></td>
                                    <td class="d-flex justify-content-center">
                                        <a href="index.php?model=thanhly&action=show&id=<?= $hoadon['hoa_don_id'] ?>"
                                           class="btn btn-info btn-sm mx-2">Xem</a>
                                        <a href="index.php?model=thanhly&action=viewedit&id=<?= $hoadon['hoa_don_id'] ?>"
                                           class="btn btn-warning btn-sm mx-2">Sửa</a>
                                        <form action="index.php?model=thanhly&action=delete&id=<?= $hoadon['hoa_don_id'] ?>"
                                              method="POST" style="display: inline-block;"
                                              onsubmit="return confirmDelete();">
                                            <button type="submit" class="btn btn-danger btn-sm mx-2">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                             $previous_hoadon_id = $hoadon['hoa_don_id'];
                        endforeach;
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
     $(document).ready(function () {
        function confirmDelete() {
            return confirm('Bạn có chắc muốn xóa?');
        }
    });
</script>