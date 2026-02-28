<!-- <script type="text/javascript">
    $(document).ready(function() {
        get_data();
        $("#jumlah_tampil").change(function() {
            get_data();
        });
    });

    function get_data() {
        let cari = $('#cari').val();
        let hitung_baris = $(`#table-data thead tr th`).length;
        $.ajax({
            url: '<php echo base_url(); ?>poli/gigi/result_data',
            data: {
                cari
            },
            type: "POST",
            dataType: "json",
            beforeSend: () => {
                let loading = `<tr id="tr-loading">
                                  <td colspan="${hitung_baris}" class="text-center">
                                      <div class="loader">
                                          <img src="<php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                      </div>
                                  </td>
                              </tr>`;

                $(`#table-data tbody`).html(loading);
            },
            success: function(res) {
                let table = "";
                if (res.result) {
                    let i = 1;
                    for (const item of res.data) {
                        table += `
                            <tr>
                                <td>${i}</td>
                                <td>${item.kode_invoice || ''}</td>
                                <td>${item.nik || ''}</td>
                                <td>${item.nama_pasien || ''}</td>
                                <td>${item.nama_dokter || ''}</td>
                                <td class="text-center">
                                    <a href="<php echo base_url(); ?>poli/gigi/proses/${item.kode_invoice}"><button type="button" class="btn btn-sm btn-info"><i class="fas fa-notes-medical"></i></button></a>
                                </td>
                            </tr>
                        `;
                        i++;
                    }
                } else {
                    table += `<tr><td colspan="${hitung_baris}" class="text-center">Tidak ada pasien yang perlu diproses</td></tr>`;
                }
                $('#table-data tbody').html(table);
                paging();
            },
            complete: () => {
                $(`#tr-loading`).hide()
            }
        });
        $('#cari').off('keyup').keyup(function() {
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
            onPageChange: function(paging) {
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
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><php echo $title; ?></li>
                    </ol>
                </div>
                <h4 class="page-title"><php echo $title; ?></h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Poli Gigi</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </div>
                                <input type="text" class="form-control" id="cari" placeholder="Cari Pasien/Invoice...">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover" id="table-data">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Kode Invoice</th>
                                    <th>NIK</th>
                                    <th>Nama Pasien</th>
                                    <th>Nama Dokter</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
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
</div> -->
<h2>Poli Gigi</h2>