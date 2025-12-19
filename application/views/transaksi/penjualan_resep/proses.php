<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end"><ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('transaksi/penjualan_resep'); ?>">Penjualan Resep</a></li>
                    <li class="breadcrumb-item active"><?= $title?></li>
                </ol></div>
                <h4 class="page-title">Proses Pembayaran</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Detail Resep</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 row"><label class="col-sm-4 col-form-label">Kode Invoice</label><div class="col-sm-8"><input class="form-control" type="text" value="<?php echo $resep_header['kode_invoice']; ?>" readonly></div></div>
                            <div class="mb-3 row"><label class="col-sm-4 col-form-label">Nama Pasien</label><div class="col-sm-8"><input class="form-control" type="text" value="<?php echo $resep_header['nama_pasien']; ?>" readonly></div></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 row"><label class="col-sm-4 col-form-label">Dokter</label><div class="col-sm-8"><input class="form-control" type="text" value="<?php echo $resep_header['nama_dokter']; ?>" readonly></div></div>
                            <div class="mb-3 row"><label class="col-sm-4 col-form-label">Tanggal</label><div class="col-sm-8"><input class="form-control" type="text" value="<?php echo date('d-m-Y', strtotime($resep_header['tanggal'])); ?>" readonly></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Rincian Obat & Resep</h4></div>
                <div class="card-body">
                    <h5 class="mb-3">Obat Non-Racikan</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="thead-professional">
                                <tr><th>Nama Obat</th><th>Satuan</th><th>Jumlah</th><th>Aturan Pakai</th><th class="text-end">Subtotal</th></tr>
                            </thead>
                            <tbody>
                                <?php if(empty($resep_obat)): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Tidak ada obat non-racikan.</td></tr>
                                <?php else: foreach($resep_obat as $ro): ?>
                                    <tr>
                                        <td><input type="text" class="form-control" value="<?php echo $ro['nama_barang']; ?>" readonly></td>
                                        <td><input type="text" class="form-control" value="<?php echo $ro['satuan_barang']; ?>" readonly></td>
                                        <td><input type="text" class="form-control text-center" value="<?php echo $ro['jumlah']; ?>" readonly></td>
                                        <td><input type="text" class="form-control" value="<?php echo $ro['aturan_pakai']; ?>" readonly></td>
                                        <td><div class="input-group"><span class="input-group-text">Rp</span><input type="text" class="form-control text-start" value="<?php echo number_format($ro['sub_total_harga'], 0, ',', '.'); ?>" readonly></div></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-3">Racikan Obat</h5>
                    <?php if(empty($resep_racikan)): ?>
                        <p class="text-center text-muted">Tidak ada obat racikan.</p>
                    <?php
                 else:
                  foreach($resep_racikan as $rr): ?>
                        <div class="card border mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6"><div class="mb-3 row"><label class="col-sm-4 col-form-label">Nama Racikan</label><div class="col-sm-8"><input class="form-control" type="text" value="<?php echo $rr['nama_racikan']; ?>" readonly></div></div></div>
                                    <div class="col-md-6"><div class="mb-3 row"><label class="col-sm-4 col-form-label">Jumlah</label><div class="col-sm-8"><input class="form-control" type="text" value="<?php echo $rr['jumlah']; ?>" readonly></div></div></div>
                                    <div class="col-md-6"><div class="mb-3 row"><label class="col-sm-4 col-form-label">Aturan Pakai</label><div class="col-sm-8"><input class="form-control" type="text" value="<?php echo $rr['aturan_pakai']; ?>" readonly></div></div></div>
                                    <div class="col-md-6"><div class="mb-3 row"><label class="col-sm-4 col-form-label">Keterangan</label><div class="col-sm-8"><textarea class="form-control" readonly><?php echo $rr['keterangan']; ?></textarea></div></div></div>                                </div>
                                <hr>
                                <p class="mb-2"><strong>Komposisi:</strong></p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-stripped">
                                        <thead class="thead-professional">
                                            <tr><th>Nama Obat</th><th>Satuan</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($rr['detail'])): ?>
                                                <tr><td colspan="5" class="text-center text-muted">Tidak ada detail komposisi.</td></tr>
                                            <?php else: foreach($rr['detail'] as $rrd): ?>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" value="<?php echo $rrd['nama_barang']; ?>" readonly></td>
                                                <td><input type="text" class="form-control form-control-sm" value="<?php echo $rrd['satuan_barang']; ?>" readonly></td>
                                                <td><input type="text" class="form-control form-control-sm text-center" value="<?php echo $rrd['jumlah']; ?>" readonly></td>
                                                <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control text-start" value="<?php echo number_format($rrd['harga'], 0, ',', '.'); ?>" readonly></div></td>
                                                <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control text-start" value="<?php echo number_format($rrd['sub_total_harga'], 0, ',', '.'); ?>" readonly></div></td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
                
                <div class="card-footer bg-light">
                    <?php
                        $total_biaya_obat = 0;
                        foreach($resep_obat as $ro) {
                            $total_biaya_obat += floatval($ro['sub_total_harga']);
                        }

                        $total_biaya_racikan = 0;
                        foreach($resep_racikan as $rr) {
                            $total_biaya_racikan += floatval($rr['sub_total_harga']);
                        }
                    ?>
                    <div class="text-end">
                        <p class="mb-1 text-muted fst-italic">
                            Rincian: 
                            (Obat Rp <?php echo number_format($total_biaya_obat, 0, ',', '.'); ?>) + 
                            (Racikan Rp <?php echo number_format($total_biaya_racikan, 0, ',', '.'); ?>)
                        </p>
                        <h5 class="mb-0">
                            Total Biaya Resep: 
                            <span class="text-primary">Rp <?php echo number_format($resep_header['total_harga'], 0, ',', '.'); ?></span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="text-end">
                <a href="<?php echo base_url('transaksi/penjualan_resep'); ?>" class="btn btn-secondary px-4">Kembali</a>
                <button type="button" class="btn btn-primary px-4" id="tombol_buka_pembayaran"><i class="fas fa-cash-register me-2"></i>Lanjutkan ke Pembayaran</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pembayaranModal" tabindex="-1" aria-labelledby="pembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pembayaranModalLabel"><i class="ti ti-wallet me-2"></i>Proses Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-pembayaran-modal">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="text-primary mb-3">RINGKASAN TRANSAKSI</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td width="30%">Invoice</td><td class="fw-bold">: <?php echo $resep_header['kode_invoice']; ?></td></tr>
                                <tr><td>Tanggal</td><td>: <?php echo date('d-m-Y', strtotime($resep_header['tanggal'])); ?></td></tr>
                                <tr><td>Pasien</td><td>: <?php echo $resep_header['nama_pasien']; ?></td></tr>
                                <tr><td>Dokter</td><td>: <?php echo $resep_header['nama_dokter']; ?></td></tr>
                            </table>
                            <hr>
                            <div style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                                <p class="mb-2 fw-bold text-muted small">DETAIL ITEM:</p>
                                <ul class="list-group list-group-flush">
                                    <?php if(!empty($resep_obat)): foreach($resep_obat as $ro): ?>
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold"><?php echo $ro['nama_barang']; ?></span> <br>
                                            <small class="text-muted"><?php echo $ro['jumlah']; ?> <?php echo $ro['satuan_barang']; ?> x Rp <?php echo number_format($ro['harga'], 0, ',', '.'); ?></small>
                                        </div>
                                        <span class="fw-bold">Rp <?php echo number_format($ro['sub_total_harga'], 0, ',', '.'); ?></span>
                                    </li>
                                    <?php endforeach; endif; ?>

                                    <?php if(!empty($resep_racikan)): foreach($resep_racikan as $rr): ?>
                                    <li class="list-group-item px-0 py-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-primary"><?php echo $rr['nama_racikan']; ?> (Qty: <?php echo $rr['jumlah']; ?>)</span>
                                            <span class="fw-bold">Rp <?php echo number_format($rr['sub_total_harga'], 0, ',', '.'); ?></span>
                                        </div>
                                        <small class="text-muted d-block fst-italic">Komposisi:</small>
                                        <ul class="mb-0 ps-3 small text-muted">
                                            <?php if(!empty($rr['detail'])): foreach($rr['detail'] as $rrd): ?>
                                                <li><?php echo $rrd['nama_barang']; ?> (<?php echo $rrd['jumlah']; ?> <?php echo $rrd['satuan_barang']; ?>)</li>
                                            <?php endforeach; endif; ?>
                                        </ul>
                                    </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Total Tagihan</h5>
                                <h3 class="text-primary fw-bold">Rp <?php echo number_format($resep_header['total_harga'], 0, ',', '.'); ?></h3>
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h6 class="text-primary mb-3">INPUT PEMBAYARAN</h6>
                            <input type="hidden" name="id_pol_resep" value="<?php echo $resep_header['id']; ?>">
                            <input type="hidden" name="total_tagihan" id="total_tagihan" value="<?php echo $resep_header['total_harga']; ?>">

                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-select form-select-md" name="metode_pembayaran" id="metode_pembayaran_modal" required>
                                    <option value="Tunai" selected>Cash</option>
                                    <option value="Transfer">Transfer</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>

                            <div class="mb-3" id="kolom-bank-modal" style="display:none;">
                                <label class="form-label">Pilih Bank</label>
                                <select class="form-select" name="bank">
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                </select>
                            </div>
                            <div class="mb-3" id="kolom-ewal-modal" style="display:none;">
                                <label class="form-label">Pilih Qris</label>
                                <select class="form-select" name="bank">
                                    <option value="Gopay">Gopay</option>
                                    <option value="Dana">Dana</option>
                                    <option value="Ovo">Ovo</option>
                                    <option value="LinkAja">LinkAja</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah Bayar (Rp)</label>
                                <div class="input-group input-group-md">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw" id="jumlah_bayar_modal" onkeyup="FormatCurrency(this); hitung_kembalian_modal();" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kembali (Rp)</label>
                                <div class="input-group input-group-md">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control bg-light " id="kembali_modal" readonly value="0">
                                    <input type="hidden" name="kembali" id="kembali_hidden" value="0">
                                </div>
                            </div>                              
                        </div>
                    </div>
                </form>     
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-md px-5" id="btn_simpan_transaksi" form="form-pembayaran-modal">
                    <i class="fas fa-save me-2"></i> Simpan & Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function FormatCurrency(input) { 
        let value = input.value.replace(/\D/g, ''); 
        input.value = value ? new Intl.NumberFormat('id-ID').format(value) : ''; 
    }
    
    function NumberToMoney(number) { 
        return new Intl.NumberFormat('id-ID').format(parseFloat(number) || 0); 
    }
    
    function MoneyToNumber(moneyString) { 
        return parseInt(String(moneyString).replace(/[^0-9]/g, ''), 10) || 0; 
    }
    
    function hitung_kembalian_modal() { 
        const total = parseFloat($('#total_tagihan').val()) || 0; 
        const bayar = MoneyToNumber($('#jumlah_bayar_modal').val()); 
        const kembali = bayar - total; 
        
        if (kembali >= 0) { 
            $('#kembali_modal').val(NumberToMoney(kembali)); 
            $('#kembali_hidden').val(kembali); 
        } else { 
            $('#kembali_modal').val('0'); 
            $('#kembali_hidden').val('0'); 
        } 
    }

    function pindahhalaman(kode_invoice) {
        window.open(`<?= base_url('transaksi/penjualan_resep/cetak_struk/'); ?>${kode_invoice}`, '_blank');
        window.open(`<?= base_url('transaksi/penjualan_resep/cetak_kwitansi/'); ?>${kode_invoice}`, '_blank');
    }
    
    $(document).ready(function() {
        $('#tombol_buka_pembayaran').on('click', function() {
            $('#pembayaranModal').modal('show');
            setTimeout(() => {
                $('#jumlah_bayar_modal').focus();
            }, 500);
        });

        $('#metode_pembayaran_modal').on('change', function() {
            if ($(this).val() === 'Transfer') {
                $('#kolom-bank-modal').slideDown('fast');
                $('#kolom-ewal-modal').slideUp('fast');
            } else if($(this).val() === 'QRIS'){
                $('#kolom-bank-modal').slideUp('fast');
                $('#kolom-ewal-modal').slideDown('fast');
            }
             else {
                $('#kolom-bank-modal').slideUp('fast');
                $('#kolom-ewal-modal').slideUp('fast');
            }
        });

        $('#form-pembayaran-modal').submit(function(e) {
            e.preventDefault();
            const total_tagihan = parseFloat($('#total_tagihan').val()) || 0;
            const jumlah_bayar = MoneyToNumber($('#jumlah_bayar_modal').val());

            if (jumlah_bayar < total_tagihan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pembayaran Kurang',
                    text: 'Jumlah uang yang dibayarkan kurang dari total tagihan.',
                    confirmButtonColor: "#35baf5"
                });
                return;
            }

            let formData = new FormData(this);
            formData.append('jumlah_bayar', jumlah_bayar);

            Swal.fire({
                title: "Konfirmasi Pembayaran", 
                text: "Anda yakin ingin memproses transaksi ini?", 
                icon: "question",
                showCancelButton: true, 
                confirmButtonText: "Ya, Proses", 
                cancelButtonText: "Batal",
                confirmButtonColor: "#35baf5",
                cancelButtonColor: "#d33"
            }).then((result) => {
                if (result.isConfirmed) {
                    const btnSimpan = $('#btn_simpan_transaksi');
                    btnSimpan.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
                    
                    $.ajax({
                        url: '<?php echo base_url(); ?>transaksi/penjualan_resep/proses_pembayaran',
                        type: 'POST', 
                        data: formData, 
                        processData: false, 
                        contentType: false, 
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                               const idTransaksi = res.id_transaksi; 
                                pindahhalaman(idTransaksi);

                                $('#pembayaranModal').modal('hide');

                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: "success",
                                    confirmButtonColor: "#35baf5",
                                    confirmButtonText: 'Selesai'
                                }).then(() => {
                                    window.location.href = '<?php echo base_url('transaksi/penjualan_resep'); ?>';
                                });

                            } else { 
                                Swal.fire('Gagal!', res.message, 'error'); 
                                btnSimpan.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan & Cetak');
                            }
                        },
                        error: function() { 
                            Swal.fire('Error!', 'Terjadi kesalahan server. Periksa log untuk detail.', 'error'); 
                            btnSimpan.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan & Cetak');
                        }
                    });
                }
            });
        });
    });

</script>