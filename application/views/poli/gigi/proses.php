<script type="text/javascript">
    let obatListRespon = [];

    $(document).ready(function() {
        $('#pagination-diagnosa, #pagination-tindakan, #pagination-obat').on('click', function(e) {
            e.stopPropagation();
        });

        $('#modalDiagnosa').on('shown.bs.modal', function() {
            $('#search_diagnosa_keyword').val('').focus();
            searchDiagnosa();
        });

        $('#modalTindakan').on('shown.bs.modal', function() {
            $('#search_tindakan_keyword').val('').focus();
            searchTindakan();
        });

        $('#search_diagnosa_keyword').keyup(function() {
            searchDiagnosa($(this).val());
        });

        $('#search_tindakan_keyword').keyup(function() {
            searchTindakan($(this).val());
        });

        $('#jumlah_tampil_diagnosa').on('change', function() {
            paging('#table-data-diagnosa', '#pagination-diagnosa', '#jumlah_tampil_diagnosa');
        });

        $('#jumlah_tampil_tindakan').on('change', function() {
            paging('#table-data-tindakan', '#pagination-tindakan', '#jumlah_tampil_tindakan');
        });

        $('#modalObat').on('shown.bs.modal', function() {
            $('#search_obat_keyword').val('').focus();
            searchObat();
        });

        $('#search_obat_keyword').keyup(function() {
            searchObat($(this).val());
        });

        $('#jumlah_tampil_obat').on('change', function() {
            paging('#table-data-obat', '#pagination-obat', '#jumlah_tampil_obat');
        });

        $('#modalObat').on('hidden.bs.modal', function() {
            $(this).removeAttr('data-racikan-target');
        });

        $(document).on('keyup change', '.input-jumlah', function() {
            updateSubtotal(this);
            hitungTotalObat();
        });

        loadDataAda();
        hitungTotalTindakan();
    });

    function paging(tableSelector, paginationSelector, jumlahSelector) {
        var jumlah_tampil = $(jumlahSelector).val();
        var $selector = $(tableSelector + " tbody tr");
        new Pagination(paginationSelector, {
            itemsCount: $selector.length,
            pageSize: parseInt(jumlah_tampil),
            onPageChange: function(paging) {
                var start = paging.pageSize * (paging.currentPage - 1),
                    end = start + paging.pageSize;
                $selector.hide();
                for (var i = start; i < end; i++) {
                    $selector.eq(i).show();
                }
            }
        });
        $(paginationSelector).find('a').off('click').on('click', function(e) {
            e.preventDefault();
        });
    }

    function formatRupiah(angka, prefix) {
        let number_string = String(angka).replace(/[^\d.]/g, ''),
            split = number_string.split('.'),
            sisa = split[0].length % 3,
            result = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? ',' : '';
            result += separator + ribuan.join(',');
        }

        result = split[1] !== undefined ? result + '.' + split[1] : result;
        return prefix === undefined ? result : (result ? prefix + result : '');
    }

    function validateForm(formSelector) {
        let isValid = true;
        $(formSelector + ' [required]').removeClass('is-invalid');
        $(formSelector + ' [required]').each(function() {
            if (!$(this).is(':disabled') && ($(this).val() === '' || $(this).val() === null)) {
                isValid = false;
                $(this).addClass('is-invalid');
            }
        });
        if (!isValid) {
            Swal.fire({
                title: 'Gagal!',
                text: 'Harap isi semua kolom yang wajib diisi.',
                icon: 'error'
            });
        }
        return isValid;
    }

    function searchDiagnosa(cari) {
        $.ajax({
            url: '<?php echo base_url("poli/gigi/search_diagnosa"); ?>',
            type: 'POST',
            data: {
                cari: cari
            },
            dataType: 'json',
            success: function(response) {
                let rows = '';
                if (response.length > 0) {
                    response.forEach((item, i) => {
                        rows += `<tr style="cursor:pointer;" onclick="addDiagnosa(${item.id_diagnosa}, '${item.nama_diagnosa.replace(/'/g, "\\'")}')"><td>${i+1}</td><td>${item.nama_diagnosa}</td></tr>`;
                    });
                } else {
                    rows = '<tr><td>Diagnosa tidak ditemukan.</td></tr>';
                }
                $('#diagnosaList').html(rows);
                paging('#table-data-diagnosa', '#pagination-diagnosa', '#jumlah_tampil_diagnosa');
            }
        });
    }

    function searchTindakan(cari) {
        $.ajax({
            url: '<?php echo base_url("poli/gigi/search_tindakan"); ?>',
            type: 'POST',
            data: {
                cari: cari
            },
            dataType: 'json',
            success: function(response) {
                let rows = '';
                if (response.length > 0) {
                    response.forEach((item, i) => {
                        let hargaDisplay = 'Rp ' + item.harga;
                        rows += `<tr style="cursor:pointer;" onclick="addTindakan(${item.id_tindakan}, '${item.nama.replace(/'/g, "\\'")}', ${item.harga_raw})">
                                    <td>${i+1}</td>
                                    <td>${item.nama}</td>
                                    <td>${hargaDisplay}</td>
                                </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="3">Tindakan tidak ditemukan.</td></tr>';
                }
                $('#tindakanList').html(rows);
                paging('#table-data-tindakan', '#pagination-tindakan', '#jumlah_tampil_tindakan');
            }
        });
    }

    function addDiagnosa(id_diagnosa, nama) {
        let isDouble = false;
        $('#table_diagnosa input[name="id_diagnosa[]"]').each(function() {
            if ($(this).val() == id_diagnosa) {
                isDouble = true;
                return false;
            }
        });

        if (isDouble) {
            Swal.fire('Gagal', 'Data diagnosa sudah dipilih.', 'warning');
            return;
        }

        let row = `<tr><td><input type="hidden" name="id_diagnosa[]" value="${id_diagnosa}"><input type="text" name="diagnosa[]" class="form-control" value="${nama}" readonly></td><td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.parentElement.remove()"><i class="far fa-trash-alt"></i></button></td></tr>`;
        $('#table_diagnosa tbody').append(row);
        $('#modalDiagnosa').modal('hide');
    }

    function addTindakan(id_tindakan, nama, harga) {
        let isDouble = false;
        $('#table_tindakan input[name="id_tindakan[]"]').each(function() {
            if ($(this).val() == id_tindakan) {
                isDouble = true;
                return false;
            }
        });

        if (isDouble) {
            Swal.fire('Gagal', 'Data tindakan sudah dipilih.', 'warning');
            return;
        }

        let hargaFormatted = formatRupiah(harga, 'Rp ');
        let row = `<tr><td><input type="hidden" name="id_tindakan[]" value="${id_tindakan}"><input type="text" name="tindakan[]" class="form-control" value="${nama}" readonly></td><td><input type="text" name="harga_tindakan[]" class="form-control input-harga-tindakan" value="${hargaFormatted}" readonly></td><td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeTindakanRow(this)"><i class="far fa-trash-alt"></i></button></td></tr>`;
        $('#table_tindakan tbody').append(row);
        $('#modalTindakan').modal('hide');
        hitungTotalTindakan();
    }

    function removeTindakanRow(btn) {
        $(btn).closest('tr').remove();
        hitungTotalTindakan();
    }

    function tambahMasterDiagnosa() {
        let nama_diagnosa = $('#input_diagnosa').val();
        if (nama_diagnosa.trim() === '') {
            Swal.fire('Gagal', 'Nama diagnosa tidak boleh kosong.', 'error');
            return;
        }
        $.ajax({
            url: '<?php echo base_url("poli/gigi/tambah_diagnosa_ajax"); ?>',
            type: 'POST',
            data: {
                nama_diagnosa: nama_diagnosa
            },
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    addDiagnosa(res.data.id, res.data.nama_diagnosa);
                    $('#input_diagnosa').val('');
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    }

    function tambahMasterTindakan() {
        let nama_tindakan = $('#input_tindakan').val();
        let harga = $('#input_harga_tindakan').val();
        if (nama_tindakan.trim() === '' || harga.trim() === '') {
            Swal.fire('Gagal', 'Nama dan harga tindakan tidak boleh kosong.', 'error');
            return;
        }
        $.ajax({
            url: '<?php echo base_url("poli/gigi/tambah_tindakan_ajax"); ?>',
            type: 'POST',
            data: {
                nama_tindakan: nama_tindakan,
                harga: harga
            },
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    addTindakan(res.data.id, res.data.nama, res.data.harga);
                    $('#input_tindakan').val('');
                    $('#input_harga_tindakan').val('');
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    }

    function searchObat(cari = '') {
        $.ajax({
            url: '<?php echo base_url("poli/gigi/search_obat"); ?>',
            type: 'POST',
            data: {
                cari: cari
            },
            dataType: 'json',
            success: function(response) {
                obatListRespon = response;
                let rows = '';
                if (response.length > 0) {
                    response.forEach((item, i) => {
                        let aksiKlik = `pilihObat(${i})`;
                        if ($('#modalObat').attr('data-racikan-target')) {
                            let target = $('#modalObat').attr('data-racikan-target');
                            aksiKlik = `pilihBahanRacikan(${i}, '${target}')`;
                        }
                        rows += `<tr style="cursor:pointer;" onclick="${aksiKlik}"><td>${i+1}</td><td>${item.nama_barang}</td></tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="2" class="text-center">Obat tidak ditemukan.</td></tr>';
                }
                $('#obatList').html(rows);
                paging('#table-data-obat', '#pagination-obat', '#jumlah_tampil_obat');
            }
        });
    }

    function pilihObat(index) {
        const item = obatListRespon[index];
        if (item) addObat(item);
    }

    function pilihBahanRacikan(index, target) {
        const item = obatListRespon[index];
        if (item) addBahanRacikan(item, target);
    }

    function addObat(item, jumlah = 1) {
        let units = [];
        if (item.units) {
            units = typeof item.units === 'string' ? JSON.parse(item.units) : item.units;
            units.sort((a, b) => a.urutan_satuan - b.urutan_satuan);
            let selectedUnit = units[0];

            item.id = selectedUnit.id;
            item.id_satuan_barang = selectedUnit.id_satuan_barang;
            item.satuan_barang = selectedUnit.satuan_barang;
            item.urutan_satuan = selectedUnit.urutan_satuan;
            item.harga_awal = selectedUnit.harga_awal;
            item.laba = selectedUnit.laba;
            item.harga_jual = selectedUnit.harga_jual;
        } else {
            units.push({
                id: item.id,
                id_satuan_barang: item.id_satuan_barang,
                satuan_barang: item.satuan_barang,
                urutan_satuan: item.urutan_satuan,
                harga_awal: item.harga_awal,
                laba: item.laba,
                harga_jual: item.harga_jual || (parseFloat(item.harga_awal) + parseFloat(item.laba))
            });
        }

        let isDouble = false;
        $('#table_resep tbody input[name$="[id_barang]"]').each(function() {
            if ($(this).val() == item.id_barang) {
                isDouble = true;
                return false;
            }
        });

        if (isDouble) {
            Swal.fire('Gagal', 'Data obat sudah dipilih.', 'warning');
            return;
        }

        let obatCount = $('#table_resep tbody tr').length;

        let selectOptions = '';
        units.forEach(u => {
            let isSelected = u.id == item.id ? 'selected' : '';
            selectOptions += `<option value="${u.id}" data-idsatuan="${u.id_satuan_barang}" data-nama="${u.satuan_barang}" data-harga="${u.harga_awal}" data-laba="${u.laba}" data-hargajual="${u.harga_jual}" data-urutan="${u.urutan_satuan}" ${isSelected}>${u.satuan_barang}</option>`;
        });

        let row = `<tr>
        <td>
            <input type="hidden" name="resep_obat[${obatCount}][id_barang_detail]" class="input-id-detail" value="${item.id}">
            <input type="hidden" name="resep_obat[${obatCount}][id_barang]" value="${item.id_barang}">
            
            <input type="hidden" name="resep_obat[${obatCount}][id_satuan_barang]" class="input-id-satuan" value="${item.id_satuan_barang || 0}">
            <input type="hidden" name="resep_obat[${obatCount}][satuan_barang]" class="input-nama-satuan" value="${item.satuan_barang}">
            
            <input type="hidden" name="resep_obat[${obatCount}][urutan_satuan]" class="input-urutan" value="${item.urutan_satuan}">
            <input type="hidden" name="resep_obat[${obatCount}][laba]" class="input-laba" value="${item.laba}">
            <input type="text" name="resep_obat[${obatCount}][nama_barang]" class="form-control" value="${item.nama_barang}" readonly>
        </td>
        <td>
            <select class="form-select status-change" onchange="ubahObat(this)">
                ${selectOptions}
            </select>
        </td>
        <td>
            <input type="text" name="resep_obat[${obatCount}][jumlah]" class="form-control input-jumlah" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="${jumlah}" min="1">
        </td>
        <td>
            <input type="text" name="resep_obat[${obatCount}][aturan_pakai]" class="form-control" placeholder="Contoh: 3x1 sehari" autocomplete="off" required>
        </td>
        <td>
            <input type="text" name="resep_obat[${obatCount}][harga]" class="form-control input-harga" value="${formatRupiah(item.harga_awal, 'Rp')}" readonly>
        </td>
        <td>
            <input type="text" class="form-control input-laba-display" value="${formatRupiah(item.laba, 'Rp')}" readonly>
        </td>
        <td>
            <input type="text" class="form-control input-subtotal" value="${formatRupiah(item.harga_jual * jumlah, 'Rp')}" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusRow(this)">
                <i class="far fa-trash-alt"></i>
            </button>
            <input type="hidden" name="resep_obat[${obatCount}][harga_jual]" class="form-control input-harga-jual" value="${formatRupiah(item.harga_jual, 'Rp')}" readonly>
        </td>
                    </tr>`;
        $('#table_resep tbody').append(row);
        $('#modalObat').modal('hide');
        hitungTotalObat();
    }

    let racikanCounter = 0;

    function addRacikan(data = null) {
        racikanCounter++;
        let nama = data ? data.nama_racikan : '';
        let jumlah = data ? data.jumlah : '';
        let aturan = data ? data.aturan_pakai : '';
        let ket = data ? data.keterangan : '';

        let racikanHtml = `<div class="racikan-card card border mb-3" id="racikan_card_${racikanCounter}">
                <div class="card-body">
                    <div class="mb-2 row">
                        <label class="col-sm-3 col-form-label">Nama Racikan</label>
                    <div class="col-sm-9">
                        <input type="text" name="racikan[${racikanCounter}][nama_racikan]" class="form-control" autocomplete="off" placeholder="Nama Racikan" value="${nama}" required>
                    </div>
                </div>
                <div class="mb-2 row">
                        <label class="col-sm-3 col-form-label">Jumlah</label>
                    <div class="col-sm-9">
                        <input type="text" name="racikan[${racikanCounter}][jumlah]" class="form-control" autocomplete="off" placeholder="Jumlah" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="${jumlah}" required>
                    </div>
                </div>
                <div class="mb-2 row">
                        <label class="col-sm-3 col-form-label">Aturan Pakai</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="racikan[${racikanCounter}][aturan_pakai]" class="form-control" autocomplete="off" placeholder="Aturan Pakai" value="${aturan} required"></textarea>
                    </div>
                </div>
                <div class="mb-2 row">
                        <label class="col-sm-3 col-form-label">Keterangan</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="racikan[${racikanCounter}][keterangan]" class="form-control" autocomplete="off" placeholder="Keterangan (jika ada)" value="${ket}"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-1 mt-3">
                        <button type="button" class="btn btn-success btn-md" onclick="openModalObat('#table_racikan_${racikanCounter} tbody')">
                            <i class="fas fa-plus"></i> Tambah Obat
                        </button>
                        <button type="button" class="btn btn-danger btn-md" onclick="this.closest('.racikan-card').remove(); hitungTotalObat();">
                            <i class="far fa-trash-alt"></i> Hapus Racikan
                        </button>
                </div>
                    <div class="table-responsive mt-2">
                    <table class="table table-md table-bordered" id="table_racikan_${racikanCounter}">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Bahan</th>
                                <th width="12%">Satuan</th>
                                <th width="15%">Qty</th>
                                <th>Harga</th>
                                <th>Laba</th>
                                <th>Subtotal</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>`;
        $('#racikan_container').append(racikanHtml);

        if (data && data.detail) {
            data.detail.forEach(bahan => {
                const item = {
                    id: bahan.id_barang_detail,
                    id_barang: bahan.id_barang,
                    id_satuan_barang: bahan.id_satuan_barang,
                    nama_barang: bahan.nama_barang,
                    satuan_barang: bahan.satuan_barang,
                    urutan_satuan: bahan.urutan_satuan,
                    harga_awal: bahan.harga,
                    laba: bahan.laba
                };
                addBahanRacikan(item, `#table_racikan_${racikanCounter} tbody`, bahan.jumlah);
            });
        }
    }

    function addBahanRacikan(item, targetTable, jumlah = 1) {
        let units = [];
        if (item.units) {
            units = typeof item.units === 'string' ? JSON.parse(item.units) : item.units;
            units.sort((a, b) => a.urutan_satuan - b.urutan_satuan);
            let selectedUnit = units[0];
            item.id = selectedUnit.id;
            item.id_satuan_barang = selectedUnit.id_satuan_barang;
            item.satuan_barang = selectedUnit.satuan_barang;
            item.urutan_satuan = selectedUnit.urutan_satuan;
            item.harga_awal = selectedUnit.harga_awal;
            item.laba = selectedUnit.laba;
            item.harga_jual = selectedUnit.harga_jual;
        } else {
            units.push({
                id: item.id,
                id_satuan_barang: item.id_satuan_barang,
                satuan_barang: item.satuan_barang,
                urutan_satuan: item.urutan_satuan,
                harga_awal: item.harga_awal,
                laba: item.laba,
                harga_jual: item.harga_jual || (parseFloat(item.harga_awal) + parseFloat(item.laba))
            });
        }

        let isDouble = false;
        $(targetTable).find('input[name$="[id_barang]"]').each(function() {
            if ($(this).val() == item.id_barang) {
                isDouble = true;
                return false;
            }
        });

        if (isDouble) {
            Swal.fire('Gagal', 'Bahan racikan ini sudah dipilih.', 'warning');
            return;
        }

        let racikanIndex = $(targetTable).closest('.racikan-card').attr('id').split('_').pop();
        let bahanCount = $(targetTable).find('tr').length;

        let selectOptions = '';
        units.forEach(u => {
            let isSelected = u.id == item.id ? 'selected' : '';
            selectOptions += `<option value="${u.id}" data-idsatuan="${u.id_satuan_barang}" data-nama="${u.satuan_barang}" data-harga="${u.harga_awal}" data-laba="${u.laba}" data-hargajual="${u.harga_jual}" data-urutan="${u.urutan_satuan}" ${isSelected}>${u.satuan_barang}</option>`;
        });

        let row = `<tr>
        <td>
            <input type="hidden" name="racikan[${racikanIndex}][bahan][${bahanCount}][id_barang_detail]" class="input-id-detail" value="${item.id}">
            <input type="hidden" name="racikan[${racikanIndex}][bahan][${bahanCount}][id_barang]" value="${item.id_barang}">
            
            <input type="hidden" name="racikan[${racikanIndex}][bahan][${bahanCount}][id_satuan_barang]" class="input-id-satuan" value="${item.id_satuan_barang || 0}">
            <input type="hidden" name="racikan[${racikanIndex}][bahan][${bahanCount}][satuan_barang]" class="input-nama-satuan" value="${item.satuan_barang}">
            
            <input type="hidden" name="racikan[${racikanIndex}][bahan][${bahanCount}][urutan_satuan]" class="input-urutan" value="${item.urutan_satuan}">
            <input type="hidden" name="racikan[${racikanIndex}][bahan][${bahanCount}][laba]" class="input-laba" value="${item.laba}">
            <input type="text" name="racikan[${racikanIndex}][bahan][${bahanCount}][nama_barang]" class="form-control" value="${item.nama_barang}" readonly>
        </td>
        <td>
             <select class="form-select status-change" onchange="ubahObat(this)">
                ${selectOptions}
            </select>
        </td>
        <td><input type="text" name="racikan[${racikanIndex}][bahan][${bahanCount}][jumlah]" class="form-control input-jumlah" value="${jumlah}" min="0.1" step="any" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
        <td><input type="text" name="racikan[${racikanIndex}][bahan][${bahanCount}][harga]" class="form-control input-harga" value="${formatRupiah(item.harga_awal, 'Rp')}" readonly></td>
        <td><input type="text" class="form-control input-laba-display" value="${formatRupiah(item.laba, 'Rp')}" readonly></td>
        <td><input type="text" class="form-control input-subtotal" value="${formatRupiah(item.harga_jual * jumlah, 'Rp')}" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="hapusRow(this)"><i class="far fa-trash-alt"></i></button><input type="hidden" name="racikan[${racikanIndex}][bahan][${bahanCount}][harga_jual]" class="form-control input-harga-jual" value="${formatRupiah(item.harga_jual, 'Rp')}" readonly></td>
                    </tr>`;
        $(targetTable).append(row);
        $('#modalObat').modal('hide');
        hitungTotalObat();
    }

    function ubahObat(select) {
        let row = $(select).closest('tr');
        let selectedOption = $(select).find(':selected');
        let idDetail = $(select).val();
        let harga = parseFloat(selectedOption.data('harga'));
        let laba = parseFloat(selectedOption.data('laba'));
        let hargaJual = parseFloat(selectedOption.data('hargajual'));
        let urutan = selectedOption.data('urutan');
        let namaSatuan = selectedOption.data('nama');
        let idSatuan = selectedOption.data('idsatuan');

        row.find('.input-id-detail').val(idDetail);
        row.find('.input-urutan').val(urutan);
        row.find('.input-nama-satuan').val(namaSatuan);
        row.find('.input-id-satuan').val(idSatuan);
        row.find('.input-laba').val(laba);
        row.find('.input-harga').val(formatRupiah(harga, 'Rp'));
        row.find('.input-laba-display').val(formatRupiah(laba, 'Rp'));
        row.find('.input-harga-jual').val(formatRupiah(hargaJual, 'Rp'));
        row.find('.input-jumlah').trigger('change');
    }

    function openModalObat(target) {
        $('#modalObat').attr('data-racikan-target', target);
        $('#modalObat').modal('show');
    }

    function hapusRow(btn) {
        $(btn).closest('tr').remove();
        hitungTotalObat();
    }

    function updateSubtotal(input) {
        let row = $(input).closest('tr');
        let hargaJualString = row.find('.input-harga-jual').val().replace(/[^\d.]/g, '');
        let harga = parseFloat(hargaJualString) || 0;
        let jumlah = parseFloat($(input).val()) || 0;
        let subtotal = harga * jumlah;
        row.find('.input-subtotal').val(formatRupiah(subtotal, 'Rp'));
    }

    function hitungTotalTindakan() {
        let totalHarga = 0;
        $('#table_tindakan .input-harga-tindakan').each(function() {
            let subtotalString = $(this).val().replace(/[^\d]/g, '');
            let subtotal = parseFloat(subtotalString) || 0;
            if (!isNaN(subtotal)) {
                totalHarga += subtotal;
            }
        });
        $('#harga_total_tindakan').text(formatRupiah(totalHarga, 'Rp'));
        hitungTotalSeluruh();
    }

    function hitungTotalObat() {
        let totalHarga = 0;
        $('.input-subtotal').each(function() {
            let subtotalString = $(this).val().replace(/[^\d.]/g, '');
            let subtotal = parseFloat(subtotalString) || 0;
            if (!isNaN(subtotal)) {
                totalHarga += subtotal;
            }
        });
        $('#harga_total_obat').text(formatRupiah(totalHarga, 'Rp'));
        hitungTotalSeluruh();
    }

    function hitungTotalSeluruh() {
        let totalTindakanString = $('#harga_total_tindakan').text().replace(/[^\d]/g, '');
        let totalObatString = $('#harga_total_obat').text().replace(/[^\d.]/g, '');
        let totalTindakan = parseFloat(totalTindakanString) || 0;
        let totalObat = parseFloat(totalObatString) || 0;
        let totalKeseluruhan = totalTindakan + totalObat;
        $('#harga_total_seluruh').text(formatRupiah(totalKeseluruhan, 'Rp'));
    }

    function loadDataAda() {
        const resepData = <?php echo json_encode($data['resep']); ?>;
        const racikanData = <?php echo json_encode($data['racikan']); ?>;
        resepData.forEach(obat => {
            addObat({
                id: obat.id_barang_detail,
                id_barang: obat.id_barang,
                id_satuan_barang: obat.id_satuan_barang,
                nama_barang: obat.nama_barang,
                satuan_barang: obat.satuan_barang,
                urutan_satuan: obat.urutan_satuan,
                harga_awal: obat.harga,
                laba: obat.laba
            }, obat.jumlah);
        });
        racikanData.forEach(racikan => {
            addRacikan(racikan);
        });
        hitungTotalObat();
    }

    function proses(e) {
        e.preventDefault();
        let btn = $(e.target).closest('button');
        if (!validateForm('#form_proses')) {
            return;
        }
        btn.prop('disabled', true).text('Memproses...');
        $.ajax({
            url: '<?php echo base_url('poli/gigi/proses_aksi') ?>',
            method: 'POST',
            data: $('#form_proses').serialize(),
            dataType: 'json',
            beforeSend: function() {
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
                if (res.status) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '<?php echo base_url() ?>antrian/antrian/index_dokter'
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        html: res.message,
                        icon: 'error'
                    });
                    btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
            }
        });
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('poli/gigi'); ?>">Poli Gigi</a>
                        </li>
                        <li class="breadcrumb-item active">Proses</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <?php echo $title; ?>
                </h4>
            </div>
        </div>
    </div>
    <form id="form_proses">
        <input type="hidden" name="id_pol_gigi" value="<?php echo $data['rekam_medis']['id_pol_gigi']; ?>">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Proses Poli Gigi</h4>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-tindakan" role="tab">Tindakan</a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-obat" role="tab">Obat</a>
                    </li>
                </ul>
            </div>
            <div class="card-body border-top">
                <div class="tab-content">
                    <div class="tab-pane active p-3" id="tab-tindakan" role="tabpanel">
                        <h5 class="mb-3">Data Pasien</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Nomor Rekam Medis</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="<?php echo $data['rekam_medis']['no_rm']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Kode Invoice</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="<?php echo $data['rekam_medis']['kode_invoice']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Nama Dokter</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="<?php echo $data['rekam_medis']['nama_dokter']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Nama Pasien</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="<?php echo $data['rekam_medis']['nama_pasien']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">NIK</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="<?php echo $data['rekam_medis']['nik']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Tanggal Lahir</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="<?php echo $data['rekam_medis']['tanggal_lahir']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Nomor Telepon</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" value="<?php echo $data['rekam_medis']['no_telp']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Alamat</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" readonly><?php echo $data['rekam_medis']['alamat']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-4">
                        <h5 class="my-3">Hasil Pemeriksaan</h5>
                        <div class="mb-3 row">
                            <label class="col-sm-2 col-form-label">Keluhan</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="keluhan" required><?php echo $data['rekam_medis']['keluhan']; ?></textarea>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-2 col-form-label">Catatan</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="catatan" required><?php echo $data['rekam_medis']['catatan']; ?></textarea>
                            </div>
                        </div>
                        <hr class="mt-4">
                        <h5 class="my-3">Diagnosa</h5>
                        <div class="input-group mb-3">
                            <input type="text" id="input_diagnosa" class="form-control" autocomplete="off" placeholder="Ketik diagnosa baru...">
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalDiagnosa" title="Cari Diagnosa">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-success" type="button" onclick="tambahMasterDiagnosa()" title="Tambah Diagnosa">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <table class="table table-md table-bordered" id="table_diagnosa">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Diagnosa</th>
                                    <th class="text-center" width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody><?php foreach ($data['diagnosa'] as $diag) { ?><tr>
                                        <td>
                                            <input type="hidden" name="id_diagnosa[]" value="<?php echo $diag['id_diagnosa']; ?>">
                                            <input type="text" name="diagnosa[]" class="form-control" value="<?php echo $diag['diagnosa']; ?>" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.parentElement.remove()">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr><?php } ?>
                            </tbody>
                        </table>
                        <hr class="mt-4">
                        <h5 class="my-3">Tindakan</h5>
                        <div class="input-group mb-3">
                            <input type="text" id="input_tindakan" class="form-control" autocomplete="off" placeholder="Ketik tindakan baru...">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="input_harga_tindakan" class="form-control" onkeyup="this.value = formatRupiah(this.value)" autocomplete="off" placeholder="Harga...">
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalTindakan" title="Cari Tindakan">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-success" type="button" onclick="tambahMasterTindakan()" title="Tambah Tindakan">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <table class="table table-md table-bordered" id="table_tindakan">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Tindakan</th>
                                    <th>Harga</th>
                                    <th class="text-center" width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody><?php foreach ($data['tindakan'] as $tind) { ?><tr>
                                        <td>
                                            <input type="hidden" name="id_tindakan[]" value="<?php echo $tind['id_tindakan']; ?>">
                                            <input type="text" name="tindakan[]" class="form-control" value="<?php echo $tind['tindakan']; ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="harga_tindakan[]" class="form-control input-harga-tindakan" value="<?php echo 'Rp' . number_format($tind['harga'], 0, '.', ','); ?>" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeTindakanRow(this)">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr><?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane p-3" id="tab-obat" role="tabpanel">
                        <h5 class="my-3">Resep Obat</h5>
                        <button class="btn btn-primary mb-3" type="button" data-bs-toggle="modal" data-bs-target="#modalObat">
                            <i class="fas fa-search me-2"></i>Cari Obat
                        </button>
                        <div class="table-responsive">
                            <table class="table table-md table-bordered" id="table_resep">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nama Obat</th>
                                        <th width="12%">Satuan</th>
                                        <th width="10%">Qty</th>
                                        <th>Aturan Pakai</th>
                                        <th>Harga</th>
                                        <th>Laba</th>
                                        <th>Subtotal</th>
                                        <th class="text-center" width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <hr class="mt-4">
                        <h5 class="my-3">Racikan Obat</h5>
                        <button class="btn btn-success mb-3" type="button" onclick="addRacikan()">
                            <i class="fas fa-plus me-2"></i>Tambah Racikan
                        </button>
                        <div id="racikan_container"></div>
                        <hr class="mt-4">
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <h5 class="text-end">Total Biaya Tindakan:
                            <span id="harga_total_tindakan" class="text-success">Rp 0</span>
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <h5 class="text-end">Total Biaya Obat:
                            <span id="harga_total_obat" class="text-success">Rp 0</span>
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <h5 class="text-end">Total Biaya Keseluruhan:
                            <span id="harga_total_seluruh" class="text-success">Rp 0</span>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" onclick="proses(event);" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="modalDiagnosa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Diagnosa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="search_diagnosa_keyword" class="form-control" placeholder="Ketik untuk mencari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="table-data-diagnosa">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Diagnosa</th>
                            </tr>
                        </thead>
                        <tbody id="diagnosaList"></tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div id="pagination-diagnosa"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-7">&nbsp;</div>
                            <label class="col-md-2 control-label d-flex align-items-center justify-content-end">Tampil</label>
                            <div class="col-md-3 pull-right">
                                <select class="form-control" id="jumlah_tampil_diagnosa">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="far fa-window-close"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalTindakan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="search_tindakan_keyword" class="form-control" placeholder="Ketik untuk mencari...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="table-data-tindakan">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Tindakan</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="tindakanList"></tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div id="pagination-tindakan"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-7">&nbsp;</div>
                            <label class="col-md-2 control-label d-flex align-items-center justify-content-end">Tampil</label>
                            <div class="col-md-3 pull-right">
                                <select class="form-control" id="jumlah_tampil_tindakan">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="far fa-window-close"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalObat" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Obat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="search_obat_keyword" class="form-control" placeholder="Ketik untuk mencari nama obat atau satuan obat...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="table-data-obat">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                            </tr>
                        </thead>
                        <tbody id="obatList"></tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div id="pagination-obat"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-7">&nbsp;</div>
                            <label class="col-md-2 control-label d-flex align-items-center justify-content-end">Tampil</label>
                            <div class="col-md-3 pull-right">
                                <select class="form-control" id="jumlah_tampil_obat">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="far fa-window-close"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>