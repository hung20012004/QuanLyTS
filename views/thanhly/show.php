<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=thanhly&action=index">Thanh lý</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Chi tiết hóa đơn</h5>
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

                     <?php 
                     $previous_id=null;
                     foreach($dstl as $tl):
                     if($tl['hoa_don_id']!= $previous_id) 
                     {
                     ?>
                        
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>ID:</strong> <?= $tl['hoa_don_id'] ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Ngày thanh lý:</strong> <?=date('d-m-Y', strtotime($tl['ngay_thanh_ly']) ) ?>
                        </li>
                    </ul>
                    <?php 
                    }
                    $previous_id=$tl['hoa_don_id'];
                endforeach;
                    ?>

                    <table class="table table-bordered mt-2">
                        <thead style="background-color: #d3d8dc;">
                            <tr style="text-align: center;">
                                <th>Mã tài sản</th>
                                <th>Tên tài sản</th>
                                <th>Số lượng</th>
                                <th>Giá thanh lý</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($dstl as $tl): ?>
                                <tr style="text-align: center;">
                                    <td><?= $tl['tai_san_id'] ?></td>
                                    <td><?= $tl['ten_tai_san'] ?></td>
                                    <td><?= $tl['so_luong'] ?></td>
                                    <td><?= number_format($tl['gia_thanh_ly'], 0, ',', '.')?></td>
                                    <td><?= number_format($tl['so_luong'] * $tl['gia_thanh_ly'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?> 

                            <?php 
                     $previous_id=null;
                     foreach($dstl as $tl):
                     if($tl['hoa_don_id']!= $previous_id) 
                     {
                     ?>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Tổng cộng:</strong></td>
                                <td style="text-align: center;" ><?=  number_format($tl['tong_gia_tri'], 0, ',', '.') ?></td>
                            </tr>
                    <?php 
                    }
                    $previous_id=$tl['hoa_don_id'];
                endforeach;
                    ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>