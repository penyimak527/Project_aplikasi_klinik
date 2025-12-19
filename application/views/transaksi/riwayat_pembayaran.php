<script type="text/javascript">
    $(document).ready(function () {
        get_data()

        $("#jumlah_tampil").change(function () {
            get_data();
        })
        const tanggalInput = document.getElementById('filter_tanggal');
        const datepicker = new Datepicker(tanggalInput, {
            format: 'dd-mm-yyyy',
            autohide: true
        });
        tanggalInput.addEventListener('changeDate', function() {
            filterTanggal();
        });
    })
    function get_data() {
        let cari = $('#cari').val();
        let count_header = $(`#table-data thead tr th`).length;
        let tanggal = $('#filter_tanggal').val();

        $.ajax({
            url: '<?php echo base_url(); ?>transaksi/pembayaran/result_Dataa',
            data: { 
            cari : cari,
            tanggal : tanggal,},
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
                if (res.result) {
                    let i = 1;
                    for (const item of res.data) {
                        const pembayaran = item.pembayaran;
                        table += `
                          <tr>
                              <td>${i}</td>
                              <td>${item.kode_invoice}</td>
                              <td>${pembayaran.nik}</td>
                              <td>${item.nama_pasien}</td>
                              <td>${formatRupiah(pembayaran.biaya_tindakan)}</td>
                              <td>${formatRupiah(pembayaran.biaya_tindakan)}</td>
                              <td>${pembayaran.metode_pembayaran}</td>
                              <td>${pembayaran.tanggal}</td>
                              <td>${pembayaran.waktu}</td>
                                <td>
                                  <div class="text-center">
                                      <button type="button" class="btn btn-shadow btn-sm btn-warning" title="Detail" onclick="Detail('${btoa(JSON.stringify(item))}')"><i class="fas fa-eye"></i></button>
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
            complete: () => { $(`#tr-loading`).hide() }
        });

        $('#cari').off('keyup').keyup(function () {
            get_data();
        });
    }

    function struk() {
        const kode = $('#detail-invoicei').val();
        window.open(`<?= base_url('transaksi/pembayaran/cetak_struk/'); ?>${kode}`, '_blank');

    }
    function kwitansi() {
        const kode = $('#detail-invoicei').val();
        window.open(`<?= base_url('transaksi/pembayaran/cetak_kwitansi/'); ?>${kode}`, '_blank');
    }

    function Detail(encodedString) {
        const ambil = JSON.parse(atob(encodedString));
        // Clear previous data
        $('#detail_tindakan_list').empty();
        $('#detail_obat_list').empty();
        $('#detail_racikan_list').empty();
        $('#detail-invoice').text(ambil.kode_invoice);
        $('#detail-invoicei').val(ambil.kode_invoice);
        $('#detail-waktu').text(ambil.pembayaran.tanggal + ' ' + ambil.pembayaran.waktu);
        $('#detail-pasien').text(ambil.nama_pasien);
        $('#detail-dokter').text(ambil.pembayaran.nama_dokter);
        $('#detail_biaya_tindakan').text(formatRupiah(ambil.biaya_tindakan));
        // $('#detail_biaya_resep').text(formatRupiah(ambil.biaya_resep));
        $('#detail-total-tagihan').text(formatRupiah(ambil.pembayaran.biaya_tindakan));
        $('#detail-metode').text(ambil.pembayaran.metode_pembayaran);
        if (ambil.pembayaran.bank == '') {
            $('#detail-bank').text('-');
        } else {
            $('#detail-bank').text(ambil.pembayaran.bank);
        }
        $('#detail-jumlah-bayar').text(formatRupiah(ambil.pembayaran.bayar));
        $('#detail-kembali').text(formatRupiah(ambil.pembayaran.kembali));
        let tindakanview = '';
        ambil.tindakan.forEach(item => {
            tindakanview += `
            <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between align-items-center">${item.tindakan}<span class="badge bg-primary rounded-pill">${formatRupiah(item.harga)}</span></li>
            </ul>
        `;
        });
        $('#detail_tindakan_list').append(tindakanview);
        // // obat resep
        // let obatview = '';
        //  ambil.resep.forEach(itemo => {
        //   const jumlah = parseFloat(itemo.jumlah) || 0;
        // const laba = parseFloat(itemo.laba) || 0;
        // const harga = parseFloat(itemo.harga) || 0;

        // const tamo = jumlah * (laba + harga);
        //     obatview += `
        //         <ul class="list-group mb-3">
        //         <li class="list-group-item d-flex justify-content-between align-items-center">${itemo.nama_barang} x ${jumlah}<span class="badge bg-primary rounded-pill">${formatRupiah(tamo)}</span></li>
        //         </ul>
        //     `;
        // });
        // $('#detail_obat_list').append(obatview);
        // // racikan
        // let racikanview = '';
        //  ambil.racikan.forEach(itemr => {
        // const jumlahr = parseFloat(itemr.jumlah) || 0;
        // const labar = parseFloat(itemr.laba) || 0;
        // const hargar = parseFloat(itemr.harga) || 0;

        // const tamr = jumlahr * (labar + hargar);
        //     racikanview += `
        //         <ul class="list-group mb-3">
        //         <li class="list-group-item d-flex justify-content-between align-items-center">${itemr.nama_racikan} x ${jumlahr}<span class="badge bg-primary rounded-pill">${formatRupiah(tamr)}</span></li>
        //         </ul>
        //     `;
        // });
        // $('#detail_racikan_list').append(racikanview);
        $('#detailRiwayatModal').modal('show');
    }
    function formatRupiah(angka) {
        // Handle undefined/null
        if (angka === undefined || angka === null) {
            return 'Rp 0';
        }
        // Handle string (hapus karakter non-digit)
        if (typeof angka === 'string') {
            angka = angka.replace(/[^0-9]/g, '');
        }
        // Konversi ke number
        const num = Number(angka);
        // Handle NaN
        if (isNaN(num)) {
            return 'Rp 0';
        }
        // Format dengan separator ribuan
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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

    function filterTanggal() {
        get_data();
    }
</script>
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><?php echo $title; ?></li>
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
                </div><!--end card-header-->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-search"></i></div>
                                <input type="text" class="form-control" id="cari" placeholder="Cari Invoice/Pasien/NIK" autocomplete="off">
                            </div>
                        </div>
                       <div class="col-sm-3">
                            <input type="text" class="form-control" id="filter_tanggal" name="filter_tanggal" placeholder="Cari riwayat tanggal..." autocomplete="off" onchange="filterTanggal()">
                        </div>
                        <div class="d-flex flex-column" style="width: auto;">
                            <button type="button" class="btn btn-warning w-100" onclick="$('#filter_tanggal').val(''); filterTanggal();">
                                <i class="fas fa-search me-2"></i>Reset Filter
                            </button>
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
                                    <th>Biaya Tindakan</th>
                                    <!-- <th>Biaya Resep & Obat</th> -->
                                    <th>Total Biaya Semua</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
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
                                <label
                                    class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah
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
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div>
    </div><!-- container -->

    <div class="modal fade" id="detailRiwayatModal" tabindex="-1" role="dialog"
        aria-labelledby="detailRiwayatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailRiwayatModalLabel">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Detail Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <h6 class="text-uppercase fw-bold text-secondary">Informasi Umum & Pembayaran</h6>
                            <hr class="mt-2 mb-3">
                            <div id="detail-info-pembayaran">
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Invoice</div>
                                    <div class="col-sm-8 fw-bold" id="detail-invoice">: </div>
                                    <input type="hidden" id="detail-invoicei" name="detail-invoicei" readonly>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Waktu</div>
                                    <div class="col-sm-8 fw-bold" id="detail-waktu">: </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Pasien</div>
                                    <div class="col-sm-8 fw-bold" id="detail-pasien">: </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Dokter</div>
                                    <div class="col-sm-8 fw-bold" id="detail-dokter">: </div>
                                </div>

                                <hr class="my-3">

                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Metode</div>
                                    <div class="col-sm-8 fw-bold" id="detail-metode">: </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Bank</div>
                                    <div class="col-sm-8 fw-bold" id="detail-bank">: </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Jumlah Bayar</div>
                                    <div class="col-sm-8 fw-bold" id="detail-jumlah-bayar">: </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">Kembali</div>
                                    <div class="col-sm-8 fw-bold" id="detail-kembali">: </div>
                                </div>

                                <div class="p-2 bg-light rounded mt-3">
                                    <div class="d-flex align-items-center">
                                        <span class="col-sm-4 text-muted">Total Tagihan</span>
                                        <span class="col-sm-8 fw-bold fs-5 text-success" id="detail-total-tagihan">Rp
                                            0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 border-start ps-4">
                            <h6 class="text-uppercase fw-bold text-secondary">Rincian Layanan</h6>
                            <hr class="mt-2">

                            <h6 class="fw-bold text-primary mt-3">Tindakan</h6>
                            <div id="detail_tindakan_list">
                            </div>

                            <!-- <hr class="my-3">
                        
                        <h6 class="fw-bold text-primary">Obat</h6>
                        <div id="detail_obat_list">
                            </div>
                        <h6 class="fw-bold text-primary">Racikan</h6>
                        <div id="detail_racikan_list">
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-success" id="btn-cetak-struk" onclick="struk()">
                        <i class="fas fa-print me-1"></i> Cetak Struk
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-cetak-kwitansi" onclick="kwitansi()">
                        <i class="fas fa-receipt me-1"></i> Cetak Kwitansi
                    </button>
                </div>
            </div>
        </div>
    </div>