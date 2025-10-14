<script type="text/javascript">
  function tambah(e) {
      e.preventDefault()
      $.ajax({
          url : '<?php echo base_url('contoh/tambah') ?>',
          method : 'POST',
          data : $('#form_tambah').serialize(),
          dataType : 'json',
          success: function (res){
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
                    allowOutsideClick : false
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = '<?php echo base_url() ?>contoh'
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
                    allowOutsideClick : false
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload()
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
                    <li class="breadcrumb-item"><?php echo $title; ?></li>
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>contoh">Data</a></li>
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
              <h4 class="card-title">Tambah <?php echo $title; ?></h4>
          </div><!--end card-header-->
          <div class="card-body">
            <div class="general-label">
              <form id="form_tambah">
                <div class="mb-3 row">
                  <label for="tambah_contoh" class="col-sm-2 col-form-label">Contoh</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="contoh" id="tambah_contoh" placeholder="Input Contoh">
                  </div>
                </div>
                <div class="row">
                    <div class="col-sm-10 ms-auto">
                        <button type="button" onclick="tambah(event);" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan</button>
                        <a href="<?php echo base_url(); ?>contoh"><button type="button" class="btn btn-warning"><i class="fas fa-reply me-2"></i>Kembali</button></a>
                    </div>
                </div>
              </form>
            </div>
          </div><!--end card-body-->
      </div><!--end card-->
    </div><!--end col-->
  </div>
</div><!-- container -->
