<script type="text/javascript">
 $(document).ready(function() {
      if (document.getElementById('filter_jenis_barang')) {
          var selectrJenis = new Selectr('#filter_jenis_barang', {
              searchable: true,
              placeholder: "-- Semua Jenis Barang --"
          });
          selectrJenis.on('selectr.change', function(option) {
              get_data();
          });
      }

      get_data();

      $("#jumlah_tampil").change(function(){
        get_data();
      })
      
      $('#cari').off('keyup').keyup(function(){
          get_data();
      });
  })
  function get_data() {
      let cari = $('#cari').val();
      let id_jenis_barang = $('#filter_jenis_barang').val();
      let count_header = $(`#table-data thead tr th`).length

      $.ajax({
          url : '<?php echo base_url(); ?>master_data/barang/result_data',
          data : {cari, id_jenis_barang}, 
          type : "POST",
          dataType :  "json",
          beforeSend : () => {
              let loading = `<tr id="tr-loading">
                                  <td colspan="${count_header}" class="text-center">
                                      <div class="loader">
                                          <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                      </div>
                                  </td>
                              </tr>`;

              $(`#table-data tbody`).html(loading);
          },
          success : function(res){
              let table = "";
              if(res.result) {
                  let i = 1;
                  for(const item of res.data) {
                      table += `
                          <tr>
                              <td>${i}</td> 
                              <td>${item.nama_barang}</td>
                              <td>${item.nama_jenis || '-'}</td> 
                              <td>
                                  <div class="text-center">
                                      <a href="<?php echo base_url(); ?>master_data/barang/view_edit/${item.id}"><button type="button" class="btn btn-shadow btn-sm btn-info"><i class="fas fa-pencil-alt"></i></button></a>
                                      <button type="button" class="btn btn-shadow btn-sm btn-primary" title="Detail" onclick="showDetail(${item.id})"><i class="fas fa-eye"></i></button> 
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
          complete : () => {$(`#tr-loading`).hide()}
      });
  }

  function paging($selector){
      var jumlah_tampil = $('#jumlah_tampil').val();

      if(typeof $selector == 'undefined')
      {
          $selector = $("#table-data tbody tr");
      }

      window.tp = new Pagination('#pagination', {
          itemsCount:$selector.length,
          pageSize : parseInt(jumlah_tampil),
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
              url : '<?php echo base_url(); ?>master_data/barang/hapus',
              method : 'POST',
              data : {id},
              dataType : 'json',
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
                            location.reload()
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
                          location.reload()
                        }
                      })
                  }
              }
          })
        }
      })
  }

  function showDetail(id) {
      $.ajax({
          url: '<?php echo base_url(); ?>master_data/barang/get_detail_data/' + id,
          method: 'GET',
          dataType: 'json',
          beforeSend: function() {
              $('#detailModalBody').html('<div class="text-center py-5"><img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="Loading..."></div>');
              $('#detailModalTitle').text('Memuat Detail Barang...');
              $('#detailModal').modal('show'); 
          },
          success: function(res) {
              if (res.result && res.data) {
                  let item = res.data;
                  $('#detailModalTitle').text('Detail Barang: ' + item.nama_barang);

                  let detailHtml = `
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <p class="mb-1"><strong><i class="ti ti-package me-2"></i>Nama Barang:</strong> ${item.nama_barang}</p>
                          </div>
                          <div class="col-md-6">
                              <p class="mb-1"><strong><i class="ti ti-tag me-2"></i>Jenis Barang:</strong> ${item.nama_jenis || '-'}</p>
                          </div>
                      </div>
                      <h5 class="mb-3">Detail Satuan:</h5>
                  `;

                  if (item.detail_satuan && item.detail_satuan.length > 0) {
                      detailHtml += `<div class="table-responsive">
                                      <table class="table table-bordered table-striped table-hover mb-0">
                                          <thead class="thead-light">
                                              <tr>
                                                  <th><i class="ti ti-list-numbers me-1"></i>Urutan</th>
                                                  <th><i class="ti ti-barcode me-1"></i>Kode Barang Detail</th>
                                                  <th><i class="ti ti-box me-1"></i>Satuan</th>
                                                  <th><i class="ti ti-arrows-maximize me-1"></i>Isi Satuan Turunan</th>
                                              </tr>
                                          </thead>
                                          <tbody>`;
                      item.detail_satuan.forEach(detail => {
                          detailHtml += `
                              <tr>
                                  <td>${detail.urutan_satuan}</td>
                                  <td>${detail.kode_barang}</td>
                                  <td>${detail.nama_satuan || '-'}</td>
                                  <td>${detail.isi_satuan_turunan}</td>
                              </tr>
                          `;
                      });
                      detailHtml += `</tbody></table></div>`;
                  } else {
                      detailHtml += `<div class="alert alert-info text-center" role="alert">
                                          Tidak ada detail satuan untuk barang ini.
                                      </div>`;
                  }

                  $('#detailModalBody').html(detailHtml);
              } else {
                  $('#detailModalTitle').text('Error');
                  $('#detailModalBody').html('<div class="alert alert-danger text-center" role="alert">Gagal memuat detail barang.</div>');
              }
          },
          error: function(xhr, status, error) {
              console.error("AJAX Error: " + status + error);
              $('#detailModalTitle').text('Error');
              $('#detailModalBody').html('<div class="alert alert-danger text-center" role="alert">Terjadi kesalahan saat mengambil data.</div>');
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
              <a href="<?php echo base_url(); ?>master_data/barang/view_tambah"><button type="button" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button></a>
          </div><!--end card-header-->
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-sm-3">
                <div class="input-group">
                  <div class="input-group-text"><i class="fas fa-search"></i></div>
                  <input type="text" class="form-control" id="cari" placeholder="Cari">
                </div>
              </div>
              <div class="col-sm-3">
                <div class="input-group">
                  <select class="form-control" id="filter_jenis_barang"> <option value="">-- Semua Jenis Barang --</option>
                    <?php foreach ($jenis_barang_list as $jenis) : ?>
                        <option value="<?php echo $jenis['id']; ?>">
                            <?php echo $jenis['nama_jenis']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                </div>
              </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 table-hover" id="table-data">
                    <thead class="thead-light">
                      <tr>
                          <th>No</th>
                          <th>Barang</th>
                          <th>Jenis Barang</th> 
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
            </div>

          </div><!--end card-body-->
      </div><!--end card-->
    </div><!--end col-->
  </div>
</div><!-- container -->

<!--detail modal-->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="detailModalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="detailModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
