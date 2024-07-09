<li class="nav-item <?= ($current_model == 'user' && $current_action == 'index') ? 'active' : '' ?>">
    <a class="nav-link" href="route.php?model=user&action=index">
        <i class="fa-solid fa-user-group"></i>
        <span>Quản lý nhân viên</span>
    </a>
</li>

<!-- Nav Item - Asset Management -->
<li
    class="nav-item <?= ($current_model == 'taisan' || $current_model == 'khauhao' || $current_model == 'loaitaisan' || $current_model == 'vitri') ? 'active' : '' ?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true"
        aria-controls="collapseTwo">
        <i class="fa-solid fa-table-cells-large"></i>
        <span>Quản lý tài sản</span>
    </a>
    <div id="collapseTwo"
        class="collapse <?= ($current_model == 'taisan' || $current_model == 'khauhao' || $current_model == 'loaitaisan' || $current_model == 'vitri') ? 'show' : '' ?>"
        aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item <?= ($current_model == 'taisan' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=taisan&action=index">Danh sách tài sản</a>
            <a class="collapse-item <?= ($current_model == 'loaitaisan' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=loaitaisan&action=index">Phân loại</a>
            <a class="collapse-item <?= ($current_model == 'vitri' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=vitri&action=index">Vị trí</a>
            <a class="collapse-item <?= ($current_model == 'taisan' && $current_action == 'statistic') ? 'active' : '' ?>"
                href="route.php?model=taisan&action=statistic">Thống kê</a>
        </div>
    </div>
</li>

<!-- Nav Item - Document Management -->
<li
    class="nav-item <?= ($current_model == 'phieunhap' || $current_model == 'phieubangiao' || $current_model == 'phieutra' || $current_model == 'phieuthanhly' || $current_model == 'phieusua') ? 'active' : '' ?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true"
        aria-controls="collapseThree">
        <i class="fa-solid fa-file-invoice-dollar"></i>
        <span>Quản lý phiếu</span>
    </a>
    <div id="collapseThree"
        class="collapse <?= ($current_model == 'phieunhap' || $current_model == 'phieubangiao' || $current_model == 'phieutra' || $current_model == 'phieuthanhly' || $current_model == 'phieusua') ? 'show' : '' ?>"
        aria-labelledby="headingThree" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item <?= ($current_model == 'phieunhap' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=phieunhap&action=index">Phiếu nhập tài sản</a>
            <a class="collapse-item <?= ($current_model == 'phieubangiao' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=phieubangiao&action=index">Phiếu bàn giao tài sản</a>
            <a class="collapse-item <?= ($current_model == 'phieutra' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=phieutra&action=index">Phiếu trả tài sản</a>
            <a class="collapse-item <?= ($current_model == 'phieuthanhly' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=phieuthanhly&action=index">Phiếu thanh lý tài sản</a>
            <a class="collapse-item <?= ($current_model == 'phieusua' && $current_action == 'index') ? 'active' : '' ?>"
                href="route.php?model=phieusua&action=index">Phiếu sửa tài sản</a>
        </div>
    </div>
</li>

<!-- Nav Item - Statistics -->
<li class="nav-item <?= ($current_model == 'user' && $current_action == 'statistic') ? 'active' : '' ?>">
    <a class="nav-link" href="route.php?model=user&action=statistic">
        <i class="fa-solid fa-chart-simple"></i>
        <span>Thống kê</span>
    </a>
</li>