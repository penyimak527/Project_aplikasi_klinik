<script type="text/javascript">
  $(document).ready(function () {
    get_data();
    filtered()
    $("#jumlah_tampil").change(function () {
      get_data();
    });
    $('#filter').on('change', function () {
      filtered();
    });
    $('#resetFilter').on('click', function () {
      $('#cari').val('');
      $('#filter').val('');
      filtered();
    });
  })
  function filtered() {
    const filter = $('#filter').val();
    if (filter == '') {
      get_data();
    }
    else {
      let count_header = $(`#table-data thead tr th`).length
      $.ajax({
        url: '<?php echo base_url(); ?>resepsionis/booking/pilih_filter',
        data: { status: filter },
        type: "POST",
        dataType: "json",
        beforeSend: function () {
          let loading = `<tr id="tr-loading">
                                  <td colspan="${count_header}" class="text-center">
                                      <div class="loader">
                                          <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                      </div>
                                  </td>
                              </tr>`;

          $(`#table-data tbody`).html(loading);
        },
        success: function (response) {
          let table = "";
          let i = 1;
          if (response.length > 0) {
            $.each(response, function (index, item) {
              let statusBadge = '';
              if (item.status_booking == 'Pending') {
                statusBadge = '<span class="badge bg-warning">Pending</span>';
              } else if (item.status_booking == 'Disetujui') {
                statusBadge = '<span class="badge bg-success">Disetujui</span>';
              } else {
                statusBadge = `<span class="badge bg-secondary">${item.status_booking}</span>`;
              };
              let aksi = `<a onclick="detail('${btoa(JSON.stringify(item))}')"><button type="button" class="btn ms-1 btn-shadow btn-sm btn-warning"><i class="fas fa-eye"></i></button></a>
                        <a href="<?php echo base_url(); ?>resepsionis/booking/view_edit/${item.id}"><button type="button" class="btn btn-shadow btn-sm btn-info"><i class="fas fa-pencil-alt"></i></button></a>
                        <button type="button" class="btn btn-shadow btn-sm btn-danger" title="Hapus" onclick="hapus(${item.id})"><i class="fas fa-trash-alt"></i></button>`;

              if (item.status_booking == 'Pending') {
                aksi = `<button type="button" class="booking-check btn btn-sm btn-success btn-lg" onclick="kirimstatus('${btoa(JSON.stringify(item))}')"><i class="fas fa-check"></i></button>` + aksi;
              }
              table += `
              <tr>
                <td>${i++}</td>
                <td>${item.nama_pasien}</td>
                <td>${item.nik}</td>
                <td>${item.nama_poli}</td>
                <td>${item.nama_dokter}</td>
                <td>${item.kode_booking}</td>
                <td>${item.tanggal}</td>
                <td>${item.waktu}</td>
                <td>${statusBadge}</td>
                <td>${aksi}</td>
              </tr>`;
            });
          } else {
            table = `<tr><td colspan="10" class="text-center">Data Tidak Ditemukan</td></tr>`;
          }
          $('#table-data tbody').html(table);
          paging(); // jika kamu punya pagination
        }
      });
    }
  }
  function get_data() {
    let cari = $('#cari').val();
    let count_header = $(`#table-data thead tr th`).length
    $.ajax({
      url: '<?php echo base_url(); ?>resepsionis/booking/result_data',
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
            let statusBadge = '';
            if (item.status_booking == 'Pending') {
              statusBadge = '<span class="badge bg-warning">Pending</span>';
            } else if (item.status_booking == 'Disetujui') {
              statusBadge = '<span class="badge bg-success">Disetujui</span>';
            } else {
              statusBadge = `<span class="badge bg-secondary">${item.status_booking}</span>`;
            };
            let aksi = `<a onclick="detail('${btoa(JSON.stringify(item))}')"><button type="button" class="btn ms-1 btn-shadow btn-sm btn-warning"><i class="fas fa-eye"></i></button></a>
                                <a href="<?php echo base_url(); ?>resepsionis/booking/view_edit/${item.id}"><button type="button" class="btn btn-shadow btn-sm btn-info"><i class="fas fa-pencil-alt"></i></button></a>
                                <button type="button" class="btn btn-shadow btn-sm btn-danger" title="Hapus" onclick="hapus(${item.id})"><i class="fas fa-trash-alt"></i></button>`;
            if (item.status_booking == 'Pending') {
              aksi = `<button type="button" class="booking-check btn btn-sm btn-success btn-lg" onclick="kirimstatus('${btoa(JSON.stringify(item))}')"><i class="fas fa-check"></i></button>` + aksi;
            };
            table += `
                          <tr>
                              <td>${i}</td>
                              <td>${item.nama_pasien}</td>
                              <td>${item.nik}</td>
                              <td>${item.nama_poli}</td>
                              <td>${item.nama_dokter}</td>
                              <td>${item.kode_booking}</td>
                              <td>${item.tanggal}</td>
                              <td>${item.waktu}</td>
                              <td>${statusBadge}</td>
                              <td>${aksi}</td>
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
  function kirimstatus(encodedData) {
    const ambil = JSON.parse(atob(encodedData));
    const id_dokter = ambil.id_dokter;
    const nama_dokter = ambil.nama_dokter;
    const id_poli = ambil.id_poli;
    const nama_poli = ambil.nama_poli;
    const id_pasien = ambil.id_pasien;
    const nama_pasien = ambil.nama_pasien;
    const nik = ambil.nik;
    const id = ambil.id;
    const kode_booking = ambil.kode_booking;
    Swal.fire({
      title: "Apakah Anda Yakin?",
      text: "Mengirim Status",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Iya, Dihapus",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '<?= base_url("resepsionis/booking/kirimstatus") ?>',
          type: 'POST',
          data: {
            id: id,
            kode_booking: kode_booking,
            id_dokter: id_dokter,
            nama_dokter: nama_dokter,
            id_poli: id_poli,
            nama_poli: nama_poli,
            id_pasien: id_pasien,
            nama_pasien: nama_pasien,
            nik: nik,
            status: 'Disetujui',
          },
          dataType: 'JSON',
          success: function (res) {
            console.log(res);
            if (res.status) {
              Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.message
              }).then(() => {
                get_data();
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Gagal",
                text: res.message
              });
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
            // console.log(error);
            Swal.fire("Error", "Tidak dapat mengubah status", "error");
          }
        })
      }
    })
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

  function hapus(id) {
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
          url: '<?php echo base_url(); ?>resepsionis/booking/hapus',
          method: 'POST',
          data: { id },
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
                  get_data();
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
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
          },

        })
      }
    })
  }
  function detail(encodedData) {
    const ambil = JSON.parse(atob(encodedData));
    let statusBadge = '';
    //data diri
    $('#detail_kode_booking').text(': ' + ambil.kode_booking);
    $('#detail_nama_pasien').text(': ' + ambil.nama_pasien);
    $('#detail_nik').text(': ' + ambil.nik);
    $('#detail_poli').text(': ' + ambil.nama_poli);
    $('#detail_dokter').text(': ' + ambil.nama_dokter);
    if (ambil.status_booking == 'Pending') {
      statusBadge = '<span class="badge bg-warning">Pending</span>';
    } else if (ambil.status_booking == 'Disetujui') {
      statusBadge = '<span class="badge bg-success">Disetujui</span>';
    } else {
      statusBadge = `<span class="badge bg-secondary">${ambil.status_booking}</span>`;
    };
    $('#detail_status').html(': ' + statusBadge);
    $('#detail_tanggal').text(': ' + ambil.tanggal);
    $('#detail_waktu').text(': ' + ambil.waktu);
    $('#modalDetailBooking').modal('show');

  }
</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <?php echo $title; ?>
            </li>
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
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 pb-3">
          <h4 class="card-title">Data
            <?php echo $title; ?>
          </h4>
          <a href="<?php echo base_url(); ?>resepsionis/booking/view_tambah"><button type="button"
              class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button></a>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="row mb-3 align-items-center">
            <!-- Filter Status -->
            <div class="col-md-3">
              <select name="filter" id="filter" class="form-control">
                <option value="">Semua Status</option>
                <option value="Pending">Pending</option>
                <option value="Disetujui">Disetujui</option>
              </select>
            </div>
            <!-- Tombol Reset -->
            <div class="col-md-2">
              <button type="button" class="btn btn-warning w-100" id="resetFilter">
                <i class="fas fa-search"></i> Reset Filter
              </button>
            </div>
            <!-- Input Pencarian -->
            <div class="col-md-4 ms-auto">
              <div class="input-group">
                <div class="input-group-text">
                  <i class="fas fa-search"></i>
                </div>
                <input type="text" class="form-control " id="cari" placeholder="Cari Pasien/Kode/Poli/Dokter..." autocomplete="off">
              </div>
            </div>
          </div>
          <!-- filter data end -->
          <div class="table-responsive">
            <table class="table mb-0 table-hover" id="table-data">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Nama Pasien</th>
                  <th>NIK</th>
                  <th>Nama Poli</th>
                  <th>Nama Dokter</th>
                  <th>Kode Booking</th>
                  <th>Tanggal Booking</th>
                  <th>Waktu Booking</th>
                  <th>Status</th>
                  <th class="text-center" colspan="4">Aksi</th>
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

  <!-- Modal Detail Booking -->
  <div class="modal fade" id="modalDetailBooking" tabindex="-1" aria-labelledby="modalDetailBookingLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalDetailBookingLabel"><i class="fas fa-info-circle me-2"></i>Detail Booking
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
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
          <hr>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Nama Poli</label>
            <div class="col-sm-9" id="detail_poli"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Nama Dokter</label>
            <div class="col-sm-9" id="detail_dokter"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Status Booking</label>
            <div class="col-sm-9" id="detail_status"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Tanggal Booking</label>
            <div class="col-sm-9" id="detail_tanggal"></div>
          </div>
          <div class="row mb-2">
            <label class="col-sm-3 fw-bold">Waktu Booking</label>
            <div class="col-sm-9" id="detail_waktu"></div>
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