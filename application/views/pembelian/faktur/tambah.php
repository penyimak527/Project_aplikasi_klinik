<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body>
    <div id="popup_load" style="display: none;">
        <div class="window_load">
              <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
        </div>
    </div>
    <div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url("pembelian/faktur"); ?>"><?php echo $title; ?></a></li>
                    <li class="breadcrumb-item active">Tambah</li>
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
                        <h4 class="card-title">Tambah Faktur Barang</h4>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST" id="form_faktur">
                            <input type="hidden" id="barang_number" value="0">
                            <input type="hidden" id="barang_modal_current_row" value="0">

                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">No Faktur</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pilih_nomer" id="default_faktur" value="default" checked>
                                        <label class="form-check-label" for="default_faktur">Default</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pilih_nomer" id="input_no_faktur_radio" value="manual">
                                        <label class="form-check-label" for="input_no_faktur_radio">Input No Faktur</label>
                                    </div>
                                    <button type="button" class="btn btn-soft-primary btn-sm ms-3" onclick="window.open('<?php echo base_url('pembelian/supplier/view_tambah'); ?>', '_blank')"><i class="ti ti-plus me-2"></i>Tambah Supplier Baru</button>
                                </div>
                            </div>

                            <div id="form_input_auto_faktur" class="mb-3 row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <input type="text" name="no_faktur_auto" class="form-control" id="input_auto_faktur" readonly value="<?php echo $no_faktur_otomatis; ?>"/>
                                </div>
                            </div>
                            <div id="form_input_manual_faktur" class="mb-3 row" style="display: none;">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <input type="text" name="no_faktur_manual" class="form-control" id="input_manual_faktur" disabled/>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="id_supplier" class="col-sm-2 col-form-label">Supplier</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="id_supplier" id="id_supplier" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        <?php foreach ($supplier_list as $supplier): ?>
                                            <option
                                                value="<?php echo htmlspecialchars($supplier['id']); ?>"
                                                data-nama_supplier="<?php echo htmlspecialchars($supplier['nama_supplier']); ?>"
                                                data-bank="<?php echo htmlspecialchars($supplier['bank']); ?>"
                                            >
                                                <?php echo htmlspecialchars($supplier['nama_supplier']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" id="selected_nama_supplier" name="selected_nama_supplier">
                                    <input type="hidden" id="selected_bank_supplier" name="selected_bank_supplier">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="tanggal_faktur" class="col-sm-2 col-form-label">Tanggal Faktur</label>
                                <div class="col-sm-10">
                                    <input type="text" name="tanggal_faktur" class="form-control flatpickr-input" id="tanggal_faktur" value="<?php echo date('d-m-Y'); ?>" required/>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="metode_pembayaran" class="col-sm-2 col-form-label">Metode Pembayaran</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="metode_pembayaran" id="metode_pembayaran" required>
                                        <option value="Tunai">Tunai</option>
                                        <option value="Kredit">Kredit</option>
                                    </select>
                                    <input type="hidden" name="status_bayar" id="status_bayar_final">
                                </div>
                            </div>

                            <div id="form_tanggal_bayar" class="mb-3 row" style="display: none;">
                                <label for="input_tanggal_bayar" class="col-sm-2 col-form-label">Tanggal Bayar</label>
                                <div class="col-sm-10">
                                    <input type="text" name="tanggal_bayar" class="form-control flatpickr-input" id="input_tanggal_bayar" disabled/>
                                </div>
                            </div>

                            <hr class="my-4"/>

                            <button type="button" class="btn btn-info mb-3" onclick="openBarangModal()"><i class="ti ti-search me-2"></i>Cari Barang</button>
                            <div class="table-responsive">
                                <table class="table table-striped  " id="table-detail-barang">
                                    <thead class="thead-light text-white">
                                        <tr>
                                            <th style="width: 50px; text-align: center;">#</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah Beli</th>
                                            <th>Harga Awal</th>
                                            <th>Harga Jual</th>
                                            <th>Laba</th>
                                            <th>Kadaluarsa</th>
                                            <th style="width: 50px; text-align: center;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel_tambah_barang">

                                    </tbody>
                                </table>
                            </div>
                            <hr class="my-4"/>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Subtotal Harga</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control" id="subtotal_harga" name="total_harga_beli_raw" readonly value="0"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Diskon</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input check_status" type="checkbox" id="flexSwitchCheckDefault" name="flexSwitchCheckDefault" value="on">
                                                <label class="form-check-label" for="flexSwitchCheckDefault">Aktifkan Diskon</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="form_diskon" style="display: none;">
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Jenis Diskon</label>
                                            <div class="col-sm-8">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_diskon" id="diskon_persen" value="persen" checked>
                                                    <label class="form-check-label" for="diskon_persen">% (Persen)</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_diskon" id="diskon_rp_radio" value="rupiah">
                                                    <label class="form-check-label" for="diskon_rp_radio">Rp (Rupiah)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="diskon_persen_input" class="mb-3 row">
                                            <label for="input_diskon_persen" class="col-sm-4 col-form-label">Diskon (%)</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" name="diskon_persen" class="form-control" id="input_diskon_persen" onkeyup="FormatCurrency(this); hitung_diskon();" value="0"/>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="diskon_rp_input" class="mb-3 row" style="display: none;">
                                            <label for="input_diskon_rp" class="col-sm-4 col-form-label">Diskon (Rp)</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" name="diskon_rp" class="form-control" id="input_diskon_rp" onkeyup="FormatCurrency(this); hitung_diskon();" value="0"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 row" id="total_harga_row">
                                        <label class="col-sm-4 col-form-label">Total Harga</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control" id="total_harga" name="total_harga_faktur" readonly value="0"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="form_bayar" class="mb-3 row">
                                        <label for="bayar_dp" class="col-sm-4 col-form-label">Bayar / DP</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" name="bayar_dp" class="form-control" id="bayar_dp" value="0" onkeyup="FormatCurrency(this); get_bayar();"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row" id="kembalian_row">
                                        <label class="col-sm-4 col-form-label">Kembalian</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control" id="kembalian" name="kembalian_final" readonly value="0"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-12 text-start">
                                    <button type="button" onclick="simpan_faktur();" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan</button>
                                    <a href="<?php echo base_url(); ?>pembelian/faktur"><button type="button" class="btn btn-warning"><i class="ti ti-arrow-back-up me-2"></i>Kembali</button></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="modalBarangLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalBarangLabel">Pilih Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="search_barang_modal" placeholder="Cari Nama Barang...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table_data_barang" id="table-barang-modal">
                            <thead class="thead-light text-white">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Stok</th>
                                    <th>Harga Awal</th>
                                    <th>Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody id="data-barang-modal">
                                <tr><td colspan="7" class="text-center text-muted py-4"><b>Ketik untuk mencari barang...</b></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <ul id="pagination_barang_modal" class="pagination float-start"></ul>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center">
                                <label for="jumlah_tampil_barang_modal" class="me-2 mb-0">Jumlah Tampil per Halaman:</label>
                                <select id="jumlah_tampil_barang_modal" class="form-select w-auto" onchange="loadBarangModal(1)">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="tutup_barang">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    var diskonType = 1;
    var currentBarangRow = 0;

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
        return new Intl.NumberFormat('id-ID', { 
            minimumFractionDigits: 0, 
            maximumFractionDigits: 2 
        }).format(number);
    }

    function MoneyToNumber(moneyString) {
        if (!moneyString) return 0;
        let cleanedString = moneyString
            .toString()
            .replace(/\./g, '') 
            .replace(/,/g, '.'); 
        return parseFloat(cleanedString) || 0;
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

    function get_bayar(){
        var nilai_transaksi = MoneyToNumber($('#total_harga').val());
        var dibayar = MoneyToNumber($('#bayar_dp').val());
        var metode_pembayaran = $('#metode_pembayaran').val();
        var kembali = parseFloat(dibayar) - parseFloat(nilai_transaksi);
        $('#kembalian').val(NumberToMoney(kembali));
        if (metode_pembayaran === 'Tunai') {
            $('#status_bayar_final').val('Lunas');
        } else if (metode_pembayaran === 'Kredit') {
            if (kembali >= 0) {
                $('#status_bayar_final').val('Lunas');
            } else {
                $('#status_bayar_final').val('Belum Lunas');
            }
        }
    }

    function hitungLaba(number) {
        var harga_awal = MoneyToNumber($('#harga_awal_' + number).val());
        var harga_jual = MoneyToNumber($('#harga_jual_' + number).val());
        var laba = harga_jual - harga_awal;
        $('#laba_' + number).val(NumberToMoney(laba));
        hitungSubTotalItem(number);
    }

    function hitungSubTotalItem(number) {
        var jumlah_beli = MoneyToNumber($('#jumlah_' + number).val());
        var harga_awal = MoneyToNumber($('#harga_awal_' + number).val());
        var sub_total_harga_awal_item = Number(jumlah_beli) * Number(harga_awal);
        $('#sub_total_harga_awal_' + number).val(NumberToMoney(sub_total_harga_awal_item));
        hitung_total();
    }

    function hitung_total() {
        let totalHargaAwalSemuaItem = 0;
        $('input[name="sub_total_harga_awal[]"]').each(function() {
            let itemNumericValue = MoneyToNumber($(this).val());
            totalHargaAwalSemuaItem = Number(totalHargaAwalSemuaItem) + Number(itemNumericValue);
        });
        $('#subtotal_harga').val(NumberToMoney(totalHargaAwalSemuaItem));
        hitung_diskon();
    }

    function hapusBaris(rowNumber){
        $('#row_barang_' + rowNumber).remove();
        let newRowNumber = 0;
        $('#tabel_tambah_barang tr').each(function(index) {
            newRowNumber = index;
            $(this).attr('id', 'row_barang_' + newRowNumber);
            $(this).find('td:first').text(newRowNumber + 1);
            $(this).find('[name^="id_barang_detail"]').attr('id', 'id_barang_detail_' + newRowNumber);
            $(this).find('[name^="id_barang"]').attr('id', 'id_barang_' + newRowNumber);
            $(this).find('[name^="kode_barang"]').attr('id', 'kode_barang_' + newRowNumber);
            $(this).find('[name^="nama_barang"]').attr('id', 'nama_barang_hidden_' + newRowNumber);
            $(this).find('[name^="id_satuan_barang"]').attr('id', 'id_satuan_barang_' + newRowNumber);
            $(this).find('[name^="satuan"]').attr('id', 'satuan_' + newRowNumber);
            $(this).find('[name^="sub_total_harga_awal"]').attr('id', 'sub_total_harga_awal_' + newRowNumber);
            $(this).find('[name^="urutan_satuan"]').attr('id', 'urutan_satuan_' + newRowNumber);
            $(this).find('[id^="display_nama_barang"]').attr('id', 'display_nama_barang_' + newRowNumber);
            $(this).find('[name^="jumlah"]').attr('id', 'jumlah_' + newRowNumber).attr('onkeyup', `FormatCurrency(this); hitungSubTotalItem(${newRowNumber});`);
            $(this).find('[name^="harga_awal"]').attr('id', 'harga_awal_' + newRowNumber).attr('onkeyup', `FormatCurrency(this); hitungLaba(${newRowNumber});`);
            $(this).find('[name^="harga_jual"]').attr('id', 'harga_jual_' + newRowNumber).attr('onkeyup', `FormatCurrency(this); hitungLaba(${newRowNumber});`);
            $(this).find('[name^="laba"]').attr('id', 'laba_' + newRowNumber);
            $(this).find('[name^="kadaluarsa"]').attr('id', 'kadaluarsa_' + newRowNumber);
            $(this).find('.btn-danger').attr('onclick', `hapusBaris(${newRowNumber})`);
        });
        // $('#barang_number').val(newRowNumber);
        $('#barang_number').val($('#tabel_tambah_barang tr').length);
        hitung_total();
    }

    function pilih_bayar(status){
        if (status == 'Tunai') {
            $('#form_tanggal_bayar').hide();
            $('#input_tanggal_bayar').prop('disabled', true).val('');
            $('#form_bayar').show();
            $('#bayar_dp').removeAttr('disabled').val('0');
            $('#kembalian_row').show();
        } else if (status == 'Kredit') {
            $('#form_tanggal_bayar').show();
            $('#input_tanggal_bayar').removeAttr('disabled');
            $('#form_bayar').show();
            $('#bayar_dp').removeAttr('disabled').val('0');
            $('#kembalian_row').show();
        }
        get_bayar();
    }

    function hitung_diskon() {
        var subtotal_before_discount = MoneyToNumber($('#subtotal_harga').val());
        var total_after_global_discount = subtotal_before_discount;
        var is_diskon_checked = $('.check_status').is(":checked");
        if (is_diskon_checked) {
            if (diskonType == 1){
                var diskon_persen = MoneyToNumber($('#input_diskon_persen').val());
                if(diskon_persen != 0){
                    total_after_global_discount = subtotal_before_discount - (subtotal_before_discount * (diskon_persen / 100));
                }
            } else if (diskonType == 2){
                var diskon_rp = MoneyToNumber($('#input_diskon_rp').val());
                if(diskon_rp != 0){
                    total_after_global_discount = subtotal_before_discount - diskon_rp;
                }
            }
        } else {
            $('#input_diskon_persen').val('0');
            $('#input_diskon_rp').val('0');
            diskonType = 1;
        }
        $('#total_harga').val(NumberToMoney(total_after_global_discount));
        get_bayar();
    }

    function buatRowBaru(i) {
        return `
            <tr id="row_barang_${i}">
                <td class="text-center">${parseInt(i) + 1}</td>
                <td>
                    <input type="hidden" name="id_barang_detail[]" id="id_barang_detail_${i}" value="">
                    <input type="hidden" name="id_barang[]" id="id_barang_${i}" value="">
                    <input type="hidden" name="kode_barang[]" id="kode_barang_${i}" value="">
                    <input type="hidden" name="nama_barang[]" id="nama_barang_hidden_${i}" value="">
                    <input type="hidden" name="id_satuan_barang[]" id="id_satuan_barang_${i}" value="">
                    <input type="hidden" name="satuan[]" id="satuan_${i}" value="">
                    <input type="hidden" name="sub_total_harga_awal[]" id="sub_total_harga_awal_${i}" value="0">
                    <input type="hidden" name="urutan_satuan[]" id="urutan_satuan_${i}" value="1">
                    <span id="display_nama_barang_${i}"></span>
                </td>
                <td>
                    <input type="text" class="form-control" name="jumlah[]" id="jumlah_${i}" min="1" value="1" onkeyup="FormatCurrency(this);hitungSubTotalItem(${i})">
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" name="harga_awal[]" id="harga_awal_${i}" value="0" onkeyup="FormatCurrency(this); hitungLaba(${i});">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" name="harga_jual[]" id="harga_jual_${i}" value="0" onkeyup="FormatCurrency(this); hitungLaba(${i});">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" name="laba[]" id="laba_${i}" readonly value="0">
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control flatpickr-input" name="kadaluarsa[]" id="kadaluarsa_${i}" value="">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${i})"><i class="ti ti-trash"></i></button>
                </td>
            </tr>
        `;
    }

    function openBarangModal() {
        $('#search_barang_modal').val('');
        loadBarangModal(1);
        $('#modalBarang').modal('show');
    }

    function appendSelectedItemToTable(data) {
        var id_barang_detail = data.id_barang_detail || '';
        var id_barang_utama = data.id_barang_utama || data.id_barang || '';
        var id_satuan_barang = data.id_satuan_barang || '';
        var kode_barang = data.kode_barang || '';
        var nama_barang = data.nama_barang || '';
        var satuan = data.satuan_barang || data.satuan || '';
        var harga_awal = parseFloat(data.harga_awal) || 0;
        var harga_jual = parseFloat(data.harga_jual) || 0;
        var laba = parseFloat(data.laba) || 0;
        var kadaluarsa = data.kadaluarsa || '';
        var urutan_satuan = 1;
        var sudahAda = false;
        $('input[name="id_barang_detail[]"]').each(function() {
            var v = $(this).val();
            if (v !== '' && v == id_barang_detail) {
                sudahAda = true;
                return false;
            }
        });
        if (!sudahAda) {
            $('input[name="id_barang[]"]').each(function() {
                var v2 = $(this).val();
                if (v2 !== '' && v2 == id_barang_utama) {
                    sudahAda = true;
                    return false;
                }
            });
        }
        if (sudahAda) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Barang ini sudah masuk di daftar.',
                icon: 'warning',
                confirmButtonColor: '#35baf5',
                confirmButtonText: 'Oke'
            });
            return;
        }
        var jml_tr = $('#barang_number').val();
        var i = parseInt(jml_tr);
        var menu = buatRowBaru(i);
        $('#tabel_tambah_barang').append(menu);
        $('#barang_number').val(parseInt(jml_tr) + 1);
        flatpickr(`#kadaluarsa_${i}`, {
            dateFormat: "d-m-Y",
        });
        $(`#id_barang_detail_${i}`).val(id_barang_detail);
        $(`#id_barang_${i}`).val(id_barang_utama);
        $(`#kode_barang_${i}`).val(kode_barang);
        $(`#nama_barang_hidden_${i}`).val(nama_barang);
        $(`#id_satuan_barang_${i}`).val(id_satuan_barang);
        $(`#satuan_${i}`).val(satuan);
        $(`#urutan_satuan_${i}`).val(urutan_satuan);
        $(`#jumlah_${i}`).val('1');
        $(`#harga_awal_${i}`).val(NumberToMoney(harga_awal));
        $(`#harga_jual_${i}`).val(NumberToMoney(harga_jual));
        $(`#laba_${i}`).val(NumberToMoney(laba));
        $(`#sub_total_harga_awal_${i}`).val(NumberToMoney(harga_awal * 1)); 
        $(`#kadaluarsa_${i}`).val(kadaluarsa || '');
        $(`#display_nama_barang_${i}`).text(`${nama_barang} (${satuan})`);
        try { if (typeof hitungLaba === 'function') hitungLaba(i); } catch (e) {}
        try { if (typeof hitungSubTotalItem === 'function') hitungSubTotalItem(i); } catch (e) {}
        hitung_total();
    }

    function loadBarangModal(page = 1) {
        let count_header = $(`#modalBarang #table-barang-modal thead tr th`).length;
        var limit = $('#jumlah_tampil_barang_modal').val();
        var search = $('#search_barang_modal').val();
        $.ajax({
            url: '<?php echo base_url(); ?>pembelian/faktur/get_all_barang_for_popup',
            type: 'POST',
            data: { search: search, page: page, limit: limit },
            dataType: 'json',
            beforeSend : () => {    
                let loading = `<tr id="tr-loading">
                                    <td colspan="${count_header}" class="text-center">
                                        <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                    </td>
                                </tr>`;
                $(`#modalBarang #data-barang-modal`).html(loading);
            },
            success: function(response) {
                let tr = "";
                if (response.result === true && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        var harga_awal_display = parseFloat(item.harga_awal) || 0;
                        var harga_jual_display = parseFloat(item.harga_jual) || 0;
                        var laba_display = parseFloat(item.laba) || 0;
                        var kadaluarsa_value = item.kadaluarsa !== null ? item.kadaluarsa : '';
                        var stok_value = parseFloat(item.stok) || 0;
                        tr += `<tr style="cursor:pointer;"
                                data-id="${item.id_barang_detail}"
                                data-id-barang-utama="${item.id_barang_utama}"
                                data-id-satuan-barang="${item.id_satuan_barang}"
                                data-kode="${item.kode_barang || ''}"
                                data-nama="${item.nama_barang}"
                                data-satuan="${item.satuan_barang}"
                                data-harga-awal="${harga_awal_display}"
                                data-harga-jual="${harga_jual_display}"
                                data-laba="${laba_display}"
                                data-kadaluarsa="${kadaluarsa_value}"
                                data-urutan-satuan="${item.urutan_satuan}">
                                <td>${((page - 1) * limit) + index + 1}</td>
                                <td>${item.kode_barang || '-'}</td>
                                <td>${item.nama_barang}</td>
                                <td>${item.satuan_barang}</td>
                                <td>${FormatStok(stok_value)}</td>
                                <td>Rp. ${NumberToMoney(harga_awal_display)}</td>
                                <td>Rp. ${NumberToMoney(harga_jual_display)}</td>
                            </tr>`;
                    });
                    updatePaginationModal(response.total_rows, limit, page);
                } else {
                    tr = `<tr><td colspan="7" class="text-center text-muted py-4"><b>Data tidak ditemukan</b></td></tr>`;
                    $('#pagination_barang_modal').empty();
                }
                $('#data-barang-modal').html(tr);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error loading barang: " + status + error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat data barang. Terjadi kesalahan pada server: ' + xhr.responseText,
                    icon: "error",
                    showConfirmButton: true
                });
                let count_header_error = $(`#modalBarang #table-barang-modal thead tr th`).length;
                $('#data-barang-modal').html(`<tr><td colspan="${count_header_error}" class="text-center text-danger py-4">Gagal memuat data barang.</td></tr>`);
                $('#pagination_barang_modal').empty();
            },
            complete : () => {
                $(`#tr-loading`).remove();
            }
        });
    }

    function updatePaginationModal(totalRows, limit, currentPage) {
        let totalPages = Math.ceil(totalRows / limit);
        let paginationHtml = '';
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadBarangModal(1)"><i class="fas fa-angle-double-left"></i></a>
        </li>`;
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadBarangModal(${currentPage - 1})"><i class="fas fa-angle-left"></i></a>
        </li>`;
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        if (startPage > 1) {
            paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        }
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="loadBarangModal(${i})">${i}</a>
                            </li>`;
        }
        if (endPage < totalPages) {
            paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        }
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadBarangModal(${currentPage + 1})"><i class="fas fa-angle-right"></i></a>
                                </li>`;
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadBarangModal(${totalPages})"><i class="fas fa-angle-double-right"></i></a>
                                </li>`;
        $('#pagination_barang_modal').html(paginationHtml);
    }

    function updateSelectedSupplierDetails(supplierId) {
        if (supplierId) {
            const selectedOption = $('#id_supplier option:selected');
            const namaSupplier = selectedOption.data('nama_supplier');
            const bank = selectedOption.data('bank');
            $('#selected_nama_supplier').val(namaSupplier || '');
            $('#selected_bank_supplier').val(bank || '');
        } else {
            $('#selected_nama_supplier').val('');
            $('#selected_bank_supplier').val('');
        }
    }

    $(document).ready(function(){
        flatpickr("#tanggal_faktur", {
            dateFormat: "d-m-Y",
        });
        flatpickr("#input_tanggal_bayar", {
            dateFormat: "d-m-Y",
        });
        if (document.getElementById('id_supplier')) {
            new Selectr('#id_supplier', {
                searchable: true,
                placeholder: "-- Pilih Supplier --"
            });
        }
        (function initPembayaranStatus(){
            const metodePembayaran = document.getElementById('metode_pembayaran');
            const statusBayarInput = document.getElementById('status_bayar_final');
            const formTanggalBayar = document.getElementById('form_tanggal_bayar');
            const inputTanggalBayar = document.getElementById('input_tanggal_bayar');
            function updateStatusPembayaran() {
                if (metodePembayaran.value === 'Kredit') {
                    statusBayarInput.value = 'Belum Lunas';
                    formTanggalBayar.style.display = 'none';
                    inputTanggalBayar.disabled = true;
                    inputTanggalBayar.value = '';
                } else {
                    statusBayarInput.value = 'Lunas';
                    formTanggalBayar.style.display = '';
                    inputTanggalBayar.disabled = false;
                    const tanggalFaktur = document.getElementById('tanggal_faktur').value;
                    inputTanggalBayar.value = tanggalFaktur || "<?php echo date('d-m-Y'); ?>";
                }
            }
            updateStatusPembayaran();
            metodePembayaran.addEventListener('change', updateStatusPembayaran);
        })();
        $('#default_faktur').click(function(){
            $('#input_manual_faktur').prop('disabled', true).val('');
            $('#input_auto_faktur').removeAttr('disabled').val('<?php echo $no_faktur_otomatis; ?>');
            $('#form_input_manual_faktur').hide();
            $('#form_input_auto_faktur').show();
        });
        $('#input_no_faktur_radio').click(function(){
            $('#input_auto_faktur').prop('disabled', true).val('');
            $('#input_manual_faktur').removeAttr('disabled').val('');
            $('#form_input_auto_faktur').hide();
            $('#form_input_manual_faktur').show();
        });
        $('#id_supplier').on('change', function() {
            updateSelectedSupplierDetails($(this).val());
        });
        updateSelectedSupplierDetails($('#id_supplier').val());
        $('#metode_pembayaran').change(function() {
            pilih_bayar($(this).val());
        });
        pilih_bayar($('#metode_pembayaran').val());
        $('.check_status').change(function(){
            var isChecked = $(this).is(":checked");
            if(isChecked) {
                $('#form_diskon').show();
                $('#diskon_persen').prop('checked', true).change();
            } else {
                $('#form_diskon').hide();
                $('#input_diskon_persen').val('0');
                $('#input_diskon_rp').val('0');
                diskonType = 1;
            }
            hitung_diskon();
        });
        $('input[name="jenis_diskon"]').change(function () {
            var selectedType = $('input[name="jenis_diskon"]:checked').val();
            if (selectedType === 'persen') {
                $('#diskon_persen_input').show();
                $('#diskon_rp_input').hide();
                diskonType = 1;
                $('#input_diskon_rp').val('0');
            } else {
                $('#diskon_persen_input').hide();
                $('#diskon_rp_input').show();
                diskonType = 2;
                $('#input_diskon_persen').val('0');
            }
            hitung_diskon();
        });
        $('#search_barang_modal').on('keyup', function() {
            loadBarangModal(1);
        });
        $(document).off('click', '#data-barang-modal tr').on('click', '#data-barang-modal tr', function(e) {
            var $tr = $(this);
            var id_barang_detail = $tr.data('id');
            if (typeof id_barang_detail === 'undefined' || id_barang_detail === null || id_barang_detail === '') {
                return;
            }
            var data = {
                id_barang_detail: $tr.data('id'),
                id_barang_utama: $tr.data('id-barang-utama'),
                id_satuan_barang: $tr.data('id-satuan-barang'),
                kode_barang: $tr.data('kode'),
                nama_barang: $tr.data('nama'),
                satuan_barang: $tr.data('satuan'),
                harga_awal: $tr.data('harga-awal'),
                harga_jual: $tr.data('harga-jual'),
                laba: $tr.data('laba'),
                kadaluarsa: $tr.data('kadaluarsa'),
                urutan_satuan: $tr.data('urutan-satuan')
            };
            appendSelectedItemToTable(data);
        });
        hitung_total();
    });

    function simpan_faktur() {
        if ($('#tabel_tambah_barang tr').length === 0) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Harap tambahkan setidaknya satu barang ke faktur.',
                icon: "warning",
                confirmButtonColor: "#35baf5",
                confirmButtonText: "Oke"
            });
            return;
        }
        var id_supplier = $('#id_supplier').val();
        if (id_supplier === '' || id_supplier === null) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Harap pilih supplier terlebih dahulu.',
                icon: "warning",
                confirmButtonColor: "#35baf5",
                confirmButtonText: "Oke"
            });
            return;
        }
        var metode_pembayaran = $('#metode_pembayaran').val();
        var bayar_dp = MoneyToNumber($('#bayar_dp').val());
        var total_harga = MoneyToNumber($('#total_harga').val());
        if (metode_pembayaran === 'Tunai' && bayar_dp < total_harga) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Untuk metode pembayaran Tunai, jumlah Bayar tidak boleh kurang dari Total Harga.',
                icon: "warning",
                confirmButtonColor: "#35baf5",
                confirmButtonText: "Oke"
            });
            return;
        }
        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: "Apakah Anda yakin ingin menyimpan faktur ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#popup_load').show();
                let formData = new FormData($('#form_faktur')[0]);
                formData.set('total_harga_beli_raw', MoneyToNumber($('#subtotal_harga').val()));
                formData.set('total_harga_faktur', MoneyToNumber($('#total_harga').val()));
                formData.set('bayar_dp', MoneyToNumber($('#bayar_dp').val()));
                formData.set('kembalian_final', MoneyToNumber($('#kembalian').val()));
                if ($('.check_status').is(":checked")) {
                    if ($('input[name="jenis_diskon"]:checked').val() === 'persen') {
                        formData.set('diskon_persen', MoneyToNumber($('#input_diskon_persen').val()));
                        formData.set('diskon_rp', 0);
                    } else {
                        formData.set('diskon_rp', MoneyToNumber($('#input_diskon_rp').val()));
                        formData.set('diskon_persen', 0);
                    }
                } else {
                    formData.set('diskon_persen', 0);
                    formData.set('diskon_rp', 0);
                }
                let filteredDetailItems = [];
                $('#tabel_tambah_barang tr').each(function() {
                    let row = $(this);
                    let id_barang_detail = row.find('[name^="id_barang_detail[]"]').val();
                    if (id_barang_detail && id_barang_detail !== '0') {
                        filteredDetailItems.push({
                            id_barang_detail: id_barang_detail,
                            id_barang: row.find('[name^="id_barang[]"]').val(),
                            kode_barang: row.find('[name^="kode_barang[]"]').val(),
                            nama_barang: row.find('[name^="nama_barang[]"]').val(),
                            id_satuan_barang: row.find('[name^="id_satuan_barang[]"]').val(),
                            satuan: row.find('[name^="satuan[]"]').val(),
                            urutan_satuan: row.find('[name^="urutan_satuan[]"]').val(),
                            jumlah: MoneyToNumber(row.find('input[name="jumlah[]"]').val()),
                            harga_awal: MoneyToNumber(row.find('[name^="harga_awal[]"]').val()),
                            sub_total_harga_awal: MoneyToNumber(row.find('[name^="sub_total_harga_awal[]"]').val()),
                            harga_jual: MoneyToNumber(row.find('[name^="harga_jual[]"]').val()),
                            laba: MoneyToNumber(row.find('[name^="laba[]"]').val()),
                            kadaluarsa: row.find('[name^="kadaluarsa[]"]').val()
                        });
                    }
                });
                formData.delete('id_barang_detail[]');
                formData.delete('id_barang[]');
                formData.delete('kode_barang[]');
                formData.delete('nama_barang[]');
                formData.delete('id_satuan_barang[]');
                formData.delete('satuan[]');
                formData.delete('urutan_satuan[]');
                formData.delete('jumlah[]');
                formData.delete('harga_awal[]');
                formData.delete('sub_total_harga_awal[]');
                formData.delete('harga_jual[]');
                formData.delete('laba[]');
                formData.delete('kadaluarsa[]');
                filteredDetailItems.forEach((item, index) => {
                    formData.append(`id_barang_detail[${index}]`, item.id_barang_detail);
                    formData.append(`id_barang[${index}]`, item.id_barang);
                    formData.append(`kode_barang[${index}]`, item.kode_barang);
                    formData.append(`nama_barang[${index}]`, item.nama_barang);
                    formData.append(`id_satuan_barang[${index}]`, item.id_satuan_barang);
                    formData.append(`satuan[${index}]`, item.satuan);
                    formData.append(`urutan_satuan[${index}]`, item.urutan_satuan);
                    formData.append(`jumlah[${index}]`, item.jumlah);
                    formData.append(`harga_awal[${index}]`, item.harga_awal);
                    formData.append(`sub_total_harga_awal[${index}]`, item.sub_total_harga_awal);
                    formData.append(`harga_jual[${index}]`, item.harga_jual);
                    formData.append(`laba[${index}]`, item.laba);
                    formData.append(`kadaluarsa[${index}]`, item.kadaluarsa);
                });
                $.ajax({
                    url : '<?php echo base_url("pembelian/faktur/tambah"); ?>',
                    data : formData,
                    processData: false,
                    contentType: false,
                    type : "POST",
                    dataType : "json",
                    success : function(res){
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
                        console.error("AJAX Error: " + status + error + ", Response Text: " + xhr.responseText);
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
