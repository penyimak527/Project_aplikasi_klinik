<style>
 .dropify-wrapper {
    height: 170px !important;
    width: 100% !important;
}

.dropify-render img {
    object-fit: contain !important;
    width: 100% !important;
    height: 100% !important;
}
</style>
<script>
    $(document).ready(function () {
        $('.dropify_lama').dropify({
            messages: {
                'default': 'Drag dan drop gambar kesini',
                'replace': 'Drag dan drop gambar kesini atau klik untuk mengganti gambar',
                'remove': 'Hapus',
                'error': 'Ooops, Terjadi kesalahan. Silahkan coba lagi.'
            }
        });
        $('.dropify').dropify({
            messages: {
                'default': 'Drag dan drop gambar kesini',
                'replace': 'Drag dan drop gambar kesini atau klik untuk mengganti gambar',
                'remove': 'Hapus',
                'error': 'Ooops, Terjadi kesalahan. Silahkan coba lagi.'
            }
        });
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
        getgambar();
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
        if (!validateForm('#form_tambah')) {
            btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
            return;
        }
        const formData = new FormData($('#form_tambah')[0]);
        $.ajax({
            url: '<?php echo base_url("poli/kecantikan/tambah_proses") ?>',
            method: 'POST',
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
                         btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
                        if (result.isConfirmed) {
                            console.log('Gagal mengirim datanya');
                        }
                    })
                }
            },
        });
    }
    function getgambar() {
        var imgUrl = "<?= base_url('upload/' . $row['foto']) ?>";
        let drEvent = $('#dropify_lama').data('dropify');
        if (!drEvent) return;
        drEvent.settings.defaultFile = imgUrl;
        drEvent.destroy();
        drEvent.init();
    }
    function cariDiagnosa(keyword) {
        let insertid = $('input[name="id_diagnosa[]"]').map(function () {
            return $(this).val();
        }).get();
        let count_headerd = $(`#table-data1 thead tr th`).length;
        $.ajax({
            url: '<?= base_url("poli/kecantikan/diagnosa") ?>',
            data: { caridiagnosa: keyword },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: () => {
                let loading = `<tr id="tr-loading">
                                    <td colspan="${count_headerd}" class="text-center">
                                        <div class="loader">
                                            <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                        </div>
                                    </td>
                                </tr>`;

                $(`#table-data1 tbody`).html(loading);
            },
            success: function (res) {
                let table = "";
                let filtered = res.data.filter(item => !insertid.includes(item.id_diagnosa))
                if (res.status && filtered.length > 0) {
                    let i = 1;
                    filtered.forEach(item => {
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

    function cariTindakan(keyword) {
        // console.log(keyword);
        let count_headert = $(`#table-data thead tr th`).length;
        let insert_idt = $('input[name="id_tindakan[]"]').map(function () {
            return $(this).val();
        }).get();
        $.ajax({
            url: '<?= base_url("poli/kecantikan/tindakan") ?>',
            data: {
                carit: keyword,
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: () => {
                let loading = `<tr id="tr-loading">
                                    <td colspan="${count_headert}" class="text-center">
                                        <div class="loader">
                                            <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                                        </div>
                                    </td>
                                </tr>`;

                $(`#table-data tbody`).html(loading);
            },
            success: function (res) {
                let filteredt = res.data.filter(item => !insert_idt.includes(item.id_tindakan));
                let table = "";
                if (res.status && filteredt.length > 0) {
                    let i = 1;
                    for (const item of filteredt) {
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
        let count_headerb = $('#table-data2 thead tr th').length;

        // AMBIL SEMUA ID OBAT DETAIL YANG SUDAH ADA DI TABEL RESEP
        let insert_idb = $('input[name^="obat"][name$="[id_obat_detail_o]"]').map(function () {
            return $(this).val().toString();
        }).get();
        console.log('cari barang insert nya :' + insert_idb);
        $.ajax({
            url: '<?= base_url("poli/kecantikan/obat") ?>',
            data: { carit: nama_barang },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: () => {
                let loading = `
                <tr id="tr-loading">
                    <td colspan="${count_headerb}" class="text-center">
                        <div class="loader">
                            <img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60" alt="loading">
                        </div>
                    </td>
                </tr>`;
                $('#table-data2 tbody').html(loading);
            },
            success: function (res) {
                console.log('Data obat dengan satuan terbesar:', res);

                // FILTER BARANG AGAR TIDAK MUNCUL LAGI
                let filteredb = res.data.filter(item =>
                    !insert_idb.includes(item.id_barang_detail.toString())
                );

                let table = "";
                if (res.status && filteredb.length > 0) {
                    let i = 1;

                    // GUNAKAN filteredb BUKAN res.data
                    for (const item of filteredb) {
                        table += `
                    <tr style="cursor:pointer;" onclick="pilihBarang('${btoa(JSON.stringify(item))}')">
                        <td>${i++}</td>
                        <td>${item.nama_barang}</td>
                    </tr>`;
                    }

                } else {
                    table = `<tr><td colspan="2" class="text-center">Data tidak ditemukan</td></tr>`;
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
    const itemId = item.id_barang_detail.toString();
    const racikanId = $('#modalObat').data('target-racikan');
    if (racikanId) {

        // hanya cek obat dalam racikan ini saja!
        let existingRacikanIds = $(`input[name^="racikan[${racikanId}]"][name$="[id]"]`)
            .map(function () {
                return $(this).val().toString();
            }).get();

        console.log("ID dalam racikan", racikanId, ":", existingRacikanIds);

        if (existingRacikanIds.includes(itemId)) {
            Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: "Obat ini sudah ada dalam racikan ini!"
            });
            return;
        }

        // Bila lolos, tambahkan ke racikan
        addRowToRacikan(normalize(item), racikanId);

        $('#modalObat').removeData('target-racikan');
        $('#modalObat').modal('hide');
        return;
    }

    let existingObatIds = $(`input[name^="obat"][name$="[id_obat_detail_o]"]`)
        .map(function () {
            return $(this).val().toString();
        }).get();

    if (existingObatIds.includes(itemId)) {
        Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Obat ini sudah ada dalam daftar obat!"
        });
        return;
    }

    // Bila lolos, tambahkan ke obat biasa
    tambah_obat_rsp(normalize(item));

    $('#modalObat').modal('hide');
}


/** Normalisasi data item */
function normalize(item) {
    return {
        id: item.id_barang_detail,
        id_barang: item.id_barang,
        nama_barang: item.nama_barang || item.nama_barang_master,
        satuan_barang: item.nama_satuan,
        id_satuan_barang: item.id_satuan_barang,
        urutan_satuan: item.urutan_satuan,
        harga_awal: parseFloat(item.harga_awal) || 0,
        harga_jual: parseFloat(item.harga_jual) || 0,
        laba: parseFloat(item.laba) || 0,
        kode_barang: item.kode_barang,
        isi_satuan_turunan: item.isi_satuan_turunan
    };
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
        let duplikat = false;
        $("input[name='id_tindakan[]']").each(function () {
            if ($(this).val() == item.id_tindakan) {
                duplikat = true;
            }
        });

        if (duplikat) {
            Swal.fire({
                title: "Gagal!",
                text: `Tindakan '${item.nama_tindakan}' sudah ditambahkan!`,
                icon: "error",
                confirmButtonColor: "#35baf5",
            });
            return;
        }
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
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove(); hitungTotalTindakan()">X</button>
            </td>
        </tr>
    `;
        $('#table_tindakan tbody').append(row);
        const newInput = $('#table_tindakan tbody tr:last .input_htindakan')[0];
        FormatCurrency1(newInput);
        hitungTotalTindakan();
        $('#modalTindakan').modal("hide");
    }
    function tambahT(nama_tindakan, harga_tindakan) {
        if (!nama_tindakan.trim() || !harga_tindakan.trim()) {
            Swal.fire({
                title: 'Gagal!',
                text: "Nama Tindakan dan Harga Tindakan tidak boleh kosong",
                icon: "error",
                showCancelButton: false,
                showConfirmButton: true,
                confirmButtonColor: "#35baf5",
                confirmButtonText: "Oke",
                closeOnConfirm: false,
                allowOutsideClick: false
            }).then(() => {
                $('#nama_tindakan').val('');
                $('#harga_tindakan').val('');
            });
            return;
        }

        // 2. Cek duplikat di table
        let duplikatValuet = null;
        $("#table_tindakan tbody input[name='tindakanb[]']").each(function () {
            if ($(this).val().toLowerCase() === nama_tindakan.toLowerCase()) {
                duplikatValuet = $(this).val();
            }
        });

        if (duplikatValuet !== null) {
            Swal.fire({
                title: 'Gagal!',
                text: "Tindakan '" + duplikatValuet + "' sudah ditambahkan!",
                icon: "error",
                showCancelButton: false,
                showConfirmButton: true,
                confirmButtonColor: "#35baf5",
                confirmButtonText: "Oke",
                closeOnConfirm: false,
                allowOutsideClick: false
            }).then(() => {
                $('#nama_tindakan').val('');
                $('#harga_tindakan').val('');
            });
            return;
        }
        let row = `
        <tr>
            <td>
                <input type="text" name="tindakanb[]" class="form-control" autocomplete="off" placeholder="Tindakan" value="${nama_tindakan}" readonly>
            </td>
            <td>
            
                <input type="text" name="harga_tindakanb[]" class="form-control" onkeyup="FormatCurrency(this);" autocomplete="off" placeholder="Harga" value="${harga_tindakan}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove(); hitungTotalTindakan()">X</button>
            </td>
        </tr>
    `;

        $('#table_tindakan tbody').append(row);
        $('#nama_tindakan').val('');
        $('#harga_tindakan').val('');
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
        // CEK DUPLIKAT berdasar id_diagnosa
        let duplikat = false;
        $("input[name='id_diagnosa[]']").each(function () {
            if ($(this).val() == item.id_diagnosa) {
                duplikat = true;
            }
        });

        if (duplikat) {
            Swal.fire({
                title: "Gagal!",
                text: `Diagnosa '${item.nama_diagnosa}' sudah ditambahkan!`,
                icon: "error",
                confirmButtonColor: "#35baf5",
            });
            return;
        }

        let row = `
        <tr>
            <td>
                <input type="hidden" name="id_diagnosa[]" id="id_diagnosa[]" class="form-control" value="${item.id_diagnosa}" readonly>
                <input type="text" name="diagnosa[]" class="form-control" value="${item.nama_diagnosa}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">X</button>
            </td>
        </tr>
    `;
        $("#modalDiagnosa").modal("hide");
        $('#table_diagnosa tbody').append(row);
    }
    function tambahformD(nama_diagnosa) {
        // 1. Cek jika input kosong
        if (!nama_diagnosa.trim()) {
            console.log("Nama diagnosa tidak boleh kosong");
            Swal.fire({
                title: 'Gagal!',
                text: "Inputan Diagnosa Kosong",
                icon: "error",
                showCancelButton: false,
                showConfirmButton: true,
                confirmButtonColor: "#35baf5",
                confirmButtonText: "Oke",
                closeOnConfirm: false,
                allowOutsideClick: false
            });
            return;
        }

        // 2. Cek duplikat di table
        let duplikatValue = null;
        $("#table_diagnosa tbody input[name='diagnosab[]']").each(function () {
            if ($(this).val().toLowerCase() === nama_diagnosa.toLowerCase()) {
                duplikatValue = $(this).val();
            }
        });

        if (duplikatValue !== null) {
            Swal.fire({
                title: 'Gagal!',
                text: "Diagnosa '" + duplikatValue + "' sudah ditambahkan!",
                icon: "error",
                showCancelButton: false,
                showConfirmButton: true,
                confirmButtonColor: "#35baf5",
                confirmButtonText: "Oke",
                closeOnConfirm: false,
                allowOutsideClick: false
            }).then(() => {
                $('#nama_diagnosa').val('');
            });

            return;
        }

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

        $('#nama_diagnosa').val('');
    }

    function tambah_obat_rsp(item) {
        event.preventDefault();

        // Hitung nomor urut
        const rowCount = $('#table_resep tbody tr').length;
        const newRowId = rowCount + 1;

        // Load opsi satuan untuk obat ini
        muatSatuanObat(item.id_barang, item, newRowId);
    }
    function muatSatuanObat(id_barang, baseItem, rowId) {
        $.ajax({
            url: '<?= base_url('poli/kecantikan/get_satuan_by_barang/') ?>' + id_barang,
            type: 'GET',
            success: function (response) {
                if (response.status && response.data.length > 0) {
                    tambahRowobat(response.data, baseItem, rowId);
                } else {
                    // Fallback: gunakan data awal jika gagal load satuan
                    createObatRowFallback(baseItem, rowId);
                }
            },
            error: function () {
                // Fallback jika error
                createObatRowFallback(baseItem, rowId);
            }
        });
    }

    // Function baru: Buat row dengan dropdown satuan
    function tambah_obat_rsp(item) {
        event.preventDefault();

        // Hitung nomor urut
        const rowCount = $('#table_resep tbody tr').length;
        const newRowId = rowCount + 1;

        // BUAT DROPDOWN MANUAL DULU, KEMUDIAN LOAD DATA REAL
        buatRowDenganDropdownManual(item, newRowId);
    }

    function buatRowDenganDropdownManual(item, rowId) {
        // console.log(' Membuat row dengan dropdown manual untuk:', item.nama_barang);

        // Buat dropdown sementara dengan data dari modal
        const satuanDropdown = `
        <select class="form-select select-satuan" name="obat[${rowId}][id_satuan_o]" 
                onchange="ubahSatuanManual(${rowId}, this)" data-row="${rowId}">
            <option value="${item.id}" selected 
                    data-harga-awal="${item.harga_awal}"
                    data-harga-jual="${item.harga_jual}"
                    data-laba="${item.laba}"
                    data-stok="${item.stok || 100}"
                    data-urutan="${item.urutan_satuan}"
                    data-satuan="${item.satuan_barang}">
                ${item.satuan_barang}
            </option>
            <option value="loading" disabled>Loading satuan lainnya...</option>
        </select>
    `;
    
        const hargaJual = parseFloat(item.harga_awal) + parseFloat(item.laba);
        const newRow = `
<tr id="resep-row-${rowId}">
    <td>
        <input type="hidden" name="obat[${rowId}][id_obat_detail_o]" value="${item.id}" class="id-barang-detail">
        <input type="hidden" name="obat[${rowId}][id_obat_o]" value="${item.id_barang}" class="id-barang">
        <input type="hidden" class="form-control harga_jual" name="obat[${rowId}][harga_jual_o]" value="${formatRupiah(hargaJual)}" readonly>
        <input type="hidden" class="form-control sub-total-laba" name="obat[${rowId}][subtotal_laba_o]" value="${formatRupiah(item.laba)}" readonly>
        <input type="text" class="form-control" name="obat[${rowId}][nama_obat_o]" value="${item.nama_barang}" readonly>
    </td>
    <td>
        <!-- DROPDOWN YANG SUDAH BERHASIL -->
        ${satuanDropdown}
        <input type="hidden" class="form-control nama-satuan" name="obat[${rowId}][satuan_o]" value="${item.satuan_barang}" readonly>
        <input type="hidden" class="form-control urutan-satuan" name="obat[${rowId}][urutan_satuan_o]" value="${item.urutan_satuan}" readonly>
    </td>
    <td>
        <input type="text" class="form-control jumlah" name="obat[${rowId}][jumlah_o]" min="1" value="1" 
               onkeyup="hitungSubtotal(${rowId})" data-row="${rowId}" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </td>
    <td>
        <input type="text" class="form-control" name="obat[${rowId}][aturan_pakai_o]" placeholder="Aturan pakai" autocomplete="off" required>
    </td>
    <td>
        <input type="text" class="form-control harga" name="obat[${rowId}][harga_o]" 
               value="${formatRupiah(item.harga_awal)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control laba" name="obat[${rowId}][laba_o]" 
               value="${formatRupiah(item.laba)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control subtotall" name="obat[${rowId}][subtotal_ol]" 
               value="${formatRupiah(hargaJual)}" readonly>
        <input type="hidden" class="form-control subtotal" name="obat[${rowId}][subtotal_o]" 
               value="${formatRupiah(item.harga_awal)}" readonly>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger" 
                onclick="$('#resep-row-${rowId}').remove(); hitungTotal()">
            <i class="far fa-trash-alt"></i>
        </button>
    </td>
    <input type="hidden" class="form-control sub-total-laba-harga" name="obat[${rowId}][subtotal_laba_harga_o]" 
           value="${formatRupiah(hargaJual)}">
</tr>`;

        $('#table_resep tbody').append(newRow);
        // console.log(' Row dengan dropdown berhasil ditambahkan');
        hitungTotal();

        // SETELAH ROW DIBUAT, LOAD DATA SATUAN ASLI DARI DATABASE
        muatSatuanObatAsli(item.id_barang, rowId, item.id);
    }

    // LOAD DATA SATUAN ASLI DARI DATABASE
    function muatSatuanObatAsli(id_barang, rowId, currentSatuanId) {
        // console.log(' Loading data satuan asli untuk barang:', id_barang);

        $.ajax({
            url: '<?= base_url('poli/kecantikan/get_satuan_by_barang/') ?>' + id_barang,
            type: 'GET',
            dataType: 'JSON',
            success: function (response) {
                if (response.status && response.data && response.data.length > 0) {
                    updateDropdownDenganDataAsli(response.data, rowId, currentSatuanId);
                } else {
                    console.log(' Tidak ada data satuan tambahan');
                }
            },
            error: function (xhr, status, error) {
                console.error(' Error load satuan asli:', error);
            }
        });
    }

    // UPDATE DROPDOWN DENGAN DATA ASLI DARI DATABASE
    function updateDropdownDenganDataAsli(satuanList, rowId, currentSatuanId) {
        var row = $('#resep-row-' + rowId);
        var selectElement = row.find('.select-satuan');

        // console.log(' Update dropdown dengan data asli:', satuanList);
        // console.log('Current satuan ID:', currentSatuanId);

        // Kosongkan dropdown
        selectElement.empty();

        // Tambahkan options baru dari data real
        satuanList.forEach(function (satuan) {
            var selected = satuan.id_barang_detail == currentSatuanId ? 'selected' : '';
            var optionText = satuan.satuan_barang;

            // Tambahkan info stok jika ada
            // if (satuan.stok && satuan.stok > 0) {
            //     optionText += ' (Stok: ' + satuan.stok + ')';
            // }

            selectElement.append(
                $('<option>', {
                    value: satuan.id_barang_detail,
                    selected: selected,
                    'data-harga-awal': satuan.harga_awal,
                    'data-harga-jual': satuan.harga_jual,
                    'data-laba': satuan.laba,
                    // 'data-stok': satuan.stok || 0,
                    'data-urutan': satuan.urutan_satuan,
                    'data-satuan': satuan.satuan_barang,
                    text: optionText
                })
            );
        });

        console.log(' Dropdown updated dengan ' + satuanList.length + ' pilihan satuan');
    }

    // FUNCTION UNTUK UBAH SATUAN (VERSI FINAL)
    function ubahSatuanManual(rowId, selectElement) {
        var selectedOption = $(selectElement).find('option:selected');
        var selectedValue = selectedOption.val();

        // console.log(' Ubah satuan ke:', selectedValue);
        // console.log('Data selected:', selectedOption.data());

        // Jika memilih satuan yang berbeda, ambil data detail dari server
        if (selectedValue && selectedValue !== 'loading') {
            $.ajax({
                url: '<?= base_url('poli/kecantikan/get_satuan_detail/') ?>' + selectedValue,
                type: 'GET',
                dataType: 'JSON',
                success: function (response) {
                    if (response.status) {
                        updateRowWithNewSatuanobat(rowId, response.data);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error load detail satuan:', error);
                    // Fallback: update dari data attributes
                    updateFromDataAttributes(rowId, selectedOption);
                }
            });
        }
    }

    // UPDATE ROW DENGAN DATA BARU DARI SERVER
    function updateRowWithNewSatuanobat(rowId, satuanData) {
        var row = $('#resep-row-' + rowId);

        // console.log(' Update row dengan data baru:', satuanData);

        // Update semua field dengan data baru
        row.find('.id-barang-detail').val(satuanData.id_barang_detail);
        row.find('.urutan-satuan').val(satuanData.urutan_satuan);
        row.find('.nama-satuan').val(satuanData.satuan_barang);

        // Update tampilan harga
        row.find('.harga').val(formatRupiah(satuanData.harga_awal));
        row.find('.laba').val(formatRupiah(satuanData.laba));

        const hargaJual = parseFloat(satuanData.harga_awal) + parseFloat(satuanData.laba);
        row.find('.harga_jual').val(formatRupiah(hargaJual));
        row.find('.subtotall').val(formatRupiah(hargaJual));
        row.find('.sub-total-laba-harga').val(formatRupiah(hargaJual));

        // Update stok maksimal
        // row.find('.jumlah').attr('max', satuanData.stok || 0);
        // row.find('.jumlah').next('small').text('Stok: ' + (satuanData.stok || 0));

        // Hitung ulang subtotal
        hitungSubtotal(rowId);

        // console.log(' Satuan berhasil diubah ke:', satuanData.satuan_barang);
    }

    // FALLBACK: UPDATE DARI DATA ATTRIBUTES
    function updateFromDataAttributes(rowId, selectedOption) {
        var row = $('#resep-row-' + rowId);

        var hargaAwal = parseFloat(selectedOption.data('harga-awal')) || 0;
        var laba = parseFloat(selectedOption.data('laba')) || 0;
        var hargaJual = hargaAwal + laba;
        // var stok = parseInt(selectedOption.data('stok')) || 0;
        var namaSatuan = selectedOption.data('satuan') || '';

        // Update hidden fields
        row.find('.id-barang-detail').val(selectedOption.val());
        row.find('.nama-satuan').val(namaSatuan);
        row.find('.urutan-satuan').val(selectedOption.data('urutan'));

        // Update tampilan
        row.find('.harga').val(formatRupiah(hargaAwal));
        row.find('.laba').val(formatRupiah(laba));
        row.find('.harga_jual').val(formatRupiah(hargaJual));
        row.find('.subtotall').val(formatRupiah(hargaJual));
        row.find('.sub-total-laba-harga').val(formatRupiah(hargaJual));

        // Update stok
        // row.find('.jumlah').attr('max', stok);
        // row.find('.jumlah').next('small').text('Stok: ' + stok);

        // Hitung ulang
        hitungSubtotal(rowId);

        // console.log(' Satuan berhasil diubah (fallback):', namaSatuan);
    }

    // Function fallback: Jika gagal load satuan, gunakan data awal
    function createObatRowFallback(item, rowId) {
        const hargaJual = parseFloat(item.harga_awal) + parseFloat(item.laba);

        // Kembali ke cara lama (tanpa dropdown)
        const newRow = `
<tr id="resep-row-${rowId}">
    <td>
        <input type="hidden" name="obat[${rowId}][id_obat_detail_o]" value="${item.id}">
        <input type="hidden" name="obat[${rowId}][id_obat_o]" value="${item.id_barang}">
        <input type="hidden" class="form-control harga_jual" name="obat[${rowId}][harga_jual_o]" value="${formatRupiah(item.harga_jual)}" readonly>
        <input type="hidden" class="form-control sub-total-laba" name="obat[${rowId}][subtotal_laba_o]" value="${formatRupiah(item.laba)}" readonly>
        <input type="text" class="form-control" name="obat[${rowId}][nama_obat_o]" value="${item.nama_barang}" readonly>
    </td>
    <td>
        <input type="hidden" class="form-control" name="obat[${rowId}][id_satuan_o]" value="${item.id_satuan_barang}" readonly>
        <input type="text" class="form-control" name="obat[${rowId}][satuan_o]" value="${item.satuan_barang}" readonly>
        <input type="hidden" class="form-control" name="obat[${rowId}][urutan_satuan_o]" value="${item.urutan_satuan}" readonly>
    </td>
    <td>
        <input type="text" class="form-control jumlah" name="obat[${rowId}][jumlah_o]" min="1" value="1" 
               onkeyup="hitungSubtotal(${rowId})" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </td>
    <td>
        <input type="text" class="form-control" name="obat[${rowId}][aturan_pakai_o]" placeholder="Aturan pakai" autocomplete="off" required>
    </td>
    <td>
        <input type="text" class="form-control harga" name="obat[${rowId}][harga_o]" 
               value="${formatRupiah(item.harga_awal)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control laba" name="obat[${rowId}][laba_o]" 
               value="${formatRupiah(item.laba)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control subtotall" name="obat[${rowId}][subtotal_ol]" 
               value="${formatRupiah(item.harga_jual)}" readonly>
        <input type="hidden" class="form-control subtotal" name="obat[${rowId}][subtotal_o]" 
               value="${formatRupiah(item.harga_awal)}" readonly>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger" 
                onclick="$('#resep-row-${rowId}').remove(); hitungTotal()">
            <i class="far fa-trash-alt"></i>
        </button>
    </td>
    <input type="hidden" class="form-control sub-total-laba-harga" name="obat[${rowId}][subtotal_laba_harga_o]" 
           value="${formatRupiah(item.harga_jual)}">
</tr>`;

        $('#table_resep tbody').append(newRow);
        hitungTotal();
    }
    // Function untuk mengubah satuan
    function ubahSatuan(rowId, idBarangDetail) {
        $.ajax({
            url: '<?= base_url('poli/kecantikan/get_satuan_detail') ?>' + idBarangDetail,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    updateRowWithNewSatuanobat(rowId, response.data);
                }
            }
        });
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

   
    function hitungTotal() {
    let totalObatHarga = 0;
    let totalObatLaba = 0;
    let totalObatHargaLaba = 0;
    
    let totalRacikanHarga = 0;
    let totalRacikanLaba = 0;
    let totalRacikanHargaLaba = 0;
    
    let totaltindakan = parseFloat($('#total_tindakan_hidden').val()) || 0;

    // Hitung total dari obat biasa
    $('[id^="resep-row-"]').each(function () {
        const subtotalHarga = parseFloat($(this).find('.subtotal').val().replace(/[^0-9]/g, '')) || 0;
        const subtotalLaba = parseFloat($(this).find('.sub-total-laba').val().replace(/[^0-9]/g, '')) || 0;
        
        totalObatHarga += subtotalHarga;
        totalObatLaba += subtotalLaba;
        totalObatHargaLaba += (subtotalHarga + subtotalLaba);
    });

    // Hitung total dari racikan - PERBAIKAN: KALIKAN DENGAN JUMLAH RACIKAN
    $('[id^="racikan-"]').each(function() {
        const racikanId = $(this).attr('id').replace('racikan-', '');
        let racikanHarga = 0;
        let racikanLaba = 0;
        let racikanHargaLaba = 0;

        // Hitung total dari semua barang dalam racikan
        $(`#racikan-obat-${racikanId} tr`).each(function() {
            const subtotalHarga = parseFloat($(this).find('.subtotal-racikan').val().replace(/[^0-9]/g, '')) || 0;
            const subtotalLaba = parseFloat($(this).find('.subtotallaba-racikan').val().replace(/[^0-9]/g, '')) || 0;
            
            racikanHarga += subtotalHarga;
            racikanLaba += subtotalLaba;
            racikanHargaLaba += (subtotalHarga + subtotalLaba);
        });

        // DAPATKAN JUMLAH RACIKAN (berapa kali racikan ini dibuat)
        const jumlahRacikan = parseFloat($(`input[name="racikan[${racikanId}][jumlah_r]"]`).val()) || 1;
        
        console.log(`Racikan ${racikanId}: Total barang = ${racikanHargaLaba}, Jumlah racikan = ${jumlahRacikan}`);

        // KALIKAN DENGAN JUMLAH RACIKAN - INI YANG TERLEWAT!
        totalRacikanHarga += (racikanHarga * jumlahRacikan);
        totalRacikanLaba += (racikanLaba * jumlahRacikan);
        totalRacikanHargaLaba += (racikanHargaLaba * jumlahRacikan);
    });

    // Grand Total
    const grandTotalHarga = totalObatHarga + totalRacikanHarga;
    const grandTotalLaba = totalObatLaba + totalRacikanLaba;
    const grandTotalHargaLaba = totalObatHargaLaba + totalRacikanHargaLaba;
    
    const totalSemua = totaltindakan + grandTotalHargaLaba;

    // Update tampilan
    $('#harga_tindakan_all').text(formatRupiah(totaltindakan));
    $('#harga_total').text(formatRupiah(grandTotalHargaLaba));
    $('#all_uang').val(grandTotalHargaLaba);
    $('#tobat1').text(formatRupiah(totalSemua));
    $('#tobat').val(totalSemua);
    
    console.log("=== DEBUG TOTAL ===");
    console.log("Total Obat (Harga): " + totalObatHarga);
    console.log("Total Obat (Laba): " + totalObatLaba);
    console.log("Total Racikan (Harga): " + totalRacikanHarga);
    console.log("Total Racikan (Laba): " + totalRacikanLaba);
    console.log("Grand Total (Harga+Laba): " + grandTotalHargaLaba);
    console.log("Total Tindakan: " + totaltindakan);
    console.log("Total Semua: " + totalSemua);
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
    function addRowToRacikan(item, racikanId) {
        console.log(' Tambah obat ke racikan:', racikanId, item);

        // LOAD SATUAN UNTUK OBAT DI RACIKAN - SAMA SEPERTI OBAT BIASA
        muatSatuanUntukRacikan(item.id_barang, item, racikanId);
    }
    function muatSatuanUntukRacikan(id_barang, baseItem, racikanId) {
        console.log(' Muat satuan untuk racikan:', racikanId);

        $.ajax({
            url: '<?= base_url('poli/kecantikan/get_satuan_by_barang/') ?>' + id_barang,
            type: 'GET',
            dataType: 'JSON',
            success: function (response) {
                console.log(' Response satuan untuk racikan:', response);

                if (response.status && response.data && response.data.length > 0) {
                    buatRowRacikanDenganDropdown(response.data, baseItem, racikanId);
                } else {
                    buatRowRacikanFallback(baseItem, racikanId);
                }
            },
            error: function (xhr, status, error) {
                console.error(' Error load satuan untuk racikan:', error);
                buatRowRacikanFallback(baseItem, racikanId);
            }
        });
    }
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
                <input type="text" name="racikan[${racikanCounter}][jumlah_r]" 
                       class="form-control" placeholder="Jumlah" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"  value="1"required>
            </div>
        </div>
        <div class="mb-2 row">
            <label class="col-sm-3 col-form-label">Aturan Pakai</label>
            <div class="col-sm-9">
            <textarea type="text" name="racikan[${racikanCounter}][aturan_r]" 
                       class="form-control" placeholder="Aturan Pakai" required></textarea>
                
            </div>
        </div>
        <div class="mb-2 row">
            <label class="col-sm-3 col-form-label">Keterangan</label>
            <div class="col-sm-9">
            <textarea type="text" name="racikan[${racikanCounter}][keterangan_r]" 
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
                        <th width="12%">Satuan</th>
                        <th width="10%">Qty</th>
                        <th>Harga</th>
                        <th>Laba</th>
                        <th>Subtotal Harga</th>
                        <th width="5%">Aksi</th>
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
// <input type="text" name="racikan[${racikanCounter}][aturan_r]" 
//                        class="form-control" placeholder="Aturan Pakai" required>
// <input type="text" name="racikan[${racikanCounter}][keterangan_r]" 
//                        class="form-control" placeholder="Keterangan">
    function buatRowRacikanDenganDropdown(satuanList, baseItem, racikanId) {
        const rowCount = $(`#racikan-obat-${racikanId} tr`).length;
        const newRowId = rowCount + 1;

        // CARI SATUAN AKTIF - SAMA SEPERTI OBAT BIASA
        var activeSatuan = satuanList.find(satuan =>
            satuan.id_barang_detail == baseItem.id
        ) || satuanList[0];

        // GENERATE DROPDOWN - SAMA SEPERTI OBAT BIASA
        var satuanDropdown = generateDropdownUntukRacikan(satuanList, activeSatuan.id_barang_detail, racikanId, newRowId);

        const hargaJual = parseFloat(activeSatuan.harga_awal) + parseFloat(activeSatuan.laba);

        const newRow = `
<tr id="racikan-obat-row-${racikanId}-${newRowId}">
    <td>
        <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id]" value="${activeSatuan.id_barang_detail}" class="id-barang-detail-racikan">
        <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id_barang_br]" value="${activeSatuan.id_barang}" class="id-barang-racikan">
        <input type="hidden" class="form-control harga-jracikan" name="racikan[${racikanId}][obat][${newRowId}][harga_jbr]" value="${formatRupiah(hargaJual)}" readonly>
        <input type="hidden" class="form-control subtotallaba-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_laba_br]" value="${formatRupiah(activeSatuan.laba)}" readonly>
        <input type="text" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][nama_br]" value="${activeSatuan.nama_barang}" readonly>
    </td>
    <td>
        <!-- DROPDOWN SATUAN - SAMA SEPERTI OBAT BIASA -->
        ${satuanDropdown}
        <input type="hidden" class="form-control nama-satuan-racikan" name="racikan[${racikanId}][obat][${newRowId}][satuan_br]" value="${activeSatuan.satuan_barang}" readonly>
        <input type="hidden" class="form-control urutan-satuan-racikan" name="racikan[${racikanId}][obat][${newRowId}][urutan_satuan_br]" value="${activeSatuan.urutan_satuan}" readonly>
    </td>
    <td>
        <input type="text" class="form-control jumlah-racikan" name="racikan[${racikanId}][obat][${newRowId}][jumlah_br]"  value="1" 
               onkeyup="hitungSubtotalRacikan(${racikanId}, ${newRowId})" data-racikan="${racikanId}" data-obat="${newRowId}" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </td>
    <td>
        <input type="text" class="form-control harga-racikan" name="racikan[${racikanId}][obat][${newRowId}][harga_br]" 
               value="${formatRupiah(activeSatuan.harga_awal)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control laba-racikan" name="racikan[${racikanId}][obat][${newRowId}][laba_br]" 
               value="${formatRupiah(activeSatuan.laba)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control subtotal-racikanl" name="racikan[${racikanId}][obat][${newRowId}][subtotal_brl]" 
               value="${formatRupiah(hargaJual)}" readonly>
        <input type="hidden" class="form-control subtotal-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_br]" 
               value="${formatRupiah(activeSatuan.harga_awal)}" readonly>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger" 
                onclick="hapusObatDariRacikan(${racikanId}, ${newRowId})">
            <i class="far fa-trash-alt"></i>
        </button>
    </td>
    <input type="hidden" class="form-control subtotalabaharga-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_laba_harga_br]" 
           value="${formatRupiah(hargaJual)}">
</tr>`;

        $(`#racikan-obat-${racikanId}`).append(newRow);
        hitungTotalRacikan(racikanId);
        console.log(' Obat berhasil ditambahkan ke racikan dengan dropdown');
    }

    function generateDropdownUntukRacikan(satuanList, selectedId, racikanId, rowId) {
        var options = '';

        var sortedSatuanList = satuanList.sort(function (a, b) {
            return (a.urutan_satuan || 0) - (b.urutan_satuan || 0);
        });

        sortedSatuanList.forEach(function (satuan) {
            var selected = satuan.id_satuan_barang == selectedId ? 'selected' : '';
            options += `<option value="${satuan.id_satuan_barang}" ${selected}
                data-harga-awal="${satuan.harga_awal}"
                data-harga-jual="${satuan.harga_jual}"
                data-laba="${satuan.laba}"
                data-urutan="${satuan.urutan_satuan}"
                data-stok="${satuan.stok || 0}"
                data-satuan="${satuan.satuan_barang}"
                data-id-barang-detail="${satuan.id_barang_detail}"> <!-- SIMPAN ID BARANG DETAIL -->
                ${satuan.satuan_barang}
            </option>`;
        });

        return `
    <select class="form-select select-satuan-racikan" 
            name="racikan[${racikanId}][obat][${rowId}][id_satuan_br]"
            onchange="ubahSatuanRacikan(${racikanId}, ${rowId}, this)" 
            data-racikan="${racikanId}" data-obat="${rowId}">
        ${options}
    </select>
`;
    }
    // 2. TAMPILKAN MODAL UNTUK TAMBAH OBAT KE RACIKAN (TIDAK PERLU DIUBAH)
    function tambahObatracikan(racikanId) {
        $('#modalObat').data('target-racikan', racikanId).modal('show');
    }

    // 3. TAMBAH OBAT KE RACIKAN (SUDAH DIMODIFIKASI DENGAN DROPDOWN)
    function addRowToRacikan(item, racikanId) {
        console.log(' Tambah obat ke racikan:', racikanId, item);
        muatSatuanUntukRacikan(item.id_barang, item, racikanId);
    }
    function ubahSatuanRacikan(racikanId, obatId, selectElement) {
        console.log(' Ubah satuan racikan:', racikanId, 'obat:', obatId);

        // DAPATKAN SELECTED OPTION DAN DATA ATTRIBUTES
        var selectedOption = $(selectElement).find('option:selected');
        var idSatuanBarang = selectedOption.val(); // id_satuan_barang dari value
        var idBarangDetail = selectedOption.data('id-barang-detail'); // id_barang_detail dari attribute

        console.log('id_satuan_barang:', idSatuanBarang);
        console.log('id_barang_detail:', idBarangDetail);

        if (!idBarangDetail) {
            console.error(' id_barang_detail tidak ditemukan');
            return;
        }

        // GUNAKAN id_barang_detail UNTUK AJAX (karena controller butuh ini)
        $.ajax({
            url: '<?= base_url('poli/kecantikan/get_satuan_detail/') ?>' + idBarangDetail,
            type: 'GET',
            dataType: 'JSON',
            success: function (response) {
                console.log('Response detail satuan:', response);
                if (response.status) {
                    updateSatuanRacikan(racikanId, obatId, response.data);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error load detail satuan racikan:', error);
                updateSatuanRacikanFromAttributes(racikanId, obatId, idBarangDetail);
            }
        });
    }

    function updateSatuanRacikan(racikanId, obatId, satuanData) {
        var row = $(`#racikan-obat-row-${racikanId}-${obatId}`);

        console.log(' Update satuan racikan:', satuanData);

        // UPDATE SEMUA FIELD - SAMA SEPERTI OBAT BIASA
        row.find('.id-barang-detail-racikan').val(satuanData.id_barang_detail);
        row.find('.urutan-satuan-racikan').val(satuanData.urutan_satuan);
        row.find('.nama-satuan-racikan').val(satuanData.satuan_barang);

        // UPDATE TAMPILAN HARGA
        row.find('.harga-racikan').val(formatRupiah(satuanData.harga_awal));
        row.find('.laba-racikan').val(formatRupiah(satuanData.laba));

        const hargaJual = parseFloat(satuanData.harga_awal) + parseFloat(satuanData.laba);
        row.find('.harga-jracikan').val(formatRupiah(hargaJual));
        row.find('.subtotal-racikanl').val(formatRupiah(hargaJual));
        row.find('.subtotalabaharga-racikan').val(formatRupiah(hargaJual));

        // UPDATE STOK
        // row.find('.jumlah').attr('max', satuanData.stok || 0);
        // row.find('.jumlah').next('small').text('Stok: ' + (satuanData.stok || 0));

        // UPDATE SELECTED OPTION
        row.find('.select-satuan-racikan option').removeAttr('selected');
        row.find('.select-satuan-racikan option[value="' + satuanData.id_barang_detail + '"]').attr('selected', true);

        // HITUNG ULANG
        hitungSubtotalRacikan(racikanId, obatId);

        console.log(' Satuan racikan berhasil diubah ke:', satuanData.satuan_barang);
    }

    function updateSatuanRacikanFromAttributes(racikanId, obatId, idBarangDetail) {
        var row = $(`#racikan-obat-row-${racikanId}-${obatId}`);
        var selectElement = row.find('.select-satuan-racikan');
        var selectedOption = selectElement.find('option[data-id-barang-detail="' + idBarangDetail + '"]');

        if (selectedOption.length > 0) {
            var hargaAwal = parseFloat(selectedOption.data('harga-awal')) || 0;
            var laba = parseFloat(selectedOption.data('laba')) || 0;
            var hargaJual = hargaAwal + laba;
            // var stok = parseInt(selectedOption.data('stok')) || 0;
            var namaSatuan = selectedOption.data('satuan') || '';

            // UPDATE DARI DATA ATTRIBUTES
            row.find('.id-barang-detail-racikan').val(idBarangDetail);
            row.find('.nama-satuan-racikan').val(namaSatuan);
            row.find('.urutan-satuan-racikan').val(selectedOption.data('urutan'));

            row.find('.harga-racikan').val(formatRupiah(hargaAwal));
            row.find('.laba-racikan').val(formatRupiah(laba));
            row.find('.harga-jracikan').val(formatRupiah(hargaJual));
            row.find('.subtotal-racikanl').val(formatRupiah(hargaJual));
            row.find('.subtotalabaharga-racikan').val(formatRupiah(hargaJual));

            // row.find('.jumlah').attr('max', stok);
            // row.find('.jumlah').next('small').text('Stok: ' + stok);

            hitungSubtotalRacikan(racikanId, obatId);
        }
    } function hapusObatDariRacikan(racikanId, obatId) {
        $(`#racikan-obat-row-${racikanId}-${obatId}`).remove();
        hitungTotalRacikan(racikanId);
        console.log(' Obat dihapus dari racikan:', racikanId, obatId);
    }
    function buatRowRacikanFallback(baseItem, racikanId) {
        const rowCount = $(`#racikan-obat-${racikanId} tr`).length;
        const newRowId = rowCount + 1;

        const hargaJual = parseFloat(baseItem.harga_awal) + parseFloat(baseItem.laba);

        const newRow = `
<tr id="racikan-obat-row-${racikanId}-${newRowId}">
    <td>
        <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id]" value="${baseItem.id}">
        <input type="hidden" name="racikan[${racikanId}][obat][${newRowId}][id_barang_br]" value="${baseItem.id_barang}">
        <input type="hidden" class="form-control subtotallaba-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_laba_br]" value="${baseItem.laba}" readonly>
        <input type="hidden" class="form-control harga-jracikan" name="racikan[${racikanId}][obat][${newRowId}][harga_jbr]" value="${baseItem.harga_jual}" readonly>
        <input type="text" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][nama_br]" value="${baseItem.nama_barang}" readonly>
    </td>
    <td>
        <input type="hidden" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][id_satuan_br]" value="${baseItem.id_satuan_barang}">
        <input type="text" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][satuan_br]" value="${baseItem.satuan_barang}" readonly>
        <input type="hidden" class="form-control" name="racikan[${racikanId}][obat][${newRowId}][urutan_satuan_br]" value="${baseItem.urutan_satuan}">
    </td>
    <td>
        <input type="text" class="form-control jumlah-racikan" name="racikan[${racikanId}][obat][${newRowId}][jumlah_br]" value="1" onkeyup="hitungSubtotalRacikan(${racikanId}, ${newRowId})" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </td>
    <td>
        <input type="text" class="form-control harga-racikan" name="racikan[${racikanId}][obat][${newRowId}][harga_br]" value="${formatRupiah(baseItem.harga_awal)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control laba-racikan" name="racikan[${racikanId}][obat][${newRowId}][laba_br]" value="${formatRupiah(baseItem.laba)}" readonly>
    </td>
    <td>
        <input type="text" class="form-control subtotal-racikanl" name="racikan[${racikanId}][obat][${newRowId}][subtotal_brl]" value="${formatRupiah(baseItem.harga_jual)}" readonly>
        <input type="hidden" class="form-control subtotal-racikan" name="racikan[${racikanId}][obat][${newRowId}][subtotal_br]" value="${formatRupiah(baseItem.harga_awal)}" readonly>
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
   
function hitungSubtotalRacikan(racikanId, rowId) {
    const row = $(`#racikan-obat-row-${racikanId}-${rowId}`);
    const jumlah = parseFloat(row.find('.jumlah-racikan').val()) || 0;
    
    const harga = parseFloat(row.find('.harga-racikan').val().replace(/[^0-9]/g, '')) || 0;
    const laba = parseFloat(row.find('.laba-racikan').val().replace(/[^0-9]/g, '')) || 0;
    
    // PERHITUNGAN YANG BENAR:
    const subtotalHarga = jumlah * harga;        // Harga pokok saja
    const subtotalLaba = jumlah * laba;          // Laba saja
    const subtotalHargaPlusLaba = jumlah * (harga + laba); // Harga + Laba
    
    // UPDATE NILAI
    row.find('.subtotal-racikan').val(formatRupiah(subtotalHarga));
    row.find('.subtotallaba-racikan').val(formatRupiah(subtotalLaba));
    row.find('.subtotal-racikanl').val(formatRupiah(subtotalHargaPlusLaba));
    row.find('.subtotalabaharga-racikan').val(formatRupiah(subtotalHargaPlusLaba));
 hitungTotalRacikan(racikanId);
}
    function hitungTotalRacikan(racikanId) {
    let totalHargaRacikan = 0;
    let totalLabaRacikan = 0;
    let totalHargaLabaRacikan = 0;

    $(`#racikan-obat-${racikanId} tr`).each(function() {
        const subtotalHarga = parseFloat($(this).find('.subtotal-racikan').val().replace(/[^0-9]/g, '')) || 0;
        const subtotalLaba = parseFloat($(this).find('.subtotallaba-racikan').val().replace(/[^0-9]/g, '')) || 0;
        
        totalHargaRacikan += subtotalHarga;
        totalLabaRacikan += subtotalLaba;
        totalHargaLabaRacikan += (subtotalHarga + subtotalLaba);
    });

    // Simpan total per racikan jika diperlukan
    $(`#racikan-${racikanId}`).data('total-harga', totalHargaRacikan);
    $(`#racikan-${racikanId}`).data('total-laba', totalLabaRacikan);
    $(`#racikan-${racikanId}`).data('total-harga-laba', totalHargaLabaRacikan);

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
                                                <textarea class="form-control" name="keluhan" id="keluhan" required
                                                    placeholder="Berikan keluhan yang dimiliki"><?php echo $row['keluhan'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="tambah_contoh" class="col-sm-2 col-form-label">Jenis
                                                treatment</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="jenis_treatment"
                                                    id="jenis_treatment" placeholder="Jenis treatment" required
                                                    autocomplete="off" value="<?php echo $row['jenis_treatment'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Riwayat
                                            Alergi</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="r_alergi" id="r_alergi"
                                                placeholder="Riwayat alergi"
                                                value="<?php echo $row['riwayat_alergi'] ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Produk
                                            Digunakan</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="produk_digunakan"
                                                id="produk_digunakan" placeholder="Produk digunakan" autocomplete="off"
                                                value="<?php echo $row['produk_digunakan'] ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Hasil
                                            Perawatan</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="hasil_perawatan"
                                                id="hasil_perawatan" placeholder="Hasil perawatan" required
                                                autocomplete="off" value="<?php echo $row['hasil_perawatan'] ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="tambah_contoh" class="col-sm-2 col-form-label">Status Foto</label>
                                        <div class="col-sm-10">
                                            <select name="status_foto" id="status_foto" class="form-select" required>
                                                <option value="">Pilih Status Foto</option>
                                                <option value="Sebelum">Sebelum</option>
                                                <option value="Sesudah">Sesudah</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
    <label class="col-sm-2 col-form-label">Upload Foto</label>

    <div class="col-sm-10">
        <div class="row">
            <?php if (!empty($row['foto'])) { ?>
            <div class="col-md-6">
                <input type="file" 
                       class="dropify_lama" 
                       data-height="250" 
                       name="foto_lama"
                       id="dropify_lama" 
                       data-max-file-size="2M" 
                       data-show-remove="false"
                       data-allowed-file-extensions="jpg png jpeg" 
                       disabled />
            </div>
            <?php } ?>

            <div class="col-md-6">
                <input type="file" 
                       class="dropify" 
                       data-height="250" 
                       name="upload_foto"
                       id="upload_foto" 
                       data-max-file-size="2M"
                       data-allowed-file-extensions="jpg png jpeg" 
                       required />
            </div>
        </div>
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
                                                        <th width="10%">Qty</th>
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
                <input type="text" class="form-control" name="nama_tindakan" id="nama_tindakanc"
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
                    <i class="far fa-window-close"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>