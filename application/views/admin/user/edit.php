<script type="text/javascript">
  $(document).ready(function () {
    level();
  });
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
    if (!validateForm('#form_edit')) {
      btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
      return;
    }
    $.ajax({
      url: '<?php echo base_url('admin/user/edit') ?>',
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
              window.location.href = '<?php echo base_url() ?>admin/user'
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
  function level() {
    $.ajax({
      url: '<?= base_url() ?>admin/user/level',
      type: 'GET',
      dataType: 'JSON',
      success: function (res) {
        if (res.data != null) {
          const id_level = <?php echo $row['id_level'] ?>;
          res.data.forEach(item => {
            $('#id_level').append($('<option>', {
              value: item.id,
              text: item.nama_level,
              'data-nama': item.nama_level,
              selected: item.id == id_level,
            }));
          });
          //inputan 
          const selecte = $('#id_level option:selected').data('nama');
          $('#nama_level').val(selecte);
        }
      },
    })
  }
  $(document).on('change', '#id_level', function () {
    var nama = $('#id_level option:selected').data('nama');
    var idlevel = $(this).val();
    $('#nama_level').val(nama);
  });

</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>admin/user"><?php echo $title; ?></a></li>
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
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                  <input type="hidden" class="form-control" name="nama_pg" id="nama_pg" autocomplete="off" readonly
                    value="<?php echo $row['nama_pegawai'] ?>">
                  <input type="hidden" name="id" id="id" value="<?php echo $row['id'] ?>" readonly class="form-control">
                  <input type="hidden" name="id_pegawai" id="id_pegawai" value="<?php echo $row['id_pegawai'] ?>"
                    readonly class="form-control">
                  <input type="text" class="form-control" name="username" id="Username" placeholder="Username"
                    value="<?php echo $row['username'] ?>" autocomplete="off" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" name="password" id="password"
                    placeholder="Kosongkan Jika tidak Ingin di Edit" autocomplete="off">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Level</label>
                <div class="col-sm-10">
                  <select name="id_level" id="id_level" class="form-select" required>
                    <option value="">Pilih Level</option>
                  </select>
                  <input type="hidden" class="form-control" name="nama_level" id="nama_level" autocomplete="off"
                    readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                  <select name="status" id="status" class="form-select" required>
                    <option value="Aktif" <?= ($row['status'] == "Aktif") ? "selected" : "" ?>>Aktif</option>
                    <option value="Nonaktif" <?= ($row['status'] == "Nonaktif") ? "selected" : "" ?>>Nonaktif</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="edit(event);" class="btn btn-success"><i
                      class="fas fa-save me-2"></i>Simpan</button>
                  <a href="<?php echo base_url(); ?>admin/user"><button type="button" class="btn btn-warning"><i
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