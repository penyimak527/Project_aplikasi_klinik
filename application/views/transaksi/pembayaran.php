<script type="text/javascript">
    $(document).ready(function () {
        get_data()
        $("#jumlah_tampil").change(function () {
            get_data();
        })
    })
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
    function format(angka) {
        // Handle undefined/null
        if (angka === undefined || angka === null) {
            return ' 0';
        }
        // Handle string (hapus karakter non-digit)
        if (typeof angka === 'string') {
            angka = angka.replace(/[^0-9]/g, '');
        }
        // Konversi ke number
        const num = Number(angka);
        // Handle NaN
        if (isNaN(num)) {
            return '0';
        }
        // Format dengan separator ribuan
        return ' ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    // untuk validasi form pada bagian required
    function validateForm(formSelector) {
        let isValid = true;
        $(formSelector + ' [required]').removeClass('is-invalid');
        $(formSelector + ' [required]').each(function () {
            if (!$(this).val() || $(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            }
        });
        if (!isValid) {
            Swal.fire({
                title: 'Gagal!',
                text: 'Harap isi semua kolom yang wajib diisi.',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
        }
        return isValid;
    }
    function tambah(e) {
        let btn = $(e.target).closest('button');
        e.preventDefault();
        btn.prop("disabled", true).text("Mengirim...");
        const formData = new FormData(document.getElementById('form_pembayaran'));
        // Convert nilai-nilai yang perlu diubah dari Rupiah ke angka
        const dataToSend = {
            id_pembayaran: formData.get('id_pembayaran'),
            kode_invoice: formData.get('kode_invoice'),
            nama_pasien: formData.get('nama_pasien'),
            biaya_tindakan: convertToNumber(formData.get('biaya_tindakan')),
            biaya_resep: convertToNumber(formData.get('biaya_resep')),
            total_invoice: convertToNumber(formData.get('total_invoice')),
            bayar: convertToNumber(formData.get('bayar')),
            kembali: convertToNumber(formData.get('kembali')),
            metode_pembayaran: formData.get('metode_pembayaran'),
            bank: formData.get('bank')
        };
        if (!validateForm('#form_pembayaran')) {
            btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
            return;
        };
        $.ajax({
            url: '<?php echo base_url('transaksi/pembayaran/tambah') ?>',
            method: 'POST',
            data: dataToSend,
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Mengupload...',
                    html: 'Mohon Ditunggu...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        $('#btn-simpan-pembayaran').prop('disabled', true);
                        Swal.showLoading();
                    }
                });
            },
            success: function (res) {
                if (res.status == true) {
                    $('#modalPembayaran').modal('hide');
                    get_data();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message,
                        icon: 'success',
                        showCancelButton: false,
                        showConfirmButton: true,
                        confirmButtonColor: "#35baf5",
                        confirmButtonText: "Oke",
                        closeOnConfirm: false,
                        allowOutsideClick: false
                    }).then(() => {
                        pindahhalaman(res.kode_invoice);
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
                        allowOutsideClick: false
                    }).then((result) => {
                        btn.prop("disabled", false).text('<i class="fas fa-save me-2"></i>Simpan');
                        if (result.isConfirmed) {
                            console.log('Terjadi error!');
                        }
                    })
                }
            }
        });
    }
    function pindahhalaman(kode_invoice) {
        window.open(`<?= base_url('transaksi/pembayaran/cetak_struk/'); ?>${kode_invoice}`, '_blank');
        window.open(`<?= base_url('transaksi/pembayaran/cetak_kwitansi/'); ?>${kode_invoice}`, '_blank');
    }
    function get_data() {
        let cari = $('#cari').val();
        let count_header = $(`#table-data thead tr th`).length;
        $.ajax({
            url: '<?php echo base_url(); ?>transaksi/pembayaran/result_data',
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
                              <td>${pembayaran.nama_dokter}</td>
                              <td>${formatRupiah(pembayaran.biaya_tindakan)}</td>
                              <td><button type="button" class="btn btn-shadow btn-sm btn-success" title="Pembayaran" onclick="pembayaran('${btoa(JSON.stringify(item))}')"><i class="fas fa-money-bill-wave me-2"></i>Bayar</button></td>
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

    //   function pembayaran(encodedString) {
    //     const ambil = JSON.parse(atob(encodedString));
    //     console.log(ambil);
    //         // Ambil dan proses data tindakan
    //         const tindakanList = ambil.tindakan_list ? ambil.tindakan_list.split(', ') : [];
    //         const hargaTindakanList = ambil.harga_tindakan_list ? ambil.harga_tindakan_list.split(', ') : [];
    //         // Ambil dan proses data tindakan
    //         // Ambil dan proses data obat
    //         const nama_obatList = ambil.nama_obat ? ambil.nama_obat.split(', ') : [];
    //         const harga_obatList = ambil.harga_obat ? ambil.harga_obat.split(', ') : [];
    //         const laba_obatList = ambil.laba_obat ? ambil.laba_obat.split(', ') : [];
    //         const jumlah_obatList = ambil.jumlah_obat ? ambil.jumlah_obat.split(', ') : [];
    //         // Ambil dan proses data obat
    //         // Ambil dan proses data racikan
    //         const racikanList = ambil.nama_racikan ? ambil.nama_racikan.split(', ') : [];
    //         const hargaracikanList = ambil.harga_racikan ? ambil.harga_racikan.split(', ') : [];
    //         const labaracikanList = ambil.laba_racikan ? ambil.laba_racikan.split(', ') : [];
    //         const jumlahracikanList = ambil.jumlah_racikan ? ambil.jumlah_racikan.split(', ') : [];
    //         // Ambil dan proses data racikan
    //     console.log('Tindakan list:', racikanList);
    //     console.log('Harga tindakan list:', hargaracikanList);
    //     console.log('laba tindakan list:', labaracikanList);
    //     console.log('jumlah tindakan list:', jumlahracikanList);
    //     // Kosongkan container tindakan sebelumnya
    //     $('#detail_tindakan_list').empty();
    //     $('#detail_resepobat_list').empty();
    //     $('#detail_racikan_list').empty();
    //     // Tambahkan setiap tindakan ke container
    //     tindakanList.forEach((tindakan, index) => {
    //         const harga = hargaTindakanList[index] ? formatRupiah(hargaTindakanList[index]) : 'Rp 0';

    //         const html = `
    //             <ul class="list-group mb-3">
    //             <li class="list-group-item d-flex justify-content-between align-items-center">${tindakan}<span class="badge bg-primary rounded-pill">${harga}</span></li>
    //             </ul>
    //         `;

    //         $('#detail_tindakan_list').append(html);
    //     });
    //     nama_obatList.forEach((nama_obat, index) => {
    //         const harga_obat = harga_obatList[index] ? formatRupiah(harga_obatList[index]) : 'Rp 0';
    //         const jumlah_obat = jumlah_obatList[index] ;

    //         const html = `
    //             <ul class="list-group mb-3">
    //             <li class="list-group-item d-flex justify-content-between align-items-center">${nama_obat.trim()} x${jumlah_obat}<span class="badge bg-primary rounded-pill">${harga_obat}</span></li>
    //             </ul>
    //         `;

    //         $('#detail_resepobat_list').append(html);
    //     });
    //     racikanList.forEach((nama_racikan, index) => {
    //         const harga_racikan = hargaracikanList[index] ? formatRupiah(hargaracikanList[index]) : 'Rp 0';
    //         const jumlah_racikan = jumlahracikanList[index] ;

    //         const html = `
    //             <ul class="list-group mb-3">
    //             <li class="list-group-item d-flex justify-content-between align-items-center">${nama_racikan.trim()} x${jumlah_racikan}<span class="badge bg-primary rounded-pill">${harga_racikan}</span></li>
    //             </ul>
    //         `;

    //         $('#detail_racikan_list').append(html);
    //     });

    //     $('#id_pembayaran').val(ambil.id);
    //     $('#nama_pasien').val(ambil.nama_pasien);
    //     $('#biaya_tindakan').val(formatRupiah(ambil.biaya_tindakan));
    //     $('#biaya_resep').val(formatRupiah(ambil.biaya_resep));
    //     $('#total_invoice').val(formatRupiah(ambil.total_invoice));
    //     $('#modalPembayaran').modal('show');

    //     $('#bayar').val('');
    //     $('#kembali').val(formatRupiah(0));
    //     $('#bayar').removeClass('is-invalid');
    //     $('#metode_pembayaran').val('');
    //     $('#bank').val('');
    //     $('#form_bank').hide();

    //     $('#modalPembayaran').modal('show');
    //   }
    function pembayaran(encodedString) {
        const ambil = JSON.parse(atob(encodedString));
        // console.log('Data untuk pembayaran:', ambil);

        // Kosongkan container sebelumnya
        $('#detail_tindakan_list').empty();
        // $('#detail_resepobat_list').empty();
        // $('#detail_racikan_list').empty();

        // 1. TAMPILKAN TINDAKAN
        if (ambil.tindakan && ambil.tindakan.length > 0) {
            ambil.tindakan.forEach(tindakan => {
                const html = `
                <ul class="list-group mb-2">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${tindakan.tindakan || 'N/A'}
                        <span class="badge bg-primary rounded-pill">
                            ${formatRupiah(tindakan.harga || 0)}
                        </span>
                    </li>
                </ul>
            `;
                $('#detail_tindakan_list').append(html);
            });
        } else {
            $('#detail_tindakan_list').html('<div class="text-muted">Tidak ada data tindakan</div>');
        }

        // // 4. SET DATA PEMBAYARAN
        const pembayaran = ambil.pembayaran || {};

        $('#id_pembayaran').val(pembayaran.id || '');
        $('#nama_pasien').val(ambil.nama_pasien || '');
        $('#biaya_tindakan').val(format(pembayaran.biaya_tindakan || 0));
        $('#total_invoice').val(format(pembayaran.biaya_tindakan || 0));
        $('#kode_invoice').val(ambil.kode_invoice);
        // Reset form input
        $('#bayar').val('');
        $('#kembali').val(format(0));
        $('#bayar').removeClass('is-invalid');
        $('#metode_pembayaran').val('');
        $('#bank').val('');
        $('#form_bank').hide();
        $('#modalPembayaran').modal('show');
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

    // Konversi format Rupiah ke angka
    function convertToNumber(rupiah) {
        if (!rupiah) return 0;
        return parseInt(rupiah.replace(/,|\./g, '')) || 0;
    }

    // Fungsi untuk menampilkan/menyembunyikan field bank
    function toggleBankField() {
        const metode = $('#metode_pembayaran').val();
        const formBank = $('#form_bank');
        const bankInput = $('#bank');
        if (metode === 'Transfer') {
            formBank.slideDown();
            bankInput.prop('required', true);
        } else {
            formBank.hide();
            bankInput.prop('required', false);
            bankInput.val('');
        }
    }

    // Event listener untuk perubahan metode pembayaran
    $(document).on('change', '#metode_pembayaran', function () {
        toggleBankField();
    });
    // Hitung kembalian
    function hitung() {
        // Mengonversi nilai dari format Rupiah ke angka
        const total = convertToNumber($('#total_invoice').val());
        const bayar = convertToNumber($('#bayar').val());
        const kembali = bayar - total;
        $('#kembali').val(format(kembali > 0 ? kembali : 0));
        // Validasi - menggunakan metode jQuery untuk manipulasi class
        const bayarInput = $('#bayar');
        if (bayar < total) {
            bayarInput.addClass('is-invalid');
        } else {
            bayarInput.removeClass('is-invalid');
        }
    }

    // Event listener untuk input bayar
    $(document).on('keyup', '#bayar', function () {
        // Simpan posisi kursor
        const cursorPosition = this.selectionStart;
        const originalLength = this.value.length;
        // Format nilai
        this.value = format(this.value);
        // Kembalikan posisi kursor setelah formatting
        const newLength = this.value.length;
        const lengthDiff = newLength - originalLength;
        this.setSelectionRange(cursorPosition + lengthDiff, cursorPosition + lengthDiff);
        // Hitung kembalian
        hitung();
    });

    // Event listener untuk tombol simpan
    // $('#btn-simpan-pembayaran').on('click', function() {
    //     const form = $('#form_pembayaran');
    //     const total = convertToNumber($('#total_invoice').val());
    //     const bayar = convertToNumber($('#bayar').val());
    //     const kembali = convertToNumber($('#kembali').val());
    //     // Validasi form
    //     if (form[0].checkValidity() === false) {
    //         form.addClass('was-validated');
    //         return;
    //     }
    //     if (bayar < total) {
    //         alert('Jumlah bayar tidak boleh kurang dari total tagihan!');
    //         return;
    //     }
    //     // Jika semua valid, simpan data
    //     alert('Pembayaran berhasil disimpan!');
    //     $('#modalPembayaran').modal('hide');
    // });
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
                </div><!--end card-header-->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="input-group-text"><i class="fas fa-search"></i></div>
                                <input type="text" class="form-control" id="cari" placeholder="Cari Invoice/Pasien/NIK"
                                    autocomplete="off">
                            </div>
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
                                    <th>Nama Dokter</th>
                                    <th>Total Invoice</th>
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
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
    </div>
</div><!-- container -->

<div class="modal fade" id="modalPembayaran" tabindex="-1" role="dialog" aria-labelledby="modalLabelPembayaran"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cash-register me-2"></i> Pembayaran Pasien
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form_pembayaran">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Nama Pasien</label>
                                <div class="col-sm-8">
                                    <input type="hidden" class="form-control" id="id_pembayaran" name="id_pembayaran">
                                    <input type="text" class="form-control" id="nama_pasien" name="nama_pasien"
                                        readonly>
                                    <input type="hidden" class="form-control" id="kode_invoice" name="kode_invoice"
                                        readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Biaya Tindakan</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="biaya_tindakan"
                                            name="biaya_tindakan" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-bold">Total Tagihan</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control fw-bold bg-light" id="total_invoice"
                                            name="total_invoice" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Metode <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-select" id="metode_pembayaran" name="metode_pembayaran"
                                        required>
                                        <option value="">-- Pilih --</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                </div>
                            </div>
                            <div id="form_bank" class="bank-options">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label required-label">Bank</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="bank" name="bank">
                                            <option value="">-- Pilih Bank --</option>
                                            <option value="BCA">BCA</option>
                                            <option value="BRI">BRI</option>
                                            <option value="Mandiri">Mandiri</option>
                                            <option value="BNI">BNI</option>
                                            <option value="CIMB">CIMB Niaga</option>
                                            <option value="Danamon">Danamon</option>
                                            <option value="Permata">Permata Bank</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Jumlah Bayar <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="bayar" name="bayar" required>
                                    </div>
                                    <div class="invalid-feedback">Jumlah bayar tidak valid</div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Kembali</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control bg-light" id="kembali" name="kembali"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 border-start ps-4">
                            <h5 class="fw-bold">Rincian Biaya</h5>
                            <hr class="mt-2">
                            <h6 class="fw-bold text-secondary">Tindakan</h6>
                            <div id="detail_tindakan_list">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="btn-simpan-pembayaran" onclick="tambah(event)">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>