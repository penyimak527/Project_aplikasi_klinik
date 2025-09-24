<script type="text/javascript">
  $(document).ready(function () {
    Object.defineProperty(Selectr.prototype, 'mobileDevice', { get: () => false });
    jenis();
  })
  function tambah(e) {
    e.preventDefault();
    const keterangan = $('#keterangan').val();
    const nominal = $('#nominal').val();
    const id_j = $('#id_jenis').val();
    const nama_jenis = $('#nama_jenis').val();
    if (keterangan == "" || nominal == "" || id_j == "" || nama_jenis == "") {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Inputan Kosong",
      });
      return;
    }
    $.ajax({
      url: '<?php echo base_url('keuangan/pengeluaran/tambah') ?>',
      method: 'POST',
      data: $('#form_tambah').serialize(),
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
              window.location.href = '<?php echo base_url() ?>keuangan/pengeluaran'
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
  function jenis() {
    $.get({
      url: '<?= base_url(); ?>keuangan/pengeluaran/jenis',
      dataType: 'JSON',
      success: function (data) {
        if (data != null) {
          data.data.forEach(item => {
            $('#id_jenis').append($('<option>', {
              value: item.id,
              text: item.nama,
              'data-nama': item.nama,
            }));
          });
          if (window.selectrJenis) {
            window.selectJenis.destroy();
          }
          window.selectJenis = new Selectr('#id_jenis', {
            searchable: true,
          });
        }
      }
    })
  }
  $(document).on('change', '#id_jenis', function () {
    var nama = $('#id_jenis option:selected').data('nama');
    $('#nama_jenis').val(nama);
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
                href="<?php echo base_url(); ?>keuangan/pengeluaran"><?php echo $title; ?></a></li>
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
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Jenis Biaya</label>
                <div class="col-sm-10">
                  <select name="id_jenis" id="id_jenis" class="form-select">
                    <option value="">Pilih Jenis Biaya</option>
                  </select>
                  <input type="hidden" class="form-control" name="nama_jenis" id="nama_jenis" placeholder="Jenis"
                    readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-10">
                  <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nominal</label>
                <div class="col-sm-10">
                  <div class="input-group">
                    <div class="input-group-text">Rp</div>
                    <input type="text" class="form-control" name="nominal" id="nominal" onkeyup="FormatCurrency(this);"
                      placeholder="nominal" autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="tambah(event);" class="btn btn-success"><i
                      class="fas fa-save me-2"></i>Simpan</button>
                  <a href="<?php echo base_url(); ?>keuangan/pengeluaran"><button type="button"
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