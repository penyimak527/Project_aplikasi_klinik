<!-- <script type="text/javascript">
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
          url : '<?php echo base_url(); ?>poli/kecantikan/result_data',
          data : {cari},
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
                console.log(res);
                
                  let i = 1;
                  for(const item of res.data) {
                      table += `
                          <tr>
                              <td>${i}</td>
                              <td>${item.kode_invoice}</td>
                              <td>${item.nik}</td>
                              <td>${item.nama_pasien}</td>
                              <td>${item.nama_dokter}</td>
                              <td>
                                  <div class="text-center">
                                      <a href="<?php echo base_url(); ?>poli/kecantikan/view_proses/${item.kode_invoice}"><button type="button" class="btn btn-shadow btn-sm btn-info"><i class="fas fa-notes-medical"></i></button></a>
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

      $('#cari').off('keyup').keyup(function(){
          get_data();
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

</script> -->
<div class="container-fluid">
  <h4>Poli Kecantikan

  </h4>
  <!-- Page-Title -->
  <!-- <div class="row">
      <div class="col-sm-12">
          <div class="page-title-box">
              <div class="float-end">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item active"><php echo $title; ?></li>
                  </ol>
              </div>
              <h4 class="page-title"><php echo $title; ?></h4> -->
</div><!--end page-title-box-->
</div><!--end col-->
</div>
<!-- end page title end breadcrumb -->
<!-- <div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 pb-3">
              <h4 class="card-title">Data <php echo $title; ?></h4> -->
</div><!--end card-header-->
<!-- <div class="card-body">
            <div class="row mb-3">
              <div class="col-sm-3">
                <div class="input-group">
                  <div class="input-group-text"><i class="fas fa-search"></i></div>
                  <input type="text" class="form-control" id="cari" placeholder="Cari">
                </div>
              </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 table-hover" id="table-data">
                    <thead class="thead-light">
                      <tr>
                          <th>No</th>
                          <th>Kode Invoice</th>
                          <th>NIK</th>
                          <th>Nama Pasien</th>
                          <th>Nama Dokter</th>
                          <th class="text-center">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody> -->
</table><!--end /table-->
</div><!--end /tableresponsive-->
<!-- <div class="row mt-3">
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
            </div> -->

</div><!--end card-body-->
</div><!--end card-->
</div><!--end col-->
</div>
</div><!-- container -->