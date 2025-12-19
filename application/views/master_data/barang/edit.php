<script type="text/javascript">
  $(document).ready(function() {
   
function toggleDetailFields(selectId, kodeBarangId, isiSatuanId) {
        let val = $(selectId).val();
        if (val == 'Kosong' || val == '') {
            $(isiSatuanId).prop('readonly', true).val('0');
            $(kodeBarangId).prop('readonly', true).val('');
        } else {
            $(isiSatuanId).prop('readonly', false);
            $(kodeBarangId).prop('readonly', false);
        }
    }

    toggleDetailFields('#satuan_barang_2', '#kode_barang_2', '#isi_satuan_turunan_2');
    toggleDetailFields('#satuan_barang_3', '#kode_barang_3', '#isi_satuan_turunan_3');

    //Satuan Turunan 2
    $(`#satuan_barang_2`).change(function() {
        toggleDetailFields(this, '#kode_barang_2', '#isi_satuan_turunan_2');
    });

    //Satuan Turunan 3
    $(`#satuan_barang_3`).change(function() {
        toggleDetailFields(this, '#kode_barang_3', '#isi_satuan_turunan_3');
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

  function edit(e) {
      e.preventDefault();

      let pesanError = '';
      if ($('#edit_jenis_barang').val() === '') pesanError = 'Jenis Barang wajib dipilih.';
      if ($('input[name="nama_barang"]').val().trim() === '') pesanError = 'Nama Barang wajib diisi.';
      
      // Validasi Satuan 1
      if ($('input[name="kode_barang[]"]').eq(0).val().trim() === '') pesanError = 'Kode Barang 1 wajib diisi.';
      if ($('select[name="id_satuan_barang[]"]').eq(0).val() === '') pesanError = 'Satuan Turunan 1 wajib dipilih.';
      if ($('input[name="isi_satuan_turunan[]"]').eq(0).val().trim() === '' || $('input[name="isi_satuan_turunan[]"]').eq(0).val() == '0') pesanError = 'Isi Satuan Turunan 1 wajib diisi dan tidak boleh nol.';
      
      // Validasi Satuan 2
      if ($('#satuan_barang_2').val() !== 'Kosong' && $('#satuan_barang_2').val() !== '') {
        if ($('#kode_barang_2').val().trim() === '') pesanError = 'Kode Barang 2 wajib diisi jika Satuan Turunan 2 dipilih.';
        if ($('#isi_satuan_turunan_2').val().trim() === '' || $('#isi_satuan_turunan_2').val() == '0') pesanError = 'Isi Satuan Turunan 2 wajib diisi dan tidak boleh nol jika Satuan 2 dipilih.';
      }

      // Validasi Satuan 3
      if ($('#satuan_barang_3').val() !== 'Kosong' && $('#satuan_barang_3').val() !== '') {
        if ($('#kode_barang_3').val().trim() === '') pesanError = 'Kode Barang 3 wajib diisi jika Satuan Turunan 3 dipilih.';
        if ($('#isi_satuan_turunan_3').val().trim() === '' || $('#isi_satuan_turunan_3').val() == '0') pesanError = 'Isi Satuan Turunan 3 wajib diisi dan tidak boleh nol jika Satuan 3 dipilih.';
      }

      if (pesanError !== '') {
          Swal.fire('Peringatan!', pesanError, 'warning');
          return;
      }

      let formData = new FormData($('#form_edit')[0]);

      $.ajax({
          url : '<?php echo base_url('master_data/barang/edit') ?>',
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
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>master_data/barang"><?php echo $title; ?></a></li>
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
              <form id="form_edit" action="#" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <div class="mb-3 row">
                  <label for="edit_barang" class="col-sm-2 col-form-label">Barang</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="nama_barang" value="<?php echo $row['nama_barang']; ?>" id="edit_barang" placeholder="Input barang" required>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="edit_jenis_barang" class="col-sm-2 col-form-label">Jenis Barang</label>
                  <div class="col-sm-10">
                      <select class="form-control js-example-basic-single" name="id_jenis_barang" id="edit_jenis_barang" required>
                          <option value="">-- Pilih Jenis Barang --</option>
                          <?php foreach ($jenis_barang_list as $jenis) : ?>
                              <option value="<?php echo $jenis['id']; ?>" <?php echo ($jenis['id'] == $row['id_jenis_barang']) ? 'selected' : ''; ?>>
                                  <?php echo $jenis['nama_jenis']; ?>
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                </div>

                <?php
                $detail_satuan_existing = [];
                if (isset($row['detail_satuan']) && is_array($row['detail_satuan'])) {
                    foreach ($row['detail_satuan'] as $detail) {
                        $detail_satuan_existing[$detail['urutan_satuan']] = $detail;
                    }
                }
                for ($i = 1; $i <= 3; $i++) :
                    $detail_data = isset($detail_satuan_existing[$i]) ? $detail_satuan_existing[$i] : null;
                    $kode_barang_val = $detail_data ? $detail_data['kode_barang'] : '';
                    $id_satuan_val = $detail_data ? $detail_data['id_satuan_barang'] : 'Kosong';
                    $isi_satuan_val = $detail_data ? $detail_data['isi_satuan_turunan'] : '0';
                    $disabled_attr = ($i > 1 && !$detail_data) ? 'readonly' : '';
                ?>
                <div class="mb-3 row">
                    
                    <input type="hidden" name="id_barang_detail[]" value="<?php echo $detail_data ? $detail_data['id'] : ''; ?>">
                    <label class="col-sm-2 col-form-label">Kode Barang <?php echo $i; ?></label>
                    <div class="col-sm-2">
                        <input type="text" name="kode_barang[]" class="form-control" id="kode_barang_<?php echo $i; ?>" value="<?php echo htmlspecialchars($kode_barang_val); ?>" <?php echo $disabled_attr; ?> <?php echo ($i == 1) ? 'required' : ''; ?>>
                    </div>
                    <label class="col-sm-2 col-form-label">Satuan Turunan <?php echo $i; ?></label>
                    <div class="col-sm-2">
                        <select class="form-control js-example-basic-single" name="id_satuan_barang[]" id="satuan_barang_<?php echo $i; ?>" <?php echo ($i == 1) ? 'required' : ''; ?>>
                          <?php if ($i > 1): ?>
                            <option value="Kosong" <?php echo ($id_satuan_val == 'Kosong' || $id_satuan_val == '') ? 'selected' : ''; ?>>Kosong</option>
                          <?php endif; ?>
                          <?php foreach ($satuan_barang_list as $sb): ?>
                            <option value="<?php echo $sb['id']; ?>" <?php echo ($sb['id'] == $id_satuan_val) ? 'selected' : ''; ?>>
                                <?php echo $sb['nama_satuan']; ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                    </div>
                    <label class="col-sm-2 col-form-label">Isi Satuan Turunan <?php echo $i; ?></label>
                      <div class="col-sm-2">
                        <input type="text" name="isi_satuan_turunan[]" class="form-control" value="<?php echo htmlspecialchars($isi_satuan_val); ?>" id="isi_satuan_turunan_<?php echo $i; ?>" <?php echo $disabled_attr; ?> <?php echo ($i == 1) ? 'required' : ''; ?>>
                        <input type="hidden" name="urutan_satuan[]" value="<?php echo $i; ?>">
                    </div>
                </div>
                <?php endfor; ?>

                <div class="row">
                    <div class="col-sm-10 ms-auto">
                        <button type="submit" onclick="edit(event);" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan</button>
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
