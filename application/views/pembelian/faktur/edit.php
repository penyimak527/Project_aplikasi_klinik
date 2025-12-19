
<body>
<div class="container-fluid">
       <div class="row">
      <div class="col-sm-12">
          <div class="page-title-box">
              <div class="float-end">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>pembelian/faktur"><?php echo $title; ?></a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
                    <h4 class="card-title">Edit Faktur Barang</h4>
                </div>
                <div class="card-body">
                    <form action="#" method="POST" id="form_faktur">
                        <input type="hidden" id="barang_number" value="<?php echo count($row['details']); ?>">
                        <input type="hidden" id="barang_modal_current_row" value="0">
                        <input type="hidden" name="id_faktur" value="<?php echo $row['id']; ?>">

                        <div class="mb-3 row">
                            <label class="col-sm-2 col-form-label">No Faktur</label>
                            <div class="col-sm-10">
                                <input type="text" name="no_faktur_manual" class="form-control" id="input_manual_faktur"readonly value="<?php echo htmlspecialchars($row['no_faktur']); ?>"/>
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
                                            <?php echo ($supplier['id'] == $row['id_supplier']) ? 'selected' : ''; ?>
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
                                <input type="text" name="tanggal_faktur" class="form-control flatpickr-input" id="tanggal_faktur" value="<?php echo htmlspecialchars($row['tanggal']); ?>" required/>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="metode_pembayaran" class="col-sm-2 col-form-label">Metode Pembayaran</label>
                            <div class="col-sm-10">
                                <select class="form-select" name="metode_pembayaran" id="metode_pembayaran" required>
                                    <option value="Tunai" <?php echo ($row['metode_pembayaran'] == 'Tunai') ? 'selected' : ''; ?>>Tunai</option>
                                    <option value="Kredit" <?php echo ($row['metode_pembayaran'] == 'Kredit') ? 'selected' : ''; ?>>Kredit</option>
                                </select>
                                <input type="hidden" name="status_bayar" id="status_bayar_final">
                            </div>
                        </div>

                        <div id="form_tanggal_bayar" class="mb-3 row">
                            <label for="input_tanggal_bayar" class="col-sm-2 col-form-label">Tanggal Bayar</label>
                            <div class="col-sm-10">
                                <input type="text" name="tanggal_bayar" class="form-control flatpickr-input" id="input_tanggal_bayar" value="<?php echo htmlspecialchars($row['tanggal_bayar']); ?>" />
                            </div>
                        </div>

                        <hr class="my-4"/>
                        <button type="button" class="btn btn-info mb-3" onclick="tambah_barang()"><i class="ti ti-search me-2"></i>Cari Barang</button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-detail-barang">
                                <thead class="text-white">
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
                                    <?php $i = 0; foreach ($row['details'] as $detail): ?>
                                        <tr id="row_barang_<?php echo $i; ?>">
                                            <td class="text-center"><?php echo $i + 1; ?></td>
                                            <td>
                                                <input type="hidden" name="id_faktur_detail[]" value="<?php echo htmlspecialchars($detail['id']); ?>">
                                                <input type="hidden" name="id_barang_detail[]" id="id_barang_detail_<?php echo $i; ?>" value="<?php echo htmlspecialchars($detail['id_barang_detail']); ?>">
                                                <input type="hidden" name="id_barang[]" id="id_barang_<?php echo $i; ?>" value="<?php echo htmlspecialchars($detail['id_barang']); ?>">
                                                <input type="hidden" name="kode_barang[]" id="kode_barang_<?php echo $i; ?>" value="<?php echo htmlspecialchars(isset($detail['kode_barang']) ? $detail['kode_barang'] : ''); ?>">
                                                <input type="hidden" name="nama_barang[]" id="nama_barang_hidden_<?php echo $i; ?>" value="<?php echo htmlspecialchars($detail['nama_barang']); ?>">
                                                <input type="hidden" name="id_satuan_barang[]" id="id_satuan_barang_<?php echo $i; ?>" value="<?php echo htmlspecialchars($detail['id_satuan_barang']); ?>">
                                                <input type="hidden" name="satuan[]" id="satuan_<?php echo $i; ?>" value="<?php echo htmlspecialchars($detail['satuan_barang']); ?>">
                                                <input type="hidden" name="sub_total_harga_awal[]" id="sub_total_harga_awal_<?php echo $i; ?>" value="<?php echo htmlspecialchars(number_format($detail['sub_total_harga_awal'], 0,'.',',')); ?>">                                                <input type="hidden" name="urutan_satuan[]" id="urutan_satuan_<?php echo $i; ?>" value="<?php echo htmlspecialchars($detail['urutan_satuan']); ?>">
                                                <span id="display_nama_barang_<?php echo $i; ?>"><?php echo htmlspecialchars($detail['nama_barang'] . ' (' . $detail['satuan_barang'] . ')'); ?></span>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="jumlah[]" id="jumlah_<?php echo $i; ?>" min="1" value="<?php echo htmlspecialchars($detail['jumlah']); ?>"  onkeyup="FormatCurrency(this); hitungSubTotalItem(<?php echo $i; ?>)">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control" name="harga_awal[]" id="harga_awal_<?php echo $i; ?>"  value="<?php echo htmlspecialchars(number_format($detail['harga_awal'], 0,'.',',')); ?>" onkeyup="FormatCurrency(this); hitungLaba(<?php echo $i; ?>);" required/>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control" name="harga_jual[]" id="harga_jual_<?php echo $i; ?>" value="<?php echo htmlspecialchars(number_format($detail['harga_jual'], 0,'.',',')); ?>" onkeyup="FormatCurrency(this); hitungLaba(<?php echo $i; ?>);" required/>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control" name="laba[]" id="laba_<?php echo $i; ?>" readonly value="<?php echo htmlspecialchars(number_format($detail['laba'], 0,'.',',')); ?>"/>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control flatpickr-input" name="kadaluarsa[]" id="kadaluarsa_<?php echo $i; ?>" value="<?php echo htmlspecialchars($detail['kadaluarsa']); ?>"/>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(<?php echo $i; ?>)"><i class="ti ti-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php $i++; endforeach; ?>
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
                                            <input type="text" class="form-control" id="subtotal_harga" name="total_harga_beli_raw" readonly value="<?php echo htmlspecialchars(number_format($row['total_harga_beli'], 0,'.',',')); ?>"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Diskon</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input check_status" type="checkbox" id="flexSwitchCheckDefault" name="flexSwitchCheckDefault" value="on" <?php echo (isset($row['status_diskon']) && $row['status_diskon'] == 'ada') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="flexSwitchCheckDefault">Aktifkan Diskon</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="form_diskon" style="display: none;">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Jenis Diskon</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="jenis_diskon" id="diskon_persen" value="persen" <?php echo (isset($row['jenis_diskon_for_display']) && $row['jenis_diskon_for_display'] == 'persen') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="diskon_persen">% (Persen)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="jenis_diskon" id="diskon_rp_radio" value="rupiah" <?php echo (isset($row['jenis_diskon_for_display']) && $row['jenis_diskon_for_display'] == 'rupiah') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="diskon_rp_radio">Rp (Rupiah)</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="diskon_persen_input" class="mb-3 row">
                                        <label for="input_diskon_persen" class="col-sm-4 col-form-label">Diskon (%)</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" name="diskon_persen" class="form-control" id="input_diskon_persen" onkeyup="FormatCurrency(this); hitung_diskon();" value="<?php echo (isset($row['diskon_persen_display'])) ? htmlspecialchars(number_format($row['diskon_persen_display'], 0,'.',',')) : '0'; ?>"/>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="diskon_rp_input" class="mb-3 row" style="display: none;">
                                        <label for="input_diskon_rp" class="col-sm-4 col-form-label">Diskon (Rp)</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control" name="diskon_rp" id="input_diskon_rp" onkeyup="FormatCurrency(this); hitung_diskon();" value="<?php echo (isset($row['diskon_rp_display'])) ? htmlspecialchars(number_format($row['diskon_rp_display'], 0,'.',',')) : '0'; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row" id="total_harga_row ">
                                    <label class="col-sm-4 col-form-label">Total Harga</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="total_harga" name="total_harga_faktur" readonly value="<?php echo htmlspecialchars(number_format($row['total_harga'], 0,'.',',')); ?>"/>
                                        </div>
                                    </div>
                                </div>

                                <div id="form_bayar" class="mb-3 row">
                                    <label for="bayar_dp" class="col-sm-4 col-form-label">Bayar / DP</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="bayar_dp" name="bayar_dp" value="<?php echo htmlspecialchars(number_format($row['bayar'], 0, '.', ',')); ?>" onkeyup="FormatCurrency(this); get_bayar();"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row" id="kembalian_row">
                                    <label class="col-sm-4 col-form-label">Kembalian</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="kembalian" name="kembalian_final" readonly value="<?php echo htmlspecialchars(number_format($row['kembalian'], 0,'.',',')); ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-sm-12 text-start">
                                <button type="button" onclick="edit_faktur();" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan</button>
                                <a href="<?php echo base_url(); ?>pembelian/faktur"><button type="button" class="btn btn-warning"><i class="ti ti-arrow-back-up me-2"></i>Kembali</button></a>
                            </div>
                        </div>
                    </form>
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
                        <table class="table table-striped table-bordered table-hover table_data_barang" id="table-barang-modal">
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
                                    <option value="10"selected>10</option>
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


    function FormatCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/^0+/, '');
    if (value) {
        input.value = new Intl.NumberFormat('id-ID', {
            style: 'decimal',
            useGrouping: true
        }).format(value).replace(/\./g, ','); 
    } else {
        input.value = '';
    }
    }

    function MoneyToNumber(moneyString) {
        if (!moneyString) return 0;
            
        let cleaned = moneyString.toString().replace(/\./g, '').replace(/,/g, '');        
                return parseFloat(cleaned) || 0;
        } 

    function NumberToMoney(number) {
        if (isNaN(number) || number === null) return '0';
        if (number === 0 || Math.abs(number) < 0.000001) return '0';
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(number).replace(/\./g, ','); 
    }

    function FormatStok(number) {
        const num = parseFloat(number) || 0;
        if (num % 1 === 0) return new Intl.NumberFormat('id-ID').format(num);
        const options = { minimumFractionDigits: 2, maximumFractionDigits: 2 };
        return new Intl.NumberFormat('id-ID', options).format(num);
    }

    function get_bayar() {
        var total = MoneyToNumber($('#total_harga').val());
        var bayar = MoneyToNumber($('#bayar_dp').val());
        var metode = $('#metode_pembayaran').val();
        var kembali = bayar - total;
        $('#kembalian').val(NumberToMoney(kembali));
        if (metode === 'Tunai' || metode === 'Kredit') {
            $('#status_bayar_final').val(kembali >= 0 ? 'Lunas' : 'Belum Lunas');
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
        var jumlah_beli = parseInt(($('#jumlah_' + number).val() || '0').toString().replace(/\D/g, '')) || 0;
        var harga_awal = parseInt(($('#harga_awal_' + number).val() || '0').toString().replace(/\D/g, '')) || 0;
        var sub_total = Number(jumlah_beli) * Number(harga_awal);
        $('#sub_total_harga_awal_' + number).val(NumberToMoney(sub_total));
        hitung_total();
    }

    function hitung_total() {
        let total = 0;
        $('input[name="sub_total_harga_awal[]"]').each(function() {
            total += parseInt(($(this).val() || '0').toString().replace(/\D/g, '')) || 0;
        });
        $('#subtotal_harga').val(NumberToMoney(total));
        hitung_diskon();
    }

    function hitung_diskon() {
        var subtotal = MoneyToNumber($('#subtotal_harga').val());
        var total = subtotal;
        var is_diskon = $('.check_status').is(":checked");
        if (is_diskon) {
            if (diskonType == 1) {
                var persen = MoneyToNumber($('#input_diskon_persen').val());
                if (persen != 0) total = subtotal - (subtotal * (persen / 100));
            } else if (diskonType == 2) {
                var rupiah = MoneyToNumber($('#input_diskon_rp').val());
                if (rupiah != 0) total = subtotal - rupiah;
            }
        } else {
            $('#input_diskon_persen').val('0');
            $('#input_diskon_rp').val('0');
            diskonType = 1;
        }
        $('#total_harga').val(NumberToMoney(total));
        get_bayar();
    }

    function create_row_html(i) {
        return `
        <tr id="row_barang_${i}">
            <td class="text-center">${parseInt(i) + 1}</td>
            <td>
                <input type="hidden" name="id_faktur_detail_new[]" value="">
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
            <td><input type="text" class="form-control" name="jumlah[]" id="jumlah_${i}" min="1" value="1" oninput="hitungSubTotalItem(${i})" onkeyup="FormatCurrency(this);"></td>
            <td><div class="input-group"><span class="input-group-text">Rp</span><input type="text" class="form-control" name="harga_awal[]" id="harga_awal_${i}" value="0" onkeyup="FormatCurrency(this); hitungLaba(${i});"></div></td>
            <td><div class="input-group"><span class="input-group-text">Rp</span><input type="text" class="form-control" name="harga_jual[]" id="harga_jual_${i}" value="0" onkeyup="FormatCurrency(this); hitungLaba(${i});"></div></td>
            <td><div class="input-group"><span class="input-group-text">Rp</span><input type="text" class="form-control" name="laba[]" id="laba_${i}" readonly value="0"></div></td>
            <td><input type="text" class="form-control flatpickr-input" name="kadaluarsa[]" id="kadaluarsa_${i}" value=""></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${i})"><i class="ti ti-trash"></i></button></td>
        </tr>`;
    }

    function removeEmptyRows() {
        $('#tabel_tambah_barang tr').each(function() {
            let row = $(this);
            let idDetail = row.find('input[name="id_barang_detail[]"]').val();
            let idBarang = row.find('input[name="id_barang[]"]').val();
            let nama = row.find('input[name="nama_barang[]"]').val();
            let kode = row.find('input[name="kode_barang[]"]').val();
            if ((typeof idDetail === 'undefined' || idDetail === '') &&
                (typeof idBarang === 'undefined' || idBarang === '') &&
                (typeof nama === 'undefined' || nama === '') &&
                (typeof kode === 'undefined' || kode === '')) {
                row.remove();
            }
        });
        reindexRows();
    }

function reindexRows() {
        $('#tabel_tambah_barang tr').each(function(index) {
            // Re-ID baris
            $(this).attr('id', 'row_barang_' + index);
            
            // Re-Number kolom pertama
            $(this).find('td:first').text(index + 1);

            // Re-ID semua input hidden
            $(this).find('input[name="id_barang_detail[]"]').attr('id', 'id_barang_detail_' + index);
            $(this).find('input[name="id_barang[]"]').attr('id', 'id_barang_' + index);
            $(this).find('input[name="kode_barang[]"]').attr('id', 'kode_barang_' + index);
            $(this).find('input[name="nama_barang[]"]').attr('id', 'nama_barang_hidden_' + index);
            $(this).find('input[name="id_satuan_barang[]"]').attr('id', 'id_satuan_barang_' + index);
            $(this).find('input[name="satuan[]"]').attr('id', 'satuan_' + index);
            $(this).find('input[name="sub_total_harga_awal[]"]').attr('id', 'sub_total_harga_awal_' + index);
            $(this).find('input[name="urutan_satuan[]"]').attr('id', 'urutan_satuan_' + index);
            
            // Re-ID display span
            $(this).find('span[id^="display_nama_barang_"]').attr('id', 'display_nama_barang_' + index);
            
            // Re-ID DAN Re-Bind event handler untuk input yang terlihat
            $(this).find('input[name="jumlah[]"]')
                .attr('id', 'jumlah_' + index)
                .attr('oninput', 'hitungSubTotalItem(' + index + ')')
                .attr('onkeyup', 'FormatCurrency(this);');
                
            $(this).find('input[name="harga_awal[]"]')
                .attr('id', 'harga_awal_' + index)
                .attr('onkeyup', 'FormatCurrency(this); hitungLaba(' + index + ');');
                
            $(this).find('input[name="harga_jual[]"]')
                .attr('id', 'harga_jual_' + index)
                .attr('onkeyup', 'FormatCurrency(this); hitungLaba(' + index + ');');
                
            $(this).find('input[name="laba[]"]')
                .attr('id', 'laba_' + index);
                
            $(this).find('input[name="kadaluarsa[]"]')
                .attr('id', 'kadaluarsa_' + index);
            
            // Re-Bind tombol hapus
            $(this).find('button[onclick^="hapusBaris"]')
                .attr('onclick', 'hapusBaris(' + index + ')');

            // Hapus instance flatpickr lama dan buat yang baru (PENTING!)
            // Ini mencegah bug kalender
            let kadaluarsaInput = $(this).find('input[name="kadaluarsa[]"]')[0];
            if (kadaluarsaInput._flatpickr) {
                kadaluarsaInput._flatpickr.destroy();
            }
            flatpickr(kadaluarsaInput, { dateFormat: "d-m-Y" });
        });
        
        // Update counter
        $('#barang_number').val($('#tabel_tambah_barang tr').length);
    }

    function tambah_barang() {
        removeEmptyRows();
        $('#barang_modal_current_row').val('');
        $('#search_barang_modal').val('');
        loadBarangModal();
        $('#modalBarang').modal('show');
    }

    function tambah_barang_quiet() {
        $('#barang_modal_current_row').val('');
    }

    function loadBarangModal(page = 1) {
        let count_header = $(`#modalBarang #table-barang-modal thead tr th`).length;
        var limit = $('#jumlah_tampil_barang_modal').val();
        var search = $('#search_barang_modal').val();
        $('#popup_load').show();
        $.ajax({
            url: '<?php echo base_url(); ?>pembelian/faktur/get_all_barang_for_popup',
            type: 'POST',
            data: { search: search, page: page, limit: limit },
            dataType: 'json',
            beforeSend: function() {
                let loading = `<tr id="tr-loading"><td colspan="${count_header}" class="text-center"><img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading"></td></tr>`;
                $(`#modalBarang #data-barang-modal`).html(loading);
            },
            success: function(response) {
                $('#popup_load').fadeOut();
                let tr = "";
                if (response.result === true && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        var harga_awal_display = parseFloat(item.harga_awal) || 0;
                        var harga_jual_display = parseFloat(item.harga_jual) || 0;
                        var laba_display = parseFloat(item.laba) || 0;
                        var kadaluarsa_value = item.kadaluarsa !== null ? item.kadaluarsa : '';
                        var stok_value = parseFloat(item.stok) || 0;
                        tr += `<tr style="cursor:pointer;" data-id="${item.id_barang_detail}" data-id-barang-utama="${item.id_barang_utama}" data-id-satuan-barang="${item.id_satuan_barang}" data-kode="${item.kode_barang || ''}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_barang}" data-harga-awal="${harga_awal_display}" data-harga-jual="${harga_jual_display}" data-laba="${laba_display}" data-kadaluarsa="${kadaluarsa_value}" data-urutan-satuan="${item.urutan_satuan}">
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
                $('#popup_load').fadeOut();
                let count_header_error = $(`#modalBarang #table-barang-modal thead tr th`).length;
                $('#data-barang-modal').html(`<tr><td colspan="${count_header_error}" class="text-center text-danger py-4">Gagal memuat data barang.</td></tr>`);
                $('#pagination_barang_modal').empty();
            },
            complete: function() {
                $(`#tr-loading`).remove();
            }
        });
    }

    function updatePaginationModal(totalRows, limit, currentPage) {
        let totalPages = Math.ceil(totalRows / limit);
        let paginationHtml = '';
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(1)"><i class="fas fa-angle-double-left"></i></a></li>`;
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${currentPage - 1})"><i class="fas fa-angle-left"></i></a></li>`;
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        if (startPage > 1) paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${i})">${i}</a></li>`;
        }
        if (endPage < totalPages) paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${currentPage + 1})"><i class="fas fa-angle-right"></i></a></li>`;
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${totalPages})"><i class="fas fa-angle-double-right"></i></a></li>`;
        $('#pagination_barang_modal').html(paginationHtml);
    }

    function updateSelectedSupplierDetails(supplierId) {
        if (supplierId) {
            const selected = $('#id_supplier option:selected');
            $('#selected_nama_supplier').val(selected.data('nama_supplier') || '');
            $('#selected_bank_supplier').val(selected.data('bank') || '');
        } else {
            $('#selected_nama_supplier').val('');
            $('#selected_bank_supplier').val('');
        }
    }

    function hapusBaris(index) {
        $('#row_barang_' + index).remove();
        reindexRows();
        hitung_total();
    }

    function pilih_bayar(metode) {
        if (metode === 'Kredit') {
            $('#status_bayar_final').val('Belum Lunas');
            $('#form_tanggal_bayar').hide();
            $('#input_tanggal_bayar').prop('disabled', true).val('');
        } else {
            $('#status_bayar_final').val('Lunas');
            $('#form_tanggal_bayar').show();
            $('#input_tanggal_bayar').prop('disabled', false);
            var tanggalFaktur = $('#tanggal_faktur').val();
            $('#input_tanggal_bayar').val(tanggalFaktur || "<?php echo date('d-m-Y'); ?>");
        }
    }

    $(document).ready(function() {
        flatpickr("#tanggal_faktur", { dateFormat: "d-m-Y" });
        flatpickr("#input_tanggal_bayar", { dateFormat: "d-m-Y" });
        if (document.getElementById('id_supplier')) {   
            new Selectr('#id_supplier', {
                searchable: true,
                placeholder: "-- Pilih Supplier --"
            });
        }
        (function initPembayaranStatus() {
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

        $('#default_faktur').click(function() {
            $('#input_manual_faktur').prop('disabled', true).val('');
            $('#input_auto_faktur').removeAttr('disabled').val('<?php echo htmlspecialchars($no_faktur_otomatis); ?>');
            $('#form_input_manual_faktur').hide();
            $('#form_input_auto_faktur').show();
        });

        $('#input_no_faktur_radio').click(function() {
            $('#input_auto_faktur').prop('disabled', true).val('');
            $('#input_manual_faktur').removeAttr('disabled').val(noFakturManual);
            $('#form_input_auto_faktur').hide();
            $('#form_input_auto_faktur').show();
        });

        $('#id_supplier').on('change', function() {
            updateSelectedSupplierDetails($(this).val());
        });

        updateSelectedSupplierDetails($('#id_supplier').val());

        $('#metode_pembayaran').change(function() {
            pilih_bayar($(this).val());
        });

        pilih_bayar($('#metode_pembayaran').val());

        $('#bayar_dp').on('keyup', function() {
            FormatCurrency(this);
            get_bayar();
        });

        var statusDiskon = "<?php echo htmlspecialchars($row['status_diskon']); ?>";
        var initialJenisDiskon = "<?php echo htmlspecialchars(isset($row['jenis_diskon_for_display']) ? $row['jenis_diskon_for_display'] : 'persen'); ?>";
        var initialDiskonPersen = "<?php echo htmlspecialchars(isset($row['diskon_persen_display']) ? $row['diskon_persen_display'] : '0'); ?>";
        var initialDiskonRp = "<?php echo htmlspecialchars(isset($row['diskon_rp_display']) ? $row['diskon_rp_display'] : '0'); ?>";

        if (statusDiskon == 'ada') {
            $('.check_status').prop('checked', true);
            $('#form_diskon').show();
            if (initialJenisDiskon === 'rupiah') {
                $('#diskon_rp_radio').prop('checked', true);
                $('#diskon_persen_input').hide();
                $('#diskon_rp_input').show();
                diskonType = 2;
                $('#input_diskon_persen').val('0');
                $('#input_diskon_rp').val(NumberToMoney(initialDiskonRp));
            } else {
                $('#diskon_persen').prop('checked', true);
                $('#diskon_persen_input').show();
                $('#diskon_rp_input').hide();
                diskonType = 1;
                $('#input_diskon_rp').val('0');
                $('#input_diskon_persen').val(NumberToMoney(initialDiskonPersen));
            }
        } else {
            $('.check_status').prop('checked', false);
            $('#form_diskon').hide();
            $('#input_diskon_persen').val('0');
            $('#input_diskon_rp').val('0');
            diskonType = 1;
        }

        hitung_diskon();

        $('.check_status').change(function() {
            var isChecked = $(this).is(":checked");
            if (isChecked) {
                $('#form_diskon').show();
                if (initialJenisDiskon === 'rupiah') {
                    $('#diskon_rp_radio').prop('checked', true);
                    $('#diskon_persen_input').hide();
                    $('#diskon_rp_input').show();
                    diskonType = 2;
                    $('#input_diskon_persen').val('0');
                    $('#input_diskon_rp').val(NumberToMoney(initialDiskonRp));
                } else {
                    $('#diskon_persen').prop('checked', true);
                    $('#diskon_persen_input').show();
                    $('#diskon_rp_input').hide();
                    diskonType = 1;
                    $('#input_diskon_rp').val('0');
                    $('#input_diskon_persen').val(NumberToMoney(initialDiskonPersen));
                }
            } else {
                $('#form_diskon').hide();
                $('#input_diskon_persen').val('0');
                $('#input_diskon_rp').val('0');
                diskonType = 1;
            }
            hitung_diskon();
        });

        $('input[name="jenis_diskon"]').change(function() {
            var selectedType = $('input[name="jenis_diskon"]:checked').val();
            if (selectedType === 'persen') {
                $('#diskon_persen_input').show();
                $('#diskon_rp_input').hide();
                diskonType = 1;
                $('#input_diskon_rp').val('0');
                $('#input_diskon_persen').val(NumberToMoney(initialDiskonPersen));
            } else {
                $('#diskon_persen_input').hide();
                $('#diskon_rp_input').show();
                diskonType = 2;
                $('#input_diskon_persen').val('0');
                $('#input_diskon_rp').val(NumberToMoney(initialDiskonRp));
            }
            hitung_diskon();
        });

        $('#search_barang_modal').on('keyup', function() {
            loadBarangModal(1);
        });

        $(document).off('click', '#data-barang-modal tr').on('click', '#data-barang-modal tr', function() {
            var $tr = $(this);
            var id_barang_detail = $tr.data('id');
            if (typeof id_barang_detail === 'undefined' || id_barang_detail === null || id_barang_detail === '') return;
            var id_barang_utama = $tr.data('id-barang-utama');
            var id_satuan_barang = $tr.data('id-satuan-barang');
            var kode_barang = $tr.data('kode');
            var nama_barang = $tr.data('nama');
            var satuan = $tr.data('satuan');
            var harga_awal = parseFloat($tr.data('harga-awal')) || 0;
            var harga_jual = parseFloat($tr.data('harga-jual')) || 0;
            var laba = parseFloat($tr.data('laba')) || 0;
            var kadaluarsa = $tr.data('kadaluarsa') || '';
            var urutan_satuan = $tr.data('urutan-satuan') || 1;
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
                Swal.fire({ title: 'Peringatan!', text: 'Barang ini sudah masuk di daftar.', icon: 'warning', confirmButtonColor: '#35baf5', confirmButtonText: 'Oke' });
                return;
            }
            var i = $('#tabel_tambah_barang tr').length;
            var menu = create_row_html(i);
            $('#tabel_tambah_barang').append(menu);
            $('#barang_number').val(i + 1);
            $(`#id_barang_detail_${i}`).val(id_barang_detail);
            $(`#id_barang_${i}`).val(id_barang_utama);
            $(`#kode_barang_${i}`).val(kode_barang);
            $(`#nama_barang_hidden_${i}`).val(nama_barang);
            $(`#id_satuan_barang_${i}`).val(id_satuan_barang);
            $(`#satuan_${i}`).val(satuan);
            $(`#urutan_satuan_${i}`).val(urutan_satuan);
            $(`#display_nama_barang_${i}`).text(`${nama_barang} (${satuan})`);
            $(`#jumlah_${i}`).val('1');
            $(`#harga_awal_${i}`).val(NumberToMoney(harga_awal));
            $(`#harga_jual_${i}`).val(NumberToMoney(harga_jual));
            $(`#laba_${i}`).val(NumberToMoney(laba));
            $(`#sub_total_harga_awal_${i}`).val(NumberToMoney(harga_awal * 1));
            $(`#kadaluarsa_${i}`).val(kadaluarsa || '');
            flatpickr(`#kadaluarsa_${i}`, { dateFormat: "d-m-Y" });
            hitungLaba(i);
            hitung_total();
        });

        FormatCurrency(document.getElementById('subtotal_harga'));
        FormatCurrency(document.getElementById('input_diskon_persen'));
        FormatCurrency(document.getElementById('input_diskon_rp'));
        FormatCurrency(document.getElementById('total_harga'));
        FormatCurrency(document.getElementById('bayar_dp'));
        FormatCurrency(document.getElementById('kembalian'));

        <?php $i = 0; foreach ($row['details'] as $detail): ?>
        FormatCurrency(document.getElementById('sub_total_harga_awal_<?php echo $i; ?>'));
        FormatCurrency(document.getElementById('harga_awal_<?php echo $i; ?>'));
        FormatCurrency(document.getElementById('harga_jual_<?php echo $i; ?>'));
        FormatCurrency(document.getElementById('laba_<?php echo $i; ?>'));
        flatpickr(`#kadaluarsa_<?php echo $i; ?>`, { dateFormat: "d-m-Y" });
        <?php $i++; endforeach; ?>

        hitung_total();

        $('#modalBarang').on('show.bs.modal', function () { $('body').css('overflow', 'hidden'); });
        $('#modalBarang').on('hidden.bs.modal', function () { $('body').css('overflow', ''); });
    });

    function edit_faktur() {
        if ($('#tabel_tambah_barang tr').length === 0) {
            Swal.fire('Peringatan!', 'Harap tambahkan setidaknya satu barang ke faktur.', 'warning');
            return;
        }
        if ($('#id_supplier').val() === '' || $('#id_supplier').val() === null) {
            Swal.fire('Peringatan!', 'Harap pilih supplier terlebih dahulu.', 'warning');
            return;
        }

        var metode_pembayaran = $('#metode_pembayaran').val();
        var bayar_dp = MoneyToNumber($('#bayar_dp').val());
        var total_harga = MoneyToNumber($('#total_harga').val());

        if (metode_pembayaran === 'Tunai' && bayar_dp < total_harga) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Untuk metode pembayaran Tunai, jumlah Bayar harus sama atau lebih besar dari Total Harga.',
                confirmButtonColor: "#35baf5",
                confirmButtonText: 'Oke'
            });
            return false;
        }

        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: "Apakah Anda yakin ingin menyimpan perubahan faktur ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#popup_load').show();
                var fakturId = $('input[name="id_faktur"]').val();
                let formData = new FormData($('#form_faktur')[0]);
                formData.set('total_harga_faktur', MoneyToNumber($('#total_harga').val()));
                formData.set('bayar_dp', MoneyToNumber($('#bayar_dp').val()));
                $('#tabel_tambah_barang tr').each(function(index) {
                    formData.set(`jumlah[${index}]`, MoneyToNumber($(this).find('input[name="jumlah[]"]').val()));
                    formData.set(`harga_awal[${index}]`, MoneyToNumber($(this).find('input[name="harga_awal[]"]').val()));
                    formData.set(`harga_jual[${index}]`, MoneyToNumber($(this).find('input[name="harga_jual[]"]').val()));
                    formData.set(`laba[${index}]`, MoneyToNumber($(this).find('input[name="laba[]"]').val()));
                    formData.set(`sub_total_harga_awal[${index}]`, MoneyToNumber($(this).find('input[name="sub_total_harga_awal[]"]').val()));
                });
                $.ajax({
                    url : '<?php echo base_url('pembelian/faktur/edit/'); ?>' + fakturId,
                    method : 'POST',
                    data : formData,
                    processData: false,
                    contentType: false,
                    dataType : 'json',
                    success : function(res){
                        $('#popup_load').fadeOut();
                        if (res.status == true) {
                            Swal.fire({ title: 'Berhasil!', text: res.message, icon: "success" }).then(() => {
                                window.location.href = '<?php echo base_url(); ?>pembelian/faktur';
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#popup_load').fadeOut();
                        Swal.fire('Error!', 'Terjadi kesalahan pada server: ' + xhr.responseText, 'error');
                    }
                });
            }
        });
    }
    </script>

</body>