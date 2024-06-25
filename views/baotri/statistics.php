<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=baotri&action=index">Lịch Bảo Trì</a></li>
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
                    }, 150); // Optional: wait for the fade-out transition to complete
                }
            }, 2000); // 2000 milliseconds = 2 seconds
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quản Lý Lịch Bảo Trì</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <canvas id="myChart"></canvas>
            <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Tổng số lịch bảo trì: <?php echo $totalSchedules; ?></li>
                        <li class="list-group-item">Tổng số ngày bảo trì: <?php echo $totalMaintenanceDays; ?></li>
                        <li class="list-group-item">Trung bình số ngày bảo trì: <?php echo round($avgMaintenanceDays, 2); ?></li>
                        <!-- Các thông tin thống kê khác có thể được thêm vào đây -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var totalSchedules = <?php echo $totalSchedules; ?>;
    var avgMaintenanceDays = <?php echo $avgMaintenanceDays; ?>;

    // Tạo biểu đồ
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Tổng số lịch bảo trì', 'Trung bình số ngày bảo trì'],
            datasets: [{
                label: 'Thống kê lịch bảo trì',
                data: [totalSchedules, avgMaintenanceDays],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
