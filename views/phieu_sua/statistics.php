<div class="container-fluid">
        <div class="row mt-3">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Thống kê phiếu sửa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <!-- Tổng số phiếu sửa đã xử lý -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng số phiếu sửa đã xử lý</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($statistics['totalProcessed']) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Số phiếu sửa chưa xử lý -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Số phiếu sửa chưa xử lý</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($statistics['totalUnprocessed']) ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Những phiếu mới hoàn thành gần đây -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Những phiếu mới hoàn thành gần đây</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID Phiếu</th>
                                <th>Ngày yêu cầu</th>
                                <th>Ngày sửa chữa</th>
                                <th>Ngày hoàn thành</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statistics['recentCompleted'] as $form): ?>
                            <tr>
                                <td><?= htmlspecialchars($form['phieu_sua_id']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_yeu_cau']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_sua_chua']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_hoan_thanh']) ?></td>
                                <td><?= htmlspecialchars($form['mo_ta']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['role'] != 'KyThuat'): ?>
        <!-- Những vị trí mới gửi phiếu gần đây -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Những vị trí mới gửi phiếu gần đây</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID Phiếu</th>
                                <th>Ngày yêu cầu</th>
                                <th>Ngày sửa chữa</th>
                                <th>Ngày hoàn thành</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statistics['recentRequests'] as $form): ?>
                            <tr>
                                <td><?= htmlspecialchars($form['phieu_sua_id']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_yeu_cau']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_sua_chua']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_hoan_thanh']) ?></td>
                                <td><?= htmlspecialchars($form['mo_ta']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Những vị trí mới gửi phiếu gần đây -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Những phiếu mới nhận gần đây</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID Phiếu</th>
                                <th>Ngày yêu cầu</th>
                                <th>Ngày sửa chữa</th>
                                <th>Ngày hoàn thành</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statistics['recentReceives'] as $form): ?>
                            <tr>
                                <td><?= htmlspecialchars($form['phieu_sua_id']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_yeu_cau']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_sua_chua']) ?></td>
                                <td><?= htmlspecialchars($form['ngay_hoan_thanh']) ?></td>
                                <td><?= htmlspecialchars($form['trang_thai']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Vị trí gửi nhiều phiếu nhất -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Vị trí gửi nhiều phiếu nhất</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($statistics['mostRequests']['ten_vi_tri']) ?> (<?= number_format($statistics['mostRequests']['total']) ?> phiếu)</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-crown fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vị trí gửi ít phiếu nhất -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Vị trí gửi ít phiếu nhất</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($statistics['leastRequests']['ten_vi_tri']) ?> (<?= number_format($statistics['leastRequests']['total']) ?> phiếu)</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-frown fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
