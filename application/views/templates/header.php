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
  <link href="<?php echo base_url(); ?>assets/libs/vanillajs-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css" />
  <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
  <link href="<?php echo base_url(); ?>assets/libs/mobius1-selectr/selectr.min.css" rel="stylesheet" type="text/css" />
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
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard" data-bs-trigger="hover">
                  <a href="#menu_dashboard" id="dasboard-tab" class="nav-link">
                      <i class="ti ti-smart-home menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Master Data" data-bs-trigger="hover">
                  <a href="#menu_master_data" id="master-data-tab" class="nav-link">
                     <i class="ti ti-apps menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Kepegawaian" data-bs-trigger="hover">
                  <a href="#menu_kepegawaian" id="kepegawaian-tab" class="nav-link">
                      <i class="ti ti-users menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Resepsionis" data-bs-trigger="hover">
                  <a href="#menu_resepsionis" id="resepsionis-tab" class="nav-link">
                     <i class="ti ti-bell menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Antrian" data-bs-trigger="hover">
                  <a href="#menu_antrian" id="antrian-tab" class="nav-link">
                     <i class="ti ti-ticket menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Poli" data-bs-trigger="hover">
                  <a href="#menu_poli" id="poli-tab" class="nav-link">
                     <i class="ti ti-building-hospital menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Transaksi" data-bs-trigger="hover">
                  <a href="#menu_transaksi" id="transaksi-tab" class="nav-link">
                     <i class="ti ti-shopping-cart menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Keuangan" data-bs-trigger="hover">
                  <a href="#menu_keuangan" id="keuangan-tab" class="nav-link">
                     <i class="ti ti-report-money menu-icon"></i>
                  </a><!--end nav-link-->
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!--end /div-->
    </div><!--end main-icon-menu-body-->
    <div class="pro-metrica-end">
        <a href="" class="profile">
            <img src="<?php echo base_url(); ?>assets/images/users/user-4.jpg" alt="profile-user" class="rounded-circle thumb-sm">
        </a>
    </div><!--end pro-metrica-end-->
</div>
<!--end main-icon-menu-->

<div class="main-menu-inner">
    <!-- LOGO -->
    <div class="topbar-left">
        <a href="index.html" class="logo">
            <span>
                <img src="<?php echo base_url(); ?>assets/images/logo-dark.png" alt="logo-large" class="logo-lg logo-dark">
                <img src="<?php echo base_url(); ?>assets/images/logo.png" alt="logo-large" class="logo-lg logo-light">
            </span>
        </a><!--end logo-->
    </div><!--end topbar-left-->
    <!--end logo-->
    <div class="menu-body navbar-vertical tab-content" data-simplebar>
        <div id="menu_dashboard" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="dasboard-tab">
            <div class="title-box">
                <h6 class="menu-title">Dashboard</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
        <div id="menu_master_data" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="master-data-tab">
            <div class="title-box">
                <h6 class="menu-title">Master Data</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>master_data/poli">Poli</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>master_data/tindakan">Tindakan</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>master_data/diagnosa">Diagnosa</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
        <div id="menu_kepegawaian" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="kepegawaian-tab">
            <div class="title-box">
                <h6 class="menu-title">Kepegawaian</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>kepegawaian/jabatan">Jabatan</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>kepegawaian/pegawai">Pegawai</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>kepegawaian/dokter">Dokter</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>kepegawaian/jadwal">Jadwal Dokter</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
        <div id="menu_resepsionis" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="resepsionis-tab">
            <div class="title-box">
                <h6 class="menu-title">Resepsionis</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>resepsionis/pasien">Pasien</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>resepsionis/booking">Booking</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>resepsionis/pendaftaran">Pendaftaran</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
        <div id="menu_antrian" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="antrian-tab">
            <div class="title-box">
                <h6 class="menu-title">Antrian</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>antrian/antrian">Panel Antrian</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>antrian/antrian/index_dokter">Panel Dokter</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
        <div id="menu_poli" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="poli-tab">
            <div class="title-box">
                <h6 class="menu-title">Poli</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="#">Poli Umum</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="#">Poli Gigi</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="#">Poli Anak</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url();?>poli/kecantikan">Poli Kecantikan</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
        <div id="menu_transaksi" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="transaksi-tab">
            <div class="title-box">
                <h6 class="menu-title">Transaksi</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>transaksi/pembayaran">Pembayaran</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>transaksi/pembayaran/riwayat">Riwayat Pembayaran</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
        <div id="menu_keuangan" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="keuangan-tab">
            <div class="title-box">
                <h6 class="menu-title">Keuangan</h6>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>keuangan/jenis_biaya">Jenis Biaya</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>keuangan/pemasukan">Pemasukan</a>
                </li><!--end nav-item-->
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url()?>keuangan/pengeluaran">Pengeluaran</a>
                </li><!--end nav-item-->
            </ul><!--end nav-->
        </div><!-- end -->
    </div>
    <!--end menu-body-->
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
                    <img src="<?php echo base_url(); ?>assets/images/users/user-4.jpg" alt="profile-user" class="rounded-circle me-2 thumb-sm" />
                    <div>
                        <small class="d-none d-md-block font-11">Nama Level</small>
                        <span class="d-none d-md-block fw-semibold font-12">Nama User <iclass="mdi mdi-chevron-down"></i></span>
                    </div>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="#"><i class="ti ti-power font-16 me-1 align-text-bottom"></i> Logout</a>
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
