<script type="text/javascript">
  $(document).ready(function () {
    Object.defineProperty(Selectr.prototype, 'mobileDevice', { get: () => false });
    jenis();
    // Format harga saat halaman dimuat
    const hargaInput = document.getElementById('nominal');
    if (hargaInput && hargaInput.value) {
      FormatCurrency(hargaInput);
    }
  })
  function edit(e) {
    let btn = $(e.target).closest('button');
    e.preventDefault();
    btn.prop("disabled", true).text("Mengirim...");
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
      btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
      return;
    }
    $.ajax({
      url: '<?php echo base_url('keuangan/pemasukan/edit') ?>',
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
              window.location.href = '<?php echo base_url() ?>keuangan/pemasukan'
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
  function jenis() {
    $.get({
      url: '<?= base_url(); ?>keuangan/pemasukan/jenis',
      dataType: 'JSON',
      success: function (data) {
        if (data != null) {
          const select_d = <?= $row['id_jenis_biaya'] ?>;
          data.data.forEach(item => {
            $('#id_jenis').append($('<option>', {
              value: item.id,
              text: item.nama,
              'data-nama': item.nama,
              selected: item.id == select_d,
            }));
          });
          //inputan 
          const selecte = $('#id_jenis option:selected').data('nama');
          $('#nama_jenis').val(selecte);
          //hapus data kalau ada
          if (window.selectrJenis) {
            Window.selectrJenis.destroy();
          }
          // inisialiasasi
          window.selectrjenis = new Selectr('#id_jenis', {
            searchable: true,
          })
        }
      }
    })
  }
  $(document).on('change', '#id_jenis', function () {
    var nama = $('#id_jenis option:selected').data('nama');
    $('#nama_jenis').val(nama);
  });

  function FormatCurrency(inputElement) {
    // Simpan posisi kursor
    const cursorPosition = inputElement.selectionStart;
    const originalLength = inputElement.value.length;
    // Ambil nilai dan bersihkan dari format sebelumnya
    let angka = inputElement.value;
    angka = angka.replace(/[^\d]/g, ''); // Hapus semua non-digit
    // Konversi ke number
    const num = Number(angka);
    // Handle NaN
    if (isNaN(num)) {
      inputElement.value = '0';
      return;
    }
    // Format dengan separator ribuan
    const formatted = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    inputElement.value = formatted;

    // Kembalikan posisi kursor setelah formatting
    const newLength = inputElement.value.length;
    const lengthDiff = newLength - originalLength;
    inputElement.setSelectionRange(cursorPosition + lengthDiff, cursorPosition + lengthDiff);
  }

</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>keuangan/pemasukan"><?php echo $title; ?></a>
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
              <div class="mb-3 row">
                <label for="edit_contoh" class="col-sm-2 col-form-label">Jenis Biaya</label>
                <div class="col-sm-10">
                  <select name="id_jenis" id="id_jenis" class="form-select">
                    <option value="">Pilih Jenis Biaya</option>
                  </select>
                  <input type="hidden" class="form-control" name="nama_jenis" id="nama_jenis" placeholder="Nama jenis"
                    readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="edit_contoh" class="col-sm-2 col-form-label">keterangan</label>
                <div class="col-sm-10">
                  <textarea class="form-control" name="keterangan" id="keterangan"
                    placeholder="keterangan"><?php echo $row['keterangan']; ?></textarea>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="edit_contoh" class="col-sm-2 col-form-label">Nominal</label>
                <div class="col-sm-10">
                  <div class="input-group">
                    <div class="input-group-text">Rp</div>
                    <input type="text" class="form-control" name="nominal" value="<?php echo $row['nominal']; ?>"
                      id="nominal" placeholder="nominal" onkeyup="FormatCurrency(this);" autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="edit(event);" class="btn btn-success"><i
                      class="fas fa-save me-2"></i>Simpan</button>
                  <a href="<?php echo base_url(); ?>keuangan/pemasukan"><button type="button" class="btn btn-warning"><i
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