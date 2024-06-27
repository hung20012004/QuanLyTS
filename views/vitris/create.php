<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=vitri&action=index">Vị Trí</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm Mới</li>
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
            }, 2000); 
        </script>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Thêm Mới Vị Trí</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?model=vitri&action=create">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="ten_vi_tri">Vị trí</label>
                        <input type="text" class="form-control" id="ten_vi_tri" name="ten_vi_tri" required>
                    </div>
                </div>

                <h5 class="mt-4">Chi Tiết Vị Trí</h5>
                <div class="table-responsive">
                    <table id="tableChiTiet" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên Tài Sản</th>
                                <th>Số Lượng</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="row0">
                                <td>
                                    <select class="form-control tai_san_select" name="tai_san_id[]" required>
                                        <option value="">Chọn tài sản</option>
                                        <?php foreach ($taiSanList as $row) : ?>
                                            <option value="<?= $row['tai_san_id']; ?>"><?= htmlspecialchars($row['ten_tai_san']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" min="0" class="form-control so-luong" name="so_luong[]" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="xoaDong(this)">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-primary mt-2" onclick="themDong()">Thêm Dòng</button>

                <div class="mt-3 d-flex justify-content-between">
                    <a href="index.php?model=vitri&action=index" class="btn btn-secondary">Quay Lại</a>
                    <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateRowNumbers() {
        document.querySelectorAll('#tableChiTiet tbody tr').forEach(function(row, index) {
            row.id = 'row' + index;
        });
    }

    function themDong() {
        var tbody = document.querySelector("#tableChiTiet tbody");
        var newRow = tbody.rows[0].cloneNode(true);
        var rowCount = tbody.rows.length;
        newRow.id = 'row' + rowCount;

        // Reset input values
        newRow.querySelectorAll('input').forEach(function(input) {
            input.value = '';
        });

        // Reset select values
        newRow.querySelectorAll('select').forEach(function(select) {
            select.selectedIndex = 0;
        });

        tbody.appendChild(newRow);
        updateRowNumbers();
    }

    function xoaDong(button) {
        var row = button.closest('tr');
        if (document.querySelectorAll('#tableChiTiet tbody tr').length > 1) {
            row.remove();
            updateRowNumbers();
        } else {
            alert('Không thể xóa dòng cuối cùng.');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateRowNumbers();
    });
</script>
