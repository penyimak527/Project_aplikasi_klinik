<script type="text/javascript">
  $(document).ready(function() {
      get_data()
      $("#jumlah_tampil").change(function(){
        get_data();
      })
  })

  function get_data() {
      let cari = $('#cari').val();
      let count_header = $(`#table-data thead tr th`).length
      $.ajax({
          url : '<?php echo base_url(); ?>master_data/pelanggan/result_data',
          data : {cari},
          type : "POST",
          dataType :  "json",
          beforeSend : () => {
              let loading = `<tr id="tr-loading"><td colspan="${count_header}" class="text-center"><div class="loader"><img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading"></div></td></tr>`;
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
                              <td>${item.nama_customer}</td>
                              <td>${item.jenis_kelamin ?? '-'}</td>
                              <td>${item.umur ?? '-'}</td>
                              <td>${item.no_telp ?? '-'}</td>
                              <td>
                                  <div class="text-center">
                                      <button type="button" class="btn btn-shadow btn-sm btn-danger" title="Hapus" onclick="hapus(${item.id})"><i class="fas fa-trash-alt"></i></button>
                                  </div>
                              </td>
                          </tr>
                      `;
                      i++
                  }
              } else {
                  table += `<tr><td colspan="${count_header}" class="text-center">Data Kosong</td></tr>`;
              }
              $('#table-data tbody').html(table);
              paging();
          },
          complete : () => {$(`#tr-loading`).hide()}
      });
      $('#cari').off('keyup').keyup(function(){ get_data(); });
  }

  function paging($selector){
      var jumlah_tampil = $('#jumlah_tampil').val();
      if(typeof $selector == 'undefined') { $selector = $("#table-data tbody tr"); }
      window.tp = new Pagination('#pagination', {
          itemsCount:$selector.length,
          pageSize : parseInt(jumlah_tampil),
          onPageChange: function (paging) {
              var start = paging.pageSize * (paging.currentPage - 1),
                  end = start + paging.pageSize,
                  $rows = $selector;
              $rows.hide();
              for (var i = start; i < end; i++) { $rows.eq(i).show(); }
          }
      });
  }

  function hapus(id) {
      Swal.fire({
          title: "Apakah Anda Yakin?",
          text: "Menghapus Data Pelanggan Ini",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Iya, Hapus",
          cancelButtonText: "Batal"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
              url : '<?php echo base_url(); ?>master_data/pelanggan/hapus',
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
                      Swal.fire('Berhasil!', res.message, 'success').then(() => get_data());
                  } else {
                      Swal.fire('Gagal!', res.message, 'error');
                  }
              }
          })
        }
      })
  }
</script>
<div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
          <div class="page-title-box">
              <div class="float-end">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item active"><?php echo $title; ?></li>
                  </ol>
              </div>
              <h4 class="page-title"><?php echo $title; ?></h4>
          </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center pt-3 pb-3">
                <h4 class="card-title">Data <?php echo $title; ?></h4>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-sm-3">
                  <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                    <input type="text" class="form-control" id="cari" placeholder="Cari...">
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                  <table class="table mb-0 table-hover" id="table-data">
                      <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Jenis Kelamin</th>
                            <th>Umur</th>
                            <th>No. Telp</th>
                            <th class="text-center">Aksi</th>
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
              </div>
            </div>
        </div>
      </div>
    </div>
</div>