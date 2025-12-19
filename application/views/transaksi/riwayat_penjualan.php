<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><?php echo $title; ?></li>
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
                    <h4 class="card-title">Riwayat Semua Penjualan</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="cari"
                                    placeholder="Cari Kode Invoice atau Nama Pelanggan...">
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
                        <!-- <div class="d-flex flex-column" style="width: auto;">
                                    <select class="form-select" name="tipe_transaksi" id="tipe_transaksi">
                                        <option value="semua">Tipe Transaksi</option>
                                    </select>
                                </div> -->
                        <div class="d-flex flex-column" style="width: auto;">
                            <button type="button" class="btn btn-warning w-100"
                                onclick="$('#cari').val(''); $('#tanggal_dari_range').val(''); $('#tanggal_sampai_range').val(''); get_data();">
                                <i class="fas fa-search me-2"></i>Reset Filter
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-data">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>No. Invoice</th>
                                    <th>Pelanggan/Pasien</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Total Belanja</th>
                                    <th>Tipe Transaksi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="riwayat_transaksi_data"></tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div id="pagination"></div>
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
                    <div class="modal fade" id="detailTransaksiModal" tabindex="-1"
                        aria-labelledby="detailTransaksiModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="detailTransaksiModalLabel">Detail Pembayaran</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="detail_content"></div>
                                </div>
                                <div class="modal-footer" id="detail_footer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function NumberToMoney(number) {
            return new Intl.NumberFormat('id-ID').format(parseFloat(number) || 0);
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
                    get_data();
                }
            });

            flatpickr("#tanggal_sampai_range", {
                dateFormat: "d-m-Y",
                defaultDate: null,
                onChange: function (selectedDates, dateStr, instance) {
                    get_data();
                }
            });

            get_data();
            $("#jumlah_tampil").change(function () { get_data(); });
            $('#cari').off('keyup').keyup(function () { get_data(); });
        });

        function get_data() {
            var tanggal_dari = $('#tanggal_dari_range').val();
            var tanggal_sampai = $('#tanggal_sampai_range').val();
            let cari = $('#cari').val();
            console.log(cari);
            console.log(tanggal_dari);
            console.log(tanggal_sampai);
            $.ajax({
                url: '<?php echo base_url(); ?>transaksi/riwayat_penjualan/result_data',
                data: {
                    cari: cari,
                    tanggal_dari: tanggal_dari,
                    tanggal_sampai: tanggal_sampai,
                },
                type: "POST",
                dataType: "json",
                beforeSend: () => $('#riwayat_transaksi_data').html('<tr><td colspan="7" class="text-center"><img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60"></td></tr>'),
                success: function (res) {
                    let table = "";
                    if (res.result) {
                        let i = 1;
                        for (const item of res.data) {
                            let tipeBadge = item.tipe_transaksi == 'resep' ? '<span class="badge fs-7 bg-success text-center">Penjualan Resep</span>' : '<span class="badge bg-success">Penjualan</span>';
                            table += `
                          <tr>
                              <td class="text-center">${i}</td>
                              <td>${item.kode_invoice}</td>
                              <td>${item.nama_customer}</td>
                              <td>${item.tanggal} ${item.waktu}</td>
                              <td>Rp ${parseInt(item.total_invoice).toLocaleString('id-ID')}</td>
                              <td>${tipeBadge}</td>
                              <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm text-white" onclick="showDetailTransaksi('${item.tipe_transaksi}', '${item.id}')">
                                      <i class="fas fa-eye"></i>
                                    </button>
                              </td>
                          </tr>`;
                            i++;
                        }
                    } else {
                        table = `<tr><td colspan="7" class="text-center">${res.message}</td></tr>`;
                    }
                    $('#riwayat_transaksi_data').html(table);
                    paging($('#riwayat_transaksi_data tr'));
                }
            });
        }

        function paging($selector) {
            var jumlah_tampil = $('#jumlah_tampil').val();
            if (typeof $selector == 'undefined') { $selector = $("#table-data tbody tr"); }
            window.tp = new Pagination('#pagination', {
                itemsCount: $selector.length,
                pageSize: parseInt(jumlah_tampil),
                onPageChange: function (paging) {
                    var start = paging.pageSize * (paging.currentPage - 1), end = start + paging.pageSize, $rows = $selector;
                    $rows.hide();
                    for (var i = start; i < end; i++) { $rows.eq(i).show(); }
                }
            });
        }

        function showDetailTransaksi(tipe, id) {
            $.ajax({
                url: `<?php echo base_url(); ?>transaksi/riwayat_penjualan/get_detail_transaksi_ajax/${tipe}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.result) {
                        let header = res.header;
                        let pelanggan = header.nama_customer || '-';

                        let leftColumn = `<h6 class="text-primary">INFORMASI UMUM & PEMBAYARAN</h6>
                                      <table class="table table-sm table-borderless">
                                        <tr><td width="40%">Invoice</td><td>: ${header.kode_invoice}</td></tr>
                                        <tr><td>Waktu</td><td>: ${header.tanggal} ${header.waktu}</td></tr>
                                        <tr><td>Pasien</td><td>: ${pelanggan}</td></tr>`;
                        if (tipe === 'resep') {
                            leftColumn += `<tr><td>Dokter</td><td>: ${header.nama_dokter || '-'}</td></tr>`;
                        }
                        leftColumn += `   <tr><td>Metode</td><td>: ${header.metode_pembayaran || '-'}</td></tr>
                                        <tr><td>Bank</td><td>: ${header.bank || '-'}</td></tr>
                                        <tr><td>Jumlah Bayar</td><td>: Rp ${NumberToMoney(header.bayar)}</td></tr>
                                        <tr><td>Kembali</td><td>: Rp ${NumberToMoney(header.kembali)}</td></tr>
                                      </table>
                                      <hr>
                                      <div class="d-flex justify-content-between">
                                        <h5>Total Tagihan</h5>
                                        <h5 class="text-primary">Rp ${NumberToMoney(header.total_invoice)}</h5>
                                      </div>`;

                        let rightColumn = `<h6 class="text-primary">RINCIAN LAYANAN</h6>`;
                        if (res.details && res.details.length > 0) {
                            rightColumn += `<p class="mb-1"><strong>Obat</strong></p><ul class="list-group list-group-flush">`;
                            res.details.forEach(item => {
                                rightColumn += `<li class="list-group-item px-0 py-1 d-flex justify-content-between">
                                              <span>- ${item.nama_barang} x ${item.jumlah}</span>
                                              <span>Rp ${NumberToMoney(item.sub_total_harga)}</span>
                                            </li>`;
                            });
                            rightColumn += `</ul>`;
                        }

                        if (tipe === 'resep' && res.racikan && res.racikan.length > 0) {
                            rightColumn += `<p class="mb-1 mt-3"><strong>Racikan</strong></p>`;
                            res.racikan.forEach(r => {
                                rightColumn += `<div class="card border mb-2">
                                              <div class="card-header bg-light py-2">
                                                <strong>${r.nama_racikan}</strong> (Jumlah: ${r.jumlah})
                                                <span class="float-end fw-bold">Rp ${NumberToMoney(r.sub_total_harga)}</span>
                                              </div>
                                              <div class="card-body py-2 px-3">
                                                <p class="mb-1"><strong>Komposisi:</strong></p>
                                                <ul class="list-unstyled mb-0">`;
                                r.detail.forEach(bahan => {
                                    rightColumn += `<li>- ${bahan.nama_barang} (${bahan.jumlah} ${bahan.satuan_barang})</li>`;
                                });
                                rightColumn += `</ul></div></div>`;
                            });
                        }

                        let contentHtml = `<div class="row"><div class="col-md-6">${leftColumn}</div><div class="col-md-6">${rightColumn}</div></div>`;
                        let footerHtml = `<a href="<?php echo base_url(); ?>transaksi/riwayat_penjualan/cetak_struk/${tipe}/${id}" target="_blank" class="btn btn-primary"><i class="fas fa-print"></i> Cetak Struk</a>`;
                        if (tipe === 'resep') {
                            footerHtml += `<a href="<?php echo base_url(); ?>transaksi/riwayat_penjualan/cetak_kwitansi/${tipe}/${id}" target="_blank" class="btn btn-info"><i class="fas fa-file-invoice"></i> Cetak Kwitansi</a>`;
                        }
                        footerHtml += `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>`;

                        $('#detail_content').html(contentHtml);
                        $('#detail_footer').html(footerHtml);
                        $('#detailTransaksiModal').modal('show');
                    }
                }
            });
        }
    </script>