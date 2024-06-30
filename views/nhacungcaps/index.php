<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=nhacungcap&action=index">Nhà Cung Cấp</a></li>
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
                <h5 class="card-title mb-0">Quản Lý Nhà Cung Cấp</h5>
                <div>
                    <button id="toggleSearch" class="btn btn-secondary">Tìm kiếm</button>
                    <a href="index.php?model=nhacungcap&action=create" class="btn btn-primary">Thêm Mới</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="searchForm" class="mb-3" style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="nhaCungCapSearch" class="mr-2 mb-0" style="white-space: nowrap;">Nhà cung cấp:</label>
                            <input type="text" id="nhaCungCapSearch" class="form-control" placeholder="Nhập tên nhà cung cấp">
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Tên Nhà Cung Cấp</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nhaCungCaps as $nhaCungCap): 
                                if($nhaCungCap['trang_thai'] !=0): ?>
                            <tr>
                                <td class="text-center"><?= $nhaCungCap['nha_cung_cap_id'] ?></td>
                                <td><?= htmlspecialchars($nhaCungCap['ten_nha_cung_cap']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=nhacungcap&action=edit&id=<?= $nhaCungCap['nha_cung_cap_id'] ?>"
                                        class="btn btn-warning btn-sm mx-2">Sửa</a>
                                    <form action="index.php?model=nhacungcap&action=delete&id=<?= $nhaCungCap['nha_cung_cap_id'] ?>" method="POST" style="display: inline-block;">
                                        <button type="submit" class="btn btn-danger btn-sm mx-2" onclick="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?');">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
  var table=$('#dataTable').DataTable({
      dom: 'rtip',
            language: {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
            }
    });
  });
    document.addEventListener('DOMContentLoaded', function () {
        function filterTable() {
            var nhaCungCapFilter = document.getElementById('nhaCungCapSearch').value.toLowerCase();
            var table = document.getElementById('dataTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var nhaCungCap = cells[1].textContent.trim().toLowerCase();

                if (nhaCungCap.includes(nhaCungCapFilter)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        document.getElementById('nhaCungCapSearch').addEventListener('input', filterTable);

        var toggleButton = document.getElementById('toggleSearch');
        var searchForm = document.getElementById('searchForm');

        toggleButton.addEventListener('click', function () {
            if (searchForm.style.display === 'none') {
                searchForm.style.display = 'block';
                toggleButton.textContent = 'Ẩn tìm kiếm';
            } else {
                searchForm.style.display = 'none';
                toggleButton.textContent = 'Tìm kiếm';
            }
        });
    });

    function confirmDelete() {
        return confirm('Bạn có chắc muốn xóa nhà cung cấp này?');
    }
</script>
