<script type="text/javascript">
  $(document).ready(function () {
    hari();
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

  function tambah(e) {
     let btn = $(e.target).closest('button');
    e.preventDefault();
    btn.prop("disabled", true).text("Mengirim...");
    const id_pe = $('#id_pegawai').val();
    const nama_pe = $('#nama_pegawai').val();
    const hari = $('#hari').val();
    const jam_mulai = $('#jam_mulai').val();
    const jam_selesai = $('#jam_selesai').val();
    if (id_pe == '' || nama_pe == '' || hari == '' || jam_mulai == '' || jam_selesai == '') {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Inputan Kosong",
      });
      btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
      return;
    }
    $.ajax({
      url: '<?php echo base_url('kepegawaian/dokter/tambah') ?>',
      method: 'POST',
      data: {
        id_pegawai: id_pe,
        nama_pegawai: nama_pe,
        hari: hari,
        jam_mulai: jam_mulai,
        jam_selesai: jam_selesai,
      },
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
              window.location.href = "<?= base_url('kepegawaian/dokter/kalender/' . $dokter['id']) ?>";
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
  function hari() {
    const hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    const hari_terpakai = <?php echo json_encode(array_column($jadwal, 'hari')); ?>;
    let option = '<option value="">Pilih Hari</option>';
    hari.forEach(item => {
      if (!hari_terpakai.includes(item)) {
        option += `<option value='${item}'>${item}</option>`;
      }
    });
    $('#hari').html(option);
  }
</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('kepegawaian/dokter'); ?>">Dokter</a></li>
            <li class="breadcrumb-item"><a
                href="<?php echo base_url('kepegawaian/dokter/kalender/' . $dokter['id']); ?>"><?php echo $title; ?></a>
            </li>
            <li class="breadcrumb-item active">Tambah</li>
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
          <h4 class="card-title">Tambah <?php echo $title; ?> : <?php echo $dokter['nama_pegawai'] ?></h4>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="general-label">
            <form id="form_tambah">
              <!-- inputan start -->
              <input type="hidden" class="form-control" name="id_pegawai" id="id_pegawai"
                value="<?php echo $dokter['id_pegawai'] ?>" placeholder="Nama pegawai" readonly>
              <input type="hidden" class="form-control" name="nama_pegawai" id="nama_pegawai"
                value="<?php echo $dokter['nama_pegawai'] ?>" placeholder="Nama pegawai" readonly>
              <input type="hidden" name="id_dokter" value="<?php echo $dokter['id']; ?>" required>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Hari </label>
                <div class="col-sm-10">
                  <select name="hari" id="hari" class="form-control">
                    <option value=""></option>
                  </select>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" step="1" class="col-sm-2 col-form-label">Jam Mulai </label>
                <div class="col-sm-10">
                  <input type="text" step="1" class="form-control" name="jam_mulai" id="jam_mulai" autocomplete="off">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Jam selesai</label>
                <div class="col-sm-10">
                  <input type="text" step="1" class="form-control" name="jam_selesai" id="jam_selesai"
                    autocomplete="off">
                </div>
              </div>

              <!-- inputan end -->
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="tambah(event);" class="btn btn-success"><i
                      class="fas fa-save me-2"></i>Simpan</button>
                  <a href="<?php echo base_url('kepegawaian/dokter/kalender/' . $dokter['id']); ?>"><button type="button"
                      class="btn btn-warning"><i class="fas fa-reply me-2"></i>Kembali</button></a>
                </div>
              </div>
            </form>
          </div>
        </div><!--end card-body-->
      </div><!--end card-->
    </div><!--end col-->
  </div>
</div><!-- container -->