<script type="text/javascript">
  $(document).ready(function () {
    jabatan();
    poli();
  });
  function edit(e) {
     let btn = $(e.target).closest('button');
    e.preventDefault();
    btn.prop("disabled", true).text("Mengirim...");
    const nama_p = $('#nama_pegawai').val();
    const tp = $('#no_tp').val();
    const nama_j = $('#nama_jabatan').val();
    const id_j = $('#id_jabatan').val();
    const alamat = $('#alamat').val();
    if (nama_p == '' || tp == '' || nama_j == '' || id_j == '' || alamat == '') {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Inputan Kosong",
      });
      btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
      return;
    }
    $.ajax({
      url: '<?php echo base_url('kepegawaian/pegawai/edit') ?>',
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
        console.log(res);
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
              window.location.href = '<?php echo base_url() ?>kepegawaian/pegawai'
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
  function jabatan() {
    $.get({
      url: '<?= base_url(); ?>kepegawaian/pegawai/jabatan',
      dataType: 'JSON',
      success: function (data) {
        if (data != null) {
          const select_d = <?= $row['id_jabatan'] ?>;
          $('#id_jabatan').empty().append('<option value="">Pilih Jabatan</option>');
          data.forEach(item => {
            $('#id_jabatan').append($('<option>', {
              value: item.id,
              text: item.nama,
              'data-nama': item.nama,
              selected: item.id == select_d,
            }));
          });

          //inputan 
          const selecte = $('#id_jabatan option:selected').data('nama');
          $('#nama_jabatan').val(selecte);
          if (selecte == 'Dokter') {
            $('#bagian_poli').slideDown();
            $('#bagian_poli').prop('required', true);
          }
          else {
            $('#bagian_poli').hide();
            $('#bagian_poli').prop('required', false);
          }

        }
      },
      error: function (error) {
        console.log(error);
      },
    })
  }
  $(document).on('change', '#id_jabatan', function () {
    var nama = $('#id_jabatan option:selected').data('nama');
    $('#nama_jabatan').val(nama);
    if (nama == 'Dokter') {
      $('#bagian_poli').slideDown();
      $('#bagian_poli').prop('required', true);
    }
    else {
      $('#bagian_poli').hide();
      $('#bagian_poli').prop('required', false);
    }
  });

  function poli() {
    $.get({
      url: '<?= base_url(); ?>kepegawaian/pegawai/poli',
      dataType: 'JSON',
      success: function (data) {
        if (data != null) {
          const select_poli = '<?= isset($row['id_poli']) ? $row['id_poli'] : '' ?>';
          data.forEach(item => {
            $('#id_poli').append($('<option>', {
              value: item.id,
              text: item.nama,
              'data-nama': item.nama,
              selected: item.id == select_poli,
            }));
          });

          //inputan 
          const selecte = $('#id_poli option:selected').data('nama');
          $('#nama_poli').val(selecte);
        }
      }
    })
  }
  $(document).on('change', '#id_poli', function () {
    var nama = $('#id_poli option:selected').data('nama');
    $('#nama_poli').val(nama);
  });

</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>kepegawaian/pegawai"><?php echo $title; ?></a>
            </li>
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
              <!-- inputan start -->
              <div class="mb-3 row">
                <label for="edit_contoh" class="col-sm-2 col-form-label">Nama Pegawai</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama_pegawai" value="<?php echo $row['nama']; ?>"
                    id="nama_pegawai" placeholder="Nama pegawai" autocomplete="off">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="edit_contoh" class="col-sm-2 col-form-label">No Telpon</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="no_tp" value="<?php echo $row['no_telp']; ?>" id="no_tp"
                    placeholder="Nomor telpon" autocomplete="off" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="edit_contoh" class="col-sm-2 col-form-label">Nama Jabatan</label>
                <div class="col-sm-10">
                  <select name="id_jabatan" id="id_jabatan" class="form-control">
                  </select>
                  <input type="hidden" class="form-control" name="nama_jabatan" id="nama_jabatan"
                    placeholder="Nama jabatan" readonly>
                </div>
              </div>
              <div class="mb-3 row" id="bagian_poli">
                <label for="edit_contoh" class="col-sm-2 col-form-label">Nama Poli</label>
                <div class="col-sm-10">
                  <select name="id_poli" id="id_poli" class="form-control">
                    <option value="">Pilih Poli</option>
                  </select>
                  <input type="hidden" class="form-control" name="nama_poli" id="nama_poli" placeholder="Nama poli"
                    readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="edit_contoh" class="col-sm-2 col-form-label">Nama Alamat</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="alamat" value="<?php echo $row['alamat']; ?>"
                    id="edit_contoh" placeholder="Nama poli" autocomplete="off">
                </div>
              </div>
              <!-- inputan end -->
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="edit(event);" class="btn btn-success"><i
                      class="fas fa-save me-2"></i>Simpan</button>
                  <a href="<?php echo base_url(); ?>kepegawaian/pegawai"><button type="button"
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