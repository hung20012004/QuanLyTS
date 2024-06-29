<div class="container">
    <!-- Total Users Card -->
    <div class="card" style="margin-bottom: 15px;">
        <div class="card-header bg-primary text-white">
            Người dùng
        </div>
        <div class="card-body">
            <h5 class="card-title" id="totalUsers"><strong>Tổng cộng:</strong> <?= $totalUsers ?></h5>
        </div>
    </div>

    <!-- Users by Role Card -->
    <div class="card">
        <div class="card-header bg-success text-white">
            Vị trí và số lượng
        </div>
        <div class="card-body">
            <canvas id="usersByRoleChart" width="900px" height="400"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch data passed from backend PHP
        var usersByRole = <?= json_encode($usersByRole) ?>;

        // Process data to prepare for chart
        var labels = usersByRole.map(item => item.role);
        var counts = usersByRole.map(item => item.total);

        // Create Chart
        var usersByRoleData = {
            labels: labels,
            datasets: [{
                label: 'Biểu đồ Người Dùng',
                data: counts,
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 159, 64, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 159, 64, 1)'],
                borderWidth: 1
            }]
        };

       var usersByRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
            var usersByRoleChart = new Chart(usersByRoleCtx, {
                type: 'bar',
                data: usersByRoleData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0, // Chỉ hiển thị số nguyên
                                font: {
                                    size: 14 // Kích thước chữ trên trục y
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 14 // Kích thước chữ trên trục x
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 14 // Kích thước chữ trong phần chú thích
                                }
                            }
                        }
                    }
                }
            });
        });
</script>