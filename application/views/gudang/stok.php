<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><?php echo $title?></li>
                        </ol>
                    </div>
                    <h4 class="page-title"><?php echo $title; ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card ">
                    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 pb-3">
                        <h4 class="card-title">Data <?php echo $title; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-md-auto d-flex flex-wrap align-items-center gap-2">
                                <div class="d-flex flex-column" style="width: auto;">
                                
                                    <div class="input-group">
                                        <div class="input-group-text" onclick="stok_result();"><i class="fas fa-search"></i></div>
                                        <input type="text" class="form-control" id="cari_stok" placeholder="Cari...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table mb-0 table-hover" id="table-data">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="text-align:center;">No</th>
                                        <th style="text-align:center;">Kode Barang</th>
                                        <th style="text-align:center;">Nama Barang</th>
                                        <th style="text-align:center;">Satuan</th>
                                        <th style="text-align:center;">Stok</th>
                                        <th style="text-align:center;">Harga Awal</th>
                                        <th style="text-align:center;">Harga Jual</th>
                                        <th style="text-align:center;">Laba</th>
                                        <th style="text-align:center;">Kadaluarsa</th>
                                    </tr>
                                </thead>
                                <tbody id="table_stok">

                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul id="pagination" class="pagination float-start"></ul>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end align-items-center">
                                    <label for="jumlah_tampil" class="me-2 mb-0">Jumlah Tampil per Halaman:</label>
                                    <select id="jumlah_tampil" class="form-select w-auto" onchange="stok_result()">
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

    <script type="text/javascript">
        function NumberToMoney(number) {
            if (isNaN(number) || number === null) {
                return '0';
            }
            if (number === 0 || Math.abs(number) < 0.000001) {
                return '0';
            }
            if (Number.isInteger(number)) {
                return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(number);
            }
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(number);
        }

        function FormatStok(number) {
            const num = parseFloat(number) || 0;

            if (num % 1 === 0) {
                return new Intl.NumberFormat('id-ID').format(num);
            } else {
                const options = {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                };
                return new Intl.NumberFormat('id-ID', options).format(num);
            }
        }
        $(document).ready(function(){
            stok_result();

            $('#cari_stok').off('keyup').keyup(function(){
                stok_result();
            });

            $("#jumlah_tampil").change(function(){
                stok_result();
            });
        });

        function stok_result(page = 1) {
            var search = $('#cari_stok').val();
            var limit = $('#jumlah_tampil').val();
            let count_header = $(`#table-data thead tr th`).length;

            $('#popup_load').show();

            $.ajax({
                url: '<?php echo base_url(); ?>gudang/stok/get_stok_data_ajax',
                type: 'GET',
                data: {
                    search: search,
                    page: page,
                    limit: limit
                },
                dataType: 'json',
                beforeSend : () => {
                    let loading = `<tr id="tr-loading">
                                        <td colspan="${count_header}" class="text-center">
                                            <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                        </td>
                                    </tr>`;
                    $(`#table_stok`).html(loading);
                },
                success: function(response) {
                    let table = "";
                    if (response.stok_list && response.stok_list.length === 0) {
                        table = '<tr>'+
                                    '<td colspan="9" style="text-align:center;">Data Kosong</td>'+
                                '</tr>';
                    } else {
                        var no = (response.current_page - 1) * response.per_page;
                        $.each(response.stok_list, function(i, stok){
                            no++;
                            table += '<tr>' +
                                        '<td style="text-align:center;">'+no+'</td>'+
                                        '<td style="text-align:center;">'+(stok.kode_barang || '-')+'</td>'+
                                        '<td style="text-align:center;">'+stok.nama_barang+'</td>'+
                                        '<td style="text-align:center;">'+stok.satuan_barang+'</td>'+
                                        '<td style="text-align:center;">'+FormatStok(stok.stok)+'</td>'+
                                        '<td style="text-align:right;">Rp. '+NumberToMoney(stok.harga_awal)+'</td>'+
                                        '<td style="text-align:right;">Rp. '+NumberToMoney(stok.harga_jual)+'</td>'+
                                        '<td style="text-align:right;">Rp. '+NumberToMoney(stok.laba)+'</td>'+
                                        '<td style="text-align:center;">'+(stok.kadaluarsa || '-')+'</td>'+
                                    '</tr>';
                        });
                    }
                    $('#table_stok').html(table);
                    updatePagination(response.total_rows, response.per_page, response.current_page);
                    $('#popup_load').fadeOut();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + ", Response Text: " + xhr.responseText);
                    Swal.fire('Error!', 'Terjadi kesalahan saat memuat data stok. Silakan cek konsol browser untuk detail lebih lanjut.', 'error');
                    $('#popup_load').fadeOut();
                },
                complete : () => {$(`#tr-loading`).hide()}
            });
        }

        function updatePagination(totalRows, limit, currentPage) {
            let totalPages = Math.ceil(totalRows / limit);
            let paginationHtml = '';

            paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="stok_result(1)"><i class="fas fa-angle-double-left"></i></a>
                                </li>`;
            paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="stok_result(${currentPage - 1})"><i class="fas fa-angle-left"></i></a>
                                </li>`;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                    <a class="page-link" href="#" onclick="stok_result(${i})">${i}</a>
                                </li>`;
            }

            if (endPage < totalPages) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }

            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="stok_result(${currentPage + 1})"><i class="fas fa-angle-right"></i></a>
                                </li>`;
            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="stok_result(${totalPages})"><i class="fas fa-angle-double-right"></i></a>
                                </li>`;
            $('#pagination').html(paginationHtml);
        }
    </script>
</body>
