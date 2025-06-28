<?php
$current_model = isset($_GET['model']) ? $_GET['model'] : '';
$current_action = isset($_GET['action']) ? $_GET['action'] : '';
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion position-sticky" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-2">Quản lý TSCĐ</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?= ($current_model == '' && $current_action == '') ? 'active' : '' ?>">
        <a class="nav-link" href="index.html">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Trang chủ</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <!-- Heading -->
    <div class="sidebar-heading">
        Công cụ
    </div>

    <?php
    if (isset($_SESSION['role'])) {
        switch ($_SESSION['role']) {
            case 'QuanLy':
                include 'views/components/quanly-side.php';
                break;
            case 'NhanVien':
                include 'views/components/nhanvien-side.php';
                break;
            case 'NhanVienQuanLy':
                include 'views/components/nvql-side.php';
                break;
            case 'KyThuat':
                include 'views/components/kythuat-side.php';
                break;
            default:
                echo "<li class='nav-item'>Không có quyền truy cập</li>";
                break;
        }
    } else {
        echo "<li class='nav-item'>Không có quyền truy cập</li>";
    }
    ?>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Thông tin người dùng
    </div>
    <li class="nav-item <?= ($current_model == 'auth' && $current_action == 'profile') ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?model=auth&action=profile">
            <i class="fa-solid fa-user"></i>
            <span>Hồ sơ</span>
        </a>
    </li>
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>