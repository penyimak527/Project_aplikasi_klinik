<script type="text/javascript">
  let isSubmitting = false;

  function tambah(e) {
      e.preventDefault();
       let kode = $('input[name="kode_supplier"]').val().trim();
      let nama = $('input[name="nama_supplier"]').val().trim();
      let alamat = $('textarea[name="alamat"]').val().trim();
      let telp = $('input[name="no_telp"]').val().trim();
      let rek = $('input[name="no_rek"]').val().trim();
      let bank = $('input[name="bank"]').val().trim();

      if (kode === '' || nama === '' || alamat === '' || telp === '' || rek === '' || bank === '') {
          Swal.fire({
              title: 'Peringatan!',
              text: 'Semua kolom wajib diisi dan tidak boleh kosong.',
              icon: "warning",
              confirmButtonColor: "#35baf5",
              confirmButtonText: "Oke"
          });
          return;
      }

      if (isSubmitting) return;
      isSubmitting = true;
      const $btn = $('#btn_simpan');
      $btn.prop('disabled', true);

      $.ajax({
          url : '<?php echo base_url("pembelian/supplier/tambah") ?>',
          method : 'POST',
          data : $('#form_tambah').serialize(),
          dataType : 'json',
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
          success: function (res){
              if (res.status === true) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: res.message || 'Supplier berhasil ditambah.',
                    icon: "success",
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonColor: "#35baf5",
                    confirmButtonText: "Oke",
                    allowOutsideClick : false
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = '<?php echo base_url("pembelian/supplier") ?>'
                    }
                })
              } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: res.message || 'Terjadi kesalahan, silakan coba lagi.',
                    icon: "error",
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonColor: "#35baf5",
                    confirmButtonText: "Oke",
                    allowOutsideClick : false
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload()
                    }
                })
              }
          },
          error: function (xhr, status, err) {
              Swal.fire({
                  title: 'Error!',
                  text: 'Tidak bisa terhubung ke server atau terjadi error internal. Silakan coba lagi. (' + xhr.status + ')',
                  icon: "error",
                  confirmButtonColor: "#35baf5",
                  confirmButtonText: "Oke",
                  allowOutsideClick : false
              });
          },
          complete: function() {
              isSubmitting = false;
              $btn.prop('disabled', false);
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
                  <li class="breadcrumb-item"><a href="<?php echo base_url("pembelian/supplier"); ?>"><?php echo $title; ?></a></li>
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
                  <label for="kode_supplier" class="col-sm-2 col-form-label">Kode Supplier</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="kode_supplier" id="kode_supplier" placeholder="Input Kode Supplier" required>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="nama_supplier" class="col-sm-2 col-form-label">Nama Supplier</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="nama_supplier" id="nama_supplier" placeholder="Input Nama Supplier" required>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                  <div class="col-sm-10">
                      <textarea class="form-control" name="alamat" id="alamat" placeholder="Input Alamat" required></textarea>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="no_telp" class="col-sm-2 col-form-label">No. Telp</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="no_telp" id="no_telp" placeholder="Input No. Telp" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="no_rek" class="col-sm-2 col-form-label">No. Rekening</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="no_rek" id="no_rek" placeholder="Input No. Rekening" required>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="bank" class="col-sm-2 col-form-label">Bank</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="bank" id="bank" placeholder="Input Bank" required>
                  </div>
                </div>
                <div class="row">
                    <div class="col-sm-10 ms-auto">
                        <button type="button" id="btn_simpan" onclick="tambah(event);" class="btn btn-success">
                          <i class="fas fa-save me-2"></i>Simpan
                        </button>
                        <a href="<?php echo base_url("pembelian/supplier"); ?>">
                          <button type="button" class="btn btn-warning">
                            <i class="fas fa-reply me-2"></i>Kembali
                          </button>
                        </a>
                    </div>
                </div>
              </form>
            </div>
          </div><!--end card-body-->
      </div><!--end card-->
    </div><!--end col-->
  </div>
</div><!-- container -->
