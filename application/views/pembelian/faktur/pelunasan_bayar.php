
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><?php echo $title; ?></a></li>
                            <li class="breadcrumb-item active">Pelunasan</li>
                        </ol>
                    </div>
                    <h4 class="page-title"><?php echo $title; ?></h4>
                </div><!--end page-title-box-->
            </div><!--end col-->
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header pt-3 pb-3">
                        <h4 class="card-title">Form Faktur</h4> 
                    </div><!--end card-header-->
                    <div class="card-body">
                        <form action="#" method="POST" id="form_pelunasan">
                            <input type="hidden" name="id_faktur" id="id_faktur" value="<?php echo $faktur['id']; ?>">

                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">No Faktur</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($faktur['no_faktur']); ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Supplier</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($faktur['nama_supplier']); ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Tanggal Faktur</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($faktur['tanggal']); ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Metode Pembayaran</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($faktur['metode_pembayaran']); ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Status Pembayaran</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($faktur['status_bayar']); ?>" readonly>
                                </div>
                            </div>

                            <hr class="my-4"/>

                            <h5 class="mb-3">Riwayat Pembayaran</h5>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped text-center">
                                    <thead class="thead-light text-white">
                                        <tr>
                                            <th>Tanggal Bayar</th>
                                            <th>Total Harga</th>
                                            <th>Dibayar</th>
                                            <th>Sisa / Kurang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($status_pembayaran_terakhir) && $status_pembayaran_terakhir): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($status_pembayaran_terakhir['tanggal_pembayaran']); ?></td>
                                                <td>Rp. <?php echo number_format($status_pembayaran_terakhir['total_harga'], 0, ',', '.'); ?></td>
                                                <td>Rp. <?php echo number_format($status_pembayaran_terakhir['dibayar'], 0, ',', '.'); ?></td>
                                                <td>Rp. <?php echo number_format($status_pembayaran_terakhir['sisa_kurang'], 0, ',', '.'); ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4">Belum ada riwayat pembayaran.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <h5 class="mb-3">Form Pelunasan</h5>
                            <div class="mb-3 row">
                                <label for="sisa_kurang_display" class="col-sm-2 col-form-label">Sisa / Kurang</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="sisa_kurang_display" value="<?php echo number_format($faktur['sisa_kurang'], 0, ',', '.'); ?>" readonly>
                                        <input type="hidden" id="sisa_kurang_raw" value="<?php echo $faktur['sisa_kurang']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="jumlah_bayar_baru" class="col-sm-2 col-form-label">Bayar Sisa / Kurang</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" name="jumlah_bayar_baru" id="jumlah_bayar_baru" value="0" onkeyup="FormatCurrency(this);" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="tanggal_pelunasan" class="col-sm-2 col-form-label">Tanggal Pelunasan</label>
                                <div class="col-sm-10">
                                    <input type="text" name="tanggal_pelunasan" class="form-control flatpickr-input" id="tanggal_pelunasan" value="<?php echo date('d-m-Y'); ?>" required/>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-12 text-start"> 
                                    <button type="button" onclick="prosesPelunasan();" class="btn btn-success"><i class="fas fa-money-bill-wave me-2"></i>Proses Pelunasan</button>
                                    <a href="<?php echo base_url(); ?>pembelian/faktur"><button type="button" class="btn btn-warning"><i class="ti ti-arrow-back-up me-2"></i>Kembali</button></a>
                                </div>
                            </div>
                        </form>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div>
    </div><!-- container -->

   

    <script type="text/javascript">
        $(document).ready(function() {
            flatpickr("#tanggal_pelunasan", {
                dateFormat: "d-m-Y",
                defaultDate: "today"
            });
        });

        function FormatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/^0+/, '');
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                input.value = '';
            }
        }

        function MoneyToNumber(moneyString) {
            if (!moneyString) return 0;
            let cleanedString = moneyString.replace(/^Rp\s*/, '').replace(/\./g, '');
            cleanedString = cleanedString.replace(/,/g, '.');
            return parseFloat(cleanedString) || 0;
        }

        function prosesPelunasan() {
            var id_faktur = $('#id_faktur').val();
            var jumlah_bayar_baru = MoneyToNumber($('#jumlah_bayar_baru').val());
            var sisa_kurang_raw = parseFloat($('#sisa_kurang_raw').val());
            var tanggal_pelunasan = $('#tanggal_pelunasan').val();

            if (jumlah_bayar_baru <= 0) {
                Swal.fire('Peringatan!', 'Jumlah bayar baru harus lebih dari 0.', 'warning');
                return;
            }

            if (jumlah_bayar_baru > sisa_kurang_raw) {
                Swal.fire('Peringatan!', 'Jumlah bayar baru tidak boleh melebihi sisa/kurang.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Pelunasan',
                text: "Apakah Anda yakin ingin memproses pelunasan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#popup_load').show();

                    $.ajax({
                        url: '<?php echo base_url("pembelian/faktur/proses_pelunasan"); ?>',
                        type: 'POST',
                        data: {
                            id_faktur: id_faktur,
                            jumlah_bayar_baru: jumlah_bayar_baru,
                            tanggal_pelunasan: tanggal_pelunasan
                        },
                        dataType: 'json',
                        success: function(res) {
                            $('#popup_load').fadeOut();
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
                                    allowOutsideClick : false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '<?php echo base_url(); ?>pembelian/faktur'; 
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
                            console.error("AJAX Error: " + status + ", Response Text: " + xhr.responseText);
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