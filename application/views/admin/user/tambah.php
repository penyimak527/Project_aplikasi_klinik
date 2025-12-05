<script type="text/javascript">
  $(document).ready(function () {
    level();
    $('#btn_cari').click(function () {
      pegawai();
    });
     $('#pagination').on('click', function (e) {
      e.stopPropagation();
    });
  });
   function validateForm(formSelector) {
    let isValid = true;
    $(formSelector + ' [required]').removeClass('is-invalid');
    $(formSelector + ' [required]').each(function() {
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

  function tambah(e) {
    let btn = $(e.target).closest('button');
    btn.prop("disabled", true).text("Mengirim...");

    e.preventDefault();
      if (!validateForm('#form_tambah')) {
      btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');

      return;
    }
    $.ajax({
      url: '<?php echo base_url('admin/user/tambah') ?>',
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
  function pegawai() {
   // const nama_p = $('#nama_pg').val(); 
    let count_header = $(`#table-data thead tr th`).length;
    $.ajax({
      url: '<?= base_url("admin/user/pegawai") ?>',
      // data: { cari: nama_p },
      type: 'GET',
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
        if (res.result && res.data.length > 0) {
          let table = "";
          let i = 1;
          for (const item of res.data) {
            table += `
            <tr style="cursor:pointer;" onclick="pilihPegawai('${btoa(JSON.stringify(item))}')" >
              <td>${i++}</td>
              <td>${item.nama}</td>
              <td>${item.nama_jabatan}</td>
            </tr>
          `;
          }
          $('#table-data tbody').html(table);
          paging();
          $('#modalpegawai').modal('show');
        } else {
          $('#table-data tbody').html('<tr><td colspan="5" class="text-center">Data tidak ditemukan</td></tr>');
        }
      },
      error: function (xhr) {
        console.error('Gagal:', xhr.responseText);
      }
    });
  }
  function level() {
    $.ajax({
      url: '<?= base_url()?>admin/user/level',
      type: 'GET',
      dataType: 'JSON',
      success: function (res) {
          if (res.data != null) {
          res.data.forEach(item => {
            $('#id_level').append($('<option>', {
              value: item.id,
              text: item.nama_level,
              'data-nama': item.nama_level,
              'data-id': item.id,
            }));
          });
        }
      },
    })
  }
  $(document).on('change', '#id_level', function () {
    var nama = $('#id_level option:selected').data('nama');
    var idlevel = $(this).val();
    $('#nama_level').val(nama);
  });
  function pilihPegawai(encodedString) {
    const ambil = JSON.parse(atob(encodedString));
    $('#id_pegawai').val(ambil.id);
    $('#nama_pg').val(ambil.nama);    
    $('#modalpegawai').modal('hide');
  }
  function cariPegawaiModal(keyword) {
    $.ajax({
      url: '<?= base_url("admin/user/pegawai") ?>',
      data: { cari: keyword },
      type: 'POST',
      dataType: 'JSON',
      success: function (res) {
        let table = "";
        if (res.result && res.data.length > 0) {
          let i = 1;
          for (const item of res.data) {
            table += `
            <tr style="cursor:pointer;" onclick="pilihPegawai('${btoa(JSON.stringify(item))}')">
              <td>${i++}</td>
                 <td>${item.nama}</td>
              <td>${item.nama_jabatan}</td>
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
            <li class="breadcrumb-item"><a
                href="<?php echo base_url(); ?>admin/user"><?php echo $title; ?></a></li>
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
                <label for="nama_p" class="col-sm-2 col-form-label">Cari Pegawai</label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-11">
                      <input type="text" class="form-control" name="nama_pg" id="nama_pg" placeholder="Klik Tombol Cari" autocomplete="off" readonly required>
                      <input type="hidden" name="id_pegawai" id="id_pegawai" readonly class="form-control">
                    </div>
                    <div class="col-sm-1">
                      <button id="btn_cari" type="button" class="btn btn-primary w-100">Cari</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="username" id="Username" placeholder="Username"
                    autocomplete="off" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" name="password" id="password" placeholder="Password"
                    autocomplete="off" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Level</label>
                <div class="col-sm-10">
                  <select name="id_level" id="id_level" class="form-select" required>
                    <option value="">Pilih Level</option>
                  </select>
                  <input type="hidden" class="form-control" name="nama_level" id="nama_level" autocomplete="off" readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                  <select name="status" id="status" class="form-select" required>
                    <option value="">Pilih Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="tambah(event);" class="btn btn-success"><i
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

<div class="modal fade bd-example-modal-lg" id="modalpegawai" tabindex="-1" role="dialog"
  aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="myLargeModalLabel">
          <i class="fas fa-user-md me-2"></i>Cari Pegawai
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="text" class="form-control" name="nama_modal" id="nama_modal" onkeyup="cariPegawaiModal(this.value)"
          placeholder="Cari pegawai">
        <div class="table-responsive">
          <table class="table table-bordered" id="table-data">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Nama Jabatan</th>
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