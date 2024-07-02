<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Thống kê tài sản</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Total Assets -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng số lượng tài sản</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAssets">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cubes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Number of Asset Types -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Số lượng loại tài sản</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAssetTypes">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Asset Type Statistics Table -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê tài sản theo loại</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0" id="assetTypeTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Loại tài sản</th>
                                    <th class="text-center">Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($assetTypes as $asset): ?>
                                    <tr>
                                        <td class="text-center"><?= htmlspecialchars($asset['loai_tai_san']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($asset['so_luong']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart: Asset Type Distribution -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ số lượng tài sản theo loại</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="assetTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Assets Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tài sản mới nhập (30 ngày gần nhất)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" id="recentAssetsTable">
                    <thead>
                        <tr>
                            <th>Tên tài sản</th>
                            <th>Loại tài sản</th>
                            <th>Số lượng</th>
                            <th>Ngày nhập</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentAssets as $asset): ?>
                            <tr>
                                <td ><?= htmlspecialchars($asset['ten_tai_san']) ?></td>
                                <td ><?= htmlspecialchars($asset['ten_loai_tai_san']) ?></td>
                                <td ><?= htmlspecialchars($asset['so_luong']) ?></td>
                                <td ><?= date('d/m/Y', strtotime($asset['ngay_mua'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Example data (replace with actual data retrieved via AJAX or PHP)
    var totalAssets = <?= $totalAssets ?>; // Example PHP variable
    var totalAssetTypes = <?= $totalAssetTypes ?>; // Example PHP variable
    var assetTypes = <?= json_encode($assetTypes) ?>;

    // Update total assets and asset types
    document.getElementById('totalAssets').textContent = numberFormat(totalAssets);
    document.getElementById('totalAssetTypes').textContent = numberFormat(totalAssetTypes);

    // Pie chart for asset type distribution
    var assetTypeCtx = document.getElementById("assetTypeChart").getContext('2d');
    var assetTypeChart = new Chart(assetTypeCtx, {
        type: 'pie',
        data: {
            labels: assetTypes.map(function(type) { return type.loai_tai_san; }),
            datasets: [{
                data: assetTypes.map(function(type) { return type.so_luong; }),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60636f', '#373840'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            labels:{display:false},
            plugins: {
                legend: {
                    position: 'top',
            },},
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: true,
                position: 'right'
            },
            cutoutPercentage: 80,
        },
    });

    // Helper functions
    function numberFormat(number) {
        return new Intl.NumberFormat().format(number);
    }

    function htmlspecialchars(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('en-US', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }
});
</script>