<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="float-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
                            <li class="breadcrumb-item active"><?= $title?></li>
                        </ol>
                    </div>
                    <h4 class="page-title"><?= $title?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="search_barang_input" placeholder="Klik disini atau tekan F2 untuk mencari barang..." readonly style="cursor:pointer;">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" id="btn_bayar_sekarang" disabled>
                                    <i class="fas fa-money-bill-wave me-2"></i>Bayar Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="keranjang_table">
                                <thead class="thead-light bg-light">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th style="width: 15%;">Satuan</th> 
                                        <th style="width: 15%;">Jumlah</th>
                                        <th style="width: 20%;">Harga Jual</th>
                                        <th style="width: 20%;">Subtotal</th>
                                        <th style="width: 5%;" class="text-center"><i class="ti ti-trash"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="keranjang_body">
                                    <tr id="placeholder-keranjang">
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <div class="fs-1 text-primary"><i class="ti ti-shopping-cart-off"></i></div>
                                            <h5 class="mt-2">Keranjang Anda Masih Kosong</h5>
                                            <p class="mb-0">Silakan cari produk untuk memulai transaksi.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12" id="footer-keranjang" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <h4 class="card-title mb-0 me-3">Total Harga:</h4>
                            <h4 class="card-title mb-0" id="grand_total_display">Rp 0</h4>
                        </div>
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
                    <div class="row mb-3">
                         <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" id="search_barang_modal" placeholder="Cari Nama atau Kode Barang..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filter_jenis_barang">
                                <option value="all">Semua Jenis</option>
                            </select>
                        </div>                       
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="table-barang-modal">
                            <thead class="thead-light bg-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Nama Barang</th>
                                </tr>
                            </thead>
                            <tbody id="data-barang-modal">
                                <tr><td colspan="2" class="text-center text-muted py-4"><b>Ketik untuk mencari barang...</b></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <ul id="pagination_barang_modal" class="pagination float-start"></ul>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center">
                                <label for="jumlah_tampil_barang_modal" class="me-2 mb-0">Tampil:</label>
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

    <div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalPembayaranLabel"><i class="ti ti-receipt me-2"></i>Detail & Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">INFORMASI & PEMBAYARAN</h6>
                            <hr class="mt-1">
                            
                            <div class="mb-3">
                                <label for="cari_pelanggan" class="form-label">Cari Pelanggan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cari_pelanggan" placeholder="Nama pelanggan...">
                                    <button class="btn btn-primary" type="button" id="btn_cari_pelanggan">
                                        <i class="ti ti-search"></i>
                                    </button>
                                </div>
                                <div id="hasil_pencarian" class="mt-2" style="display: none;">
                                    <div class="list-group" id="list_pelanggan">
                                    </div>
                                </div>
                            </div>
                            
                            <div id="detail_pelanggan_form" style="border: 1px dashed #ddd; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                                <h6 class="mb-3">Data Pelanggan</h6>
                                <input type="hidden" id="id_pelanggan" value="">
                                <div class="mb-2">
                                    <label for="nama_customer" class="form-label">Nama Pelanggan</label>
                                    <input type="text" class="form-control" id="nama_customer" placeholder="Nama pelanggan">
                                </div>
                                <div class="mb-2">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jenis_kelamin">
                                        <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="umur" class="form-label">Umur</label>
                                    <input type="number" class="form-control" id="umur" placeholder="Umur">
                                </div>
                                <div class="mb-2">
                                    <label for="no_telp" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telp" placeholder="No. Telepon">
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" id="btn_pelanggan_baru">
                                    <i class="ti ti-user-plus me-1"></i>Pelanggan Baru
                                </button>
                            </div>
                            
                            <div class="mb-2">
                                <label for="metode_bayar" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="metode_bayar" onchange="cek_metode_bayar(this.value)">
                                    <option value="Cash" selected>Cash</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                            </div>
                            <div class="mb-2 form_nama_bank" style="display: none;">
                                <label for="nama_bank" class="form-label">Nama Bank</label>
                                <select class="form-select" id="nama_bank">
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="BNI">BNI</option>
                                    <option value="BRI">BRI</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="dibayar" class="form-label">Jumlah Dibayar (F3)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="dibayar" onkeyup="FormatCurrency(this); get_bayar();">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="kembali" class="form-label">Kembali</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="kembali" readonly value="0" style="background-color: #e9ecef;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">RINGKASAN KERANJANG</h6>
                            <hr class="mt-1">
                            <div id="ringkasan_keranjang_modal" style="max-height: 250px; overflow-y: auto; padding-right: 10px;">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Total Tagihan:</h5>
                                <h5 class="fw-bold text-primary mb-0" id="total_tagihan_modal">Rp 0</h5>
                                <input type="hidden" id="nilai_transaksi" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="proses_transaksi()">
                        <i class="ti ti-device-floppy me-2"></i>Proses & Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
