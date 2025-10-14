<script type="text/javascript">
  $(document).ready(function () {
    get_data()

    $("#jumlah_tampil").change(function () {
      get_data();
    })
  })

  function get_data() {
    let cari = $('#cari').val();
    let count_header = $(`#table-data thead tr th`).length

    $.ajax({
      url: '<?php echo base_url(); ?>resepsionis/pendaftaran/result_data',
      data: { cari },
      type: "POST",
      dataType: "json",
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
        let table = "";
        if (res.result) {
          let i = 1;
          for (const item of res.data) {

            table += `
                          <tr>
                              <td>${i}</td>
                              <td>${item.kode_invoice}</td>
                              <td>${item.kode_antrian}</td>
                              <td>${item.nama_pasien}</td>
                              <td>${item.nama_poli}</td>
                              <td>${item.nama_dokter}</td>
                              <td>${item.tanggal}</td>
                              <td>${item.waktu}</td>
                              <td ><span class="badge bg-success fs-6">${item.status_registrasi}</span></td>
                              <td>
                                  <div class="text-center">
                                      <a onclick="detail('${btoa(JSON.stringify(item))}')"><button type="button" class="btn btn-shadow btn-sm btn-warning"><i class="fas fa-eye"></i></button></a>
                                      <a href="<?php echo base_url(); ?>resepsionis/pendaftaran/view_edit/${item.id}"><button type="button" class="btn btn-shadow btn-sm btn-info"><i class="fas fa-pencil-alt"></i></button></a>
                                      <button type="button" class="btn btn-shadow btn-sm btn-danger" title="Hapus" onclick="hapus('${item.kode_invoice}')"><i class="fas fa-trash-alt"></i></button>
                                  </div>
                              </td>
                          </tr>
                      `;

            i++
          }
        } else {
          table += `
                      <tr>
                          <td colspan="${count_header}" class="text-center">Data Kosong</td>
                      </tr>
                  `;
        }

        $('#table-data tbody').html(table);
        paging();
      },
      complete: () => { $(`#tr-loading`).hide() }
    });

    $('#cari').off('keyup').keyup(function () {
      get_data();
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

  function hapus(kode_invoice) {
    Swal.fire({
      title: "Apakah Anda Yakin?",
      text: "Menghapus Data Saat Ini",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Iya, Dihapus",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '<?php echo base_url(); ?>resepsionis/pendaftaran/hapus',
          method: 'POST',
          data: { kode_invoice },
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
                  get_data()
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
                  get_data()
                }
              })
            }
          }
        })
      }
    })
  }
  function detail(encodedData) {
    const ambil = JSON.parse(atob(encodedData));
    $('#detail_kode_invoice').text(': ' + ambil.kode_invoice);
    if (ambil.kode_booking == null) {
    $('#detail_kode_booking').text(': ' + '-');
    }else{
    $('#detail_kode_booking').text(': ' + ambil.kode_booking);
    }
    $('#detail_nama_pasien').text(': ' + ambil.nama_pasien);
    $('#detail_nik').text(': ' + ambil.nik);
    $('#detail_telpon').text(': ' + ambil.telp_pasien);
    $('#detail_alamat').text(': ' + ambil.alamat_pasien);
    $('#detail_nama_dokter').text(': ' + ambil.nama_dokter);
    $('#detail_nama_poli').text(': ' + ambil.nama_poli);
    let statusBadge = '<span class="badge bg-success">Sukses</span>';
    $('#detail_status_registrasi').html(': ' + statusBadge);
    $('#modalDetailregister').modal('show');
  }

</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><?php echo $title; ?></li>
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
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 pb-3">
          <h4 class="card-title">Data <?php echo $title; ?></h4>
          <a href="<?php echo base_url(); ?>resepsionis/pendaftaran/view_tambah"><button type="button"
              class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button></a>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-3">
              <div class="input-group">
                <div class="input-group-text"><i class="fas fa-search"></i></div>
                <input type="text" class="form-control" id="cari" placeholder="Cari Invoice/Pasien/Poli" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table mb-0 table-hover" id="table-data">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Kode Invoice</th>
                  <th>Nomor Antrian</th>
                  <th>Nama Pasien</th>
                  <th>Poli</th>
                  <th>Nama Dokter</th>
                  <th>Tanggal</th>
                  <th>Waktu</th>
                  <th>Status Registrasi</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table><!--end /table-->
          </div><!--end /tableresponsive-->

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
            </di>

          </div><!--end card-body-->
        </div><!--end card-->
      </div><!--end col-->
    </div>
  </div><!-- container -->

  <!-- Modal Detail Registrasi -->
  <div class="modal fade" id="modalDetailregister" tabindex="-1" aria-labelledby="modalDetailRegisterLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalDetailRegisterLabel">
            <i class="fas fa-info-circle me-2"></i>Detail Registrasi
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Kode Invoice</label>
            <div class="col-sm-9" id="detail_kode_invoice"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Kode Booking</label>
            <div class="col-sm-9" id="detail_kode_booking"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Nama Pasien</label>
            <div class="col-sm-9" id="detail_nama_pasien"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">NIK</label>
            <div class="col-sm-9" id="detail_nik"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">No Telpon</label>
            <div class="col-sm-9" id="detail_telpon"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Alamat</label>
            <div class="col-sm-9" id="detail_alamat"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Nama Dokter</label>
            <div class="col-sm-9" id="detail_nama_dokter"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Nama Poli</label>
            <div class="col-sm-9" id="detail_nama_poli"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Status Registrasi</label>
            <div class="col-sm-9" id="detail_status_registrasi"></div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Tutup
          </button>
        </div>
      </div>
    </div>
  </div>