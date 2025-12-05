<script type="text/javascript">
    function validateForm(formSelector) {
        let isValid = true;
        $(formSelector + ' [required]').removeClass('is-invalid');
        $(formSelector + ' [required]').each(function () {
            if (!$(this).val() || $(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire({
                title: 'Gagal!',
                text: 'Harap isi semua kolom yang wajib diisi',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
        }

        return isValid;
    }
    function edit(e) {
        let btn = $(e.target).closest('button');
        e.preventDefault();
        btn.prop("disabled", true).text("Mengirim...");
        e.preventDefault();
        if (!validateForm('#form_edit')) {
            btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
            return;
        }
        $.ajax({
            url: '<?php echo base_url('admin/hak_akses/edit_aksi') ?>',
            method: 'POST',
            data: $('#form_edit').serialize(),
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Mengupload...',
                    html: 'Mohon Ditunggu...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (res) {
                if (res.status == true) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message,
                        icon: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        confirmButtonColor: "#35baf5",
                        confirmButtonText: "Oke",
                        closeOnConfirm: false,
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '<?php echo base_url() ?>admin/hak_akses'
                        }
                    })
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
                            console.log('Terjadi error!');
                        }
                    })
                }
            }
        });
    }
</script>
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="<?php echo base_url(); ?>admin/user"><?php echo $title; ?></a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title"><?php echo $title; ?></h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pt-3 pb-3">
                    <h4 class="card-title">Edit <?php echo $title; ?></h4>
                </div><!--end card-header-->
                <div class="card-body">
                    <div class="general-label">
                        <form id="form_edit">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Nama Hak Akses</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nama_hak_akses"
                                        value="<?php echo $row['nama_hak_akses']; ?>" required autocomplete="off">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Link (Controller/Method)</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="link"
                                        value="<?php echo $row['link']; ?>" required autocomplete="off">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Grup Hak Akses</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="id_grup_hak_akses" required>
                                        <option value="">-- Pilih Grup --</option>
                                        <?php foreach ($grup as $g) { ?>
                                            <option value="<?php echo $g->id; ?>" <?php echo ($g->id == $row['id_grup_hak_akses']) ? 'selected' : ''; ?>>
                                                <?php echo $g->nama_grup_hak_akses; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10 ms-auto">
                                    <button type="button" onclick="edit(event);" class="btn btn-success"><i
                                            class="fas fa-save me-2"></i>Simpan</button>
                                    <a href="<?php echo base_url(); ?>admin/hak_akses"><button type="button"
                                            class="btn btn-warning"><i
                                                class="fas fa-reply me-2"></i>Kembali</button></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
    </div>
</div><!-- container -->