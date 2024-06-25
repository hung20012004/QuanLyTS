<h3 class="display-9 fw-bold text-body-emphasis text-center py-3">Quản lý sinh viên</h3>
<div class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
        <input type="search" id="searchInput" class="form-control form-control-dark text-bg-light" placeholder="Search by name..."
        aria-label="Search">
      </div>
<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col" class="text-center">#</th>
            <th scope="col">Mã sinh viên</th>
            <th scope="col">Tên</th>
            <th scope="col">Ngày sinh</th>
            <th scope="col">Điểm chuyên cần</th>
            <th scope="col">Điểm giữa kỳ</th>
            <th scope="col">Điểm cuối kỳ</th>
            <th colspan="2">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php $count = 1;
        foreach ($sinhviens as $sinhvien): ?>
            <tr>
                <form action="route.php?model=sinhvien&action=delete&id=<?php echo $sinhvien['ID']; ?>" method="post"
                    id="deleteForm" style="display: none;">
                </form>
                <th class="text-center" scope="row"><?php echo $count;
                $count++ ?></th>
                <td><?php echo $sinhvien['MaSV']; ?></td>
                <td><?php echo $sinhvien['Ten']; ?></td>
                <td><?php echo $sinhvien['NgaySinh']; ?></td>
                <td><?php echo $sinhvien['DiemChuyenCan']; ?></td>
                <td><?php echo $sinhvien['DiemGiuaKy']; ?></td>
                <td><?php echo $sinhvien['DiemCuoiKy']; ?></td>
                <td>
                    <a type="button" class=" btn btn-primary" href="" data-bs-toggle="modal"
                        data-bs-target="#showSinhvienModal_<?php echo $sinhvien['ID']; ?>" class="text-decoration-none">
                        Edit
                    </a>
                    <?php include 'views\sinhviens\edit.php'; ?>
                </td>
                <td>

                    <button type="button" class=" btn btn-primary" onclick="confirmDelete()">
                        Delete
                    </button>

                </td>
            </tr>


        <?php endforeach; ?>
        <tr>
            <th>
                <div class="text-center">
                    <a type="button" class="btn btn-primary" href="route.php?model=sinhvien&action=create">
                        New
                    </a>
                </div>

            </th>
            <th colspan="8"></th>
        </tr>
    </tbody>
</table>

<script>
    function confirmDelete() {
        if (confirm("Are you sure you want to delete this item?")) {
            document.getElementById("deleteForm").submit();
        } else {
            console.log('Item not deleted');
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('searchInput');
        var rows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function () {
            var searchQuery = searchInput.value.toLowerCase();
            rows.forEach(function (row) {
                var nameCell = row.querySelector('td:nth-child(3)'); 
                var name = nameCell.textContent.toLowerCase();
                if (name.includes(searchQuery)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none'; 
                }
            });
        });
    });
</script>