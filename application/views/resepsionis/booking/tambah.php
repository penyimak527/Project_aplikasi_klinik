<script type="text/javascript">
  $(document).ready(function () {
    const tanggalInput = document.getElementById('tanggal');
    const datepicker = new Datepicker(tanggalInput, {
      format: 'dd-mm-yyyy',
      autohide: true
    });

    // Event listener yang benar untuk vanillajs-datepicker
    tanggalInput.addEventListener('changeDate', function (e) {
      // Detail ada di e.detail
      const selectedDate = e.detail.date;
      const hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      const namaHari = hariNames[selectedDate.getDay()];
      $('#hari').val(namaHari);
      const idPoli = $('#id_poli').val();
      if (idPoli) {
        dokter(idPoli);
      }
    });
    waktuu();
    poli();
    dokter();
    $('#pagination').on('click', function (e) {
      e.stopPropagation();
    });
    $("#jumlah_tampil").change(function () {
      pasien();
    })
  })
 // untuk validasi form pada bagian required
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
        text: 'Harap isi semua kolom yang wajib diisi.',
        icon: 'error',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Oke'
      });
    }
    return isValid;
  }

  function waktuu() {
    var timeInput = document.getElementById('waktu');
    var timeMask = IMask(timeInput, {
      mask: 'HH:MM',
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
        }
      },
      lazy: false,
      placeholderChar: '_'
    });
  }
  $(document).on('change', '#waktu', function () {
    const idPoli = $('#id_poli').val();
    if (idPoli) {
      dokter(idPoli); // Panggil dengan parameter idPoli
    }
  });
  function tambah(e) {
     let btn = $(e.target).closest('button');
    e.preventDefault();
    btn.prop("disabled", true).text("Mengirim...");
   if (!validateForm('#form_tambah')) {
      btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
      return;
    };
    $.ajax({
      url: '<?php echo base_url('resepsionis/booking/tambah') ?>',
      method: 'POST',
      data: $('#form_tambah').serialize(),
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
      success: function (res, status) {
        console.log(res, status);
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
              window.location.href = '<?php echo base_url() ?>resepsionis/booking'
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
  function pasien() {
    let count_header = $(`#table-data thead tr th`).length;
    const nama_p = $('#nama_p').val();
    console.log(count_header);
    
    $.ajax({
      url: '<?= base_url("resepsionis/booking/pasien") ?>',
      data: { cari: nama_p },
      type: 'POST',
      dataType: 'JSON',
       beforeSend: () => {
        let loading = `<tr id="tr-loading">
                                  <td colspan="${count_header}" class="text-center">
                                      <div class="loader">
                                          <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                      </div>
                                  </td>
                              </tr>`;
        $(`#table-data tbody`).html(loading);
      },
      success: function (res) {
        if (res.status && res.data.length > 0) {
          let table = "";
          let i = 1;
          for (const item of res.data) {
            table += `
            <tr style="cursor:pointer;" onclick="pilihPasien('${btoa(JSON.stringify(item))}')" >
              <td>${i++}</td>
              <td>${item.nama_pasien}</td>
              <td>${item.nik}</td>
              <td>${item.umur}</td>
              <td>${item.jenis_kelamin}</td>
            </tr>
          `;
          }
          $('#table-data tbody').html(table);
          paging();
          $('#modalcari').modal('show');
        } else {
          $('#table-data tbody').html('<tr><td colspan="5" class="text-center">Data tidak ditemukan</td></tr>');
        }
      },
      error: function (xhr) {
        console.error('Gagal:', xhr.responseText);
      }
    });
  }
  function paging($selector) {
    var jumlah_tampil = $('#jumlah_tampil').val();
    if (typeof $selector == 'undefined') {
      $selector = $("#table-data tbody tr");
    }
    window.tp = new Pagination('#pagination', {
      itemsCount: $selector.length,
      pageSize: parseInt(jumlah_tampil),
      onPageSizeChange: function (ps) {
        console.log('changed to ' + ps);
      },
      onPageChange: function (paging) {
        var start = paging.pageSize * (paging.currentPage - 1),
          end = start + paging.pageSize,
          $rows = $selector;

        $rows.hide();

        for (var i = start; i < end; i++) {
          $rows.eq(i).show();
        }
      }
    });
  }

  function pilihPasien(encodedString) {
    const ambil = JSON.parse(atob(encodedString));
    if (ambil != null) {
      $('#id_pasien').val(ambil.id);
      $('#tampil_nama').val(ambil.nama_pasien);
      $('#nama_pasien').val(ambil.nama_pasien);
      $('#no_rm').val(ambil.no_rm);
      $('#nik').val(ambil.nik);
      $('#jenis_kelamin').val(ambil.jenis_kelamin);
      $('#tanggal_lahir').val(ambil.tanggal_lahir);
      $('#umur').val(ambil.umur);
      $('#alamat').val(ambil.alamat);
      $('#pekerjaan').val(ambil.pekerjaan);
      $('#telpon').val(ambil.no_telp);
      $('#st_perkawinan').val(ambil.status_perkawinan);
      $('#nama_wali').val(ambil.nama_wali);
      $('#golongan_darah').val(ambil.golongan_darah);
      $('#alergi').val(ambil.alergi);
      $('#riwayat_operasi').val(ambil.status_operasi);
      $('#modalcari').modal('hide');

    }
  }
  function cariPasienModal(keyword) {
    $.ajax({
      url: '<?= base_url("resepsionis/booking/pasien") ?>',
      data: { cari: keyword },
      type: 'POST',
      dataType: 'JSON',
      success: function (res) {
        let table = "";
        if (res.status && res.data.length > 0) {
          let i = 1;
          for (const item of res.data) {
            table += `
            <tr style="cursor:pointer;" onclick="pilihPasien('${btoa(JSON.stringify(item))}')">
              <td>${i++}</td>
              <td>${item.nama_pasien}</td>
              <td>${item.nik}</td>
              <td>${item.umur}</td>
              <td>${item.jenis_kelamin}</td>
            </tr>
          `;
          }
        } else {
          table = '<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>';
        }
        $('#table-data tbody').html(table);
      },
      error: function (xhr) {
        console.error('Gagal:', xhr.responseText);
      }
    });
  }
  function poli() {
    $.get({
      url: '<?= base_url(); ?>resepsionis/booking/poli',
      dataType: 'JSON',
      success: function (data) {
        if (data != null) {
          data.forEach(item => {
            $('#id_poli').append($('<option>', {
              value: item.id,
              text: item.nama,
              'data-nama': item.nama,
              'data-id': item.id,
            }));
          });
        }
      }
    })
  }
  $(document).on('change', '#id_poli', function () {
    var nama = $('#id_poli option:selected').data('nama');
    var idPoli = $(this).val();
    $('#nama_poli').val(nama);
    dokter(idPoli);
  });
  function dokter(idPoli) {
    // masih terjadi error waktu
    var waktu = $('#waktu').val();
    var hari = $('#hari').val();
    // Validasi: pastikan hari dan waktu sudah dipilih
    if (!hari || !waktu) {
      $('#id_dokter').empty().append('<option value="">Pilih tanggal dan waktu terlebih dahulu</option>').prop('disabled', true);
      return;
    }
    // console.log(idPoli, hari, waktu);   masih terjadi error
    $.ajax({
      url: '<?= base_url("resepsionis/booking/dokter") ?>',
      type: 'POST',
      data: {
        id_poli: idPoli,
        hari: hari,
        waktu: waktu + ':00',
      },
      dataType: 'JSON',
      success: function (data) {
        $('#id_dokter').empty();
        if (data && data.length > 0) {
          // Ada dokter, tampilkan list dokter
          $('#id_dokter').append('<option value="">Pilih Dokter</option>');
          data.forEach(item => {
            $('#id_dokter').append($('<option>', {
              value: item.id_pegawai,
              text: item.nama_pegawai,
              'data-nama': item.nama_pegawai,
            }));
          });
          $('#id_dokter').prop('disabled', false);
        } else {
          // Tidak ada dokter
          $('#id_dokter').append('<option value="">Tidak ada dokter untuk poli ini</option>');
          $('#id_dokter').prop('disabled', true);
        }
      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
        $('#id_dokter').empty().append('<option value="">Loading data</option>');
      }
    })
  }
  $(document).on('change', '#id_dokter', function () {
    var nama = $('#id_dokter option:selected').data('nama');
    $('#nama_dokter').val(nama);
  });
</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>resepsionis/booking">
                <?php echo $title; ?>
              </a></li>
            <li class="breadcrumb-item active">Tambah</li>
          </ol>
        </div>
        <h4 class="page-title">
          <?php echo $title; ?>
        </h4>
      </div><!--end page-title-box-->
    </div><!--end col-->
  </div>
  <!-- end page title end breadcrumb -->
  <input type="hidden" id="hari" name="hari" class="form-control">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header pt-3 pb-3">
          <h4 class="card-title">Tambah
            <?php echo $title; ?>
          </h4>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="general-label">
            <div class="mb-3 row">
              <label for="nama_p" class="col-sm-2 col-form-label">Cari Pasien</label>
              <div class="col-sm-10">
                <div class="row">
                  <div class="col-sm-11">
                    <input type="text" class="form-control" id="tampil_nama" placeholder="Klik Tombol Cari" readonly required>
                  </div>
                  <div class="col-sm-1">
                    <button onclick="pasien()" class="btn btn-primary w-100">Cari</button>
                  </div>
                </div>
              </div>
            </div>
            <form id="form_tambah">
              <!-- 2 kolom inputan data pasien -->
              <input type="hidden" class="form-control" name="nama_pasien" id="nama_pasien" placeholder="Nama pasien" required
                readonly>
              <input type="hidden" class="form-control" name="id_pasien" id="id_pasien" placeholder="Nama pasien" required>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">No RM</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="no_rm" id="no_rm" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">NIK</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="nik" id="nik" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Jenis Kelamin</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="jenis_kelamin" id="jenis_kelamin" required readonly>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Tanggal Lahir</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="tanggal_lahir" id="tanggal_lahir" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Umur</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="umur" id="umur" readonly>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Alamat</label>
                    <div class="col-sm-8">
                      <textarea name="alamat" id="alamat" class="form-control" readonly required></textarea>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Pekerjaan</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="pekerjaan" id="pekerjaan" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">No Telepon</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="telpon" id="telpon" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Status Kawin</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="st_perkawinan" id="st_perkawinan" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Nama Wali</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="nama_wali" id="nama_wali" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Gol. Darah</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="golongan_darah" id="golongan_darah" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Alergi</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="alergi" id="alergi" readonly required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-4 col-form-label">Riwayat Operasi</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" name="riwayat_operasi" id="riwayat_operasi" readonly required>
                    </div>
                  </div>
                </div>
                <!-- Data Booking -->
                <hr>
                <div class="row">
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-2 col-form-label">Tanggal Kunjungan</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="tanggal" id="tanggal" placeholder="Tanggal booking"
                        autocomplete="off" required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-2 col-form-label">Waktu Kunjungan</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="waktu" id="waktu" placeholder="Waktu booking"
                        autocomplete="off" required>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Poli</label>
                    <div class="col-sm-10">
                      <select name="id_poli" id="id_poli" class="form-select" required>
                        <option value="">Pilih Poli..</option>
                      </select>
                      <input type="hidden" class="form-control" name="nama_poli" id="nama_poli"
                        placeholder="Input nama poli" readonly>
                    </div>
                  </div>
                  <div class="mb-3 row">
                    <label for="tambah_contoh" class="col-sm-2 col-form-label">Dokter Tersedia</label>
                    <div class="col-sm-10">
                      <select name="id_dokter" id="id_dokter" class="form-select" required>Pilih Nama Dokter</select>
                      <input type="hidden" class="form-control" name="nama_dokter" id="nama_dokter" readonly>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-10 ms-auto">
                    <button type="button" onclick="tambah(event);" class="btn btn-success"><i
                        class="fas fa-save me-2"></i>Simpan</button>
                    <a href="<?php echo base_url(); ?>resepsionis/booking"><button type="button"
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

<div class="modal fade bd-example-modal-lg" id="modalcari" tabindex="-1" role="dialog"
  aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="myLargeModalLabel">
          <i class="fas fa-user-md me-2"></i>Cari Pasien <span id="detail_nama_pasien"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control" name="nama_modal" id="nama_modal" oninput="cariPasienModal(this.value)"
          placeholder="Cari pasien">
        <div class="table-responsive">
          <table class="table table-bordered" id="table-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Umur</th>
                <th>Jenis Kelamin</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="row mt-3">
          <div class="col-sm-6">
            <div id="pagination"></div>
          </div>
          <div class="col-sm-6">
            <div class="row">
              <div class="col-md-6">&nbsp;</div>
              <label class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah Tampil</label>
              <div class="col-md-3 pull-right">
                <select class="form-control" id="jumlah_tampil">
                  <option value="10">10</option>
                  <option value="20">20</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>
            </div>
          </div>
          </di>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Close
          </button>
        </div>
      </div>
    </div>
  </div>
</div>