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
                            <li class="breadcrumb-item"><?php echo $title; ?></li>
                        </ol>
                    </div>
                    <h4 class="page-title"><?php echo $title; ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card ">
                    <div
                        class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 pb-3">
                        <h4 class="card-title">Data <?php echo $title; ?></h4>
                        <a href="<?php echo base_url(); ?>pembelian/faktur/tambah"><button type="button"
                                class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button></a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-1">
                            <div class="col-md-auto d-flex flex-wrap align-items-center gap-2">
                                <div class="d-flex flex-column" style="width: auto;">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="fas fa-search"
                                                onclick="faktur_result();"></i></div>
                                        <input type="text" class="form-control" id="cari_no_transaksi"
                                            placeholder="Cari No Faktur, ">
                                    </div>
                                </div>
                                <div class="d-flex flex-column" style="width: auto;">
                                    <div class="input-group" id="DateRange">
                                        <input type="text" class="form-control" id="tanggal_dari_range"
                                            placeholder="Tanggal Mulai" aria-label="StartDate">
                                        <span class="input-group-text">Sampai</span>
                                        <input type="text" class="form-control" id="tanggal_sampai_range"
                                            placeholder="Tanggal Akhir" aria-label="EndDate">
                                    </div>
                                </div>
                                <!-- </div> -->

                                <!-- <div class="d-flex flex-column" style="width: auto;">
                                    <select class="form-select" name="supplier_filter" id="supplier_filter">
                                        <option value="semua">SEMUA SUPPLIER</option>
                                        <php if (isset($supplier_list) && is_array($supplier_list)): ?>
                                            <php foreach($supplier_list as $s):
                                                $no_hp_display = ($s['no_telp'] === '0' || $s['no_telp'] === '-' || empty($s['no_telp'])) ? '' : " ({$s['no_telp']})";
                                            ?>
                                                <option value="<= $s['id']; ?>"><= $s['nama_supplier'] . $no_hp_display; ?></option>
                                            <php endforeach; ?>
                                        <php endif; ?>
                                    </select>
                                </div> -->
                                <div class="d-flex flex-column" style="width: auto;">
                                    <select class="form-select" name="supplier_filter" id="supplier_filter">
                                        <option value="semua">SEMUA SUPPLIER</option>
                                    </select>
                                </div>

                                <div class="d-flex flex-column" style="width: auto;">
                                    <button type="button" class="btn btn-warning w-100"
                                        onclick="$('#cari').val(''); $('#tanggal_dari_range').val(''); $('#tanggal_sampai_range').val('');  window.selectJenis.setValue('semua');  faktur_result();">
                                        <i class="fas fa-search me-2"></i>Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table mb-0 table-hover" id="table-data">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="text-align:center;">No</th>
                                        <th style="text-align:center;">No Faktur</th>
                                        <th style="text-align:center;">Supplier</th>
                                        <th style="text-align:center;">Tanggal Faktur</th>
                                        <th style="text-align:center;">Metode Bayar</th>
                                        <th style="text-align:center;">Status Bayar</th>
                                        <th style="text-align:center;">Diskon </th>
                                        <th style="text-align:center;">Total Harga</th>
                                        <th style="text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table_faktur">

                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul id="pagination" class="pagination float-start"></ul>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <!-- <div class="col-md-6">&nbsp;</div> -->
                                    <label
                                        class="col-md-9 control-label d-flex align-items-center justify-content-end">Jumlah
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailFaktur" tabindex="-1" aria-labelledby="modalDetailFakturLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetailFakturLabel">Detail Faktur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">Detail Barang</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered nowrap table_detail_faktur">
                            <thead class="text-white">
                                <tr>
                                    <th style="text-align:center;">Nama Barang</th>
                                    <th style="text-align:center;">Jumlah Beli</th>
                                    <th style="text-align:center;">Harga Awal</th>
                                    <th style="text-align:center;">Harga Jual</th>
                                    <th style="text-align:center;">Laba</th>
                                    <th style="text-align:center;">Kadaluarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div id="form-table-bayar" class="mt-4">
                        <h6 class="mb-3">Detail Pelunasan</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered nowrap table_detail_bayar">
                                <thead class="text-white">
                                    <tr>
                                        <th style="text-align:center;">Tanggal Pembayaran</th>
                                        <th style="text-align:center;">Status Pembayaran</th>
                                        <th style="text-align:center;">Total Harga</th>
                                        <th style="text-align:center;">Dibayar</th>
                                        <th style="text-align:center;">Sisa / Kurang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
        function FormatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/^0+/, '');
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                input.value = '';
            }
        }

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

        function MoneyToNumber(moneyString) {
            if (!moneyString) return 0;
            let cleanedString = moneyString.replace(/^Rp\s*/, '').replace(/\./g, '');
            cleanedString = cleanedString.replace(/,/g, '.');
            return parseFloat(cleanedString) || 0;
        }

        $(document).ready(function () {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            var todayFormatted = dd + '-' + mm + '-' + yyyy;

            flatpickr("#tanggal_dari_range", {
                dateFormat: "d-m-Y",
                defaultDate: null,
                onChange: function (selectedDates, dateStr, instance) {
                    faktur_result();
                }
            });

            flatpickr("#tanggal_sampai_range", {
                dateFormat: "d-m-Y",
                defaultDate: null,
                onChange: function (selectedDates, dateStr, instance) {
                    faktur_result();
                }
            });

            $('#supplier_filter').on('change', function () {
                faktur_result();
            });

            $("#jumlah_tampil").change(function () {
                faktur_result();
            });

            faktur_result();

            $('#cari_no_transaksi').off('keyup').keyup(function () {
                faktur_result();
            });
            suplier();
        });
        // supplier_filter
        function suplier() {
            var suppliers = <?php echo json_encode($supplier_list); ?>;
            if (suppliers != null) {
                suppliers.forEach(item => {
                    $('#supplier_filter').append($('<option>', {
                        value: item.id,
                        text: item.nama_supplier,
                    }));
                });
                if (window.selectrJenis) {
                    window.selectJenis.destroy();
                }
                window.selectJenis = new Selectr('#supplier_filter', {
                    searchable: true,
                });
            }

        }

        function faktur_result() {
            var tanggal_dari = $('#tanggal_dari_range').val();
            var tanggal_sampai = $('#tanggal_sampai_range').val();
            var search = $('#cari_no_transaksi').val();
            var selectedSupplierId = $('#supplier_filter').val();
            let count_header = $(`#table-data thead tr th`).length;

            $('#popup_load').show();

            $.ajax({
                url: '<?php echo base_url(); ?>pembelian/faktur/get_faktur_data_ajax',
                type: 'GET',
                data: {
                    tanggal_dari: tanggal_dari,
                    tanggal_sampai: tanggal_sampai,
                    search: search,
                    id_supplier: selectedSupplierId
                },
                dataType: 'json',
                beforeSend: () => {
                    let loading = `<tr id="tr-loading">
                                        <td colspan="${count_header}" class="text-center">
                                            <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                        </td>
                                    </tr>`;
                    $(`#table_faktur`).html(loading);
                },
                success: function (response) {
                    let table = "";
                    if (response.faktur_list && response.faktur_list.length === 0) {
                        table = '<tr>' +
                            '<td colspan="9" style="text-align:center;">Data Kosong</td>' +
                            '</tr>';
                    } else {
                        var no = 0;
                        $.each(response.faktur_list, function (i, faktur) {
                            no++;

                            let tombol_bayar = '';
                            if (faktur.metode_pembayaran == 'Kredit' && faktur.status_bayar == 'Belum Lunas') {
                                tombol_bayar = `<a href="<?php echo base_url(); ?>pembelian/faktur/pelunasan_bayar/${faktur.id}" class="btn btn-success btn-sm" style="margin-right:8px;" title="Bayar Faktur"><i class="ti ti-cash"></i></a>`;
                            } else if (faktur.metode_pembayaran == 'Kredit' && faktur.status_bayar == 'Lunas') {

                            }

                            let total_faktur_display = 'Rp. ' + NumberToMoney(faktur.total_harga);
                            let status_diskon_badge = (faktur.status_diskon == 'ada') ? '<span>Ada</span>' : '<span>Tidak Ada</span>';

                            let total_harga_for_popup = MoneyToNumber(faktur.total_harga);
                            let bayar_for_popup = MoneyToNumber(faktur.bayar);

                            table += '<tr>' +
                                '<td style="text-align:center;">' + no + '</td>' +
                                '<td style="text-align:center;">' + faktur.no_faktur + '</td>' +
                                '<td style="text-align:center;">' + faktur.nama_supplier + '</td>' +
                                '<td style="text-align:center;">' + faktur.tanggal + '</td>' +
                                '<td style="text-align:center;">' + faktur.metode_pembayaran + '</td>' +
                                '<td style="text-align:center;">' + faktur.status_bayar + '</td>' +
                                '<td style="text-align:center;">' + status_diskon_badge + '</td>' +
                                '<td style="text-align:right;">' + total_faktur_display + '</td>' +
                                '<td style="text-align:center;">' +
                                `<button onclick="klik_tombol_popup(${faktur.id},'${faktur.metode_pembayaran}',${total_harga_for_popup},${bayar_for_popup},'${faktur.tanggal_bayar || ''}','${faktur.status_bayar}')" type="button" class="btn btn-primary btn-sm" style="margin-right:5px;" name="button" title="Lihat Detail"><i class="ti ti-eye"></i></button>` +
                                tombol_bayar +
                                `<a href="<?php echo base_url(); ?>pembelian/faktur/edit/${faktur.id}"><button type="button" class="btn btn-info btn-sm" style="margin-right:8px;" name="button" title="Edit Faktur"><i class="ti ti-pencil"></i></button></a>` +
                                `<button onclick="hapus(${faktur.id})" type="button" class="btn btn-danger btn-sm" style="margin-right:5px;" name="button" title="Hapus Faktur"><i class="ti ti-trash"></i></button>` +
                                '</td>' +
                                '</tr>';
                        });
                    }
                    $('#table_faktur').html(table);

                    paging($('#table_faktur tr'));
                    $('#popup_load').fadeOut();
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: " + status + ", Response Text: " + xhr.responseText);
                    Swal.fire('Error!', 'Terjadi kesalahan saat memuat data faktur. Silakan cek konsol browser untuk detail lebih lanjut.', 'error');
                    $('#popup_load').fadeOut();
                },
                complete: () => { $(`#tr-loading`).hide() }
            });
        }

        function paging($selector) {
            var jumlah_tampil = $('#jumlah_tampil').val();

            if (typeof $selector == 'undefined' || !$selector.jquery) {
                $selector = $("#table_faktur tbody tr");
            }

            if (typeof window.Pagination === 'undefined') {
                window.Pagination = function (elementId, options) {
                    this.element = $(elementId);
                    this.options = options;
                    this.init = function () {
                        this.render();
                    };
                    this.render = function () {
                        let totalPages = Math.ceil(this.options.itemsCount / this.options.pageSize);
                        let paginationHtml = '';

                        paginationHtml += `<li class="page-item ${this.options.currentPage === 1 ? 'disabled' : ''}">
                                            <a class="page-link" href="#" data-page="1">&laquo;&laquo;</a>
                                           </li>`;
                        paginationHtml += `<li class="page-item ${this.options.currentPage === 1 ? 'disabled' : ''}">
                                            <a class="page-link" href="#" data-page="${this.options.currentPage - 1}">&laquo;</a>
                                           </li>`;
                        for (let p = 1; p <= totalPages; p++) {
                            paginationHtml += `<li class="page-item ${p === this.options.currentPage ? 'active' : ''}">
                                                <a class="page-link" href="#" data-page="${p}">${p}</a>
                                               </li>`;
                        }

                        paginationHtml += `<li class="page-item ${this.options.currentPage === totalPages ? 'disabled' : ''}">
                                            <a class="page-link" href="#" data-page="${this.options.currentPage + 1}">&raquo;</a>
                                           </li>`;
                        paginationHtml += `<li class="page-item ${this.options.currentPage === totalPages ? 'disabled' : ''}">
                                            <a class="page-link" href="#" data-page="${totalPages}">&raquo;&raquo;</a>
                                           </li>`;

                        this.element.html(paginationHtml);
                        this.element.find('.page-link').off('click').on('click', (e) => {
                            e.preventDefault();
                            let page = parseInt($(e.target).data('page'));
                            if (page > 0 && page <= totalPages) {
                                this.options.onPageChange({ currentPage: page, pageSize: this.options.pageSize });
                                this.options.currentPage = page;
                                this.render();
                            }
                        });
                        this.options.onPageChange({ currentPage: this.options.currentPage || 1, pageSize: this.options.pageSize });
                    };
                    this.init();
                };
            }

            let currentPage = 1;
            if (window.tp && window.tp.options && window.tp.options.currentPage) {
                currentPage = window.tp.options.currentPage;
            }

            window.tp = new Pagination('#pagination', {
                itemsCount: $selector.length,
                pageSize: parseInt(jumlah_tampil),
                currentPage: currentPage,
                onPageSizeChange: function (ps) {
                    $('#jumlah_tampil').val(ps);
                    faktur_result();
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
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus faktur ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#popup_load').show();
                    $.ajax({
                        url: '<?php echo base_url(); ?>pembelian/faktur/hapus/' + id,
                        type: 'POST',
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
                            if (res.status) {
                                Swal.fire('Dihapus!', res.message, 'success').then(() => { faktur_result(); });
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus: ' + xhr.responseText, 'error');
                        },
                        complete: function () { $('#popup_load').fadeOut(); }
                    });
                }
            });
        }

        function klik_tombol_popup(id, metode_pembayaran, total_harga, bayar, tanggal_bayar, status_bayar) {
            $('#modalDetailFaktur').modal('show');
            popup_detail(id, metode_pembayaran, total_harga, bayar, tanggal_bayar, status_bayar);
        }

        function popup_detail(id, metode_pembayaran, total_harga, bayar, tanggal_bayar, status_bayar) {
            $('#popup_load').show();

            $.ajax({
                url: '<?php echo base_url(); ?>pembelian/faktur/get_detail_faktur/' + id,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    let table_barang = "";
                    if (response.barang_detail && response.barang_detail.length > 0) {
                        $.each(response.barang_detail, function (i, item) {
                            table_barang += '<tr>' +
                                '<td style="text-align:center;">' + item.nama_barang + ' / ' + item.satuan_barang + '</td>' +
                                '<td style="text-align:center;">' + NumberToMoney(item.jumlah) + '</td>' +
                                '<td style="text-align:right;">Rp. ' + NumberToMoney(item.harga_awal) + '</td>' +
                                '<td style="text-align:right;">Rp. ' + NumberToMoney(item.harga_jual) + '</td>' +
                                '<td style="text-align:right;">Rp. ' + NumberToMoney(item.laba) + '</td>' +
                                '<td style="text-align:center;">' + (item.kadaluarsa || '-') + '</td>' +
                                '</tr>';
                        });
                    } else {
                        table_barang = '<tr><td colspan="6" style="text-align:center;">Data Barang Kosong</td></tr>';
                    }
                    $('.table_detail_faktur tbody').html(table_barang);
                    $('.table_detail_bayar thead').html(`
                <tr class="text-white">
                    <th style="text-align:center;">Tanggal Pembayaran</th>
                    <th style="text-align:center;">Status Pembayaran</th>
                    <th style="text-align:center;">Total Harga</th>
                    <th style="text-align:center;">Dibayar</th>
                    <th style="text-align:center;">Sisa / Kurang</th>
                </tr>
            `);

                    let table_bayar = "";
                    if (response.riwayat_bayar && response.riwayat_bayar.length > 0) {
                        $.each(response.riwayat_bayar, function (i, item) {
                            table_bayar += '<tr>' +
                                '<td style="text-align:center;">' + item.tanggal_pembayaran + '</td>' +
                                '<td style="text-align:center;">' + item.status_pembayaran + '</td>' +
                                '<td style="text-align:right;">Rp. ' + NumberToMoney(item.total_harga) + '</td>' +
                                '<td style="text-align:right;">Rp. ' + NumberToMoney(item.dibayar) + '</td>' +
                                '<td style="text-align:right;">Rp. ' + NumberToMoney(item.sisa_kurang) + '</td>' +
                                '</tr>';
                        });
                    } else {
                        table_bayar = '<tr><td colspan="5" class="text-center">Belum ada riwayat pembayaran.</td></tr>';
                    }
                    $('.table_detail_bayar tbody').html(table_bayar);

                    $('.table_detail_bayar tfoot').remove();

                    $('#form-table-bayar').show();
                    $('#popup_load').fadeOut();
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: " + status + ", Response Text: " + xhr.responseText);
                    Swal.fire('Error!', 'Terjadi kesalahan saat memuat detail faktur.', 'error');
                    $('#popup_load').fadeOut();
                }
            });
        }
    </script>
</body>