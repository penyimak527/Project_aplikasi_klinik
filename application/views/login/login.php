<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8" />
    <title>Login Aplikasi Klinik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url() ?>assets/images/favicon.ico">
    <!-- App css -->
    <link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <link href="<?php echo base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
</head>

<body id="body" class="auth-page"
    style="background-image: url('<?= base_url() ?>assets/images/background_login.png'); background-size: cover; background-position: center center;">
    <!-- Log In page -->
    <div class="container-md">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card " style="border-radius: 10px;">
                                <div class="card-body p-0" style="border-radius: 10px  10px 0 0; background: white;">
                                    <div class="text-center p-3">
                                        <a href="<?= base_url() ?>login/login" class="logo logo-admin">
                                            <img src="<?= base_url() ?>assets/images/logo-sm.png" height="50" alt="logo"
                                                class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-3 fw-semibold text-black font-18">Sistem Informasi Klinik
                                        </h4>
                                        <p class="mb-0 text-black">Masuk untuk akses halaman.</p>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <form class="my-4" id="form-login">
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                placeholder="Masukkan Username">
                                        </div><!--end form-group-->

                                        <div class="form-group mt-2 mb-3">
                                            <label class="form-label" for="userpassword">Password</label>
                                            <div class="d-flex">
                                                <input type="password" class="form-control" name="password"
                                                    id="userpassword" placeholder="Masukkan Password"
                                                    autocomplete="off">
                                                <button class="btn btn-outline-secondary ms-1" type="button"
                                                    id="btn-lihat-pass">
                                                    <i class="fas fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary" type="submit">Login <i
                                                            class="fas fa-sign-in-alt ms-1"></i></button>
                                                </div>
                                            </div><!--end col-->
                                        </div> <!--end form-group-->
                                    </form><!--end form-->
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!--end container-->
    <!-- vendor js -->

    <script src="<?= base_url() ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= base_url() ?>assets/libs/feather-icons/feather.min.js"></script>
    <!-- App js -->
    <script src="<?= base_url() ?>assets/js/app.js"></script>

</body>
<script src="<?php echo base_url(); ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/pages/sweet-alert.init.js"></script>

<script>
      $('#btn-lihat-pass').click(function () {
            let input = $('#userpassword');
            let icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });
    $(document).ready(function () {
        $('#form-login').submit(function (e) {
            let btn = $(e.target).closest('button');
            e.preventDefault();
            btn.prop("disabled", true).text("Mengirim...");
            $.ajax({
                url: '<?= base_url() ?>login/login/aksi_login',
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function (res) {
                    console.log(res);
                    if (res.status == true) {
                        if (res.data.status == "Aktif") {
                            window.location.href = '<?php echo base_url() ?>welcome'
                            //         Swal.fire({
                            //             title: 'Berhasil!',
                            //             text: res.message,
                            //             icon: "success",
                            //                timer: 3000,
                            // showConfirmButton: false
                            //         }).then((result) => {
                            //         })
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Status Akun Tidak Aktif!',
                                icon: "error",
                                showCancelButton: false,
                                showConfirmButton: true,
                                confirmButtonColor: "#35baf5",
                                confirmButtonText: "Oke",
                                closeOnConfirm: false,
                                allowOutsideClick: false
                            }).then((result) => {
                                btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })
                        }
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: res.message,
                            icon: "error",
                            showCancelButton: false,
                            showConfirmButton: true,
                            confirmButtonColor: "#35baf5",
                            confirmButtonText: "Oke",
                            closeOnConfirm: false,
                            allowOutsideClick: false
                        }).then((result) => {
                            btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }
                },
                error: function (xhr) {
                    console.log("ERROR:", xhr.responseText);
                    Swal.fire("Error!", "Terjadi kesalahan pada server!", "error").then(() => {
                        location.reload();

                    });
                }
            })
        })

    })
</script>

</html>