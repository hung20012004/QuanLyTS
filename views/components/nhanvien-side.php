<li class="nav-item <?= ($current_model == 'taisan' || $current_model == 'khauhao' || $current_model == 'loaitaisan' || $current_model == 'vitri') ? 'active' : '' ?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fa-solid fa-table-cells-large"></i>
        <span>Danh sách tài sản</span>
    </a>
    <div id="collapseTwo" class="collapse <?= ($current_model == 'taisan' || $current_model == 'khauhao' || $current_model == 'loaitaisan' || $current_model == 'vitri') ? 'show' : '' ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item <?= ($current_model == 'taisan' && $current_action == 'index') ? 'active' : '' ?>" href="route.php?model=taisan&action=index">Danh sách tài sản</a>
            <a class="collapse-item <?= ($current_model == 'loaitaisan' && $current_action == 'index') ? 'active' : '' ?>" href="route.php?model=loaitaisan&action=index">Phân loại</a>
            <a class="collapse-item <?= ($current_model == 'vitri' && $current_action == 'index') ? 'active' : '' ?>" href="route.php?model=vitri&action=index">Vị trí</a>
        </div>
    </div>
</li>
<li class="nav-item <?= ($current_model == 'phieunhap' || $current_model == 'phieubangiao' || $current_model == 'phieutra' || $current_model == 'phieuthanhly' || $current_model == 'phieusua') ? 'active' : '' ?>">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
        <i class="fa-solid fa-file-invoice-dollar"></i>
        <span>Phiếu yêu cầu</span>
    </a>
    <div id="collapseThree" class="collapse <?= ($current_model == 'phieunhap' || $current_model == 'phieubangiao' || $current_model == 'phieutra' || $current_model == 'phieuthanhly' || $current_model == 'phieusua') ? 'show' : '' ?>" aria-labelledby="headingThree" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item <?= ($current_model == 'phieubangiao' && $current_action == 'index') ? 'active' : '' ?>" href="route.php?model=phieubangiao&action=index">Phiếu bàn giao tài sản</a>
            <a class="collapse-item <?= ($current_model == 'phieutra' && $current_action == 'index') ? 'active' : '' ?>" href="route.php?model=phieutra&action=index">Phiếu trả tài sản</a>
            <a class="collapse-item <?= ($current_model == 'phieusua' && $current_action == 'index') ? 'active' : '' ?>" href="route.php?model=phieusua&action=index">Phiếu sửa tài sản</a>
        </div>
    </div>
</li>
