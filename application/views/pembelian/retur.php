<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><?php echo $title; ?></li>
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
                        <h4 class="card-title">Data Retur</h4>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST" id="return_form">
                            <input type="hidden" id="selected_invoice_id" name="selected_invoice_id">
                            <input type="hidden" id="return_row_count" value="0">
                            
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Kode Retur</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="return_code" value="<?php echo $kode_retur_otomatis; ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">No Faktur</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="invoice_number_display" placeholder="Pilih Nota Faktur" readonly>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-info w-100 text-white" onclick="searchInvoice()"><i class="ti ti-search me-2"></i>Cari Nota</button>
                                </div>
                            </div>

                            <div class="alert alert-warning text-center" role="alert">
                                Jumlah retur harus lebih kecil atau sama dengan jumlah beli.
                            </div>

                            <button type="button" class="btn btn-info mb-3 text-white" onclick="addItemForReturn()" id="add_item_return_button" disabled><i class="ti ti-search me-2"></i>Cari Barang</button>
                            <div class="table-responsive">
                                <table class="table" id="item_detail_return_table">
                                    <thead class="thead-light text-white">
                                        <tr>
                                            <th style="width: 50px; text-align: center;">No</th>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah Beli</th>
                                            <th>Jumlah Retur</th>
                                            <th style="width: 50px; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="return_item_table_body">
                                        <tr><td colspan="6" class="text-center text-muted py-4"><a>Pilih nota faktur terlebih dahulu.</a></td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-12 text-start">
                                    <button type="button" onclick="saveReturn();" class="btn btn-primary w-100"><i class="fas fa-save me-2"></i>Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="searchInvoiceModal" tabindex="-1" aria-labelledby="searchInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="searchInvoiceModalLabel">Cari Nota Faktur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="search_invoice_modal_input" placeholder="Cari No Faktur atau Nama Supplier...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="invoice_table_modal">
                            <thead class="text-white">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>No Faktur</th>
                                    <th>Supplier</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal & Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="invoice_data_modal">
                                <tr><td colspan="5" class="text-center text-muted py-4"><b>Ketik untuk mencari nota faktur...</b></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <ul id="invoice_pagination_modal" class="pagination"></ul>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center">
                                <label for="invoice_display_limit_modal" class="me-2 mb-0">Jumlah Tampil per Halaman:</label>
                                <select id="invoice_display_limit_modal" class="form-select w-auto" onchange="loadInvoiceModal(1)">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="searchReturnItemModal" tabindex="-1" aria-labelledby="searchReturnItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="searchReturnItemModalLabel">Pilih Barang dari Faktur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="search_return_item_modal_input" placeholder="Cari Kode atau Nama Barang...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="return_item_table_modal">
                            <thead class=" text-white">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Stok</th>
                                    <th>Harga Awal</th>
                                    <th>Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody id="item_data_return_modal">
                                <tr><td colspan="7" class="text-center text-muted py-4"><b>Ketik untuk mencari barang...</b></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <ul id="return_item_pagination_modal" class="pagination"></ul>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center">
                                <label for="return_item_display_limit_modal" class="me-2 mb-0">Jumlah Tampil per Halaman:</label>
                                <select id="return_item_display_limit_modal" class="form-select w-auto" onchange="loadReturnItemModal(1)">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/^0+/, '');
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                input.value = '';
            }
        }
        
        function numberToMoney(number) {
            if (isNaN(number) || number === null) {
                return '0';
            }
            if (number === 0 || Math.abs(number) < 0.000001) {
                return '0';
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
        function moneyToNumber(moneyString) {
            if (!moneyString) return 0;
            let cleanedString = moneyString.toString().replace(/^Rp\s*/, '').replace(/\./g, '');
            cleanedString = cleanedString.replace(/,/g, '.');
            return parseFloat(cleanedString) || 0;
        }

        var selectedInvoiceId = null;
        var returnRowCount = 0;
        var selectedReturnItems = {}; 
        
        $(document).ready(function() {
            flatpickr("#return_date", {
                dateFormat: "d-m-Y",
                defaultDate: "today"
            });

            $('#search_invoice_modal_input').on('keyup', function() {
                loadInvoiceModal(1);
            });

            $('#invoice_data_modal').on('click', 'tr', function() {
                $('#invoice_data_modal tr').removeClass('table-primary');
                $(this).addClass('table-primary');

                selectedInvoiceId = $(this).data('id');
                var invoiceNumber = $(this).data('invoice-number');

                $('#invoice_number_display').val(invoiceNumber);
                $('#selected_invoice_id').val(selectedInvoiceId);
                $('#add_item_return_button').prop('disabled', false);
                $('#searchInvoiceModal').modal('hide');
                resetReturnTable();
            });
            
            $('#search_return_item_modal_input').on('keyup', function() {
                loadReturnItemModal(1);
            });
            
            $('#item_data_return_modal').on('click', 'tr', function() {
                let id_barang_detail = $(this).data('id_barang_detail');
                if (selectedReturnItems[id_barang_detail]) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Barang ini sudah ada di daftar retur.',
                        icon: 'warning'
                    });
                    return;
                }
                
                const item = $(this).data();

                if ($('#return_item_table_body tr').length === 1 && $('#return_item_table_body tr').text().includes('Pilih nota faktur')) {
                    $('#return_item_table_body').empty();
                }

                returnRowCount++;
                let newRow = `<tr id="row_${id_barang_detail}" 
                                data-id_barang_detail="${id_barang_detail}"
                                data-harga_awal="${item.harga_awal}">
                                <td class="text-center">${returnRowCount}</td>
                                <td>${item.kode_barang}</td>
                                <td>${item.nama_barang}</td>
                                <td>${item.jumlah_beli}</td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" 
                                            class="form-control text-center"
                                            value="1" 
                                            onkeyup="formatCurrency(this); validateReturnQuantity(this, ${item.jumlah_beli})">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem('${id_barang_detail}')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                             </tr>`;
                
                $('#return_item_table_body').append(newRow);
                
                selectedReturnItems[id_barang_detail] = {
                    id_barang_detail: id_barang_detail,
                    jumlah_retur: 1,
                    harga_awal: item.harga_awal
                };

              
            });
        });

        function resetReturnTable() {
            $('#return_item_table_body').html('<tr><td colspan="6" class="text-center text-muted py-4 "><a>Pilih nota faktur terlebih dahulu.</a></td></tr>');
            returnRowCount = 0;
            selectedReturnItems = {};
        }

        function searchInvoice() {
            $('#search_invoice_modal_input').val('');
            loadInvoiceModal(1);
            $('#searchInvoiceModal').modal('show');
        }

        function loadInvoiceModal(page = 1) {
            let header_col_count = $(`#searchInvoiceModal #invoice_table_modal thead tr th`).length;
            var limit = $('#invoice_display_limit_modal').val();
            var search = $('#search_invoice_modal_input').val();

            $.ajax({
                url: '<?php echo base_url(); ?>pembelian/retur/get_faktur_data_for_retur_modal',
                type: 'POST',
                data: { search: search, page: page, limit: limit },
                dataType: 'json',
                beforeSend : () => {
                    let loading = `<tr id="tr-loading-invoice">
                                        <td colspan="${header_col_count}" class="text-center">
                                            <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                        </td>
                                    </tr>`;
                    $(`#searchInvoiceModal #invoice_data_modal`).html(loading);
                },
                success: function(response) {
                    let table_rows = "";
                    if (response.result === true && response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            table_rows += `<tr data-id="${item.id}" data-invoice-number="${item.no_faktur}" data-supplier-name="${item.nama_supplier}"style="cursor:pointer;">
                                                <td>${((page - 1) * limit) + index + 1}</td>
                                                <td>${item.no_faktur}</td>
                                                <td>${item.nama_supplier}</td>
                                                <td>Rp. ${numberToMoney(moneyToNumber(item.total_harga))}</td>
                                                <td>${item.tanggal_waktu}</td>
                                            </tr>`;
                        });
                        updateInvoicePaginationModal(response.total_rows, limit, page);
                    } else {
                        table_rows = `<tr><td colspan="${header_col_count}" class="text-center text-muted py-4"><b>Data tidak ditemukan</b></td></tr>`;
                        $('#invoice_pagination_modal').empty();
                    }
                    $('#invoice_data_modal').html(table_rows);
                
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error muat invoice: " + status + error + ", Respon Teks: " + xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal memuat data faktur. Terjadi kesalahan pada server: ' + xhr.responseText,
                        icon: "error",
                        showConfirmButton: true
                    });
                    let error_col_count = $(`#searchInvoiceModal #invoice_table_modal thead tr th`).length;
                    $('#invoice_data_modal').html(`<tr><td colspan="${error_col_count}" class="text-center text-danger py-4">Gagal memuat data faktur.</td></tr>`);
                    $('#invoice_pagination_modal').empty();
                },
                complete : () => {
                    $(`#tr-loading-invoice`).remove();
                }
            });
        }

        function updateInvoicePaginationModal(totalRows, limit, currentPage) {
            let totalPages = Math.ceil(totalRows / limit);
            let paginationHtml = '';
            
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                <a class="page-link" href="#" onclick="loadInvoiceModal(1)"><i class="fas fa-angle-double-left"></i></a>
                            </li>`;
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                <a class="page-link" href="#" onclick="loadInvoiceModal(${currentPage - 1})"><i class="fas fa-angle-left"></i></a>
                            </li>`;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                        <a class="page-link" href="#" onclick="loadInvoiceModal(${i})">${i}</a>
                                    </li>`;
            }

            if (endPage < totalPages) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }

            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadInvoiceModal(${currentPage + 1})"><i class="fas fa-angle-right"></i></a>
                                </li>`;
            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadInvoiceModal(${totalPages})"><i class="fas fa-angle-double-right"></i></a>
                                </li>`;
            
            $('#invoice_pagination_modal').html(paginationHtml);
        }


        function addItemForReturn() {
            if (selectedInvoiceId) {
                $('#search_return_item_modal_input').val('');
                loadReturnItemModal(1);
                $('#searchReturnItemModal').modal('show');
            } else {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silahkan pilih nota faktur terlebih dahulu.',
                    icon: "warning",
                    showConfirmButton: true
                });
            }
        }

        function loadReturnItemModal(page = 1) {
            if (!selectedInvoiceId) {
                return;
            }
            
            let header_col_count = $(`#searchReturnItemModal #return_item_table_modal thead tr th`).length;
            var limit = $('#return_item_display_limit_modal').val();
            var search = $('#search_return_item_modal_input').val();
            
            $.ajax({
                url: '<?php echo base_url(); ?>pembelian/retur/get_barang_from_faktur_detail_modal',
                type: 'POST',
                data: { id_faktur: selectedInvoiceId, search: search, page: page, limit: limit },
                dataType: 'json',
                beforeSend : () => {
                    let loading = `<tr id="tr-loading-item">
                                        <td colspan="${header_col_count}" class="text-center">
                                            <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                        </td>
                                    </tr>`;
                    $(`#searchReturnItemModal #item_data_return_modal`).html(loading);
                },
                success: function(response) {
                    let table_rows = "";
                    if (response.result === true && response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            const isSelected = selectedReturnItems[item.id_barang_detail] ? 'table-primary' : '';
                            
                            table_rows += `<tr class="${isSelected};" style="cursor:pointer;"
                                                data-id_barang_detail="${item.id_barang_detail}"
                                                data-jumlah_beli="${item.jumlah}"
                                                data-stok="${item.stok}"
                                                data-harga_awal="${item.harga_awal}"
                                                data-harga_jual="${item.harga_jual}"
                                                data-kode_barang="${item.kode_barang}"
                                                data-nama_barang="${item.nama_barang}"
                                                data-satuan="${item.satuan_barang}">
                                                <td>${((page - 1) * limit) + index + 1}</td>
                                                <td>${item.kode_barang}</td>
                                                <td>${item.nama_barang}</td>
                                                <td>${item.satuan_barang}</td>
                                                <td>${FormatStok(item.stok)}</td>
                                                <td>Rp. ${numberToMoney(moneyToNumber(item.harga_awal))}</td>
                                                <td>Rp. ${numberToMoney(moneyToNumber(item.harga_jual))}</td>
                                            </tr>`;
                        });
                        updateReturnItemPaginationModal(response.total_rows, limit, page);
                    } else {
                        table_rows = `<tr><td colspan="${header_col_count}" class="text-center text-muted py-4"><b>Data tidak ditemukan</b></td></tr>`;
                        $('#return_item_pagination_modal').empty();
                    }
                    $('#item_data_return_modal').html(table_rows);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error muat barang retur: " + status + error + ", Respon Teks: " + xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal memuat data barang. Terjadi kesalahan pada server: ' + xhr.responseText,
                        icon: "error",
                        showConfirmButton: true
                    });
                    let error_col_count = $(`#searchReturnItemModal #return_item_table_modal thead tr th`).length;
                    $('#item_data_return_modal').html(`<tr><td colspan="${error_col_count}" class="text-center text-danger py-4">Gagal memuat data barang.</td></tr>`);
                    $('#return_item_pagination_modal').empty();
                },
                complete: () => {
                    $(`#tr-loading-item`).remove();
                }
            });
        }

        function updateReturnItemPaginationModal(totalRows, limit, currentPage) {
            let totalPages = Math.ceil(totalRows / limit);
            let paginationHtml = '';
            
            paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadReturnItemModal(1)"><i class="fas fa-angle-double-left"></i></a>
                                </li>`;
            paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadReturnItemModal(${currentPage - 1})"><i class="fas fa-angle-left"></i></a>
                                </li>`;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                        <a class="page-link" href="#" onclick="loadReturnItemModal(${i})">${i}</a>
                                    </li>`;
            }

            if (endPage < totalPages) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }

            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadReturnItemModal(${currentPage + 1})"><i class="fas fa-angle-right"></i></a>
                                </li>`;
            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadReturnItemModal(${totalPages})"><i class="fas fa-angle-double-right"></i></a>
                                </li>`;
            
            $('#return_item_pagination_modal').html(paginationHtml);
        }

        function validateReturnQuantity(input, max_value) {
            let value = moneyToNumber(input.value); 
            let max = parseFloat(max_value);

            if (isNaN(value) || value < 1) {
                input.value = 1;
            } else if (value > max_value) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: `Jumlah retur tidak boleh melebihi jumlah beli (${max_value}).`,
                    icon: 'warning',
                    confirmButtonText: 'Oke'
                });
                input.value = max_value;
            }
            let id_barang_detail = $(input).closest('tr').data('id_barang_detail');
            selectedReturnItems[id_barang_detail].jumlah_retur = parseInt(input.value);
        }

        function removeItem(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus barang ini dari daftar retur!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#row_${id}`).remove();
                    delete selectedReturnItems[id];
                    $(`#item_data_return_modal tr[data-id_barang_detail="${id}"]`).removeClass('table-primary');
                    $('#return_item_table_body tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });
                    if ($('#return_item_table_body tr').length === 0) {
                        $('#return_item_table_body').html('<tr><td colspan="6" class="text-center text-muted py-4 "><a>Pilih nota faktur terlebih dahulu.</a></td></tr>');
                    }

                    Swal.fire(
                        'Dihapus!',
                        'Barang berhasil dihapus dari daftar retur.',
                        'success'
                    );
                }
            });
        }

        
        function saveReturn() {
            if (!selectedInvoiceId) {
                Swal.fire('Peringatan!', 'Silahkan pilih nota faktur terlebih dahulu.', 'warning');
                return;
            }

            if (Object.keys(selectedReturnItems).length === 0) {
                Swal.fire('Peringatan!', 'Silahkan tambahkan barang untuk diretur.', 'warning');
                return;
            }

            const returItems = [];
            $('#return_item_table_body tr').each(function() {
                const id_barang_detail = $(this).data('id_barang_detail');
                const jumlah_retur = moneyToNumber($(this).find('input[type="text"]').val());
                const jumlah_beli = parseInt($(this).find('td:eq(3)').text()); 
                const harga_awal = $(this).data('harga_awal');
                
                returItems.push({
                    id_barang_detail: id_barang_detail,
                    jumlah_retur: jumlah_retur,
                    jumlah: jumlah_beli, 
                    harga_awal: harga_awal
                });
            });

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data retur akan disimpan dan stok akan diperbarui!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#popup_load').fadeIn();
                    $.ajax({
                        url: '<?php echo base_url(); ?>pembelian/retur/save_retur',
                        type: 'POST',
                        data: {
                            faktur_id: selectedInvoiceId,
                            retur_data: JSON.stringify(returItems)
                        },
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
                        success: function(res) {
                            $('#popup_load').fadeOut();
                            if (res.status) {
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
                                        window.location.href = '<?php echo base_url(); ?>pembelian/retur';
                                    }
                                });
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
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#popup_load').fadeOut();
                            console.error("AJAX Error: " + status + error + ", Respon Teks: " + xhr.responseText);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan pada server: ' + xhr.responseText,
                                icon: "error",
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>