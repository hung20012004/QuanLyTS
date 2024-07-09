<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=phieusua&action=index">Phiếu sửa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lịch Sửa Chữa</h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="user-select">Chọn kỹ thuật viên:</label>
                <select id="user-select" class="form-control">
                    <option value="">Tất cả</option>
                    <?php foreach ($data['users'] as $user): ?>
                        <option value="<?= $user['user_id'] ?>" <?= $user['user_id'] == $data['user_sua_chua_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['ten']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="calendar"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: 'route.php?controller=phieusua&action=getRepairForms',
                    dataType: 'json',
                    data: {
                        user_sua_chua_id: $('#user-select').val()
                    },
                    success: function(events) {
                        successCallback(events);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },
            eventClick: function(info) {
                // Hiển thị thông tin chi tiết của phiếu sửa
                alert('Phiếu sửa ID: ' + info.event.id + '\nTrạng thái: ' + info.event.title);
            }
        });
        
        calendar.render();

        // Cập nhật lịch khi chọn người dùng khác
        $('#user-select').change(function() {
            calendar.refetchEvents();
        });
    });
</script>
