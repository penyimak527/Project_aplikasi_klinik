
<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
    var base_url_retur = base_url + 'pembelian/retur/'; 
    
    $(document).ready(function() {
        loadRiwayatRetur(1);

        $('#search_riwayat_retur').on('keyup', function() {
            loadRiwayatRetur(1);
        });

        $('#display_limit_riwayat').on('change', function() {
            loadRiwayatRetur(1);
        });
    });

    function loadRiwayatRetur(page = 1) {
        var limit = $('#display_limit_riwayat').val();
        var search = $('#search_riwayat_retur').val();

        $.ajax({
            url: '<?php echo base_url(); ?>pembelian/retur/get_riwayat_retur_data',
            type: 'POST',
            data: { search: search, page: page, limit: limit },
            dataType: 'json',
            beforeSend: function() {
                $('#riwayat_retur_data').html('<tr><td colspan="6" class="text-center text-muted py-4"><img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading"></td></tr>');
            },
            success: function(response) {
                if (response.result === true && response.data.length > 0) {
                    var tableRows = '';
                    $.each(response.data, function(index, item) {
                        tableRows += `<tr>
                            <td>${((page - 1) * limit) + index + 1}</td>
                            <td>${item.kode_retur}</td>
                            <td>${item.no_faktur}</td>
                            <td>${item.nama_supplier}</td>
                            <td>${item.tanggal} ${item.waktu}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm text-white" onclick="showDetailRetur('${item.id}')">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </td>
                        </tr>`;
                    });
                    $('#riwayat_retur_data').html(tableRows);
                    updateRiwayatReturPagination(response.total_rows, limit, page);
                    $('#riwayat_retur_info').text(`Menampilkan ${response.data.length} dari ${response.total_rows} data`);
                } else {
                    $('#riwayat_retur_data').html('<tr><td colspan="6" class="text-center text-muted py-4"><b>Data tidak ditemukan</b></td></tr>');
                    $('#riwayat_retur_pagination').empty();
                    $('#riwayat_retur_info').text('Menampilkan 0 dari 0 data');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + error);
                $('#riwayat_retur_data').html('<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data retur</td></tr>');
            }
        });
    }

    function updateRiwayatReturPagination(totalRows, limit, currentPage) {
        var totalPages = Math.ceil(totalRows / limit);
        var paginationHtml = '';
        
            paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                 <a class="page-link" href="#" onclick="loadRiwayatRetur(1)"><i class="fas fa-angle-double-left"></i></a>
                               </li>`;
            paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                 <a class="page-link" href="#" onclick="loadRiwayatRetur(${currentPage - 1})"><i class="fas fa-angle-left"></i></a>
                               </li>`;

        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        }

        for (var i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="loadRiwayatRetur(${i})">${i}</a>
                              </li>`;
        }

        if (endPage < totalPages) {
            paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        }

        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                <a class="page-link" href="#" onclick="loadRiwayatRetur(${currentPage + 1})"><i class="fas fa-angle-right"></i></a>
                            </li>`;
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                <a class="page-link" href="#" onclick="loadRiwayatRetur(${totalPages})"><i class="fas fa-angle-double-right"></i></a>
                            </li>`;
        $('#riwayat_retur_pagination').html(paginationHtml);
    }

    function showDetailRetur(id_retur) {
        $.ajax({
            url: base_url_retur + 'get_detail_retur_data',
            type: 'POST',
            data: { id_retur: id_retur },
            dataType: 'json',
            beforeSend: function() {
                $('#detail_retur_items').html('<tr><td colspan="5" class="text-center text-muted py-4">Memuat data...</td></tr>');
            },
            success: function(response) {
                if (response.result === true) {
                    // Format tanggal jika perlu
                    let tanggal = response.header.tanggal + ' ' + response.header.waktu;
                    
                    $('#detail_kode_retur').text(response.header.kode_retur || '-');
                    $('#detail_no_faktur').text(response.header.no_faktur || '-');
                    $('#detail_supplier').text(response.header.nama_supplier || '-');
                    $('#detail_tanggal').text(tanggal || '-');

                    var detailRows = '';
                    if (response.details && response.details.length > 0) {
                        $.each(response.details, function(index, item) {
                            detailRows += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.kode_barang || '-'}</td>
                                <td>${item.nama_barang || '-'}</td>
                                <td>${item.jumlah_beli || '0'}</td>
                                <td>${item.jumlah_retur || '0'}</td>
                            </tr>`;
                        });
                    } else {
                        detailRows = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data barang</td></tr>';
                    }
                    $('#detail_retur_items').html(detailRows);
                    $('#detailReturModal').modal('show');
                } else {
                    Swal.fire('Error!', response.message || 'Gagal memuat detail retur', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                $('#detail_retur_items').html('<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data</td></tr>');
                Swal.fire('Error!', 'Terjadi kesalahan saat memuat detail retur', 'error');
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
                        <li class="breadcrumb-item"><a><?php echo $title; ?></a></li>
                    </ol>
                </div>
                <h4 class="page-title"><?php echo $title; ?></h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pt-3 pb-3">
                    <h4 class="card-title">Riwayat Retur Barang</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" id="search_riwayat_retur" placeholder="Cari kode retur, no faktur atau supplier...">
                            </div>
                        </div>
                        
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover " id="riwayat_retur_table">
                            <thead class="thead-light text-white">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Kode Retur</th>
                                    <th>No Faktur</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="riwayat_retur_data">
                                <tr><td colspan="6" class="text-center text-muted py-4"><img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading"></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <ul id="riwayat_retur_pagination" class="pagination float-start"></ul>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center">
                                <label for="display_limit_riwayat" class="me-2 mb-0">Jumlah Tampil:</label>
                                <select id="display_limit_riwayat" class="form-select w-auto">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailReturModal" tabindex="-1" aria-labelledby="detailReturModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailReturModalLabel">Detail Retur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Informasi Retur</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Kode Retur</td>
                                <td id="detail_kode_retur"></td>
                            </tr>
                            <tr>
                                <td>No Faktur</td>
                                <td id="detail_no_faktur"></td>
                            </tr>
                            <tr>
                                <td>Supplier</td>
                                <td id="detail_supplier"></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td id="detail_tanggal"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <h6>Daftar Barang Diretur</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light text-white">
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Beli</th>
                                <th>Jumlah Retur</th>
                            </tr>
                        </thead>
                        <tbody id="detail_retur_items">
                            <tr><td colspan="5" class="text-center text-muted py-4">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

