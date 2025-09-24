<script type="text/javascript">
  $(document).ready(function () {
    poli()
  });
  function edit(e) {
    e.preventDefault();
    const nama = $('#nama_diag').val();
    const id = $('#id_poli').val();
    const nama_p = $('#nama_poli').val();
    if (nama == "" || id == "" || nama_p == "") {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Inputan Kosong",
      });
      return;
    }

    $.ajax({
      url: '<?php echo base_url('master_data/diagnosa/edit') ?>',
      method: 'POST',
      data: $('#form_edit').serialize(),
      dataType: 'json',
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
              window.location.href = '<?php echo base_url() ?>master_data/diagnosa'
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
            if (result.isConfirmed) {
              location.reload()
            }
          })
        }
      }
    });
  }
  function poli() {
    $.get({
      url: '<?= base_url(); ?>master_data/diagnosa/poli',
      dataType: 'JSON',
      success: function (data) {
        if (data != null) {
          const select_d = <?= $row['id_poli'] ?>;
          data.forEach(item => {
            $('#id_poli').append($('<option>', {
              value: item.id,
              text: item.nama,
              'data-nama': item.nama,
              selected: item.id == select_d,
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
            <li class="breadcrumb-item"><a
                href="<?php echo base_url(); ?>master_data/diagnosa"><?php echo $title; ?></a></li>
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
                <label for="edit_contoh" class="col-sm-2 col-form-label">Nama diagnosa</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama_diag" value="<?php echo $row['nama_diagnosa']; ?>"
                    id="nama_diag" placeholder="Input nama diagnosa" autocomplete="off">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama poli</label>
                <div class="col-sm-10">
                  <select name="id_poli" id="id_poli" class="form-control">
                  </select>
                </div>
              </div>
              <input type="hidden" class="form-control" name="nama_poli" id="nama_poli" placeholder="Nama Poli"
                readonly>
              <!-- inputan end -->
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="edit(event);" class="btn btn-success"><i
                      class="fas fa-save me-2"></i>Simpan</button>
                  <a href="<?php echo base_url(); ?>master_data/diagnosa"><button type="button"
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