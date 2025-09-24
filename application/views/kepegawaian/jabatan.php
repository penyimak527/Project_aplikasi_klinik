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
      url: '<?php echo base_url(); ?>kepegawaian/jabatan/result_data',
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

        if (res.data) {
          let i = 1;
          for (const item of res.data) {
            table += `
                          <tr>
                              <td>${i}</td>
                              <td>${item.nama}</td>
                              <td>
                                  <div class="text-center">
                                      <a href="<?php echo base_url(); ?>kepegawaian/jabatan/view_edit/${item.id}"><button type="button" class="btn btn-shadow btn-sm btn-info"><i class="fas fa-pencil-alt"></i></button></a>
                                      <button type="button" class="btn btn-shadow btn-sm btn-danger" title="Hapus" onclick="hapus(${item.id})"><i class="fas fa-trash-alt"></i></button>
                                  </div>
                              </td>
                          </tr>
                      `;

            i++
          }
        }
        else {
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
          url: '<?php echo base_url(); ?>kepegawaian/jabatan/hapus',
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
                  location.reload()
                }
              })
            }
          }
        })
      }
    })
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
          <a href="<?php echo base_url(); ?>kepegawaian/jabatan/view_tambah"><button type="button"
              class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button></a>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-3">
              <div class="input-group">
                <div class="input-group-text"><i class="fas fa-search"></i></div>
                <input type="text" class="form-control" id="cari" placeholder="Cari Jabatan" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table mb-0 table-hover" id="table-data">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Nama Jabatan</th>
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