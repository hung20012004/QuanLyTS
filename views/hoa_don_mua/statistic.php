<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Thống kê Hóa Đơn Mua</h1>
    <p class="mb-4">Tổng quan về hóa đơn mua trong hệ thống.</p>

    <div class="row">
        <!-- Tổng số hóa đơn -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng số hóa đơn</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalInvoices) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng giá trị -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng giá trị</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalValue, 0, ',', '.') ?> VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giá trị trung bình -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Giá trị trung bình</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($avgValue, 0, ',', '.') ?> VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ cột: Số lượng hóa đơn theo tháng -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Số lượng hóa đơn theo tháng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="myBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ tròn: Tỷ lệ hóa đơn theo nhà cung cấp -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ hóa đơn theo nhà cung cấp</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="myPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 tài sản được mua nhiều nhất -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top 5 tài sản được mua nhiều nhất</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tên tài sản</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topAssets as $asset): ?>
                        <tr>
                            <td><?= htmlspecialchars($asset['ten_tai_san']) ?></td>
                            <td><?= number_format($asset['total_quantity']) ?></td>
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
    var chartData = <?= json_encode($chartData) ?>;

    // Biểu đồ cột
    var barCtx = document.getElementById("myBarChart").getContext('2d');
    var myBarChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: chartData.monthlyLabels,
            datasets: [{
                label: "Số lượng hóa đơn",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: chartData.monthlyData,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                x: {
                    time: {
                        unit: 'month'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    },
                    maxBarThickness: 25,
                },
                y: {
                    ticks: {
                        min: 0,
                        max: Math.max(...chartData.monthlyData),
                        maxTicksLimit: 5,
                        padding: 10,
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
            },
            legend: {
                display: false
            },
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
        }
    });

    // Biểu đồ tròn
    var pieCtx = document.getElementById("myPieChart").getContext('2d');
    var myPieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: chartData.supplierLabels,
            datasets: [{
                data: chartData.supplierData,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
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
                position: 'bottom'
            },
            cutoutPercentage: 80,
        },
    });
});
</script>
