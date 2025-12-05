<script>
  $(document).ready(function () {
    poli();
    tgl();
    tgl1();
    $('#pagination').on('click', function (e) {
      e.stopPropagation();
    });
    $('#jumlah_tampil').on('change', function () {
      paging();
    });
    $('#btn_cari').click(function () {
      pasien();
    });
    $('input[name="tipe_pasien"]').change(function () {
      if ($('input[name="tipe_pasien"]:checked').val() == 'baru') {
        $('#section-lama').hide();
        $('#section-baru').slideDown();
        $('#nama_pc').prop('required', false);
        $('#id_pasien').prop('required', false);
        $('#section-baru').find('input, select, textarea').not('[readonly]').prop('required', true);

      } else {
        $('#section-baru').hide();
        $('#section-lama').slideDown();
        $('#section-baru').find('input, select, textarea').not('[readonly]').prop('required', false);
        $('#nama_pc').prop('required', true);
        $('#id_pasien').prop('required', true);
      }
    }).trigger('change');
  });

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

  function tambah(e) {
    let btn = $(e.target).closest('button');
    e.preventDefault();
    btn.prop("disabled", true).text("Mengirim...");
    if (!validateForm('#form_tambah')) {
      btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
      return;
    };
    $.post({
      url: '<?php echo base_url('resepsionis/pendaftaran/tambah') ?>',
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
              window.location.href = '<?php echo base_url() ?>resepsionis/pendaftaran'
            }
          })
        } else {
          Swal.fire({
            title: 'Gagal!',
            html: res.message,
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
    })
  }
  function pasien() {
    const nama_p = $('#nama_p').val();
    let count_header = $(`#table-data thead tr th`).length;
    $.ajax({
      url: '<?= base_url("resepsionis/pendaftaran/pasien") ?>',
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
  function pilihPasien(encodedString) {
    const ambil = JSON.parse(atob(encodedString));

    if (ambil != null) {
      $('#id_pasien').val(ambil.id);
      $('#nama_pc').val(ambil.nama_pasien);
      $('#nama_pasien').val(ambil.nama_pasien);
      $('#no_rm').val(ambil.no_rm);
      $('#nik').val(ambil.nik);
      $('#tgl_lahir').val(ambil.tanggal_lahir);
      $('#umur').val(ambil.umur);
      $('#alamat').val(ambil.alamat);
      $('#pekerjaan').val(ambil.pekerjaan);
      $('#no_telpon').val(ambil.no_telp);
      $('#nama_wali').val(ambil.nama_wali);
      $('#alergi').val(ambil.alergi);
      $('#status_op').val(ambil.status_operasi);
      $('#jk').val(ambil.jenis_kelamin).trigger('change');
      $('#st_perkawinan').val(ambil.st_perkawinan).trigger('change');
      $('#golongan_darah').val(ambil.golongan_darah).trigger('change');
      $('#modalcari').modal('hide');
    }
  }
  function cariPasienModal(keyword) {
    $.ajax({
      url: '<?= base_url("resepsionis/pendaftaran/pasien") ?>',
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
          table = `<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>`;
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
      url: '<?= base_url(); ?>resepsionis/pendaftaran/poli',
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
    var idpoli = $(this).val();
    $('#nama_poli').val(nama);
    dokter(idpoli);
  });
  function dokter(idpoli) {
    $.post({
      data: {
        id_poli: idpoli,
      },
      url: '<?= base_url("resepsionis/pendaftaran/dokter") ?>',
      dataType: 'JSON',
      success: function (data) {
        if (data != null) {
          $('#id_dokter').empty().append('<option value="">Pilih Dokter</option>');
          if (data && data.length > 0) {
            let dokterDitemukan = false;
            data.forEach(item => {
              // if (item.id_poli == idpoli ) {
              $('#id_dokter').append($('<option>', {
                value: item.id_pegawai,
                text: item.nama_pegawai,
                'data-nama': item.nama_pegawai,
              }));
              dokterDitemukan = true;
            });
            $('#id_dokter').prop('disabled', false);
          } else {
            $('#id_dokter').html('<option value="">Tidak ada dokter untuk poli ini</option>');
            $('#id_dokter').prop('disabled', true);
          }
        }
      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
        $('#id_dokter').empty().append('<option value="">Error loading data</option>');
      }
    })
  }
  $(document).on('change', '#id_dokter', function () {
    var nama = $('#id_dokter option:selected').data('nama');
    $('#nama_dokter').val(nama);
  });
  function tgl() {
    const tanggalInput = document.getElementById('tgl_lahir');
    tanggalInput.addEventListener('changeDate', function () {
      hitungUmur();
    })
  }
  function hitungUmur() {
    var tanggal_lahir = $('#tgl_lahir').val();
    if (tanggal_lahir) {
      const [day, month, year] = tanggal_lahir.split("-");
      var birthDate = new Date(`${year}-${month}-${day}`);
      var today = new Date();
      var age = today.getFullYear() - birthDate.getFullYear();
      var m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      $('#umur').val(age);
    }
  }
  function tgl1() {
    const tanggalInput = document.getElementById('tgl_lahir1');
    const datepicker = new Datepicker(tanggalInput, {
      format: 'dd-mm-yyyy',
      autohide: true,
    });
    tanggalInput.addEventListener('changeDate', function () {
      hitungUmur1();
    })
  }
  function hitungUmur1() {
    var tanggal_lahir = $('#tgl_lahir1').val();
    if (tanggal_lahir) {
      const [day, month, year] = tanggal_lahir.split("-");
      var birthDate = new Date(`${year}-${month}-${day}`);
      var today = new Date();
      var age = today.getFullYear() - birthDate.getFullYear();
      var m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      $('#umur1').val(age);
    }
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
</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>resepsionis/pendaftaran">
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
            <form id="form_tambah">
              <div class="mb-3">
                <div class="mb-3 row">
                  <label for="tambah_contoh" class="col-sm-2 col-form-label">Type Pasien</label>
                  <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="tipe_pasien" id="btn-lama" value="lama"
                        checked>
                      <label class="form-check-label" for="lama">Pasien Lama</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="tipe_pasien" id="btn-baru" value="baru">
                      <label class="form-check-label" for="baru">Pasien Baru</label>
                    </div>
                  </div>
                </div>
              </div>
              <div id="section-lama">
                <div class="row">
                  <div class="mb-3 row" id="pencarian_pasien">
                    <label for="nama_p" class="col-sm-2 col-form-label">Cari Pasien</label>
                    <div class="col-sm-10">
                      <div class="row">
                        <div class="col-sm-11">
                          <input type="text" class="form-control" name="nama_pc" id="nama_pc"
                            placeholder="Klik Tombol Cari" autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-1">
                          <button id="btn_cari" type="button" class="btn btn-primary w-100">Cari</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">No. RM</label>
                      <div class="col-sm-8">
                        <input type="hidden" class="form-control" name="id_pasien" id="id_pasien" readonly>
                        <input type="hidden" class="form-control" name="nama_pasien" id="nama_pasien"
                          placeholder="Nama pasien" readonly>
                        <input type="text" class="form-control" name="no_rm" id="no_rm" placeholder="No RM" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">NIK</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="nik" id="nik" placeholder="NIK" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Jenis Kelamin</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="jk" id="jk" placeholder="Jenis kelamin" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Tanggal Lahir</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="tgl_lahir" id="tgl_lahir"
                          placeholder="Tanggal lahir" autocomplete="off" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Umur</label>
                      <div class="col-sm-8">
                        <input type="number" class="form-control" name="umur" id="umur" placeholder="Umur" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">No Telpon</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="no_telpon" id="no_telpon" placeholder="No telpon"
                          readonly >
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Alamat</label>
                      <div class="col-sm-8">
                        <textarea name="alamat" id="alamat" placeholder="Alamat" class="form-control"
                          readonly></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Bagian form input manual pasien baru -->
              <div id="section-baru" style="display: none;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Nama Pasien</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="nama_pasien1" id="nama_pasien1"
                          placeholder="Nama pasien" autocomplete="off" required>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">NIK</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="nik1" id="nik1" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="NIK" autocomplete="off" required >
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label ">Jenis Kelamin</label>
                      <div class="col-sm-8">
                        <select name="jk1" id="jk1" class=" form-select" required>
                          <option value="">Pilih Jenis Kelamin..</option>
                          <option value="Laki-laki">Laki-laki</option>
                          <option value="Perempuan">Perempuan</option>
                        </select>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Tanggal Lahir</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="tgl_lahir1" id="tgl_lahir1"
                          placeholder="Tanggal lahir" autocomplete="off" required>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Umur</label>
                      <div class="col-sm-8">
                        <input type="number" class="form-control" name="umur1" id="umur1" placeholder="Umur" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Alamat</label>
                      <div class="col-sm-8">
                        <textarea name="alamat1" id="alamat1" placeholder="Alamat" class="form-control"
                          required></textarea>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Pekerjaan</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="pekerjaan1" id="pekerjaan1"
                          placeholder="Pekerjaan" autocomplete="off" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">No Telpon</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="no_telpon1" id="no_telpon1"
                          placeholder="No telpon" autocomplete="off" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Status Perkawinan</label>
                      <div class="col-sm-8">
                        <select name="st_perkawinan1" id="st_perkawinan1" class="form-select" required>
                          <option value="">Pilih Status..</option>
                          <option value="Belum Kawin">Belum Kawin</option>
                          <option value="Kawin">Kawin</option>
                          <option value="Cerai Hidup">Cerai Hidup</option>
                          <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Nama Wali</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="nama_wali1" id="nama_wali1"
                          placeholder="Nama wali" autocomplete="off" required>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Golongan Darah</label>
                      <div class="col-sm-8">
                        <select name="golongan_darah1" id="golongan_darah1" class="form-select" required>
                          <option value="">Pilih Golongan Darah..</option>
                          <option value="A">A</option>
                          <option value="B">B</option>
                          <option value="AB">AB</option>
                          <option value="O">O</option>
                        </select>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Alergi</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="alergi1" id="alergi1" placeholder="Alergi"
                          autocomplete="off" required>
                        <span class="form-text mt-1 text-danger"><strong>Perhatian: </strong>jika pasien tidak ada
                          riwayat alergi isi dengan "-"</span>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tambah_contoh" class="col-sm-4 col-form-label">Status Operasi</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="status_op1" id="status_op1"
                          placeholder="Status operasi" autocomplete="off" required>
                        <span class="form-text mt-1 text-danger"><strong>Perhatian: </strong>jika pasien tidak ada
                          riwayat operasi isi dengan "-"</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr>
              <div class="mb-3 row">
                <p>Detail Kunjungan</p>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Poli</label>
                <div class="col-sm-10">
                  <select name="id_poli" id="id_poli" class="form-select" required>
                    <option value="">Pilih Poli..</option>
                  </select>
                  <input type="hidden" class="form-control" name="nama_poli" id="nama_poli" placeholder="Nama poli"
                    readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Dokter</label>
                <div class="col-sm-10">
                  <select name="id_dokter" id="id_dokter" class="form-select" required>
                    <option value="">Pilih Dokter..</option>
                  </select>
                  <input type="hidden" class="form-control" name="nama_dokter" id="nama_dokter"
                    placeholder="Nama dokter" readonly>
                </div>
              </div>
            </form>
            <div class="row">
              <div class="col-sm-10 ms-auto">
                <button type="button" onclick="tambah(event);" class="btn btn-success"><i
                    class="fas fa-save me-2"></i>Simpan</button>
                <a href="<?php echo base_url(); ?>resepsionis/pendaftaran"><button type="button"
                    class="btn btn-warning"><i class="fas fa-reply me-2"></i>Kembali</button></a>
              </div>
            </div>
            <!-- Bagian pasien lama (pencarian) -->
          </div>
        </div>
      </div><!--end card-body-->
    </div><!--end card-->
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
          <input type="text" class="form-control" name="nama_modal" id="nama_modal"
            oninput="cariPasienModal(this.value)" placeholder="Cari pasien">
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
                <label class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah
                  Tampil</label>
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
</div>