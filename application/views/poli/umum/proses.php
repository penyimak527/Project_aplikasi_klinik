<script>
    $(document).ready(function () {
        formatAllCurrencyInputs();
        // Event untuk input yang ditambahkan nanti
        $(document).on('keyup', '.input_htindakan', function () {
            FormatCurrency1(this);
        });

        // Event untuk input harga tindakan manual SEBELUM ditambahkan ke tabel
        $(document).on('keyup', '#harga_tindakan', function () {
            FormatCurrency(this);
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


        // Hapus event listener lama dan ganti dengan yang lebih sederhana
        $(document).on('click', '.btn-hapus-tindakan', function () {
            const row = $(this).closest('tr');

            // Ambil ID atau nama dari input hidden
            const idInput = row.find('input[name*="[id_tindakan]"]');
            const namaInput = row.find('input[name*="[nama]"]');

            if (idInput.length > 0) {
                // Ini dari modal (ada ID)
                const id = idInput.val();
                console.log('Hapus tindakan modal dengan ID:', id);
                selectedTindakanIds.delete(parseInt(id));
            } else if (namaInput.length > 0) {
                // Ini dari manual (hanya nama)
                const nama = namaInput.val().toLowerCase();
                console.log('Hapus tindakan manual dengan nama:', nama);
                selectedTindakanManual.delete(nama);
            }

            row.remove();
            hitungTotalTindakan();
        });

        // Untuk diagnosa
        $(document).on('click', '.btn-hapus-diagnosa', function () {
            const row = $(this).closest('tr');

            // Ambil ID atau nama dari input hidden
            const idInput = row.find('input[name*="[id_diagnosa]"]');
            const namaInput = row.find('input[name*="[nama]"]');

            if (idInput.length > 0) {
                // Ini dari modal (ada ID)
                const id = idInput.val();
                console.log('Hapus diagnosa modal dengan ID:', id);
                selectedDiagnosaIds.delete(parseInt(id));
            } else if (namaInput.length > 0) {
                // Ini dari manual (hanya nama)
                const nama = namaInput.val().toLowerCase();
                console.log('Hapus diagnosa manual dengan nama:', nama);
                selectedDiagnosaManual.delete(nama);
            }

            row.remove();
        });
        hitungTotalTindakan();
        initSelectedFromExistingRows();
    })
function initSelectedFromExistingRows() {
  // ===== TINDAKAN =====
  $('#table_tindakan tbody tr').each(function () {
    const $tr = $(this);

    // Dari modal (punya hidden id_tindakan)
    const idT = $tr.find('input[name="tindakan_modal[id_tindakan][]"]').val();
    if (idT) selectedTindakanIds.add(Number(idT));

    // Dari manual (punya nama)
    const namaT = $tr.find('input[name="tindakan_manual[nama][]"]').val();
    if (namaT) selectedTindakanManual.add(namaT.toLowerCase().trim());
  });

  // ===== DIAGNOSA =====
  $('#table_diagnosa tbody tr').each(function () {
    const $tr = $(this);

    // Dari modal (punya hidden id_diagnosa)
    const idD = $tr.find('input[name="diagnosa_modal[id_diagnosa][]"]').val();
    if (idD) selectedDiagnosaIds.add(Number(idD));

    // Dari manual (punya nama)
    const namaD = $tr.find('input[name="diagnosa_manual[nama][]"]').val();
    if (namaD) selectedDiagnosaManual.add(namaD.toLowerCase().trim());
  });

//   console.log('INIT tindakan IDs:', [...selectedTindakanIds]);
//   console.log('INIT tindakan manual:', [...selectedTindakanManual]);
//   console.log('INIT diagnosa IDs:', [...selectedDiagnosaIds]);
//   console.log('INIT diagnosa manual:', [...selectedDiagnosaManual]);
}

    // var global
    let selectedObatBiasaIds = new Set(); // Untuk obat biasa
    let selectedObatRacikanIds = new Set(); // Untuk obat racikan

    let selectedTindakanIds = new Set(); // Untuk tracking ID tindakan
    let selectedDiagnosaIds = new Set(); // Untuk tracking ID diagnosa
    let selectedTindakanManual = new Set(); // Untuk tracking nama tindakan manual
    let selectedDiagnosaManual = new Set(); // Untuk tracking nama diagnosa manual

    // Global variable untuk menyimpan data satuan
    let obatSatuanData = {};

    function formatDataBeforeSubmit() {
        // Format data obat - HAPUS FORMATTING HANYA DARI FIELD YANG TAMPIL
        $('[name^="obat["]').each(function () {
            // Hanya bersihkan field yang TIDAK hidden dan TAMPIL DI UI
            if ($(this).attr('type') !== 'hidden' &&
                ($(this).hasClass('harga') ||
                    $(this).hasClass('laba') ||
                    $(this).hasClass('subtotall'))) {
                let val = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(val);
            }
        });

        // Format data racikan - HAPUS FORMATTING HANYA DARI FIELD YANG TAMPIL
        $('[name^="racikan["]').each(function () {
            // Hanya bersihkan field yang TIDAK hidden
            if ($(this).attr('type') !== 'hidden' &&
                ($(this).hasClass('harga-racikan-display') ||
                    $(this).hasClass('laba-racikan-display') ||
                    $(this).hasClass('subtotal-racikanl-display'))) {
                let val = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(val);
            }
        });
    }


    // Panggil fungsi ini sebelum submit
    $('#form_tambah').on('submit', function () {
        formatDataBeforeSubmit();
    });
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
    // function tambah data 
    function tambah(e) {
        e.preventDefault();
        if (!validateForm('#form_tambah')) {
            return;
        }

        const formData = new FormData($('#form_tambah')[0]);
        $.ajax({
            url: '<?php echo base_url("poli/umum/tambah_proses") ?>',
            method: 'POST',
            // data : $('#form_tambah').serialize(),
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
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
        $.ajax({
            url: '<?= base_url("poli/umum/tindakan") ?>',
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
                    table = `<tr> <td colspan = "6" class = "text-center" > Data tidak ditemukan </td></tr >`;
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
            url: '<?= base_url("poli/umum/obat") ?>',
            data: {
                carit: nama_barang,
            },
            type: 'POST',
            dataType: 'JSON',
            success: function (res) {
                let table = "";
                if (res.status && res.data.length > 0) {
                    console.log('Data ditemukan:', res.data.length, 'baris');
                    let i = 1;
                    for (const item of res.data) {
                        table += `
                            <tr style="cursor:pointer;" onclick="pilihBarang('${btoa(JSON.stringify(item))}')">
                            <td>${i++}</td>
                            <td>${item.nama_barang}</td>
                            <td>${item.satuan_barang}</td>
                            <td>${formatRupiah(item.harga_jual)}</td>
                            </tr>
                        `;
                    }
                } else {
                    console.log('Data tidak ditemukan atau status false');
                    table = `<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>`;
                }
                $('#table-data2 tbody').html(table);
                paging2();
            },
            error: function (xhr) {
                console.error('Gagal mengambil data:', xhr.responseText);
            }
        });
    }

    function getSatuanObat(id_barang) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '<?= base_url("poli/umum/obat") ?>',
                data: {
                    id_barang: id_barang
                },
                type: 'POST',
                dataType: 'JSON',
                success: function (res) {
                    if (res.status && res.data.length > 0) {
                        obatSatuanData[id_barang] = res.data;

                        // Urutkan berdasarkan urutan_satuan (BOX = 1, STRIP = 2, TABLET = 3)
                        res.data.sort((a, b) => a.urutan_satuan - b.urutan_satuan);

                        resolve(res.data);
                    } else {
                        reject('Tidak ada data satuan');
                    }
                },
                error: function (xhr) {
                    reject(xhr.responseText);
                }
            });
        });
    }

    // Fungsi untuk menghitung konversi satuan
    function calculateSatuanConversion(selectedSatuan, targetSatuan, satuanData) {
        // Cari data satuan yang dipilih dan target
        const fromSatuan = satuanData.find(s => s.id_barang_detail == selectedSatuan.id_barang_detail);
        const toSatuan = satuanData.find(s => s.id_barang_detail == targetSatuan.id_barang_detail);

        if (!fromSatuan || !toSatuan) return 1;

        // Urutan satuan: 1 = BOX (terbesar), 2 = STRIP, 3 = TABLET (terkecil)
        const urutanDifference = fromSatuan.urutan_satuan - toSatuan.urutan_satuan;

        // Jika berpindah ke satuan yang lebih kecil (misal BOX ke STRIP), kalikan
        // Jika berpindah ke satuan yang lebih besar (misal STRIP ke BOX), bagi
        if (urutanDifference > 0) {
            // Dari besar ke kecil: kalikan dengan faktor konversi
            return Math.pow(10, urutanDifference);
        } else if (urutanDifference < 0) {
            // Dari kecil ke besar: bagi dengan faktor konversi
            return 1 / Math.pow(10, Math.abs(urutanDifference));
        }

        return 1; // Satuan sama
    }

    // Update fungsi pilihBarang dengan async
    async function pilihBarang(itemBase64) {
        const item = JSON.parse(atob(itemBase64));
        const racikanId = $('#modalObat').data('target-racikan');

        try {
            // Dapatkan semua satuan untuk obat ini
            const satuanData = await getSatuanObat(item.id_barang);

            // Ambil data satuan BOX dari satuanData untuk default
            const boxSatuan = satuanData.find(s => s.id_satuan_barang == 1);
            if (boxSatuan) {
                // Update item dengan data dari satuan BOX
                item.id = boxSatuan.id_barang_detail;
                item.harga_awal = boxSatuan.harga_awal;
                item.harga_jual = boxSatuan.harga_jual;
                item.laba = boxSatuan.laba;
                item.satuan_barang = boxSatuan.satuan_barang;
                item.urutan_satuan = boxSatuan.urutan_satuan;
                item.id_satuan_barang = boxSatuan.id_satuan_barang;
            }

            if (racikanId) {
                // Untuk racikan
                if (selectedObatRacikanIds.has(item.id_barang)) {
                    Swal.fire({
                        title: 'Obat Sudah Dipilih di Racikan!',
                        text: `Obat "${item.nama_barang}" sudah ada di racikan ini.`,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Oke'
                    });
                    return;
                }
                addRowToRacikan(item, racikanId, satuanData);

            } else {
                // Untuk obat biasa
                if (selectedObatBiasaIds.has(item.id_barang)) {
                    Swal.fire({
                        title: 'Obat Sudah Dipilih!',
                        text: `Obat "${item.nama_barang}" sudah ada di resep obat.`,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Oke'
                    });
                    return;
                }
                tambah_obat_rsp(item, satuanData);
            }
            $('#modalObat').modal('hide');
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal memuat data satuan obat', 'error');
        }
    }

    function cariDiagnosa(keyword) {
        $.ajax({
            url: '<?= base_url("poli/umum/diagnosa") ?>',
            data: {
                caridiagnosa: keyword
            },
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
                    table = `<tr> <td colspan = "6" class = "text-center" > Data tidak ditemukan </td></tr>`;
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

        if (!inputElement) return; // TAMBAHKAN VALIDASI INI

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

        // Format ribuan
        const formatted = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        // Tambahkan Rp.
        inputElement.value = 'Rp ' + formatted;

        // Kembalikan posisi kursor setelah formatting
        const newLength = inputElement.value.length;
        const lengthDiff = newLength - originalLength;
        inputElement.setSelectionRange(cursorPosition + lengthDiff, cursorPosition + lengthDiff);
    }

    // Di fungsi pilihTindakan (dari modal):
    function pilihTindakan(itemBase64) {
        const item = JSON.parse(atob(itemBase64));
        const itemId = Number(item.id); // ← Normalisasi ke number

        console.log('pilihTindakan dipanggil, item.id:', item.id);
        console.log('selectedTindakanIds sebelum:', Array.from(selectedTindakanIds));

        // Cek apakah tindakan sudah dipilih
        if (selectedTindakanIds.has(itemId)) {
            Swal.fire({
                title: 'Tindakan Sudah Dipilih!',
                text: `Tindakan "${item.nama}" sudah ada di daftar tindakan.`,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
            return;
        }

        let row = `
    <tr data-tindakan-id="${itemId}" data-type="modal">
        <td>
            <input type="hidden" name="tindakan_modal[id_tindakan][]" class="form-control" value="${itemId}" readonly>
            <input type="text" name="tindakan_modal[nama][]" class="form-control" value="${item.nama}" readonly>
        </td>
        <td>
            <input type="text" name="tindakan_modal[harga][]" class="form-control input_htindakan" value="${item.harga}" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger btn-hapus-tindakan">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    </tr>
    `;

        $('#table_tindakan tbody').append(row);
        selectedTindakanIds.add(itemId); // ← Simpan sebagai number
        $('#modalTindakan').modal('hide'); // Ganti 'modalDiagnosa' dengan ID modal Anda

        const newInput = $('#table_tindakan tbody tr:last .input_htindakan')[0];
        FormatCurrency1(newInput);
        hitungTotalTindakan();
    }

    // Di fungsi tambahT (manual):
    function tambahT(nama_tindakan, harga_tindakan) {
        // Validasi input
        if (!nama_tindakan || !harga_tindakan) {
            Swal.fire('Peringatan!', 'Nama tindakan dan harga harus diisi', 'warning');
            return;
        }

        const namaLower = nama_tindakan.toLowerCase();

        // Cek duplikasi untuk tindakan manual
        if (selectedTindakanManual.has(namaLower)) {
            Swal.fire({
                title: 'Tindakan Sudah Dipilih!',
                text: `Tindakan "${nama_tindakan}" sudah ada di daftar tindakan.`,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
            return;
        }

        let hargaNum = parseFloat(harga_tindakan.replace(/[^0-9]/g, '')) || 0;

        let row = `
    <tr data-tindakan-manual="${namaLower}" data-type="manual">
        <td>
            <input type="text" name="tindakan_manual[nama][]" class="form-control" autocomplete="off" placeholder="Tindakan" value="${nama_tindakan}" readonly>
        </td>
        <td>
            <input type="text" name="tindakan_manual[harga][]" class="form-control input_htindakan" autocomplete="off" placeholder="Harga" value="${hargaNum}" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger btn-hapus-tindakan">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    </tr>
    `;

        $('#table_tindakan tbody').append(row);
        selectedTindakanManual.add(namaLower); // INI YANG LUPA! ← TAMBAHKAN INI

        const newInput = $('#table_tindakan tbody tr:last .input_htindakan')[0];
        if (newInput) {
            FormatCurrency1(newInput);
        }

        $('#nama_tindakan').val('');
        $('#harga_tindakan').val('');
        hitungTotalTindakan();
    }


    // Update hitungTotalTindakan untuk field baru:
    function hitungTotalTindakan() {
        let total = 0;

        // Hitung dari input tindakan modal
        $('input[name="tindakan_modal[harga][]"]').each(function () {
            const harga = parseFloat($(this).val().replace(/[^0-9]/g, '')) || 0;
            total += harga;
        });

        // Hitung dari input tindakan manual
        $('input[name="tindakan_manual[harga][]"]').each(function () {
            const harga = parseFloat($(this).val().replace(/[^0-9]/g, '')) || 0;
            total += harga;
        });

        $('#total_tindakan_hidden').val(total);
        $('#harga_tindakan_all').text(formatRupiah(total));
        hitungTotal();
    }

    // Di fungsi pilihDiagnosa (dari modal):
    function pilihDiagnosa(itemBase64) {
        const item = JSON.parse(atob(itemBase64));

        // Pastikan ID adalah number
        const itemId = Number(item.id);

        if (selectedDiagnosaIds.has(itemId)) {
            Swal.fire({
                title: 'Diagnosa Sudah Dipilih!',
                text: `Diagnosa "${item.nama_diagnosa}" sudah ada di daftar diagnosa.`,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
            return;
        }

        let row = `
    <tr data-diagnosa-id="${itemId}" data-type="modal">
        <td>
            <input type="hidden" name="diagnosa_modal[id_diagnosa][]" class="form-control" value="${itemId}" readonly>
            <input type="text" name="diagnosa_modal[nama][]" class="form-control" value="${item.nama_diagnosa}" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger btn-hapus-diagnosa">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    </tr>`;

        $('#table_diagnosa tbody').append(row);
        selectedDiagnosaIds.add(itemId); // ← Simpan sebagai number
        // TAMBAHKAN KODE INI UNTUK MENUTUP MODAL
        $('#modalDiagnosa').modal('hide'); // Ganti 'modalDiagnosa' dengan ID modal Anda
    }

    // Di fungsi tambahformD (manual):
    function tambahformD(nama_diagnosa) {
        if (!nama_diagnosa) {
            Swal.fire('Peringatan!', 'Isi Nama Diagnosa Terlebih Dahulu', 'warning');
            return;
        }

        const namaLower = nama_diagnosa.toLowerCase();

        // Cek duplikasi untuk diagnosa manual
        if (selectedDiagnosaManual.has(namaLower)) {
            Swal.fire({
                title: 'Diagnosa Sudah Dipilih!',
                text: `Diagnosa "${nama_diagnosa}" sudah ada di daftar diagnosa.`,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
            return;
        }

        let row = `
    <tr data-diagnosa-manual="${namaLower}" data-type="manual">
        <td>
            <input type="text" name="diagnosa_manual[nama][]" class="form-control" value="${nama_diagnosa}" autocomplete="off" placeholder="diagnosa" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger btn-hapus-diagnosa">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    </tr>
    `;

        $('#table_diagnosa tbody').append(row);
        selectedDiagnosaManual.add(namaLower);
    }




    // Update fungsi tambah_obat_rsp dengan dropdown satuan
    function tambah_obat_rsp(item, satuanData) {
        event.preventDefault();

        // Hitung nomor urut
        const rowCount = $('#table_resep tbody tr').length;
        const newRowId = rowCount + 1;

        // Cari satuan default (BOX) dari satuanData
        const defaultSatuan = satuanData.find(s => s.id_satuan_barang == 1) || satuanData[0];

        // Buat dropdown satuan
        let satuanOptions = '';
        if (satuanData && satuanData.length > 0) {
            satuanData.forEach(function (satuan) {
                const selected = satuan.id_barang_detail == defaultSatuan.id_barang_detail ? 'selected' : '';
                satuanOptions += `<option value="${satuan.id_barang_detail}" 
                    data-harga-awal="${satuan.harga_awal}" 
                    data-harga-jual="${satuan.harga_jual}" 
                    data-laba="${satuan.laba}" 
                    data-satuan="${satuan.satuan_barang}" 
                    data-urutan="${satuan.urutan_satuan}" 
                    data-id-satuan="${satuan.id_satuan_barang}"
                    ${selected}>
                    ${satuan.satuan_barang}
                </option>`;
            });
        }

        // Gunakan harga dari defaultSatuan (BOX), bukan dari item modal
        const hargaAwal = defaultSatuan.harga_awal || 0;
        const hargaJual = defaultSatuan.harga_jual || 0;
        const laba = defaultSatuan.laba || 0;
        const satuan = defaultSatuan.satuan_barang || '';
        const urutanSatuan = defaultSatuan.urutan_satuan || 0;

        // Buat baris baru dengan dropdown satuan

        // Buat baris baru dengan dropdown satuan
        const newRow = `
    <tr id="resep-row-${newRowId}" data-obat-id="${item.id_barang}" data-satuan-data='${JSON.stringify(satuanData)}'>
        <td>
            <input type="hidden" name="obat[${newRowId}][id_obat_detail_o]" class="id-barang-detail" value="${defaultSatuan.id_barang_detail}">
            <input type="hidden" name="obat[${newRowId}][id_obat_o]" value="${item.id_barang}">
            <!-- Harga per satuan (hidden) -->
            <input type="hidden" class="harga-awal-satuan" value="${hargaAwal}">
            <input type="hidden" class="harga-jual-satuan" value="${hargaJual}">
            <input type="hidden" class="laba-satuan" value="${laba}">
            <!-- Harga total untuk submit (ANGKA MURNI) -->
            <input type="hidden" class="form-control harga_jual" name="obat[${newRowId}][harga_jual_o]" value="${hargaJual}">
            <input type="hidden" class="form-control sub-total-laba" name="obat[${newRowId}][subtotal_laba_o]" value="${laba}">
            <input type="text" class="form-control" name="obat[${newRowId}][nama_obat_o]" value="${item.nama_barang}" readonly>
        </td>
        <td>
            <select class="form-select select-satuan" name="obat[${newRowId}][id_satuan_o]" 
                    onchange="ubahSatuanObat(${newRowId}, this)">
                ${satuanOptions}
            </select>
            <input type="hidden" class="form-control nama-satuan" name="obat[${newRowId}][satuan_o]" value="${satuan}">
            <input type="hidden" class="form-control urutan-satuan" name="obat[${newRowId}][urutan_satuan_o]" value="${urutanSatuan}">
        </td>
        <td>
            <div class="input-group">
                <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required class="form-control jumlah" name="obat[${newRowId}][jumlah_o]" min="1" value="1" 
                    onkeyup="hitungSubtotalWithConversion(${newRowId})">
            </div>
        </td>
        <td>
            <input type="text" class="form-control" name="obat[${newRowId}][aturan_pakai_o]" placeholder="Contoh: 2x1 sehari" autocomplete="off" required>
        </td>
        <td>
            <!-- UI: formatted, Hidden: angka murni -->
            <input type="text" class="form-control harga" 
                   value="${formatRupiah(hargaAwal)}" readonly>
            <!-- Hidden input untuk submit -->
            <input type="hidden" class="harga-unit" name="obat[${newRowId}][harga_o]" value="${hargaAwal}">
        </td>
        <td>
            <!-- UI: formatted, Hidden: angka murni -->
            <input type="text" class="form-control laba" 
                   value="${formatRupiah(laba)}" readonly>
            <!-- Hidden input untuk submit -->
            <input type="hidden" class="laba-unit" name="obat[${newRowId}][laba_o]" value="${laba}">
        </td>
        <td>
            <!-- UI: formatted -->
            <input type="text" class="form-control subtotall" 
                   value="${formatRupiah(hargaJual)}" readonly>
            <!-- Hidden: angka murni -->
            <input type="hidden" class="form-control subtotal" name="obat[${newRowId}][subtotal_o]" 
                   value="${hargaAwal}">
            <input type="hidden" name="obat[${newRowId}][subtotal_ol]" value="${hargaJual}">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" 
                    onclick="hapusObat('${item.id_barang}', ${newRowId})">
                <i class="far fa-trash-alt"></i>
            </button>
            <input type="hidden" class="form-control sub-total-laba-harga" name="obat[${newRowId}][subtotal_laba_harga_o]" 
                   value="${hargaJual}">
        </td>
    </tr>`;

        // Tambahkan ke tabel
        $('#table_resep tbody').append(newRow);
        selectedObatBiasaIds.add(item.id_barang); // Tracking

        // Hitung total
        hitungTotal();
    }



    // Fungsi untuk mengubah satuan obat dengan sistem konversi
    function ubahSatuanObat(rowId, selectElement) {
        const row = $(`#resep-row-${rowId}`);
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const satuanData = JSON.parse(row.attr('data-satuan-data'));

        // Ambil data satuan lama
        const currentJumlah = parseFloat(row.find('.jumlah').val()) || 1;
        const currentSatuanId = row.find('.id-barang-detail').val();
        const currentSatuan = satuanData.find(s => s.id_barang_detail == currentSatuanId);

        // Data satuan baru
        const newSatuanId = selectedOption.value;
        const newSatuan = satuanData.find(s => s.id_barang_detail == newSatuanId);

        // Hitung konversi
        const conversionRate = calculateSatuanConversion(currentSatuan, newSatuan, satuanData);

        // Konversi jumlah
        const newJumlah = currentJumlah * conversionRate;

        console.log('Debug ubahSatuan:');
        console.log('- Dari satuan:', currentSatuan?.satuan_barang);
        console.log('- Ke satuan:', newSatuan?.satuan_barang);
        console.log('- Konversi rate:', conversionRate);
        console.log('- Jumlah lama:', currentJumlah);
        console.log('- Jumlah baru:', newJumlah);
        console.log('- Harga awal baru:', newSatuan?.harga_awal);
        console.log('- Harga jual baru:', newSatuan?.harga_jual);
        console.log('- Laba baru:', newSatuan?.laba);

        // Update nilai di form dengan data satuan baru
        row.find('.id-barang-detail').val(newSatuanId);
        row.find('.harga-awal-satuan').val(newSatuan.harga_awal);
        row.find('.harga-jual-satuan').val(newSatuan.harga_jual);
        row.find('.laba-satuan').val(newSatuan.laba);
        row.find('.urutan-satuan').val(newSatuan.urutan_satuan);
        row.find('.nama-satuan').val(newSatuan.satuan_barang);



        // Hitung ulang subtotal dengan jumlah dan satuan baru
        hitungSubtotalWithConversion(rowId);
    }


    // Fungsi untuk update info konversi
    function updateKonversiInfo(rowId, satuan, jumlah) {
        const row = $(`#resep-row-${rowId}`);
        const satuanData = JSON.parse(row.attr('data-satuan-data'));
        const currentJumlah = parseFloat(row.find('.jumlah').val()) || 1; // Ambil jumlah saat ini
        // Cari satuan dasar (TABLET) untuk konversi
        const tabletSatuan = satuanData.find(s => s.urutan_satuan == 3); // TABLET

        if (tabletSatuan) {
            const conversionRate = calculateSatuanConversion(satuan, tabletSatuan, satuanData);
            const jumlahTablet = currentJumlah * conversionRate;

            // Tampilkan di suatu tempat (misalnya di kolom Qty sebagai tooltip)
            console.log(`Konversi: ${currentJumlah} ${satuan.satuan_barang} = ${jumlahTablet.toFixed(0)} tablet`);
        }
    }

    // Fungsi hitung subtotal dengan konversi yang benar (Real Time)
    // function hitungSubtotalWithConversion(rowId) {
    //     const row = $(`#resep-row-${rowId}`);
    //     const jumlah = parseFloat(row.find('.jumlah').val()) || 0;

    //     // Ambil harga PER SATUAN dari hidden fields (ANGKA MURNI)
    //     const hargaAwalSatuan = parseFloat(row.find('.harga-awal-satuan').val()) || 0;
    //     const hargaJualSatuan = parseFloat(row.find('.harga-jual-satuan').val()) || 0;
    //     const labaSatuan = parseFloat(row.find('.laba-satuan').val()) || 0;


    //     // Hitung TOTAL berdasarkan jumlah (ANGKA MURNI)
    //     const totalHargaAwal = jumlah * hargaAwalSatuan;
    //     const totalHargaJual = jumlah * hargaJualSatuan;
    //     const totalLaba = jumlah * labaSatuan;


    //     // ✅ UPDATE UI: format Rupiah untuk display
    //     row.find('.harga').val(formatRupiah(totalHargaAwal));
    //     row.find('.laba').val(formatRupiah(totalLaba));
    //     row.find('.subtotall').val(formatRupiah(totalHargaJual));

    //     // ✅ UPDATE HIDDEN FIELDS: angka murni untuk backend
    //     row.find('.subtotal').val(totalHargaAwal); // angka murni
    //     row.find('.harga_jual').val(totalHargaJual); // angka murni
    //     row.find('.sub-total-laba').val(totalLaba); // angka murni
    //     row.find('.sub-total-laba-harga').val(totalHargaJual); // angka murni

    //     hitungTotal();
    // }
function hitungSubtotalWithConversion(rowId) {
    const row = $(`#resep-row-${rowId}`);
    const jumlah = parseFloat(row.find('.jumlah').val()) || 0;

    // nilai per unit (murni)
    const hargaAwalSatuan = parseFloat(row.find('.harga-awal-satuan').val()) || 0;
    const hargaJualSatuan = parseFloat(row.find('.harga-jual-satuan').val()) || 0;
    const labaSatuan      = parseFloat(row.find('.laba-satuan').val()) || 0;

    // total (yang boleh berubah hanya subtotal)
    const totalHargaAwal = jumlah * hargaAwalSatuan;
    const totalHargaJual = jumlah * hargaJualSatuan;
    const totalLaba      = jumlah * labaSatuan;

    // ✅ UI: harga & laba tetap PER UNIT
    row.find('.harga').val(formatRupiah(hargaAwalSatuan));
    row.find('.laba').val(formatRupiah(labaSatuan));

    // ✅ UI: subtotal (total jual) berubah sesuai jumlah
    row.find('.subtotall').val(formatRupiah(totalHargaJual));

    // ✅ Hidden untuk backend:
    // - unit
    row.find('.harga-unit').val(hargaAwalSatuan);
    row.find('.laba-unit').val(labaSatuan);

    // - subtotal
    row.find('.subtotal').val(totalHargaAwal);        // subtotal harga awal
    row.find('[name^="obat["][name$="[subtotal_ol]"]').val(totalHargaJual); // subtotal jual (kalau ada)
    row.find('.sub-total-laba').val(totalLaba);       // subtotal laba

    // kalau kamu pakai harga_jual_o sebagai UNIT jual, set unit:
    row.find('.harga_jual').val(hargaJualSatuan);

    hitungTotal();
}


    // Update fungsi hitungTotal untuk handle konversi
    // Update fungsi hitungTotal untuk handle konversi
    function hitungTotal() {
        let totalObat = 0;
        let totalObatl = 0;
        let totalRacikan = 0;
        let totalRacikanl = 0;
        let totaltindakan = parseFloat($('#total_tindakan_hidden').val()) || 0;

        // Hitung total dari obat biasa - AMBIL DARI HIDDEN FIELDS (angka murni)
        $('[id^="resep-row-"]').each(function () {
            // Ambil dari .subtotal (hidden, angka murni), bukan dari .subtotall (formatted)
            const subtotal = parseFloat($(this).find('.subtotal').val()) || 0;
            const subtotalLaba = parseFloat($(this).find('.sub-total-laba').val()) || 0;

            totalObat += subtotal;
            totalObatl += (subtotal + subtotalLaba);
        });

        // Hitung total dari racikan - AMBIL DARI INPUT HIDDEN
        $('[id^="racikan-obat-row-"]').each(function () {
            const subtotal = parseFloat($(this).find('.subtotal-racikan').val()) || 0;
            const subtotalLaba = parseFloat($(this).find('.subtotallaba-racikan').val()) || 0;

            totalRacikan += subtotal;
            totalRacikanl += (subtotal + subtotalLaba);
        });

        const grandTotal = totalObat + totalRacikan;
        const grandTotall = totalObatl + totalRacikanl;
        const tobatt = totaltindakan + grandTotall;

        // UI: formatted, Hidden: angka murni
        $('#harga_tindakan_all').text(formatRupiah(totaltindakan));
        $('#harga_total').text(formatRupiah(grandTotall));
        $('#all_uang').val(grandTotall); // angka murni
        $('#tobat1').text(formatRupiah(tobatt));
        $('#tobat').val(tobatt); // angka murni
    }


    // Event listener untuk perubahan jumlah
    $(document).on('change', '.jumlah', function () {
        const rowId = $(this).closest('tr').attr('id').replace('resep-row-', '');
        hitungSubtotalWithConversion(rowId);
    });

    // Event listener untuk perubahan dropdown satuan
    $(document).on('change', '.select-satuan', function () {
        const rowId = $(this).closest('tr').attr('id').replace('resep-row-', '');
        ubahSatuanObat(rowId, this);
    });

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

        // Handle string (hapus karakter non-digit jika formatted)
        if (typeof angka === 'string') {
            // Cek apakah sudah formatted (ada koma atau titik)
            if (angka.includes(',') || angka.includes('.')) {
                angka = angka.replace(/[^0-9]/g, '');
            }
            // Jika tidak, anggap sudah angka murni
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
                            class="form-control" placeholder="Nama Racikan" autocomplete="off" required>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-3 col-form-label">Jumlah</label>
                    <div class="col-sm-9">
                        <input type="text" name="racikan[${racikanCounter}][jumlah_r]" 
                            class="form-control" placeholder="Jumlah" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"  value="1" required>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-3 col-form-label">Aturan Pakai</label>
                    <div class="col-sm-9">
                        <textarea type="text" name="racikan[${racikanCounter}][aturan_r]" 
                            class="form-control" placeholder="Aturan Pakai" autocomplete="off" required></textarea>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-3 col-form-label">Keterangan</label>
                    <div class="col-sm-9">
                        <textarea name="racikan[${racikanCounter}][keterangan_r]" 
                            class="form-control" placeholder="Keterangan"></textarea>
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
                                <th>Qty</th>
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
    function addRowToRacikan(item, racikanId, satuanData) {
        const rowCount = $(`#racikan-obat-${racikanId} tr`).length;
        const newRowId = rowCount + 1;

        console.log(`Tambah obat ke racikan ${racikanId}, row ke-${newRowId}, total rows: ${rowCount}`);

        // Pastikan semua field yang diperlukan ada di item
        if (!item.id_barang || !item.id_satuan_barang || !item.urutan_satuan) {
            console.error("Data item tidak lengkap:", item);
            alert("Data obat tidak lengkap, silakan pilih obat lagi");
            return;
        }

        // Cari satuan default (BOX) dari satuanData
        const defaultSatuan = satuanData.find(s => s.id_satuan_barang == 1) || satuanData[0];

        // Gunakan harga dari defaultSatuan (BOX), bukan dari item modal
        const hargaAwal = defaultSatuan.harga_awal || 0;
        const hargaJual = defaultSatuan.harga_jual || 0;
        const laba = defaultSatuan.laba || 0;
        const satuan = defaultSatuan.satuan_barang || '';
        const urutanSatuan = defaultSatuan.urutan_satuan || 0;
        const idBarangDetail = defaultSatuan.id_barang_detail || item.id;

        // Buat dropdown satuan untuk racikan
        let satuanOptions = '';
        if (satuanData && satuanData.length > 0) {
            satuanData.forEach(function (satuanItem) {
                const selected = satuanItem.id_barang_detail == defaultSatuan.id_barang_detail ? 'selected' : '';
                satuanOptions += `<option value="${satuanItem.id_barang_detail}" 
                data-harga-awal="${satuanItem.harga_awal}" 
                data-harga-jual="${satuanItem.harga_jual}" 
                data-laba="${satuanItem.laba}" 
                data-satuan="${satuanItem.satuan_barang}" 
                data-urutan="${satuanItem.urutan_satuan}" 
                data-id-satuan="${satuanItem.id_satuan_barang}"
                ${selected}>
                ${satuanItem.satuan_barang}
            </option>`;
            });
        }

        // Cek duplikasi HANYA di racikan yang sama
        if (selectedObatRacikanIds.has(item.id_barang + '-' + racikanId)) {
            Swal.fire({
                title: 'Obat Sudah Dipilih di Racikan!',
                text: `Obat "${item.nama_barang}" sudah ada di racikan ini.`,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Oke'
            });
            return;
        }

        let newRow = `
    <tr id="racikan-obat-row-${racikanId}-${newRowId}" data-obat-id="${item.id_barang}" data-satuan-data='${JSON.stringify(satuanData)}'>
        <td>
            <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id]" value="${idBarangDetail}">
            <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id_barang_br]" value="${item.id_barang}">
            
            <!-- Harga per satuan (hidden) -->
            <input type="hidden" class="harga-awal-satuan-racikan" value="${hargaAwal}">
            <input type="hidden" class="harga-jual-satuan-racikan" value="${hargaJual}">
            <input type="hidden" class="laba-satuan-racikan" value="${laba}">
            
            <input type="text" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][nama_br]" value="${item.nama_barang}" readonly>
        </td>
        <td>
            <select class="form-select select-satuan-racikan" name="racikan[${racikanId}][obat][${newRowId}][id_satuan_br]" 
                    onchange="ubahSatuanRacikan(${racikanId}, ${newRowId}, this)">
                ${satuanOptions}
            </select>
            <input type="hidden" class="form-control nama-satuan-racikan" name="racikan[${racikanId}][obat][${newRowId}][satuan_br]" value="${satuan}">
            <input type="hidden" class="form-control urutan-satuan-racikan" name="racikan[${racikanId}][obat][${newRowId}][urutan_satuan_br]" value="${urutanSatuan}">
        </td>
        <td>
            <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required class="form-control jumlah-racikan" name="racikan[${racikanId}][obat][${newRowId}][jumlah_br]" min="1" value="1" 
                onkeyup="hitungSubtotalRacikan(${racikanId}, ${newRowId})">
        </td>
        <td>
            <!-- Input untuk tampilan (formatted) -->
            <input type="text" class="form-control harga-racikan-display" value="${formatRupiah(hargaAwal)}" readonly>
            <!-- Input untuk submit (hidden) -->
            <input type="hidden" class="form-control harga-racikan" name="racikan[${racikanId}][obat][${newRowId}][harga_br]" value="${hargaAwal}">
        </td>
        <td>
            <!-- Input untuk tampilan (formatted) -->
            <input type="text" class="form-control laba-racikan-display" value="${formatRupiah(laba)}" readonly>
            <!-- Input untuk submit (hidden) -->
            <input type="hidden" class="form-control laba-racikan" name="racikan[${racikanId}][obat][${newRowId}][laba_br]" value="${laba}">
        </td>
        <td>
            <!-- Input untuk tampilan (formatted) -->
            <input type="text" class="form-control subtotal-racikanl-display" value="${formatRupiah(hargaJual)}" readonly>
            <!-- Input untuk submit (hidden) -->
            <input type="hidden" class="form-control subtotal-racikanl" name="racikan[${racikanId}][obat][${newRowId}][subtotal_brl]" value="${hargaJual}">
            <input type="hidden" class="form-control subtotal-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_br]" value="${hargaAwal}">
            <input type="hidden" class="form-control subtotallaba-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_laba_br]" value="${laba}">
            <input type="hidden" class="form-control harga-jracikan" name="racikan[${racikanId}][obat][${newRowId}][harga_jbr]" value="${hargaJual}">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" 
                    onclick="hapusObatRacikan('${item.id_barang}', ${racikanId}, ${newRowId}, event)">
                <i class="far fa-trash-alt"></i>
            </button>
        </td>
    </tr>`;

        $(`#racikan-obat-${racikanId}`).append(newRow);
        selectedObatRacikanIds.add(item.id_barang + '-' + racikanId); // Tracking dengan ID racikan
        hitungSubtotalRacikan(racikanId, newRowId);
    }

    // Fungsi untuk mengubah satuan obat racikan TANPA mengubah jumlah
    function ubahSatuanRacikan(racikanId, rowId, selectElement) {
        const row = $(`#racikan-obat-row-${racikanId}-${rowId}`);
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const satuanData = JSON.parse(row.attr('data-satuan-data'));

        // Data satuan baru yang dipilih
        const newSatuanId = selectedOption.value;
        const newSatuan = satuanData.find(s => s.id_barang_detail == newSatuanId);

        if (!newSatuan) {
            console.error('Satuan tidak ditemukan:', newSatuanId);
            return;
        }

        console.log('Debug ubahSatuanRacikan:');
        console.log('- Ke satuan:', newSatuan.satuan_barang);
        console.log('- Harga awal baru:', newSatuan.harga_awal);
        console.log('- Harga jual baru:', newSatuan.harga_jual);
        console.log('- Laba baru:', newSatuan.laba);

        // Update nilai di form dengan data satuan baru
        row.find('input[name*="[id]"]').val(newSatuanId);
        row.find('.harga-awal-satuan-racikan').val(newSatuan.harga_awal);
        row.find('.harga-jual-satuan-racikan').val(newSatuan.harga_jual);
        row.find('.laba-satuan-racikan').val(newSatuan.laba);
        row.find('.urutan-satuan-racikan').val(newSatuan.urutan_satuan);
        row.find('.nama-satuan-racikan').val(newSatuan.satuan_barang);

        // Hitung ulang subtotal dengan jumlah yang SAMA tapi satuan yang BARU
        hitungSubtotalRacikan(racikanId, rowId);
    }

    function tambahObatracikan(racikanId) {
        // Simpan ID racikan sebagai data pada modal
        $('#modalObat').data('target-racikan', racikanId).modal('show');
    }

    // Fungsi hapus obat biasa
    function hapusObat(obatId, rowId) {
        $(`#resep-row-${rowId}`).remove();
        selectedObatBiasaIds.delete(obatId); // Hapus dari tracking
        console.log('Obat dihapus dari resep biasa, ID:', obatId, 'Sisa tracking:', Array.from(selectedObatBiasaIds));
        hitungTotal();
    }

    // Fungsi hapus obat racikan  
    function hapusObatRacikan(obatId, racikanId, rowId) {
        // Remove dari DOM
        $(`#racikan-obat-row-${racikanId}-${rowId}`).remove();

        // Remove dari tracking
        selectedObatRacikanIds.delete(obatId + '-' + racikanId);

        // Hitung ulang total
        hitungTotalRacikan(racikanId);
        hitungTotal();

        // Re-index rows yang tersisa (OPTIONAL tapi direkomendasikan)
        reindexRacikanRows(racikanId);
    }

    // TAMBAH fungsi untuk re-index (optional):
    function reindexRacikanRows(racikanId) {
        let newIndex = 1;
        $(`#racikan-obat-${racikanId} tr`).each(function () {
            // Update semua input names
            $(this).find('[name^="racikan"]').each(function () {
                let oldName = $(this).attr('name');
                let newName = oldName.replace(
                    /racikan\[(\d+)\]\[obat\]\[(\d+)\]/g,
                    `racikan[$1][obat][${newIndex}]`
                );
                $(this).attr('name', newName);
            });

            // Update ID tr
            $(this).attr('id', `racikan-obat-row-${racikanId}-${newIndex}`);

            // Update onclick handler untuk button hapus
            $(this).find('button').attr('onclick',
                `hapusObatRacikan(${$(this).data('obat-id')}, ${racikanId}, ${newIndex})`
            );

            newIndex++;
        });
    }

    // Ganti onclick di button hapus racikan
    function hapusSeluruhRacikan(racikanId) {
        // Hapus semua obat dalam racikan dari tracking
        $(`#racikan-obat-${racikanId} tr`).each(function () {
            const obatId = $(this).data('obat-id');
            if (obatId) {
                selectedObatRacikanIds.delete(obatId + '-' + racikanId);
            }
        });

        $(`#racikan-${racikanId}`).remove();
        hitungTotal();
    }

    // Event listener untuk perubahan jumlah racikan
    $(document).on('change', '.jumlah-racikan', function () {
        const rowId = $(this).closest('tr').attr('id').match(/racikan-obat-row-(\d+)-(\d+)/);
        if (rowId && rowId.length >= 3) {
            hitungSubtotalRacikan(parseInt(rowId[1]), parseInt(rowId[2]));
        }
    });

    // Event listener untuk perubahan dropdown satuan racikan
    $(document).on('change', '.select-satuan-racikan', function () {
        const rowId = $(this).closest('tr').attr('id').match(/racikan-obat-row-(\d+)-(\d+)/);
        if (rowId && rowId.length >= 3) {
            ubahSatuanRacikan(parseInt(rowId[1]), parseInt(rowId[2]), this);
        }
    });

    // Fungsi reset
    function resetModalObat() {
        $('#modalObat').removeData('target-racikan');
        console.log('Modal obat direset untuk resep biasa');
    }

    // function hitungSubtotalRacikan(racikanId, rowId) {
    //     const row = $(`#racikan-obat-row-${racikanId}-${rowId}`);

    //     // Ambil nilai dari input (nilai asli)
    //     const jumlah = parseFloat(row.find('.jumlah-racikan').val()) || 0;
    //     const hargaAwalSatuan = parseFloat(row.find('.harga-awal-satuan-racikan').val()) || 0;
    //     const hargaJualSatuan = parseFloat(row.find('.harga-jual-satuan-racikan').val()) || 0;
    //     const labaSatuan = parseFloat(row.find('.laba-satuan-racikan').val()) || 0;

    //     console.log('Debug hitungSubtotalRacikan:', {
    //         jumlah: jumlah,
    //         hargaAwalSatuan: hargaAwalSatuan,
    //         hargaJualSatuan: hargaJualSatuan,
    //         labaSatuan: labaSatuan
    //     });

    //     // Hitung subtotal
    //     const subtotalHargaAwal = jumlah * hargaAwalSatuan;
    //     const subtotalHargaJual = jumlah * hargaJualSatuan;
    //     const subtotalLaba = jumlah * labaSatuan;

    //     console.log('Hasil perhitungan:', {
    //         subtotalHargaAwal: subtotalHargaAwal,
    //         subtotalHargaJual: subtotalHargaJual,
    //         subtotalLaba: subtotalLaba
    //     });

    //     // Update input hidden untuk submit
    //     row.find('.harga-racikan').val(subtotalHargaAwal);
    //     row.find('.laba-racikan').val(subtotalLaba);
    //     row.find('.subtotal-racikan').val(subtotalHargaAwal);
    //     row.find('.subtotal-racikanl').val(subtotalHargaJual);
    //     row.find('.subtotallaba-racikan').val(subtotalLaba);
    //     row.find('.harga-jracikan').val(subtotalHargaJual);

    //     // Update tampilan dengan format Rupiah
    //     row.find('.harga-racikan-display').val(formatRupiah(subtotalHargaAwal));
    //     row.find('.laba-racikan-display').val(formatRupiah(subtotalLaba));
    //     row.find('.subtotal-racikanl-display').val(formatRupiah(subtotalHargaJual));

    //     // Hitung total racikan
    //     hitungTotalRacikan(racikanId);
    //     hitungTotal();
    // }

    function hitungSubtotalRacikan(racikanId, rowId) {
    const row = $(`#racikan-obat-row-${racikanId}-${rowId}`);

    const jumlah = parseFloat(row.find('.jumlah-racikan').val()) || 0;

    // per unit (murni)
    const hargaAwalSatuan = parseFloat(row.find('.harga-awal-satuan-racikan').val()) || 0;
    const hargaJualSatuan = parseFloat(row.find('.harga-jual-satuan-racikan').val()) || 0;
    const labaSatuan      = parseFloat(row.find('.laba-satuan-racikan').val()) || 0;

    // subtotal (total)
    const subtotalHargaAwal = jumlah * hargaAwalSatuan;
    const subtotalHargaJual = jumlah * hargaJualSatuan;
    const subtotalLaba      = jumlah * labaSatuan;

    // ✅ harga & laba tetap PER UNIT (display + hidden unit)
    row.find('.harga-racikan').val(hargaAwalSatuan);              // harga_br (unit)
    row.find('.laba-racikan').val(labaSatuan);                    // laba_br (unit)
    row.find('.harga-racikan-display').val(formatRupiah(hargaAwalSatuan));
    row.find('.laba-racikan-display').val(formatRupiah(labaSatuan));

    // ✅ yang berubah cuma subtotal
    row.find('.subtotal-racikan').val(subtotalHargaAwal);         // subtotal_br (total)
    row.find('.subtotal-racikanl').val(subtotalHargaJual);        // subtotal_brl (total)
    row.find('.subtotallaba-racikan').val(subtotalLaba);          // subtotal_laba_br (total)
    row.find('.harga-jracikan').val(hargaJualSatuan);             // kalau ini UNIT jual (lebih konsisten)

    row.find('.subtotal-racikanl-display').val(formatRupiah(subtotalHargaJual));

    hitungTotalRacikan(racikanId);
    hitungTotal();
}


    function hitungTotalRacikan(racikanId) {
        let subtotalR = 0;
        let subtotalLabaR = 0;
        let subtotalHargaJualR = 0;

        $(`#racikan-obat-${racikanId} tr`).each(function () {
            const subtotal = parseFloat($(this).find('.subtotal-racikan').val()) || 0;
            const subtotalLaba = parseFloat($(this).find('.subtotallaba-racikan').val()) || 0;
            const subtotalHargaJual = parseFloat($(this).find('.subtotal-racikanl').val()) || 0;

            subtotalR += subtotal;
            subtotalLabaR += subtotalLaba;
            subtotalHargaJualR += subtotalHargaJual;
        });

        // Ambil jumlah racikan (berapa kali racikan ini dibuat)
        const jumlahRacikan = parseFloat($(`input[name="racikan[${racikanId}][jumlah_r]"]`).val()) || 1;

        console.log('Total per racikan:', {
            subtotalR: subtotalR,
            subtotalLabaR: subtotalLabaR,
            subtotalHargaJualR: subtotalHargaJualR,
            jumlahRacikan: jumlahRacikan
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
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>poli/umum">
                                <?php echo $title; ?>
                            </a></li>
                        <li class="breadcrumb-item active">Proses</li>
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
                                                    <!-- ambil dari rsp_registrasi -->
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
                                                    <!-- ambil dari rsp_registrasi -->
                                                </div>
                                            </div>
                                            <!-- semua ini ambil data dari rsp_registrasi -->
                                            <div class="mb-3 row">
                                                <label for="tambah_contoh" class="col-sm-4 col-form-label">Nama
                                                    Pasien</label>
                                                <div class="col-sm-8">
                                                    <input type="hidden" class="form-control" name="id" id="id"
                                                        placeholder="Id" value="<?php echo $id['id'] ?>" readonly
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
                                            <!-- untuk tab nya ini ambil data dari mst_pasien -->
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
                                                <textarea class="form-control" name="keluhan" id="keluhan" required
                                                    placeholder="Berikan keluhan yang dimiliki"><?= $row['keluhan'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="tambah_contoh" class="col-sm-2 col-form-label">Tekanan
                                                Darah</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="tekanan_darah"
                                                    id="tekanan_darah" placeholder="Tekanan Darah" required
                                                    autocomplete="off" value="<?= $row['tekanan_darah'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Suhu</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="suhu" id="suhu"
                                                placeholder="Suhu" autocomplete="off" required autocomplete="off"
                                                value="<?= $row['suhu'] ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Nadi</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="nadi" id="nadi"
                                                placeholder="Detak Nadi" required autocomplete="off"
                                                value="<?= $row['nadi'] ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Catatan</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" name="catatan" id="catatan" required
                                                placeholder="Tulis Catatan"><?= $row['catatan'] ?></textarea>
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
                                        <!-- <tbody>
                                        </tbody> -->
                                        <tbody>
                                            <?php if (!empty($diagnosa_terisi)): ?>
                                                <?php foreach ($diagnosa_terisi as $d): ?>
                                                    <tr data-diagnosa-id="<?= (int) $d['id_diagnosa'] ?>" data-type="modal">
                                                        <td>
                                                            <input type="hidden" name="diagnosa_modal[id_diagnosa][]"
                                                                class="form-control" value="<?= (int) $d['id_diagnosa'] ?>"
                                                                readonly>
                                                            <input type="text" name="diagnosa_modal[nama][]"
                                                                class="form-control"
                                                                value="<?= htmlspecialchars($d['nama_diagnosa'] ?? '', ENT_QUOTES) ?>"
                                                                readonly>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger btn-hapus-diagnosa">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
                                        <!-- <tbody>
                                        </tbody> -->
                                        <tbody>
                                            <?php if (!empty($tindakan_terisi)): ?>
                                                <?php foreach ($tindakan_terisi as $t): ?>
                                                    <tr data-tindakan-id="<?= (int) $t['id_tindakan'] ?>" data-type="modal">
                                                        <td>
                                                            <input type="hidden" name="tindakan_modal[id_tindakan][]"
                                                                class="form-control" value="<?= (int) $t['id_tindakan'] ?>"
                                                                readonly>
                                                            <input type="text" name="tindakan_modal[nama][]"
                                                                class="form-control"
                                                                value="<?= htmlspecialchars($t['nama'] ?? '', ENT_QUOTES) ?>"
                                                                readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="tindakan_modal[harga][]"
                                                                class="form-control input_htindakan"
                                                                value="<?= htmlspecialchars($t['harga'] ?? '', ENT_QUOTES) ?>"
                                                                readonly>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger btn-hapus-tindakan">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>

                                    </table>
                                    <!-- tindakan end -->
                                </div>
                                <!-- tab obat start -->
                                <div class="tab-pane fade" id="tab-obat" role="tabobat">
                                    <div class="tab-pane p-3" id="tab-obat" role="tabpanel">
                                        <h5 class="my-3">Resep Obat</h5>
                                        <button class="btn btn-primary mb-3" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalObat" onclick="resetModalObat()">
                                            <i class="fas fa-search me-2"></i>Cari Obat</button>
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
                                                        <th>Subtotal Harga</th>
                                                        <th class="text-center" width="5%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                 <tbody>
                                                    <?php $no = 0; ?>
                                                    <?php if (!empty($obat_terisi)):?>
                                                        <?php foreach ($obat_terisi as $o):
                                                            $no++; ?>
                                                            <tr id="resep-row-<?= $no ?>">
                                                                <td>
                                                                    <input type="hidden"
                                                                        name="obat[<?= $no ?>][id_obat_detail_o]"
                                                                        value="<?= $o['id_barang_detail'] ?>">
                                                                    <input type="hidden" name="obat[<?= $no ?>][id_obat_o]"
                                                                        value="<?= $o['id_barang'] ?>">

                                                                    <input type="text" class="form-control"
                                                                        name="obat[<?= $no ?>][nama_obat_o]"
                                                                        value="<?= htmlspecialchars($o['nama_barang']) ?>"
                                                                        readonly>
                                                                </td>

                                                                <td>
                                                                    <!-- dropdown awal: 1 option dulu, nanti diload options lengkap via ajax -->
                                                                    <select class="form-select select-satuan"
                                                                        name="obat[<?= $no ?>][id_satuan_o]"
                                                                        onchange="ubahSatuan(<?= $no ?>, this)"
                                                                        data-row="<?= $no ?>">
                                                                        <option value="<?= $o['id_satuan_barang'] ?>"
                                                                            data-id-barang-detail="<?= $o['id_barang_detail'] ?>"
                                                                            selected>
                                                                            <?= htmlspecialchars($o['satuan_barang']) ?>
                                                                        </option>
                                                                    </select>

                                                                    <input type="hidden" class="nama-satuan"
                                                                        name="obat[<?= $no ?>][satuan_o]"
                                                                        value="<?= htmlspecialchars($o['satuan_barang']) ?>">
                                                                    <input type="hidden" class="urutan-satuan"
                                                                        name="obat[<?= $no ?>][urutan_satuan_o]"
                                                                        value="<?= $o['urutan_satuan'] ?>">
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control jumlah"
                                                                        name="obat[<?= $no ?>][jumlah_o]"
                                                                        value="<?= (int) $o['jumlah'] ?>"
                                                                        onkeyup="hitungSubtotal(<?= $no ?>)"
                                                                        data-row="<?= $no ?>">
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        name="obat[<?= $no ?>][aturan_pakai_o]"
                                                                        value="<?= htmlspecialchars($o['aturan_pakai']) ?>">
                                                                </td>

                                                                <td><input type="text" class="form-control harga"
                                                                        name="obat[<?= $no ?>][harga_o]"
                                                                        value="<?= 'Rp ' . number_format($o['harga'], 0, '.', ',') ?>"
                                                                        readonly></td>
                                                                <td><input type="text" class="form-control laba"
                                                                        name="obat[<?= $no ?>][laba_o]"
                                                                        value="<?= 'Rp ' . number_format($o['laba'], 0, '.', ',') ?>"
                                                                        readonly></td>
                                                                <td>
                                                                    <input type="text" class="form-control subtotall"
                                                                        name="obat[<?= $no ?>][subtotal_ol]"
                                                                        value="<?= 'Rp ' . number_format($o['sub_total_harga'] + $o['sub_total_laba'], 0, '.', ',') ?>"
                                                                        readonly>
                                                                    <input type="hidden" class="form-control subtotal"
                                                                        name="obat[<?= $no ?>][subtotal_o]"
                                                                        value="<?= 'Rp ' . number_format($o['sub_total_harga'], 0, '.', ',') ?>"
                                                                        readonly>
                                                                    <input type="hidden" class="form-control sub-total-laba"
                                                                        name="obat[<?= $no ?>][subtotal_laba_o]"
                                                                        value="<?= 'Rp ' . number_format($o['sub_total_laba'], 0, '.', ',') ?>"
                                                                        readonly>
                                                                </td>

                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-danger"
                                                                        onclick="$('#resep-row-<?= $no ?>').remove(); hitungTotal()">
                                                                        <i class="far fa-trash-alt"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr class="mt-4">
                                        <h5 class="my-3">Racikan Obat</h5>
                                        <button class="btn btn-success mb-3" type="button" onclick="tambahRacikan()">
                                            <i class="fas fa-plus me-2"></i>Tambah Racikan
                                        </button>
                                        <!-- <div id="racikan_container"></div> -->
                                         <div id="racikan_container">
                                            <?php $rc = 0;
                                            $maxRc = 0; ?>
                                            <?php if (!empty($racikan_terisi)):?>
                                                <?php foreach ($racikan_terisi as $r):
                                                    $rc++; $maxRc = $rc;?>
                                                    <div class="racikan-card card border mb-3" id="racikan-<?= $rc ?>">
                                                        <div class="card-body">

                                                            <div class="mb-2 row">
                                                                <label class="col-sm-3 col-form-label">Nama Racikan</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="racikan[<?= $rc ?>][nama_r]"
                                                                        class="form-control"
                                                                        value="<?= htmlspecialchars($r['nama_racikan']) ?>"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="mb-2 row">
                                                                <label class="col-sm-3 col-form-label">Jumlah</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="racikan[<?= $rc ?>][jumlah_r]"
                                                                        class="form-control" value="<?= (int) $r['jumlah'] ?>"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="mb-2 row">
                                                                <label class="col-sm-3 col-form-label">Aturan Pakai</label>
                                                                <div class="col-sm-9">
                                                                    <textarea name="racikan[<?= $rc ?>][aturan_r]"
                                                                        class="form-control"
                                                                        required><?= htmlspecialchars($r['aturan_pakai']) ?></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="mb-2 row">
                                                                <label class="col-sm-3 col-form-label">Keterangan</label>
                                                                <div class="col-sm-9">
                                                                    <textarea name="racikan[<?= $rc ?>][keterangan_r]"
                                                                        class="form-control"><?= htmlspecialchars($r['keterangan']) ?></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex justify-content-end gap-1 mt-3">
                                                                <button type="button" class="btn btn-success btn-md"
                                                                    onclick="tambahObatracikan(<?= $rc ?>)">
                                                                    <i class="fas fa-plus"></i> Tambah Obat
                                                                </button>
                                                                <button type="button" class="btn btn-danger btn-md"
                                                                    onclick="$('#racikan-<?= $rc ?>').remove(); hitungTotal()">
                                                                    <i class="far fa-trash-alt"></i> Hapus Racikan
                                                                </button>
                                                            </div>

                                                            <div class="table-responsive mt-2">
                                                                <table class="table table-md table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nama Obat</th>
                                                                            <th width="12%">Satuan</th>
                                                                            <th width="10%">Qty</th>
                                                                            <th>Harga</th>
                                                                            <th>Laba</th>
                                                                            <th>Subtotal</th>
                                                                            <th width="5%" class="text-center">Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="racikan-obat-<?= $rc ?>">
                                                                        <?php $ob = 0; ?>
                                                                        <?php foreach (($r['obat'] ?? []) as $d):
                                                                            $ob++; ?>
                                                                            <tr id="racikan-obat-row-<?= $rc ?>-<?= $ob ?>">
                                                                                <td>
                                                                                    <input type="hidden"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][id]"
                                                                                        value="<?= $d['id_barang_detail'] ?>">
                                                                                    <input type="hidden"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][id_barang_br]"
                                                                                        value="<?= $d['id_barang'] ?>">
                                                                                    <input type="text" class="form-control"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][nama_br]"
                                                                                        value="<?= htmlspecialchars($d['nama_barang']) ?>"
                                                                                        readonly>
                                                                                </td>
                                                                                <td>
                                                                                    <select
                                                                                        class="form-select select-satuan-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][id_satuan_br]"
                                                                                        onchange="ubahSatuanRacikan(<?= $rc ?>, <?= $ob ?>, this)"
                                                                                        data-racikan="<?= $rc ?>"
                                                                                        data-obat="<?= $ob ?>">
                                                                                        <option
                                                                                            value="<?= $d['id_satuan_barang'] ?>"
                                                                                            data-id-barang-detail="<?= $d['id_barang_detail'] ?>"
                                                                                            selected>
                                                                                            <?= htmlspecialchars($d['satuan_barang']) ?>
                                                                                        </option>
                                                                                    </select>

                                                                                    <input type="hidden" class="nama-satuan-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][satuan_br]"
                                                                                        value="<?= htmlspecialchars($d['satuan_barang']) ?>">
                                                                                    <input type="hidden"
                                                                                        class="urutan-satuan-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][urutan_satuan_br]"
                                                                                        value="<?= $d['urutan_satuan'] ?>">
                                                                                </td>

                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control jumlah-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][jumlah_br]"
                                                                                        value="<?= (int) $d['jumlah'] ?>"
                                                                                        onkeyup="hitungSubtotalRacikan(<?= $rc ?>, <?= $ob ?>)"
                                                                                        data-racikan="<?= $rc ?>"
                                                                                        data-obat="<?= $ob ?>">
                                                                                </td>

                                                                                <td><input type="text"
                                                                                        class="form-control harga-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][harga_br]"
                                                                                        value="<?= 'Rp ' . number_format($d['harga'], 0, '.', ',') ?>"
                                                                                        readonly></td>
                                                                                <td><input type="text"
                                                                                        class="form-control laba-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][laba_br]"
                                                                                        value="<?= 'Rp ' . number_format($d['laba'], 0, '.', ',') ?>"
                                                                                        readonly></td>
                                                                                <!-- <td>
                                                                                    <input type="text" class="form-control"
                                                                                        name="racikan[<= $rc >][obat][<= $ob >][subtotal_br]"
                                                                                        value="<= 'Rp ' . number_format($d['sub_total_harga'], 0, '.', ',') >"
                                                                                        readonly>
                                                                                    <input type="hidden"
                                                                                        name="racikan[<= $rc >][obat][<= $ob >][subtotal_laba_br]"
                                                                                        value="<= 'Rp ' . number_format($d['sub_total_laba'], 0, '.', ',') >">
                                                                                </td> -->
                                                                                <td>
                                                                                    <!-- subtotal harga jual yang tampil -->
                                                                                    <input type="text"
                                                                                        class="form-control subtotal-racikanl"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][subtotal_brl]"
                                                                                        value="<?= 'Rp ' . number_format($d['sub_total_harga'] + $d['sub_total_laba'], 0, '.', ',') ?>"
                                                                                        readonly>

                                                                                    <!-- subtotal modal/awal (yang dipakai hitungTotalRacikan) -->
                                                                                    <input type="hidden"
                                                                                        class="form-control subtotal-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][subtotal_br]"
                                                                                        value="<?= 'Rp ' . number_format($d['sub_total_harga'], 0, '.', ',') ?>"
                                                                                        readonly>

                                                                                    <!-- subtotal laba (yang dipakai hitungTotalRacikan) -->
                                                                                    <input type="hidden"
                                                                                        class="form-control subtotallaba-racikan"
                                                                                        name="racikan[<?= $rc ?>][obat][<?= $ob ?>][subtotal_laba_br]"
                                                                                        value="<?= 'Rp ' . number_format($d['sub_total_laba'], 0, '.', ',') ?>">
                                                                                </td>

                                                                                <td class="text-center">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-danger"
                                                                                        onclick="$('#racikan-obat-row-<?= $rc ?>-<?= $ob ?>').remove(); hitungTotal(); hitungTotalRacikan()">
                                                                                        <i class="far fa-trash-alt"></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
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
                            <input type="hidden" name="kode_invoice" value="<?= $row['kode_invoice'] ?>">
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

<script>
    racikanCounter = <?= (int) $maxRc ?>;
</script>

