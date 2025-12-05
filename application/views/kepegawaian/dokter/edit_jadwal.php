<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('kepegawaian/dokter'); ?>">Dokter</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('kepegawaian/dokter/kalender/' . $dokter['id']); ?>">Jadwal
                                Dokter</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <?php echo $title; ?>
                </h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pt-3 pb-3">
                    <h4 class="card-title">Edit Jadwal
                        <?php echo $title; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="general-label">
                        <form id="form_edit">
                            <input type="hidden" name="id_dokter" value="<?php echo $dokter['id']; ?>" readonly>
                            <input type="hidden" name="id" id="id" value="<?php echo $jadwal['id']; ?>" readonly>
                            <input type="hidden" name="nama_pegawai" id="nama_pegawai"
                                value="<?php echo $jadwal['nama_pegawai'] ?>" readonly>
                            <input type="hidden" name="id_pegawai" id="id_pegawai"
                                value="<?php echo $jadwal['id_pegawai'] ?>" readonly>
                            <div class="mb-3 row">
                                <label for="tambah_contoh" class="col-sm-2 col-form-label">Hari</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="hari" id="hari"
                                        value="<?php echo $jadwal['hari']; ?>" disabled required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="tambah_contoh" class="col-sm-2 col-form-label">Jam Mulai</label>
                                <div class="col-sm-10">
                                    <input type="text" step="1" name="jam_mulai" id="jam_mulai" class="form-control"
                                        value="<?php echo $jadwal['jam_mulai']; ?>" required autocomplete="off">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="tambah_contoh" class="col-sm-2 col-form-label">Jam Selesai</label>
                                <div class="col-sm-10">
                                    <input type="text" step="1" name="jam_selesai" id="jam_selesai" class="form-control"
                                        value="<?php echo $jadwal['jam_selesai']; ?>" required autocomplete="off">
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-10 ms-auto">
                                        <button type="button" onclick="edit(event);" class="btn btn-success"><i
                                                class="fas fa-save me-2"></i>Simpan</button>
                                        <a
                                            href="<?php echo base_url('kepegawaian/dokter/kalender/' . $dokter['id']); ?>">
                                            <button type="button" class="btn btn-warning">
                                                <i class="fas fa-reply me-2"></i>Kembali
                                            </button>
                                        </a>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        waktuu();
        waktuu1();
    });
    function waktuu() {
        var timeInput = document.getElementById('jam_mulai');
        var timeMask = IMask(timeInput, {
            mask: 'HH:MM:ss',
            blocks: {
                HH: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 23,
                    maxLength: 2
                },
                MM: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 59,
                    maxLength: 2
                },
                ss: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 59,
                    maxLength: 2
                }
            },
            lazy: false,
            placeholderChar: '_'
        });
    }
    function waktuu1() {
        var timeInput = document.getElementById('jam_selesai');
        var timeMask = IMask(timeInput, {
            mask: 'HH:MM:ss',
            blocks: {
                HH: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 23,
                    maxLength: 2
                },
                MM: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 59,
                    maxLength: 2
                },
                ss: {
                    mask: IMask.MaskedRange,
                    from: 0,
                    to: 59,
                    maxLength: 2
                }
            },
            lazy: false,
            placeholderChar: '_'
        });
    }
    function edit(e) {
        let btn = $(e.target).closest('button');
    e.preventDefault();
    btn.prop("disabled", true).text("Mengirim...");
        const id = $('#id').val();
        const id_pegawai = $('#id_pegawai').val();
        const nama_pegawai = $('#nama_pegawai').val();
        const hari = $('#hari').val();
        const jam_mulai = $('#jam_mulai').val();
        const jam_selesai = $('#jam_selesai').val();
        if (jam_mulai == '' || jam_selesai == '') {
            console.log('Inputan Kosong');
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Inputan Kosong!",
            });
            btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
            return;
        }
        $.ajax({
            url: '<?= base_url("kepegawaian/dokter/edit_jadwal") ?>',
            type: 'POST',
            data: {
                id: id,
                id_pegawai: id_pegawai,
                nama_pegawai: nama_pegawai,
                hari: hari,
                jam_mulai: jam_mulai,
                jam_selesai: jam_selesai
            },
            dataType: 'JSON',
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
                Swal.fire({
                    icon: res.status ? "success" : "error",
                    title: res.status ? "Berhasil" : "Gagal",
                    text: res.message
                }).then(() => {
                    btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
                    window.location.href = '<?= base_url('kepegawaian/dokter/kalender/' . $dokter['id']) ?>';
                });
            },
        })
    }
</script>