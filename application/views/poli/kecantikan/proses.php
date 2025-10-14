<script>
    $(document).ready(function () {
        formatAllCurrencyInputs();
        // Event untuk input yang ditambahkan nanti
        $(document).on('keyup', '.input_htindakan', function () {
            FormatCurrency1(this);
        });
        $('#btn-tindakan').on('click', function () {
            $('#tab-tindakan').show();
            $('#tab-obat').hide();
        });
        $('#btn-obat').on('click', function () {
            $('#tab-obat').show();
            $('#tab-tindakan').hide();
        });
        cariBarang();
        // satuan();
        $('#pagination1, #pagination2, #pagination').on('click', function (e) {
            e.stopPropagation();
        });
        $("#jumlah_tampil").change(function () {
            cariTindakan();
        });
        $("#jumlah_tampil1").change(function () {
            cariDiagnosa();
        })
        $("#jumlah_tampil2").change(function () {
            cariBarang();
        });
    })
    function formatDataBeforeSubmit() {
        // Format data obat
        $('[name^="obat["]').each(function () {
            if ($(this).attr('type') !== 'hidden' &&
                ($(this).hasClass('harga') ||
                    $(this).hasClass('laba') ||
                    $(this).hasClass('subtotal') ||
                    $(this).hasClass('sub-total-laba'))) {
                let val = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(val);
            }
        });

        // Format data racikan
        $('[name^="racikan["]').each(function () {
            if ($(this).attr('type') !== 'hidden' &&
                ($(this).hasClass('harga-racikan') ||
                    $(this).hasClass('laba-racikan') ||
                    $(this).hasClass('subtotal-racikan') ||
                    $(this).hasClass('subtotallaba-racikan'))) {
                let val = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(val);
            }
        });
    }

    // Panggil fungsi ini sebelum submit
    $('#form_tambah').on('submit', function () {
        formatDataBeforeSubmit();
    });
    // funt tambah data 
    function tambah() {
        const keluhan = $('#keluhan').val();
        const jenis_treatment = $('#jenis_treatment').val();
        const produk = $('#produk_digunakan').val();
        const hasil = $('#hasil_perawatan').val();
        const status = $('#status_foto').val();
        if (keluhan == '' || jenis_treatment == '' || produk == '' || hasil == '' || status == '') {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Inputan Kosong",
            });
            return;
        }
        const formData = new FormData($('#form_tambah')[0]);
        $.ajax({
            url: '<?php echo base_url("poli/kecantikan/tambah_proses") ?>',
            method: 'POST',
            // data : $('#form_tambah').serialize(),
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res, status) {
                console.log(res, status);
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
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '<?php echo base_url() ?>antrian/antrian/index_dokter'
                        }
                    })
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
                        if (result.isConfirmed) {
                            //   location.reload()
                            console.log('Gagal mengirim datanya');
                        }
                    })
                }
            },
        });
    }
    function cariTindakan(keyword) {
        // console.log(keyword);
        $.ajax({
            url: '<?= base_url("poli/kecantikan/tindakan") ?>',
            data: {
                carit: keyword,
            },
            type: 'POST',
            dataType: 'JSON',
            success: function (res) {
                let table = "";
                if (res.status && res.data.length > 0) {
                    let i = 1;
                    for (const item of res.data) {
                        table += `
            <tr style="cursor:pointer;" onclick="pilihTindakan('${btoa(JSON.stringify(item))}')">
              <td>${i++}</td>
              <td>${item.nama}</td>
              <td>${formatRupiah(item.harga)}</td>
            </tr>
          `;
                    }
                } else {
                    table = `<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>`;
                }
                $('#table-data tbody').html(table);
                paging();
            },
            error: function (xhr) {
                console.error('Gagal:', xhr.responseText);
            }
        });
    }
    function cariBarang(nama_barang) {
        $.ajax({
            url: '<?= base_url("poli/kecantikan/obat") ?>',
            data: {
                carit: nama_barang,
            },
            type: 'POST',
            dataType: 'JSON',
            success: function (res) {
                let table = "";
                if (res.status && res.data.length > 0) {
                    let i = 1;
                    for (const item of res.data) {
                        table += `
            <tr style="cursor:pointer;" onclick="pilihBarang('${btoa(JSON.stringify(item))}')">
              <td>${i++}</td>
              <td>${item.nama_barang}</td>
              <td>${item.satuan_barang}</td>
              <td>${formatRupiah(item.harga_awal)}</td>
            </tr>
          `;
                    }
                } else {
                    table = `<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>`;
                }
                $('#table-data2 tbody').html(table);
                paging2();
            },
            error: function (xhr) {
                console.error('Gagal:', xhr.responseText);
            }
        });
    }


    function pilihBarang(itemBase64) {
        const item = JSON.parse(atob(itemBase64));
        const racikanId = $('#modalObat').data('target-racikan');
        if (racikanId) {
            // Panggil fungsi yang sudah di-rename
            addRowToRacikan(item, racikanId);
            // Hapus data setelah digunakan
            $('#modalObat').removeData('target-racikan');
        } else {
            tambah_obat_rsp(item);
        }

        $('#modalObat').modal('hide');
    }
    function cariDiagnosa(keyword) {
        $.ajax({
            url: '<?= base_url("poli/kecantikan/diagnosa") ?>',
            data: { caridiagnosa: keyword },
            type: 'POST',
            dataType: 'JSON',
            success: function (res) {
                let table = "";
                if (res.status && res.data.length > 0) {
                    let i = 1;
                    res.data.forEach(item => {
                        table += `
            <tr style="cursor:pointer;" onclick="pilihDiagnosa('${btoa(JSON.stringify(item))}')">
              <td>${i++}</td>
              <td>${item.nama_diagnosa}</td>
            </tr>
          `;
                    });
                } else {
                    table = `<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>`;
                }
                $('#table-data1 tbody').html(table);
                paging1();
            },
            error: function (xhr) {
                console.error('Gagal:', xhr.responseText);
            }
        });
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
    function paging1($selector) {
        var jumlah_tampil1 = $('#jumlah_tampil1').val();

        if (typeof $selector == 'undefined') {
            $selector = $("#table-data1 tbody tr");
        }

        window.tp = new Pagination('#pagination1', {
            itemsCount: $selector.length,
            pageSize: parseInt(jumlah_tampil1),
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
    function paging2($selector) {
        var jumlah_tampil2 = $('#jumlah_tampil2').val();
        if (typeof $selector == 'undefined') {
            $selector = $("#table-data2 tbody tr");
        }

        window.tp = new Pagination('#pagination2', {
            itemsCount: $selector.length,
            pageSize: parseInt(jumlah_tampil2),
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
    function formatAllCurrencyInputs() {
        $('.input_htindakan').each(function () {
            if (this.value) {
                FormatCurrency1(this);
            }
        });
    }
    function FormatCurrency1(inputElement) {
        // Simpan posisi kursor
        const cursorPosition = inputElement.selectionStart;
        const originalLength = inputElement.value.length;

        // Ambil nilai dan bersihkan dari format sebelumnya
        let angka = inputElement.value;
        angka = angka.replace(/[^\d]/g, ''); // Hapus semua non-digit

        // Konversi ke number
        const num = Number(angka);

        // Handle NaN
        if (isNaN(num)) {
            inputElement.value = '0';
            return;
        }

        // Format dengan separator ribuan
        const formatted = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        inputElement.value = formatted;

        // Kembalikan posisi kursor setelah formatting
        const newLength = inputElement.value.length;
        const lengthDiff = newLength - originalLength;
        inputElement.setSelectionRange(cursorPosition + lengthDiff, cursorPosition + lengthDiff);
    }

    function pilihTindakan(itemBase64) {
        const item = JSON.parse(atob(itemBase64));
        let row = `
        <tr>
            <td>
                <input type="hidden" name="id_tindakan[]" class="form-control" value="${item.id_tindakan}" readonly>
                <input type="text" name="tindakan[]" class="form-control" value="${item.nama}" readonly>
            </td>
            <td>
                <input type="text" name="harga_tindakan[]"  class="form-control input_htindakan"  value="${item.harga}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">X</button>
            </td>
        </tr>
    `;
        $('#table_tindakan tbody').append(row);
        const newInput = $('#table_tindakan tbody tr:last .input_htindakan')[0];
        FormatCurrency1(newInput);
        hitungTotalTindakan();
    }
    function tambahT(nama_tindakan, harga_tindakan) {
        let row = `
        <tr>
            <td>
                <input type="text" name="tindakanb[]" class="form-control" autocomplete="off" placeholder="Tindakan" value="${nama_tindakan}" readonly>
            </td>
            <td>
            
                <input type="text" name="harga_tindakanb[]" class="form-control" onkeyup="FormatCurrency(this);" autocomplete="off" placeholder="Harga" value="${harga_tindakan}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">X</button>
            </td>
        </tr>
    `;

        $('#table_tindakan tbody').append(row);
        hitungTotalTindakan();
    }
    // hitung tindakan
    function hitungTotalTindakan() {
        let total = 0;
        // Hitung dari input tindakan yang sudah ada (dipilih dari daftar)
        $('input[name="harga_tindakan[]"]').each(function () {
            const harga = parseFloat($(this).val().replace(/[^0-9]/g, '')) || 0;
            total += harga;
        });
        // Hitung dari input tindakan manual
        $('input[name="harga_tindakanb[]"]').each(function () {
            const harga = parseFloat($(this).val().replace(/[^0-9]/g, '')) || 0;
            total += harga;
        });
        // Simpan nilai asli (tanpa format) di input hidden jika diperlukan
        $('#total_tindakan_hidden').val(total);
        hitungTotal();
    }
    function pilihDiagnosa(itemBase64) {
        const item = JSON.parse(atob(itemBase64));
        let row = `
        <tr>
            <td>
                <input type="hidden" name="id_diagnosa[]" class="form-control" value="${item.id_diagnosa}" readonly>
                <input type="text" name="diagnosa[]" class="form-control" value="${item.nama_diagnosa}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">X</button>
            </td>
        </tr>
    `;

        $('#table_diagnosa tbody').append(row);
    }
    function tambahformD(nama_diagnosa) {
        //error
        let row = `
        <tr>
            <td>
                <input type="text" name="diagnosab[]" class="form-control" value="${nama_diagnosa}" autocomplete="off" placeholder="diagnosa" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">X</button>
            </td>
        </tr>
    `;

        $('#table_diagnosa tbody').append(row);
    }


    // Fungsi untuk menambah baris obat racikan
    function tambah_obat_rsp(item) {
        event.preventDefault();
        var jml_tr = $('#number_obat_multiple').val();
        var i = parseInt(jml_tr) + 1;
        // Hitung nomor urut
        const rowCount = $('#table_resep tbody tr').length;
        const newRowId = rowCount + 1;
        const hargaJual = parseFloat(item.harga_awal) + parseFloat(item.laba);
        // Buat baris baru
        const newRow = `
<tr id="resep-row-${newRowId}">
    <td>
        <input type="hidden" name="obat[${newRowId}][id_obat_detail_o]" value="${item.id}">
        <input type="hidden" name="obat[${newRowId}][id_obat_o]" value="${item.id_barang}">
        <input type="hidden" class="form-control harga_jual" name="obat[${newRowId}][harga_jual_o]" value="${formatRupiah(item.harga_jual)}" readonly>
        <input type="hidden" class="form-control sub-total-laba" name="obat[${newRowId}][subtotal_laba_o]" value="${formatRupiah(item.laba)}" readonly>
        <input type="text" class="form-control" name="obat[${newRowId}][nama_obat_o]" value="${item.nama_barang}" readonly>
    </td>
    <td>
        <input type="hidden" class="form-control" name="obat[${newRowId}][id_satuan_o]" value="${item.id_satuan_barang}">
        <input type="text" class="form-control" name="obat[${newRowId}][satuan_o]" value="${item.satuan_barang}" readonly>
        <input type="hidden" class="form-control" name="obat[${newRowId}][urutan_satuan_o]" value="${item.urutan_satuan}" readonly>
    </td>
    <td>
        <input type="number" class="form-control jumlah" name="obat[${newRowId}][jumlah_o]" min="1" value="1" 
               onchange="hitungSubtotal(${newRowId})">
    </td>
    <td>
        <input type="text" class="form-control" name="obat[${newRowId}][aturan_pakai_o]" placeholder="Aturan pakai" autocomplete="off">
    </td>
    <td>
        <input type="text" class="form-control harga" name="obat[${newRowId}][harga_o]" 
               value="${formatRupiah(item.harga_awal)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control laba" name="obat[${newRowId}][laba_o]" 
               value="${formatRupiah(item.laba)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control subtotall" name="obat[${newRowId}][subtotal_ol]" 
               value="${formatRupiah(item.harga_jual)}" readonly>
        <input type="hidden" class="form-control subtotal" name="obat[${newRowId}][subtotal_o]" 
               value="${formatRupiah(item.harga_awal)}" readonly>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger" 
                onclick="$('#resep-row-${newRowId}').remove(); hitungTotal()">
            <i class="far fa-trash-alt"></i>
        </button>
    </td>
    <input type="hidden" class="form-control sub-total-laba-harga" name="obat[${newRowId}][subtotal_laba_harga_o]" 
           value="${formatRupiah(item.harga_jual)}">
</tr>
`;
        // Tambahkan ke tabel
        $('#table_resep tbody').append(newRow);

        // Hitung total
        hitungTotal();
    }

    function hitungSubtotal(rowId) {
        const row = $(`#resep-row-${rowId}`);
        const jumlah = parseInt(row.find('.jumlah').val()) || 0;
        const harga = parseFloat(row.find('.harga').val().replace(/[^0-9]/g, '')) || 0;
        const harga_jual = parseFloat(row.find('.harga_jual').val().replace(/[^0-9]/g, '')) || 0;
        const laba = parseFloat(row.find('.laba').val().replace(/[^0-9]/g, '')) || 0;
        const subtotall = jumlah * harga_jual;
        const subtotal = jumlah * harga;
        // const subtotal = (harga + laba) * jumlah;
        const subTotalLaba = jumlah * laba;
        const subTotalLabaHarga = subtotal + subTotalLaba;

        row.find('.subtotall').val(formatRupiah(subtotall));
        row.find('.subtotal').val(formatRupiah(subtotal));
        row.find('.sub-total-laba').val(formatRupiah(subTotalLaba));
        row.find('.sub-total-laba-harga').val(formatRupiah(subTotalLabaHarga));
        hitungTotal();
    }

    // funct hitung semua total
    function hitungTotal() {
        let totalObat = 0;
        let totalObatl = 0;
        let totalRacikan = 0;
        let totalRacikanl = 0;
        let totaltindakan = parseFloat($('#total_tindakan_hidden').val()) || 0;

        // Hitung total dari obat biasa
        $('[id^="resep-row-"]').each(function () {
            const subtotal = $(this).find('.subtotal').val().replace(/[^0-9]/g, '') || 0;
            const subtotalLaba = $(this).find('.sub-total-laba').val().replace(/[^0-9]/g, '') || 0;
            // totalObat += (parseFloat(subtotal) + parseFloat(subtotalLaba));
            totalObat += (parseFloat(subtotal));
            totalObatl += (parseFloat(subtotal) + parseFloat(subtotalLaba));
        });

        // Hitung total dari racikan
        $('[id^="racikan-obat-"]').each(function () {
            $(this).find('tr').each(function () {
                const subtotal = $(this).find('.subtotal-racikan').val();
                const subtotalLaba = $(this).find('.subtotallaba-racikan').val();

                if (subtotal && subtotalLaba) {
                    const subtotalNum = parseFloat(subtotal.replace(/[^0-9]/g, '')) || 0;
                    const subtotalLabaNum = parseFloat(subtotalLaba.replace(/[^0-9]/g, '')) || 0;
                    totalRacikan += subtotalNum;
                    totalRacikanl += subtotalNum + subtotalLabaNum;
                }
            });
        });
        const grandTotal = totalObat + totalRacikan;
        const grandTotall = totalObatl + totalRacikanl;
        const tobatt = totaltindakan + grandTotall;
        $('#harga_tindakan_all').text(formatRupiah(totaltindakan));
        // $('#total_obat').text(formatRupiah(totalObat));
        // $('#utotal_obat').val(formatRupiah(totalObat));
        $('#harga_total').text(formatRupiah(grandTotall));
        $('#all_uang').val(grandTotall);
        $('#tobat1').text(formatRupiah(tobatt));
        $('#tobat').val(tobatt);
    }
    // Fungsi untuk menghapus baris obat
    function hapus_obat_multiple(rowId) {
        $(`#row_obat_${rowId}`).remove();
        hitungTotalObat();
    }

    // Fungsi untuk menghitung subtotal per baris
    function hitungSubtotalObat(rowId) {
        const row = $(`#row_obat_${rowId}`);
        const jumlah = parseFloat(row.find('.jumlah').val()) || 0;
        const harga = parseFloat(row.find('.harga').val().replace(/[^0-9]/g, '')) || 0;
        const laba = parseFloat(row.find('.laba').val().replace(/[^0-9]/g, '')) || 0;

        const subTotal = jumlah * harga;
        const subTotalLaba = jumlah * laba;

        row.find('.sub-total').val(formatRupiah(subTotal));
        row.find('.sub-total-laba').val(formatRupiah(subTotalLaba));

        hitungTotalObat();
    }

    // Fungsi untuk menghitung total keseluruhan
    function hitungTotalObat() {
        let totalHarga = 0;
        let totalLaba = 0;

        $('.sub-total').each(function () {
            totalHarga += parseFloat($(this).val().replace(/[^0-9]/g, '')) || 0;
        });

        $('.sub-total-laba').each(function () {
            totalLaba += parseFloat($(this).val().replace(/[^0-9]/g, '')) || 0;
        });

        // Update footer tabel

        $('#table_resep tfoot #total_harga').text(formatRupiah(totalHarga));
        $('#table_resep tfoot #total_laba').text(formatRupiah(totalLaba));
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


    // Fungsi untuk format input currency
    function formatCurrency(input) {
        let value = input.value.replace(/[^0-9]/g, '');

        if (value.length > 0) {
            value = parseInt(value, 10).toString();
            input.value = formatRupiah(value);
        } else {
            input.value = '';
        }
    }
    let racikanCounter = 0;
    function tambahRacikan() {
        racikanCounter++;
        const racikanId = 'racikan-' + racikanCounter;
        let html = '';
        html += `
         <div class="racikan-card card border mb-3" id="${racikanId}">
        <div class="card-body">
            <div class="mb-2 row">
                <label class="col-sm-3 col-form-label">Nama Racikan</label>
                <div class="col-sm-9">
                    <input type="text" name="racikan[${racikanCounter}][nama_r]" 
                           class="form-control" placeholder="Nama Racikan" required>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-3 col-form-label">Jumlah</label>
                <div class="col-sm-9">
                    <input type="number" name="racikan[${racikanCounter}][jumlah_r]" 
                           class="form-control" placeholder="Jumlah" min="1" required>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-3 col-form-label">Aturan Pakai</label>
                <div class="col-sm-9">
                    <input type="text" name="racikan[${racikanCounter}][aturan_r]" 
                           class="form-control" placeholder="Aturan Pakai">
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-3 col-form-label">Keterangan</label>
                <div class="col-sm-9">
                    <input type="text" name="racikan[${racikanCounter}][keterangan_r]" 
                           class="form-control" placeholder="Keterangan">
                </div>
            </div>
            <div class="d-flex justify-content-end gap-1 mt-3">
                <button type="button" class="btn btn-success btn-md" 
                        onclick="tambahObatracikan(${racikanCounter})">
                    <i class="fas fa-plus"></i> Tambah Obat
                </button>
                <button type="button" class="btn btn-danger btn-md" 
                        onclick="$('#${racikanId}').remove(); hitungTotal()">
                    <i class="far fa-trash-alt"></i> Hapus Racikan
                </button>
            </div>
            <div class="table-responsive mt-2">
                <table class="table table-md table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Laba</th>
                            <th>Subtotal Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="racikan-obat-${racikanCounter}"></tbody>
                </table>
            </div>
        </div>
    </div>
        `;
        $('#racikan_container').append(html);
    }


    // Nama baru untuk fungsi yang menambahkan baris ke tabel racikan
    function addRowToRacikan(item, racikanId) {
        const rowCount = $(`#racikan-obat-${racikanId} tr`).length;
        const newRowId = rowCount + 1;

        // Pastikan semua field yang diperlukan ada di item
        if (!item.id_barang || !item.id_satuan_barang || !item.urutan_satuan) {
            console.error("Data item tidak lengkap:", item);
            alert("Data obat tidak lengkap, silakan pilih obat lagi");
            return;
        }

        let newRow = `
    <tr id="racikan-obat-row-${racikanId}-${newRowId}">
        <td>
            <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id]" value="${item.id}">
            <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id_barang_br]" value="${item.id_barang}">
            <input type="hidden" class="form-control subtotallaba-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_laba_br]" value="${item.laba}" readonly>
            <input type="hidden" class="form-control harga-jracikan" name="racikan[${racikanId}][obat][${newRowId}][harga_jbr]" value="${item.harga_jual}" readonly>
            <input type="text" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][nama_br]" value="${item.nama_barang}" readonly>
        </td>
        <td>
            <input type="hidden" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][id_satuan_br]" value="${item.id_satuan_barang}">
            <input type="text" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][satuan_br]" value="${item.satuan_barang}" readonly>
            <input type="hidden" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][urutan_satuan_br]" value="${item.urutan_satuan}">
        </td>
        <td>
            <input type="number" class="form-control jumlah-racikan" name="racikan[${racikanId}][obat][${newRowId}][jumlah_br]" min="1" value="1" onchange="hitungSubtotalRacikan(${racikanId}, ${newRowId})">
        </td>
        <td>
            <input type="text" class="form-control harga-racikan" name="racikan[${racikanId}][obat][${newRowId}][harga_br]" value="${formatRupiah(item.harga_awal)}" readonly>
        </td>
        <td>
            <input type="text" class="form-control laba-racikan" name="racikan[${racikanId}][obat][${newRowId}][laba_br]" value="${formatRupiah(item.laba)}" readonly>
        </td>
        <td>
            <input type="text" class="form-control subtotal-racikanl" name="racikan[${racikanId}][obat][${newRowId}][subtotal_brl]" value="${formatRupiah(item.harga_jual)}" readonly>
            <input type="hidden" class="form-control subtotal-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_br]" value="${formatRupiah(item.harga_awal)}" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove(); hitungTotalRacikan(${racikanId})">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    </tr>`;

        $(`#racikan-obat-${racikanId}`).append(newRow);
        hitungTotalRacikan(racikanId);
    }

    function tambahObatracikan(racikanId) {
        // Simpan ID racikan sebagai data pada modal
        $('#modalObat').data('target-racikan', racikanId).modal('show');
    }
    function hitungSubtotalRacikan(racikanId, rowId) {
        const row = $(`#racikan-obat-row-${racikanId}-${rowId}`);
        const jumlah = parseFloat(row.find('.jumlah-racikan').val()) || 0;
        console.log(jumlah);

        const harga = parseFloat(row.find('.harga-racikan').val().replace(/[^0-9]/g, '')) || 0;
        const jharga = parseFloat(row.find('.harga-jracikan').val().replace(/[^0-9]/g, '')) || 0;
        const laba = parseFloat(row.find('.laba-racikan').val().replace(/[^0-9]/g, '')) || 0;
        const subtotal = jumlah * harga;
        const subtotall = jumlah * jharga;
        const subtotalaba = jumlah * laba;
        const subtotalaba_harga = subtotal + subtotalaba;

        row.find('.subtotal-racikan').val(formatRupiah(subtotal));
        row.find('.subtotal-racikanl').val(formatRupiah(subtotall));
        row.find('.subtotallaba-racikan').val(formatRupiah(subtotalaba));
        row.find('.subtotalabaharga-racikan').val(formatRupiah(subtotalaba_harga));
        hitungTotalRacikan(racikanId);
    }

    function hitungTotalRacikan(racikanId) {
        let subtotalR = 0;

        $(`#racikan-obat-${racikanId} .subtotal-racikan`).each(function () {
            subtotalR += parseFloat($(this).val().replace(/[^0-9]/g, '')) || 0;
        });

        // Juga update grand total
        hitungTotal();
    }
</script>
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>poli/kecantikan">
                                <?php echo $title; ?>
                            </a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
                <h4 class="page-title">Proses
                    <?php echo $title; ?>
                </h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pt-3 pb-3">
                    <h4 class="card-title">Proses
                        <?php echo $title; ?>
                    </h4>
                </div><!--end card-header-->
                <ul class="nav nav-pills nav-justified " role="tablist">
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-tindakan" role="tab">Tindakan</a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-obat" role="tab">Obat</a>
                    </li>
                </ul>
                <div class="card-body">
                    <form id="form_tambah" enctype="multipart/form-data">
                        <div class="general-label">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-tindakan" role="tabpanel">
                                    <!-- inputan start -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">No RM</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="rm" id="rm"
                                                        placeholder="No rm" value="<?php echo $row['no_rm'] ?>" readonly
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">Kode
                                                    Invoice</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="kode_invoice"
                                                        id="kode_invoice" placeholder="Kode invoice"
                                                        value="<?php echo $row['kode_invoice'] ?>" readonly
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">Nama
                                                    Dokter</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nama_dokter"
                                                        id="nama_dokter" placeholder="Nama dokter"
                                                        value="<?php echo $row['nama_dokter'] ?>" readonly
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">Nama
                                                    Pasien</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" class="form-control" name="id" id="id"
                                                        placeholder="Id" value="<?php echo $row['id'] ?>" readonly
                                                        autocomplete="off">
                                                    <input type="hidden" class="form-control" name="id_poli"
                                                        id="id_poli" placeholder="Id_poli"
                                                        value="<?php echo $row['id_poli'] ?>" readonly
                                                        autocomplete="off">
                                                    <input type="hidden" class="form-control" name="nama_poli"
                                                        id="nama_poli" placeholder="nama_poli"
                                                        value="<?php echo $row['nama_poli'] ?>" readonly
                                                        autocomplete="off">
                                                    <input type="hidden" class="form-control" name="id_dokter"
                                                        id="id_dokter" placeholder="Id dokter"
                                                        value="<?php echo $row['id_dokter'] ?>" readonly
                                                        autocomplete="off">
                                                    <input type="hidden" class="form-control" name="nama_dokter"
                                                        id="nama_dokter" placeholder="nama dokter"
                                                        value="<?php echo $row['nama_dokter'] ?>" readonly
                                                        autocomplete="off">
                                                    <input type="hidden" class="form-control" name="id_pasien"
                                                        id="id_pasien" placeholder="Id pasien"
                                                        value="<?php echo $row['id_pasien'] ?>" readonly
                                                        autocomplete="off">
                                                    <input type="text" class="form-control" name="nama_pasien"
                                                        id="nama_pasien" placeholder="Nama pasien"
                                                        value="<?php echo $row['nama_pasien'] ?>" readonly
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- untuk tab nya ini  -->
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">NIK</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nik" id="nik"
                                                        placeholder="Nik" value="<?php echo $row['nik'] ?>" readonly
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">Tanggal
                                                    Lahir</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="tgl_lahir"
                                                        id="tgl_lahir" placeholder="Tanggal lahir"
                                                        value="<?php echo $row['tanggal_lahir'] ?>" readonly
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">Nomor
                                                    Telpon</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="no_telp" id="no_telp"
                                                        placeholder="Nomor telpon" value="<?php echo $row['no_telp'] ?>"
                                                        readonly autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh"
                                                    class="col-sm-4 col-form-label">Alamat</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="alamat" id="alamat"
                                                        placeholder="Alamat" value="<?php echo $row['alamat'] ?>"
                                                        readonly autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div>
                                        <!-- detail start -->
                                        <h5>Detail</h5>
                                        <div class="mb-3 row">
                                            <label for="tambah_contoh" class="col-sm-2 col-form-label">Keluhan</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="keluhan"
                                                    id="keluhan"><?php echo $row['keluhan'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="tambah_contoh" class="col-sm-2 col-form-label">Jenis
                                                treatment</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="jenis_treatment"
                                                    id="jenis_treatment" placeholder="Jenis treatment"
                                                    value="<?php echo $row['jenis_treatment'] ?>" required
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Riwayat
                                            Alergi</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="r_alergi" id="r_alergi"
                                                placeholder="Riwayat alergi"
                                                value="<?php echo $row['riwayat_alergi'] ?>" required
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Produk
                                            Digunakan</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="produk_digunakan"
                                                id="produk_digunakan" placeholder="Produk digunakan"
                                                value="<?php echo $row['produk_digunakan'] ?>" required
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Hasil
                                            Perawatan</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="hasil_perawatan"
                                                id="hasil_perawatan" placeholder="Hasil perawatan"
                                                value="<?php echo $row['hasil_perawatan'] ?>" required
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Status Foto</label>
                                        <div class="col-sm-10">
                                            <select name="status_foto" id="status_foto" class="form-select">
                                                <option value="">Pilih Status Foto</option>
                                                <option value="Sebelum">Sebelum</option>
                                                <option value="Sesudah">Sesudah</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Upload Foto</label>
                                        <div class="col-sm-10">
                                            <input type="file" accept="image/*" class="form-control" name="upload_foto"
                                                id="upload_foto" placeholder="Upload foto" autocomplete="off">
                                        </div>
                                    </div>
                                    <!-- detail end -->
                                    <hr>
                                    <!-- diagnosa start -->
                                    <h5>Diagnosa</h5>
                                    <div class="input-group mb-3">
                                        <input type="text" id="nama_diagnosa" class="form-control"
                                            placeholder="Ketik manual" autocomplete="off">
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalDiagnosa" onclick="cariDiagnosa()">
                                            <i class="fas fa-search "></i>
                                        </button>
                                        <button type="button" class="btn btn-success"
                                            onclick="tambahformD($('#nama_diagnosa').val())">
                                            <i class="fas fa-plus "></i>
                                        </button>
                                    </div>
                                    <table class="table table-sm table-bordered" id="table_diagnosa">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Diagnosa</th>
                                                <th width="5%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <!-- diagnosa end -->
                                    <!-- tindakan start -->
                                    <h5>Tindakan</h5>
                                    <div class="input-group mb-3">
                                        <input type="text" id="nama_tindakan" class="form-control"
                                            placeholder="Ketik manual" autocomplete="off">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="harga_tindakan" class="form-control"
                                            onkeyup="FormatCurrency(this);" placeholder="Harga..." autocomplete="off">
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalTindakan" onclick="cariTindakan()">
                                            <i class="fas fa-search "></i>
                                        </button>
                                        <button type="button" class="btn btn-success"
                                            onclick="tambahT($('#nama_tindakan').val(), $('#harga_tindakan').val())">
                                            <i class="fas fa-plus "></i>
                                        </button>
                                    </div>

                                    <table class="table table-sm table-bordered" id="table_tindakan">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Tindakan</th>
                                                <th>Harga</th>
                                                <th width="5%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <!-- tindakan end -->
                                </div>
                                <!-- tab obat start -->
                                <div class="tab-pane fade" id="tab-obat" role="tabobat">
                                    <div class="tab-pane p-3" id="tab-obat" role="tabpanel">
                                        <h5 class="my-3">Resep Obat</h5>
                                        <button class="btn btn-primary mb-3" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalObat">
                                            <i class="fas fa-search me-2"></i>Cari Obat</button>
                                        <div class="table-responsive">
                                            <table class="table table-md table-bordered" id="table_resep">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Nama Obat</th>
                                                        <th width="12%">Satuan</th>
                                                        <th width="10%">Jumlah</th>
                                                        <th>Aturan Pakai</th>
                                                        <th>Harga</th>
                                                        <th>Laba</th>
                                                        <th>Subtotal Harga</th>
                                                        <th class="text-center" width="5%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <hr class="mt-4">
                                        <h5 class="my-3">Racikan Obat</h5>
                                        <button class="btn btn-success mb-3" type="button" onclick="tambahRacikan()">
                                            <i class="fas fa-plus me-2"></i>Tambah Racikan
                                        </button>
                                        <div id="racikan_container"></div>
                                        <hr class="mt-4 row">
                                    </div>
                                </div>
                                <!-- racikan obat end -->
                                <!-- ini digunakan untuk sub total semua harga mulai obat sampai racikan -->
                                <div class="row justify-content-end">
                                    <div class="col-md-4">
                                        <h5 class="text-end">Total Biaya Tindakan:
                                            <input type="hidden" name="utindakan_all[]" id="total_tindakan_hidden"
                                                readonly>
                                            <span id="harga_tindakan_all" class="text-success">Rp 0</span>
                                        </h5>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-end">Total Biaya Obat:

                                            <input type="hidden" name="subtotal_hl_all[]" id="all_uang" readonly>
                                            <span id="harga_total" class="text-success">Rp 0</span>
                                        </h5>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-end">Total Biaya Semua:
                                            <!-- <input type="hidden" name="subtotal_all_obat[]" id="utotal_obat" readonly>
                                    <span id="total_obat" class="text-success">Rp 0</span> -->
                                            <input type="hidden" name="subtotal_all_to[]" id="tobat" readonly>
                                            <span id="tobat1" class="text-success">Rp 0</span>
                                        </h5>
                                    </div>
                                </div>
                                <!-- inputan end -->
                    </form>
                    <div class="row">
                        <div class="col-sm-10 ">
                            <button type="button" onclick="tambah(event);" class="btn btn-success"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                        </div>
                    </div>
                </div>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>
</div><!-- container -->

<div class="modal fade bd-example-modal-lg" id="modalTindakan" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="myLargeModalLabel">
                    <i class="fas fa-user-md me-2"></i>Tindakan <span id="detail_nama_pasien"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" name="nama_tindakan" id="nama_tindakan"
                    oninput="cariTindakan(this.value)" placeholder="Cari Tindakan" autocomplete="off">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover" id="table-data">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div id="pagination"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-6">&nbsp;</div>
                            <label class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah
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
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- diagnosa -->
<div class="modal fade bd-example-modal-lg" id="modalDiagnosa" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="myLargeModalLabel">
                    <i class="fas fa-user-md me-2"></i>Diagnosa <span id="detail_nama_pasien"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" name="nama_diagnosa" id="nama_diagnosa"
                    oninput="cariDiagnosa(this.value)" placeholder="Cari Diagnosa" autocomplete="off">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover" id="table-data1">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Diagnosa</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div id="pagination1"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-6">&nbsp;</div>
                            <label class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah
                                Tampil</label>
                            <div class="col-md-3 pull-right">
                                <select class="form-control" id="jumlah_tampil1">
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
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Nama Barang -->
<div class="modal fade" id="modalObat" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Obat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" name="nama_barang" id="nama_barang"
                        oninput="cariBarang($('#nama_barang').val())" placeholder="Cari Barang" autocomplete="off">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="table-data2">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="obatList"></tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div id="pagination2"></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-md-7">&nbsp;</div>
                            <label
                                class="col-md-2 control-label d-flex align-items-center justify-content-end">Tampil</label>
                            <div class="col-md-3 pull-right">
                                <select class="form-control" id="jumlah_tampil2">
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
                    <i class="far fa-window-close"></i> Tutup</button>
            </div>
        </div>
    </div>
</div>