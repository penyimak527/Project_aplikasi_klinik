<?php
$ci = &get_instance();
$ci->load->model('admin/m_level');
$id_level = $ci->session->userdata('id_level');
$nama_user = $ci->session->userdata('username');
$nama_level = $ci->session->userdata('nama_level');

$menu_sidebar = $ci->m_level->get_sidebar_menu($id_level);

function is_active($link)
{
    $ci = &get_instance();
    $uri = $ci->uri->uri_string();
    return (strpos($uri, $link) !== false) ? 'active' : '';
}

$daftar_ikon = [
    'Dashboard' => 'ti ti-smart-home',
    'Master Data' => 'ti ti-apps',
    'Kepegawaian' => 'ti ti-users',
    'Resepsionis' => 'ti ti-bell',
    'Antrian' => 'ti ti-ticket',
    'Poli' => 'ti ti-building-hospital',
    'Transaksi' => 'ti ti-shopping-cart',
    'Keuangan' => 'ti ti-report-money',
    'Admin' => 'ti ti-crown'
];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8" />
    <title>Aplikasi Klinik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.ico">
    <!-- App css -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert -->
    <link href="<?php echo base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/libs/animate.css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>assets/libs/vanillajs-datepicker/css/datepicker.min.css" rel="stylesheet"
        type="text/css" />
    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <link href="<?php echo base_url(); ?>assets/libs/mobius1-selectr/selectr.min.css" rel="stylesheet" type="text/css"
        />
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/dropify/dist/css/dropify.min.css">
</head>

<body id="body" class="enlarge-menu">
    <!-- leftbar-tab-menu -->
    <div class="leftbar-tab-menu">
        <div class="main-icon-menu">
            <a href="index.html" class="logo logo-metrica d-block text-center">
                <span>
                    <img src="<?php echo base_url(); ?>assets/images/logo-sm.png" alt="logo-small" class="logo-sm">
                </span>
            </a>
            <div class="main-icon-menu-body">
                <div class="position-reletive h-100" data-simplebar style="overflow-x: hidden;">
                    <ul class="nav nav-tabs" role="tablist" id="tab-menu">
                         <?php foreach ($menu_sidebar as $grup => $daftar_sub_menu) :
                            $slug = url_title($grup, 'underscore', true);
                            $icon = isset($daftar_ikon[$grup]) ? $daftar_ikon[$grup] : 'ti ti-file';
                        ?>
                            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo $grup; ?>" data-bs-trigger="hover">
                                <a href="#menu_<?php echo $slug; ?>" id="<?php echo $slug; ?>-tab" class="nav-link">
                                    <i class="<?php echo $icon; ?> menu-icon"></i>
                                </a>
                            </li><?php endforeach; ?>
                    </ul><!--end nav-->
                </div><!--end /div-->
            </div><!--end main-icon-menu-body-->
            <div class="pro-metrica-end">
                <a href="#" class="profile">
                    <img src="<?php echo base_url(); ?>assets/images/users/user-4.jpg" alt="profile-user"
                        class="rounded-circle thumb-sm">
                </a>
            </div><!--end pro-metrica-end-->
        </div>
        <!--end main-icon-menu-->

        <div class="main-menu-inner">
            <!-- LOGO -->
            <div class="topbar-left">
                <a href="index.html" class="logo">
                    <span>
                        <img src="<?php echo base_url(); ?>assets/images/logo-dark.png" alt="logo-large"
                            class="logo-lg logo-dark">
                        <img src="<?php echo base_url(); ?>assets/images/logo.png" alt="logo-large"
                            class="logo-lg logo-light">
                    </span>
                </a><!--end logo-->
            </div><!--end topbar-left-->
            <!--end logo-->
         <div class="menu-body navbar-vertical tab-content" data-simplebar>
                <?php foreach ($menu_sidebar as $grup => $daftar_sub_menu) :
                    $slug = url_title($grup, 'underscore', true);
                ?>
                    <div id="menu_<?php echo $slug; ?>" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="<?php echo $slug; ?>-tab">
                        <div class="title-box">
                            <h6 class="menu-title">
                                <?php echo $grup; ?>
                            </h6>
                        </div>
                        <ul class="nav flex-column">
                            <?php foreach ($daftar_sub_menu as $menu) : ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo is_active($menu['link']); ?>" href="<?php echo base_url($menu['link']); ?>"><?php echo $menu['nama']; ?></a>
                                </li><?php endforeach; ?>
                        </ul>
                    </div><?php endforeach; ?>
                <!--end menu-body-->
            </div><!-- end main-menu-inner-->
        </div><!-- end main-menu-inner-->
    </div>
    <!-- end leftbar-tab-menu-->

    <!-- Top Bar Start -->
    <!-- Top Bar Start -->
    <div class="topbar">
        <!-- Navbar -->
        <nav class="navbar-custom" id="navbar-custom">
            <ul class="list-unstyled topbar-nav float-end mb-0">
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle nav-user" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo base_url(); ?>assets/images/users/user-4.jpg" alt="profile-user"
                                class="rounded-circle me-2 thumb-sm" />
                            <div>
                                <small
                                    class="d-none d-md-block font-11">  <?php echo $nama_level; ?></small>
                                <span
                                    class="d-none d-md-block fw-semibold font-12">  <?php echo $nama_user; ?>
                                    <iclass="mdi mdi-chevron-down"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="<?= base_url() ?>login/login/logout"><i
                                class="ti ti-power font-16 me-1 align-text-bottom"></i> Logout</a>
                    </div>
                </li><!--end topbar-profile-->
            </ul><!--end topbar-nav-->
            <ul class="list-unstyled topbar-nav mb-0">
                <li>
                    <button class="nav-link button-menu-mobile nav-icon" id="togglemenu">
                        <i class="ti ti-menu-2"></i>
                    </button>
                </li>
            </ul>
        </nav>
        <!-- end navbar-->
    </div>
    <!-- Top Bar End -->
    <!-- Top Bar End -->
    <div class="page-wrapper">
        <!-- Page Content-->
        <div class="page-content-tab">