<script>
    let keranjang = [];
    let searchTimeout;
    let selectrJenis = null; 

    function FormatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/^0+/, ''); 
        input.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
    }

    function NumberToMoney(number) {
        if (isNaN(number) || number === null) return '0';
        return new Intl.NumberFormat('id-ID').format(parseFloat(number) || 0);
    }

    function MoneyToNumber(moneyString) {
        if (!moneyString) return 0;
        return parseFloat(String(moneyString).replace(/\./g, '').replace(/,/g, '.')) || 0;
    }

    function FormatStok(number) {
        const num = parseFloat(number) || 0;
        if (num % 1 === 0) {
            return new Intl.NumberFormat('id-ID').format(num);
        } else {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
        }
    }

    function loadFilterJenis() {
        $.ajax({
            url: '<?php echo base_url("transaksi/penjualan/get_jenis_barang_ajax"); ?>', 
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#filter_jenis_barang').empty();
                $('#filter_jenis_barang').append('<option value="all">Semua Jenis</option>');

                if (response) {
                    response.forEach(jenis => {
                        $('#filter_jenis_barang').append(new Option(jenis.nama_jenis, jenis.id));
                    });
                }

                if (selectrJenis) {
                    selectrJenis.destroy();
                }

                selectrJenis = new Selectr('#filter_jenis_barang', {
                    searchable: true,
                    placeholder: "Pilih Jenis Barang"
                });

                selectrJenis.on('selectr.change', function(option) {
                    loadBarangModal(1); 
                });
            },
            error: function(xhr, status, error) {
                console.error("Gagal memuat jenis barang:", error);
            }
        });
    }

    function loadBarangModal(page = 1) {
        let limit = $('#jumlah_tampil_barang_modal').val();
        let search = $('#search_barang_modal').val();
        let jenis_id = $('#filter_jenis_barang').val(); 

        $.ajax({
            url: '<?php echo base_url("transaksi/penjualan/get_barang_list_pagination"); ?>', 
            type: 'POST',
            data: { search: search, page: page, limit: limit, jenis_id: jenis_id },
            dataType: 'json',
            beforeSend: () => {
                let loading = `<tr><td colspan="2" class="text-center"><div class="spinner-border text-primary" role="status"></div></td></tr>`;
                $(`#data-barang-modal`).html(loading);
            },
            success: function(response) {
                let tr = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        let itemJson = JSON.stringify(item).replace(/"/g, '&quot;');
                        
                        tr += `<tr style="cursor:pointer;" 
                                onclick="pilihBarang(this)"
                                data-json="${itemJson}">
                                <td class="text-center">${((page - 1) * limit) + index + 1}</td>
                                <td>
                                    <span>${item.nama_barang}</span>
                                </td>
                            </tr>`;
                    });
                    updatePaginationModal(response.total_rows, limit, page);
                } else {
                    tr = `<tr><td colspan="2" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>`;
                    $('#pagination_barang_modal').empty();
                }
                $('#data-barang-modal').html(tr);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + error);
                $('#data-barang-modal').html(`<tr><td colspan="2" class="text-center text-danger">Gagal memuat data.</td></tr>`);
            }
        });
    }

    function updatePaginationModal(totalRows, limit, currentPage) {
        let totalPages = Math.ceil(totalRows / limit);
        let paginationHtml = '';
        
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(1)"><i class="ti ti-chevrons-left"></i></a></li>`;
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${currentPage - 1})"><i class="ti ti-chevron-left"></i></a></li>`;
        
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${i})">${i}</a></li>`;
        }
        
        if (endPage < totalPages) paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${currentPage + 1})"><i class="ti ti-chevron-right"></i></a></li>`;
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadBarangModal(${totalPages})"><i class="ti ti-chevrons-right"></i></a></li>`;
        
        $('#pagination_barang_modal').html(paginationHtml);
    }

    function pilihBarang(element) {
        let data = $(element).data('json');
        
        let existingIndex = keranjang.findIndex(x => x.id_barang_detail == data.id_barang_detail);

        if (existingIndex > -1) {
            let item = keranjang[existingIndex];
            let newQty = parseFloat(item.jumlah_beli) + 1;
            if (newQty > item.stok_batas_saat_ini) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Cukup',
                    text: `Maksimal pembelian: ${NumberToMoney(item.stok_batas_saat_ini)} ${item.satuan_dipilih}`,
                    confirmButtonColor: "#35baf5",
                    confirmButtonText: 'Oke'
                });
                return;
            }
            keranjang[existingIndex].jumlah_beli = newQty;
        } else {
            
            let listSatuan = data.satuan_list || [{
                satuan: data.satuan_barang, 
                harga: data.harga_jual, 
                konversi: 1,
                id_satuan: data.id_satuan_barang,
                stok_satuan: parseFloat(data.stok) 
            }];

            let satuanAwal = listSatuan[0];

            keranjang.push({
                id_barang: data.id_barang_utama || data.id_barang || data.id,
                id_barang_detail: data.id_barang_detail,
                nama_barang: data.nama_barang,                
                list_satuan: listSatuan,                
                satuan_dipilih: satuanAwal.satuan, 
                harga_saat_ini: parseFloat(satuanAwal.harga),
                nilai_konversi: parseFloat(satuanAwal.konversi),                
                stok_batas_saat_ini: parseFloat(satuanAwal.stok_satuan),                 
                jumlah_beli: 1
            });
        }
        render_keranjang();
    }

    function ubahSatuan(index, element) {
        let namaSatuanBaru = element.value;
        let item = keranjang[index];
        let satuanObj = item.list_satuan.find(s => s.satuan === namaSatuanBaru);

        if (satuanObj) {
            keranjang[index].satuan_dipilih = satuanObj.satuan;
            keranjang[index].harga_saat_ini = parseFloat(satuanObj.harga);
            keranjang[index].nilai_konversi = parseFloat(satuanObj.konversi);
            keranjang[index].stok_batas_saat_ini = parseFloat(satuanObj.stok_satuan);

            if (item.jumlah_beli > keranjang[index].stok_batas_saat_ini) {
                let maxQtyBaru = keranjang[index].stok_batas_saat_ini;
                keranjang[index].jumlah_beli = maxQtyBaru;
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Qty Disesuaikan',
                    text: `Jumlah disesuaikan ke stok maksimal ${namaSatuanBaru} (${NumberToMoney(maxQtyBaru)}).`,
                    confirmButtonColor: "#35baf5",
                    confirmButtonText: 'Oke'
                });
            }
        }

        render_keranjang(); 
    }

    function ubahQty(index, element) {
        let val = MoneyToNumber(element.value);
        let item = keranjang[index];

        if (val > item.stok_batas_saat_ini) {
            
            let maxQty = item.stok_batas_saat_ini;
            
            Swal.fire({
                icon: 'warning',
                title: 'Stok Tidak Cukup',
                text: `Maksimal pembelian: ${NumberToMoney(maxQty)} ${item.satuan_dipilih}`,
                confirmButtonColor: "#35baf5",
                confirmButtonText: 'Oke'
            });
            
            val = maxQty;
            element.value = NumberToMoney(maxQty);
        }

        keranjang[index].jumlah_beli = val;
        
        let newSubtotal = val * item.harga_saat_ini;
        $(`#subtotal_display_${index}`).val(NumberToMoney(newSubtotal));
        
        update_grand_total();
    }

    function render_keranjang() {
        $('#keranjang_body tr:not(#placeholder-keranjang)').remove();
        
        if (keranjang.length > 0) {
            $('#placeholder-keranjang').hide();
            $('#footer-keranjang').show();
            let html = '';

            keranjang.forEach((item, index) => {
                let subtotal = item.jumlah_beli * item.harga_saat_ini;
                
                let optionsSatuan = '';
                item.list_satuan.forEach(sat => {
                    let selected = (sat.satuan == item.satuan_dipilih) ? 'selected' : '';
                    optionsSatuan += `<option value="${sat.satuan}" data-harga="${sat.harga}" data-konversi="${sat.konversi}" ${selected}>${sat.satuan}</option>`;
                });

                html += `
                    <tr>
                        <td class="align-middle">
                            <span>${item.nama_barang}</span>
                        </td>
                        <td class="align-middle">
                            <select class="form-select form-select-sm" onchange="ubahSatuan(${index}, this)">
                                ${optionsSatuan}
                            </select>
                        </td>
                        <td class="align-middle">
                            <input type="text" class="form-control form-control-sm text-center" 
                                value="${String(item.jumlah_beli).replace('.', ',')}" 
                                onkeyup="FormatCurrency(this); ubahQty(${index}, this)"
                                onclick="this.select()">
                        </td>
                        
                        <td class="align-middle">
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control form-control-sm text-start" 
                                       id="harga_display_${index}"
                                       value="${NumberToMoney(item.harga_saat_ini)}" readonly>
                            </div>
                        </td>
                        <td class="align-middle">
                             <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control form-control-sm text-start" 
                                       id="subtotal_display_${index}"
                                       value="${NumberToMoney(subtotal)}" readonly>
                            </div>
                        </td>

                        <td class="align-middle text-center">
                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="hapus_item(${index})"><i class="ti ti-trash"></i></button>
                        </td>
                    </tr>
                `;
            });

            $('#keranjang_body').append(html);
            $('#btn_bayar_sekarang').prop('disabled', false);
        } else {
            $('#placeholder-keranjang').show();
            $('#footer-keranjang').hide();
            $('#btn_bayar_sekarang').prop('disabled', true);
        }
        update_grand_total();
    }

    function FormatCurrency_Raw(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function hapus_item(index) {
        keranjang.splice(index, 1);
        render_keranjang();
    }

    function update_grand_total() {
        const total_transaksi = keranjang.reduce((total, item) => {
            return total + (item.jumlah_beli * item.harga_saat_ini);
        }, 0);

        $('#grand_total_display').text('Rp ' + NumberToMoney(total_transaksi));
        $('#total_tagihan_modal').text('Rp ' + NumberToMoney(total_transaksi));
        $('#nilai_transaksi').val(total_transaksi);
        
        get_bayar();
    }

    function cek_metode_bayar(value) {
        if (value === 'Transfer') {
            $('.form_nama_bank').slideDown();
        } else {
            $('.form_nama_bank').slideUp();
        }
    }

    function get_bayar() {
        let total = parseFloat($('#nilai_transaksi').val()) || 0; 
        let bayar = MoneyToNumber($('#dibayar').val());
        let kembali = bayar - total;
        $('#kembali').val(NumberToMoney(kembali < 0 ? 0 : kembali));
    }

    function cari_pelanggan() {
        const keyword = $('#cari_pelanggan').val().trim();
        if (keyword.length < 2) {
            $('#hasil_pencarian').hide();
            return;
        }

        $.ajax({
            url: '<?php echo base_url("transaksi/penjualan/get_pelanggan_ajax"); ?>',
            type: 'GET',
            data: { search: keyword },
            dataType: 'json',
            success: function(response) {
                const listPelanggan = $('#list_pelanggan');
                listPelanggan.empty();
                
                if (response && response.length > 0) {
                    response.forEach(pelanggan => {
                        listPelanggan.append(`
                            <a href="#" class="list-group-item list-group-item-action" 
                               data-id="${pelanggan.id}" 
                               data-nama="${pelanggan.text}"
                               onclick="pilih_pelanggan(this)">
                                ${pelanggan.text}
                            </a>
                        `);
                    });
                    $('#hasil_pencarian').show();
                } else {
                    listPelanggan.append(`
                        <div class="list-group-item text-muted">
                            Tidak ditemukan pelanggan dengan nama "${keyword}"
                        </div>
                    `);
                    $('#hasil_pencarian').show();
                }
            }
        });
    }

    function pilih_pelanggan(element) {
        const id = $(element).data('id');
        
        $.ajax({
            url: '<?php echo base_url("transaksi/penjualan/get_pelanggan_detail_ajax"); ?>',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(pelanggan) {
                if (pelanggan) {
                    $('#id_pelanggan').val(pelanggan.id);
                    $('#nama_customer').val(pelanggan.nama_customer);
                    $('#jenis_kelamin').val(pelanggan.jenis_kelamin || '');
                    $('#umur').val(pelanggan.umur || '');
                    $('#no_telp').val(pelanggan.no_telp || '');
                    
                    $('#nama_customer').prop('readonly', true);
                    $('#jenis_kelamin').prop('disabled', true);
                    $('#umur').prop('readonly', true);
                    $('#no_telp').prop('readonly', true);
                    
                    $('#hasil_pencarian').hide();
                    $('#cari_pelanggan').val('');
                }
            }
        });
    }

    function set_pelanggan_baru() {
        $('#id_pelanggan').val('');
        $('#nama_customer').val('').prop('readonly', false);
        $('#jenis_kelamin').val('').prop('disabled', false);
        $('#umur').val('').prop('readonly', false);
        $('#no_telp').val('').prop('readonly', false);
    }

    function proses_transaksi() {
        if (keranjang.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Keranjang tidak boleh kosong!',
                confirmButtonColor: "#35baf5",
                confirmButtonText: 'Oke'
            });
            return;
        }

        const total_tagihan = parseFloat($('#nilai_transaksi').val()) || 0;
        const jumlah_bayar = MoneyToNumber($('#dibayar').val());

        if (jumlah_bayar < total_tagihan) {
            Swal.fire({
                icon: 'warning',
                title: 'Pembayaran Kurang!',
                text: 'Jumlah uang yang dibayarkan tidak cukup.',
                confirmButtonColor: "#35baf5",
                confirmButtonText: 'Oke'
            });
            return;
        }

        const btnProses = $('button[onclick="proses_transaksi()"]');
        btnProses.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Memproses...');

        const id_pelanggan = $('#id_pelanggan').val();
        const nama_customer = $('#nama_customer').val().trim() || 'Umum';
        const jenis_kelamin = $('#jenis_kelamin').val();
        const umur = $('#umur').val();
        const no_telp = $('#no_telp').val();

        let detail_items = keranjang.map(item => ({
            id_barang: item.id_barang,
            id_barang_detail: item.id_barang_detail,
            nama_barang: item.nama_barang,
            satuan_barang: item.satuan_dipilih, 
            jumlah_beli: item.jumlah_beli,
            harga_jual: item.harga_saat_ini,
            konversi: item.nilai_konversi 
        }));

        let sendData = {
            id_pelanggan: id_pelanggan || '',
            nama_customer: nama_customer,
            jenis_kelamin: jenis_kelamin || '',
            umur: umur || '',
            no_telp: no_telp || '',
            metode_bayar: $('#metode_bayar').val(),
            nama_bank: ($('#metode_bayar').val() === 'Transfer') ? $('#nama_bank').val() : '',
            nilai_transaksi: total_tagihan,
            dibayar: jumlah_bayar,
            kembali: MoneyToNumber($('#kembali').val()),
            detail: JSON.stringify(detail_items)  
        };

        Swal.fire({
            title: 'Memproses Transaksi...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url("transaksi/penjualan/simpan_penjualan"); ?>',
            type: 'POST',
            data: sendData,  
            dataType: 'json',
            success: function(response) {
                Swal.close(); 
                
                if (response.status === 'success' || response.status === true) {
                    let id_transaksi = response.id_transaksi;
                    let url_struk = '<?= base_url("transaksi/penjualan/cetak_struk/"); ?>' + id_transaksi;
                    window.open(url_struk, '_blank');

                    $('#modalPembayaran').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Transaksi berhasil disimpan',
                        showConfirmButton: true,
                        confirmButtonColor: "#35baf5",
                        confirmButtonText: 'Oke'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    btnProses.prop('disabled', false).html('<i class="ti ti-device-floppy me-2"></i>Proses & Simpan Transaksi');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan saat menyimpan transaksi',
                        showConfirmButton: true,
                        confirmButtonColor: "#35baf5",
                        confirmButtonText: 'Oke'
                    });
                }
            },
            error: function(xhr, status, err) {
                Swal.close();
                btnProses.prop('disabled', false).html('<i class="ti ti-device-floppy me-2"></i>Proses & Simpan Transaksi');
                
                console.error('AJAX Error:', xhr.responseText || status);
                Swal.fire({
                    icon: 'error',
                    title: 'Network / Server Error',
                    text: xhr.responseText || status,
                    confirmButtonColor: "#35baf5",
                    confirmButtonText: 'Oke'
                });
            }
        });
    }

    $(document).ready(function() {
        loadFilterJenis();
        render_keranjang();

        $('#modalBarang').on('click', '.card-hover', function(e) { e.stopPropagation(); });

        $('#modalBarang .btn-close').on('click', function() {
            $('#modalBarang').modal('hide');
        });

        $('#search_barang_input').on('click focus', function() {
            $('#modalBarang').modal('show');
        });

        $('#modalBarang').on('shown.bs.modal', function() {
            $('#search_barang_modal').focus();
            loadBarangModal(1); 
        });

        $('#search_barang_modal').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadBarangModal(1), 300);
        });
        
        $('#btn_cari_pelanggan').on('click', cari_pelanggan);
        $('#cari_pelanggan').on('keyup', function(e) {
            if (e.key === 'Enter') cari_pelanggan();
        });

        $('#btn_pelanggan_baru').on('click', set_pelanggan_baru);

        $('#btn_bayar_sekarang').on('click', function() {
            if (keranjang.length > 0) {
                let summaryHtml = '<ul class="list-group list-group-flush">';
                keranjang.forEach(item => {
                    let subtotal = item.jumlah_beli * item.harga_saat_ini;
                    summaryHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                ${item.nama_barang} 
                                <br>
                                <small class="text-muted">${String(item.jumlah_beli).replace('.', ',')} ${item.satuan_dipilih}</small>
                            </div>
                            <span class="fw-bold">Rp ${NumberToMoney(subtotal)}</span>
                        </li>`;
                });
                summaryHtml += '</ul>';
                $('#ringkasan_keranjang_modal').html(summaryHtml);
                
                set_pelanggan_baru();
                $('#hasil_pencarian').hide();
                
                $('#modalPembayaran').modal('show');
            }
        });

        $('#modalPembayaran').on('shown.bs.modal', function() {
            $('#dibayar').val('');
            $('#kembali').val('0');
            $('#metode_bayar').val('Cash');
            cek_metode_bayar('Cash');
            $('#cari_pelanggan').focus();
        });

        $(window).keydown(function(e) {
            if (e.keyCode == 113) { // F2
                e.preventDefault();
                $('#modalBarang').modal('show');
            } else if (e.keyCode == 114) { // F3
                e.preventDefault();
                 if ($('#modalPembayaran').is(':visible')) {
                    $('#dibayar').focus().select();
                } else if (!$('#btn_bayar_sekarang').is(':disabled')) {
                    $('#btn_bayar_sekarang').click();
                }
            }
        });
    });
</script>
</body>