<script type="text/javascript">
  $(document).ready(function() {
    
    $(`#isi_satuan_turunan_3`).prop('disabled', true).val('0');
    $(`#kode_barang_3`).prop('disabled', true).val('');
    $(`#satuan_barang_3`).val('Kosong').trigger('change'); 

    $(`#isi_satuan_turunan_2`).prop('disabled', true).val('0');
    $(`#kode_barang_2`).prop('disabled', true).val('');
    $(`#satuan_barang_2`).val('Kosong').trigger('change'); 

    $(`#satuan_barang_2`).change(function() {
        let val = $(this).val();
        if(val == 'Kosong' || val == '') {
            $(`#isi_satuan_turunan_2`).prop('disabled', true).val('0');
            $(`#kode_barang_2`).prop('disabled', true).val('');
        } else {
            $(`#isi_satuan_turunan_2`).prop('disabled', false);
            $(`#kode_barang_2`).prop('disabled', false);
        }
    });

    $(`#satuan_barang_3`).change(function() {
        let val = $(this).val();
        if(val == 'Kosong' || val == '') {
            $(`#isi_satuan_turunan_3`).prop('disabled', true).val('0');
            $(`#kode_barang_3`).prop('disabled', true).val('');
        } else {
            $(`#isi_satuan_turunan_3`).prop('disabled', false);
            $(`#kode_barang_3`).prop('disabled', false);
        }
    });
  });

  function FormatCurrency(input) {
      let value = input.value.replace(/\D/g, ''); 
      value = value.replace(/^0+/, ''); 
      if (value) {
          input.value = new Intl.NumberFormat('id-ID').format(value);
      } else {
          input.value = '';
      }
  }

  function tambah(e) {
      e.preventDefault();

    let pesanError = '';
    if ($('#id_jenis_barang').val() === '') pesanError = 'Jenis Barang wajib dipilih.';
    if ($('#nama_barang').val().trim() === '') pesanError = 'Nama Barang wajib diisi.';
    
    // Validasi Satuan 1 (Wajib)
    if ($('input[name="kode_barang[]"]').eq(0).val().trim() === '') pesanError = 'Kode Barang 1 wajib diisi.';
    if ($('select[name="id_satuan_barang[]"]').eq(0).val() === '') pesanError = 'Satuan Turunan 1 wajib dipilih.';
    if ($('input[name="isi_satuan_turunan[]"]').eq(0).val().trim() === '' || $('input[name="isi_satuan_turunan[]"]').eq(0).val() == '0') pesanError = 'Isi Satuan Turunan 1 wajib diisi dan tidak boleh nol.';
    
    // Validasi Satuan 2 (Kondisional)
    if ($('#satuan_barang_2').val() !== 'Kosong' && $('#satuan_barang_2').val() !== '') {
      if ($('#kode_barang_2').val().trim() === '') pesanError = 'Kode Barang 2 wajib diisi jika Satuan Turunan 2 dipilih.';
      if ($('#isi_satuan_turunan_2').val().trim() === '' || $('#isi_satuan_turunan_2').val() == '0') pesanError = 'Isi Satuan Turunan 2 wajib diisi dan tidak boleh nol jika Satuan 2 dipilih.';
    }

    // Validasi Satuan 3 (Kondisional)
    if ($('#satuan_barang_3').val() !== 'Kosong' && $('#satuan_barang_3').val() !== '') {
      if ($('#kode_barang_3').val().trim() === '') pesanError = 'Kode Barang 3 wajib diisi jika Satuan Turunan 3 dipilih.';
      if ($('#isi_satuan_turunan_3').val().trim() === '' || $('#isi_satuan_turunan_3').val() == '0') pesanError = 'Isi Satuan Turunan 3 wajib diisi dan tidak boleh nol jika Satuan 3 dipilih.';
    }

    if (pesanError !== '') {
        Swal.fire('Peringatan!', pesanError, 'warning');
        return;
    }

      let formData = new FormData($('#form_tambah')[0]);

      $.ajax({
          url : '<?php echo base_url('master_data/barang/tambah') ?>',
          method : 'POST',
          data : formData,
          dataType : 'json',
          processData: false,
          contentType: false, 
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
                      window.location.href = '<?php echo base_url() ?>master_data/barang'
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
                      // location.reload() 
                    }
                  })
              }
          },
          error: function(xhr, status, error) {
              console.error("AJAX Error: " + status + error);
              Swal.fire({
                  title: 'Error!',
                  text: 'Terjadi kesalahan pada server: ' + xhr.responseText,
                  icon: "error",
                  showConfirmButton: true
              });
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
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>master_data/barang/"><?php echo $title; ?></a></li>
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
              <form id="form_tambah" action="#" method="POST" enctype="multipart/form-data">
                <div class="mb-3 row">
                  <label for="id_jenis_barang" class="col-sm-2 col-form-label">Jenis Barang</label>
                  <div class="col-sm-10">
                      <select class="form-control js-example-basic-single" name="id_jenis_barang" id="id_jenis_barang" required>
                          <option value="">-- Pilih Jenis Barang --</option>
                          <?php foreach ($jenis_barang_list as $jb): ?>
                            <option value="<?php echo $jb['id']; ?>"><?php echo $jb['nama_jenis']; ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="nama_barang" class="col-sm-2 col-form-label">Nama Barang</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="nama_barang" id="nama_barang" placeholder="Input nama barang" required>
                  </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Kode Barang 1</label>
                    <div class="col-sm-2">
                        <input type="text" name="kode_barang[]" class="form-control" required>
                    </div>
                    <label class="col-sm-2 col-form-label">Satuan Turunan 1</label>
                    <div class="col-sm-2">
                        <select class="form-control js-example-basic-single" name="id_satuan_barang[]" required>
                          <option value="">-- Pilih Satuan --</option>
                          <?php foreach ($satuan_barang_list as $sb): ?>
                            <option value="<?php echo $sb['id']; ?>"><?php echo $sb['nama_satuan']; ?></option>
                          <?php endforeach; ?>
                        </select>
                    </div>
                    <label class="col-sm-2 col-form-label">Isi Satuan Turunan 1</label>
                    <div class="col-sm-2">
                        <input type="text" name="isi_satuan_turunan[]" onkeyup="FormatCurrency(this);" class="form-control" value="1" required>
                        <input type="hidden" name="urutan_satuan[]" value="1">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Kode Barang 2</label>
                    <div class="col-sm-2">
                        <input type="text" name="kode_barang[]" class="form-control" id="kode_barang_2">
                    </div>
                    <label class="col-sm-2 col-form-label">Satuan Turunan 2</label>
                    <div class="col-sm-2">
                        <select class="form-control js-example-basic-single" id="satuan_barang_2" name="id_satuan_barang[]">
                            <option value="Kosong" selected>Kosong</option>
                            <?php foreach ($satuan_barang_list as $sb): ?>
                                <option value="<?php echo $sb['id']; ?>"><?php echo $sb['nama_satuan']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label class="col-sm-2 col-form-label">Isi Satuan Turunan 2</label>
                    <div class="col-sm-2">
                        <input type="text" name="isi_satuan_turunan[]" onkeyup="FormatCurrency(this);" class="form-control" value="0" id="isi_satuan_turunan_2">
                        <input type="hidden" name="urutan_satuan[]" value="2">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Kode Barang 3</label>
                    <div class="col-sm-2">
                        <input type="text" name="kode_barang[]" class="form-control" id="kode_barang_3">
                    </div>
                    <label class="col-sm-2 col-form-label">Satuan Turunan 3</label>
                    <div class="col-sm-2">
                        <select class="form-control js-example-basic-single" name="id_satuan_barang[]" id="satuan_barang_3">
                            <option value="Kosong" selected>Kosong</option>
                            <?php foreach ($satuan_barang_list as $sb): ?>
                                <option value="<?php echo $sb['id']; ?>"><?php echo $sb['nama_satuan']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label class="col-sm-2 col-form-label">Isi Satuan Turunan 3</label>
                    <div class="col-sm-2">
                        <input type="text" name="isi_satuan_turunan[]" onkeyup="FormatCurrency(this);" class="form-control" value="0" id="isi_satuan_turunan_3">
                        <input type="hidden" name="urutan_satuan[]" value="3">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-10 ms-auto">
                        <button type="submit" onclick="tambah(event);" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan</button>
                        <a href="<?php echo base_url(); ?>master_data/barang"><button type="button" class="btn btn-warning"><i class="fas fa-reply me-2"></i>Kembali</button></a>
                    </div>
                </div>
              </form>
            </div>
          </div><!--end card-body-->
      </div><!--end card-->
    </div><!--end col-->
  </div>
</div><!-- container -->
