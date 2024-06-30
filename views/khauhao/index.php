<div class="container-fluid">
    <div class="row mt-3">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?model=taisan&action=index">Khấu hao</a></li>
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
                <h5 class="card-title mb-0">Quản lý khấu hao</h5>
                <div>
                    <button id="toggleSearch" class="btn btn-secondary">Tìm kiếm</button>
                </div>
            </div>
        </div>
        <div class="card-body" id="searchForm" style="display: none;">
            <form action="index.php?model=khauhao&action=search" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="taiSanSearch" class="mr-2 mb-0" style="white-space: nowrap;">Tài sản:</label>
                            <input type="text" id="taiSanSearch" name="ten_tai_san" class="form-control" placeholder="Nhập tên tài sản">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex align-items-center">
                            <label for="loaiTaiSanSearch" class="mr-2 mb-0" style="white-space: nowrap;">Loại tài sản:</label>
                            <select id="loaiTaiSanSearch" name="loai_tai_san" class="form-control">
                                <option value="">Chọn loại tài sản</option>
                                <?php foreach ($loaitss as $loaiTaiSan): ?>
                                    <option value="<?= htmlspecialchars($loaiTaiSan['ten_loai_tai_san']) ?>">
                                        <?= htmlspecialchars($loaiTaiSan['ten_loai_tai_san']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">  
                        <button type="submit" class="btn btn-success">Tìm kiếm</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-black text-center">
                        <tr>
                            <th>ID</th>
                            <th>Tên Tài Sản</th>
                            <th>Mô Tả</th>
                            <th>Loại Tài Sản</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($khau_hao_all as $kh): ?>
                            <tr>
                                <td class="text-center"><?= $kh['tai_san_id'] ?></td>
                                <td><?= htmlspecialchars($kh['ten_tai_san']) ?></td>
                                <td><?= htmlspecialchars($kh['mo_ta']) ?></td>
                                <td><?= htmlspecialchars($kh['ten_loai_tai_san']) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="index.php?model=khauhao&action=detail&id=<?= $kh['tai_san_id'] ?>" class="btn btn-info btn-sm mx-2">Chi Tiết</a>
                                </td>
                            </tr>
                            <tr id="detail-<?= $kh['tai_san_id'] ?>" style="display: none;">
                                <td colspan="5">
                                    <div class="detail-content"></div>
                                </td>
                            </tr>
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
</script>