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
      url: '<?php echo base_url(); ?>resepsionis/pasien/result_data',
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
                              <td>${item.no_rm}</td>
                              <td>${item.nama_pasien}</td>
                              <td>${item.nik}</td>
                              <td>${item.jenis_kelamin}</td>
                              <td>${item.no_telp}</td>
                              <td>${item.alamat}</td>
                              <td>
                                  <div class="text-center">
                                      <a onclick="detail('${btoa(JSON.stringify(item))}')"><button type="button" class="btn btn-shadow btn-sm btn-warning"><i class="fas fa-eye"></i></button></a>
                                      <a href="<?php echo base_url(); ?>resepsionis/pasien/view_edit/${item.id}"><button type="button" class="btn btn-shadow btn-sm btn-info"><i class="fas fa-pencil-alt"></i></button></a>
                                      <button type="button" class="btn btn-shadow btn-sm btn-danger" title="Hapus" onclick="hapus(${item.id})"><i class="fas fa-trash-alt"></i></button>
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
          url: '<?php echo base_url(); ?>resepsionis/pasien/hapus',
          method: 'POST',
          data: { id },
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
    //data diri
    $('#detail_no_rm').text(ambil.no_rm);
    $('#detail_nama').text(ambil.nama_pasien);
    $('#detail_nik').text(ambil.nik);
    $('#detail_jk').text(ambil.jenis_kelamin);
    $('#detail_tgl_lahir').text(ambil.tanggal_lahir);
    $('#detail_umur').text(ambil.umur);
    //kontak & domisili
    $('#detail_alamat').text(ambil.alamat);
    $('#detail_no_telp').text(ambil.no_telp);
    $('#detail_st_perkawinan').text(ambil.status_perkawinan);
    $('#detail_pekerjaan').text(ambil.pekerjaan);
    $('#detail_nama_wali').text(ambil.nama_wali);
    //kesehatan
    $('#detail_golongan_darah').text(ambil.golongan_darah);
    $('#detail_alergi').text(ambil.alergi);
    $('#detail_status_operasi').text(ambil.status_operasi);
    $('#modaldetail').modal('show');
  }
</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><?php echo $title; ?></li>
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
          <a href="<?php echo base_url(); ?>resepsionis/pasien/view_tambah"><button type="button"
              class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button></a>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-3">
              <div class="input-group">
                <div class="input-group-text"><i class="fas fa-search"></i></div>
                <input type="text" class="form-control" id="cari" placeholder="Cari RM/Nama/NIk" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table mb-0 table-hover" id="table-data">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>No Rekam Medis</th>
                  <th>Nama Pasien</th>
                  <th>NIK</th>
                  <th>Jenis Kelamin</th>
                  <th>No Telpon</th>
                  <th>Alamat</th>
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
  <div class="modal fade bd-example-modal-lg" id="modaldetail" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="myLargeModalLabel">
            <i class="fas fa-user-md me-2"></i>Detail Pasien <span id="detail_nama_pasien"></span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Data Identitas -->
            <div class="col-lg-6 mb-4">
              <h5 class="mb-3">Informasi Utama</h5>
              <dl class="row">
                <dt class="col-sm-5">No. Rekam Medis</dt>
                <dd class="col-sm-7">: <span id="detail_no_rm"></span></dd>
                <dt class="col-sm-5">Nama Pasien</dt>
                <dd class="col-sm-7">: <span id="detail_nama"></span></dd>
                <dt class="col-sm-5">NIK</dt>
                <dd class="col-sm-7">: <span id="detail_nik"></span></dd>
                <!-- <dt class="col-sm-5">Username</dt>
                                <dd class="col-sm-7">: <span id="data_username"></span></dd> -->
                <dt class="col-sm-5">Jenis Kelamin</dt>
                <dd class="col-sm-7">: <span id="detail_jk"></span></dd>
                <dt class="col-sm-5">Tanggal Lahir</dt>
                <dd class="col-sm-7">: <span id="detail_tgl_lahir"></span></dd>
                <dt class="col-sm-5">Umur</dt>
                <dd class="col-sm-7">: <span id="detail_umur"></span> tahun</dd>
              </dl>
            </div>
            <!-- Data Kontak & Sosial -->
            <div class="col-lg-6 mb-4">
              <h5 class="mb-3">Kontak & Domisili</h5>
              <dl class="row">
                <dt class="col-sm-5">Alamat</dt>
                <dd class="col-sm-7">: <span id="detail_alamat"></span></dd>
                <dt class="col-sm-5">Pekerjaan</dt>
                <dd class="col-sm-7">: <span id="detail_pekerjaan"></span></dd>
                <dt class="col-sm-5">No. Telepon</dt>
                <dd class="col-sm-7">: <span id="detail_no_telp"></span></dd>
                <dt class="col-sm-5">Status Perkawinan</dt>
                <dd class="col-sm-7">: <span id="detail_st_perkawinan"></span></dd>
                <dt class="col-sm-5">Nama Wali</dt>
                <dd class="col-sm-7">: <span id="detail_nama_wali"></span></dd>
              </dl>
            </div>
            <!-- Data Medis -->
            <div class="col-12">
              <h5 class="mb-3">Kesehatan</h5>
              <dl class="row">
                <dt class="col-sm-2">Golongan Darah</dt>
                <dd class="col-sm-4">: <span id="detail_golongan_darah"></span></dd>
                <dt class="col-sm-2">Alergi</dt>
                <dd class="col-sm-4">: <span id="detail_alergi"></span></dd>
                <dt class="col-sm-2">Riwayat Operasi</dt>
                <dd class="col-sm-4">: <span id="detail_status_operasi"></span></dd>
              </dl>
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