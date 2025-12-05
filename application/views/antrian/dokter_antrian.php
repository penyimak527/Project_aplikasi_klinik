<script>
  $(document).ready(function () {
    get_data();
    select();
    $("#jumlah_tampil_antrian").change(function () {
      get_data();
    });
    $("#jumlah_tampil_konfirmasi").change(function () {
      get_data();
    });
    $("#antrian-tab").on("click", function () {
      $("#masih_antri").show();
      $("#sudah_konfirmasi").hide();
    });
    $("#konfirmasi-tab").on("click", function () {
      $("#sudah_konfirmasi").show();
      $("#masih_antri").hide();
    });
    $(document).on('change', '#poli-select', function () {
      get_data(); // Refresh data ketika poli berubah
    });
  });
  function get_data() {
    let cari = $("#cari").val();
    let count_header = $(`#table-antrian thead tr th`).length;
    let count_headerk = $(`#table-konfirmasi thead tr th`).length;
    let selectedPoli = $("#poli-select").val(); // Ambil nilai poli yang dipilih
    const pilih = 'Pilih terlebih poli terlebih dahulu!';
    if (selectedPoli == null || selectedPoli === '-') {

        // TAMPILKAN PESAN DI TABLE ANTRIAN
        $("#table-antrian tbody").html(`
            <tr>
                <td colspan="${count_header}" class="text-center">
                    Pilih poli terlebih dahulu!
                </td>
            </tr>
        `);

        // TAMPILKAN PESAN DI TABLE KONFIRMASI
        $("#table-konfirmasi tbody").html(`
            <tr>
                <td colspan="${count_headerk}" class="text-center">
                    Pilih poli terlebih dahulu!
                </td>
            </tr>
        `);

        return; 
    }
    $.ajax({
      url: "<?php echo base_url(); ?>antrian/antrian/result_data",
      data: {
        poli: selectedPoli,
      },
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

        $(`#table-antrian tbody`).html(loading);
      },
      success: function (res) {
        let table = "";
        let table1 = "";
        if (res.result) {
          let dataMenunggu = res.data.filter(item => item.status_antrian !== "Konfirmasi");
          let dataKonfirmasi = res.data.filter(item => item.status_antrian === "Konfirmasi");
          // Jika kosong → tampilkan placeholder
          if (dataMenunggu.length === 0) {
            table += `
        <tr>
            <td colspan="${count_header}" class="text-center">Antrian Kosong</td>
        </tr>
    `;
          }

          if (dataKonfirmasi.length === 0) {
            table1 += `
        <tr>
            <td colspan="${count_headerk}" class="text-center">Konfirmasi Antrian Kosong</td>
        </tr>
    `;
          }
          let i = 1;
          let activeIndex = res.data.findIndex(item => item.status_antrian !== "Konfirmasi");
          if (activeIndex === -1) activeIndex = 99999;
          for (const item of res.data) {
            let statusbd = "";
            if (item.status_antrian === "Menunggu") {
              statusbd = `<span class="badge bg-warning">${item.status_antrian}</span>`;
            }
            if (item.status_antrian === "Dipanggil") {
              statusbd = `<span class="badge bg-info">${item.status_antrian}</span>`;
            }
            if (item.status_antrian === "Konfirmasi") {
              statusbd = `<span class="badge bg-success">${item.status_antrian}</span>`;
            }
            if (item.status_antrian == "Konfirmasi") {
              table1 += `
                            <tr>
                                <td>${item.no_antrian}</td>
                                <td>${item.nama_pasien}</td>
                                <td>${item.nama_poli}</td>
                                <td>${statusbd}</td>
                                <td><button id="btnView_${item.kode_invoice}" 
        class="btn btn-info btn-sm"
        onclick="v_konfirm('${item.kode_invoice}')">
    <i class="fas fa-eye"></i> View
</button></td>
                            </tr>
                        `;
               cekButtonKecantikan(item.kode_invoice);
            } else {
              let aksiButton = "";
              if ((i - 1) === activeIndex) {
                if (item.status_antrian == "Menunggu") {
                  aksiButton = `
                          <button type="button" class="btn btn-shadow btn-sm btn-success" title="Panggil" onclick="panggil(${item.id})" ><i class="fas fa-volume-up me-2"></i>Panggil</button>
                          `;
                }
                if (item.status_antrian == "Dipanggil") {

                  aksiButton = `
                          <button type="button" class="btn btn-shadow btn-sm btn-info" title="Panggil" onclick="panggil(${item.id
                    })" ><i class="fas fa-redo-alt me-2"></i>Panggil Ulang</button>
                          <button type="button" class="btn btn-shadow btn-sm btn-primary" title="Konfirmasi" onclick="konfirmasi('${btoa(
                      JSON.stringify(item)
                    )}')" ><i class="fas fa-check-circle me-2"></i>Konfirmasi</button>
                          `;
                }
              } else {
                // BARIS TIDAK AKTIF – tombol mati
                aksiButton = `
                <button class="btn btn-secondary btn-sm" disabled>Tidak Aktif</button>
            `;
              }

              table += `
                             <tr>
                                <td>${item.no_antrian}</td>
                                <td>${item.nama_pasien}</td>
                                <td>${item.nama_poli}</td>
                                <td>${statusbd}</td>
                                <td class="text-center">${aksiButton}</td>
                            </tr>
                        `;
            }
            i++;
          }
        } else {
          table += `
                        <tr>
                            <td colspan="${count_header}" class="text-center">Antrian Kosong</td>
                        </tr>
                    `;
          table1 += `
                        <tr>
                            <td colspan="${count_headerk}" class="text-center">Antrian Kosong</td>
                        </tr>
                    `;
        }

        $("#table-antrian tbody").html(table);
        paging();
        $("#table-konfirmasi tbody").html(table1);
        paging1();
      },
      complete: () => {
        $(`#tr-loading`).hide();
      },
    });

    $("#cari")
      .off("keyup")
      .keyup(function () {
        get_data();
      });
  }
  function cekButtonKecantikan(kode_invoice) {
    $.ajax({
      url: "<?= base_url('antrian/antrian/cek_btn') ?>",
      type: "POST",
      data: {
        kode_invoice: kode_invoice  // Perbaiki format data
      },
      dataType: "json",
      success: function (res) {
        let btn = $("#btnView_" + kode_invoice);
        if (!btn.length) {
          console.log('Button not found: btnView_' + kode_invoice);
          return;
        }

        // Jika tidak ada pol_kecantikan → tombol hide
        if (res.status === 'tidak_ada_pol') {
          btn.hide();
          console.log('Hiding button - tidak ada pol');
          return;
        }
        if (res.status === 'data_belum_lengkap') {
          btn.hide();
          console.log('Hiding button - Data Belum Lengkap');
          return;
        }

        // Jika status error
        if (res.status === 'error') {
          console.log('Error:', res.message);
          return;
        }

        // Jika tindakan atau diagnosa kosong → disable
        if (res.tindakan == 0 || res.diagnosa == 0) {
          // Lengkap → tombol aktif
          btn.prop("disabled", false)
            .removeClass("btn-secondary")
            .addClass("btn-info")
            .html('<i class="fas fa-eye"></i> View');
          console.log('Button enabled - Data Belum Lengkap');
        }

        else {
          btn.hide();
          console.log('Hiding button - Data Belum Lengkap');
          return;
        }
      },
      error: function (xhr, status, error) {
        console.log('AJAX Error:', error);
        console.log('Status:', status);
        console.log('Response:', xhr.responseText);
      }
    });
  }
  function v_konfirm(kode_invoice) {
    $.post({
      url: "<?= base_url() ?>antrian/antrian/cek_konfirmasi",
      data: {
        kode_invoice: kode_invoice  // Perbaiki format data
      },
      dataType: "JSON",
      beforeSend: function () {
        Swal.fire({
          title: 'Mengecek Data Pasien...',
          html: 'Mohon Ditunggu...',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
      },
      success: function (data) {
        Swal.close();
        if (data.status == 'ada') {
          Swal.fire({
            title: "Berhasil!",
            text: "Data pasien siap dilihat",
            icon: "success",
            showCancelButton: false,
            showConfirmButton: true,
            confirmButtonColor: "#35baf5",
            confirmButtonText: "Oke",
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed) {
              // Redirect ke poli kecantikan
              window.location.href = "<?php echo base_url() ?>poli/kecantikan/view_proses/" + kode_invoice;
            }
          });
        } else {
          Swal.fire({
            title: "Data Belum Lengkap",
            text: "Tindakan atau diagnosa belum diisi",
            icon: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            confirmButtonColor: "#35baf5",
            confirmButtonText: "Oke",
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed) {
              get_data();
            }
          });
        }
      },
      error: function (xhr, status, error) {
        console.log('Error:', error);
        Swal.fire({
          title: "Error",
          text: "Terjadi kesalahan saat memeriksa data",
          icon: "error",
          confirmButtonText: "Oke"
        });
      }
    });
  }
  function panggil(id) {
    $.ajax({
      url: "<?= base_url() ?>antrian/antrian/panggil",
      type: "POST",
      dataType: "JSON",
      data: { id },
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
      success: function (data) {
        if (data.status) {
          Swal.fire({
            title: "Berhasil!",
            text: data.message,
            icon: "success",
            showCancelButton: false,
            showConfirmButton: true,
            confirmButtonColor: "#35baf5",
            confirmButtonText: "Oke",
            closeOnConfirm: false,
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed) {
              get_data();
            }
          });
        } else {
          Swal.fire({
            title: "Gagal!",
            text: data.message,
            icon: "error",
            showCancelButton: false,
            showConfirmButton: true,
            confirmButtonColor: "#35baf5",
            confirmButtonText: "Oke",
            closeOnConfirm: false,
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed) {
              get_data();
            }
          });
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  }
  function konfirmasi(encodedString) {
    const ambil = JSON.parse(atob(encodedString));
    const id = ambil.id;
    const kode_invoice = ambil.kode_invoice;
    const id_pasien = ambil.id_pasien;
    const nama_pasien = ambil.nama_pasien;
    const nik = ambil.nik;
    const alergi = ambil.alergi;
    const id_poli = ambil.id_poli;
    const nama_poli = ambil.nama_poli;
    const id_dokter = ambil.id_dokter;
    const nama_dokter = ambil.nama_dokter;
    $.post({
      url: "<?= base_url() ?>antrian/antrian/konfirmasi",
      data: {
        id: id,
        kode_invoice: kode_invoice,
        id_pasien: id_pasien,
        nama_pasien: nama_pasien,
        nik: nik,
        riwayat_alergi: alergi,
        id_poli: id_poli,
        nama_poli: nama_poli,
        id_dokter: id_dokter,
        nama_dokter: nama_dokter,
      },
      dataType: "JSON",
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
      success: function (data) {
        if (data.status == true) {
          Swal.fire({
            title: "Berhasil!",
            text: data.message,
            icon: "success",
            showCancelButton: false,
            showConfirmButton: true,
            confirmButtonColor: "#35baf5",
            confirmButtonText: "Oke",
            closeOnConfirm: false,
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed, id_poli) {
              if (id_poli == 16) {
                window.location.href =
                  "<?php echo base_url() ?>poli/kecantikan/view_proses/" +
                  kode_invoice;
              } else {
                get_data();
              }
            }
          });
        } else {
          Swal.fire({
            title: "Gagal",
            text: data.message,
            icon: "error",
            showCancelButton: false,
            showConfirmButton: true,
            confirmButtonColor: "#35baf5",
            confirmButtonText: "Oke",
            closeOnConfirm: false,
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed) {
              get_data();
            }
          });
        }
      },
    });
  }

  function paging($selector) {
    var jumlah_tampil = $("#jumlah_tampil_antrian").val();
    if (typeof $selector == "undefined") {
      $selector = $("#table-antrian tbody tr");
    }
    window.tp = new Pagination("#pagination-antrian", {
      itemsCount: $selector.length,
      pageSize: parseInt(jumlah_tampil),
      onPageSizeChange: function (ps) {
        console.log("changed to " + ps);
      },
      onPageChange: function (paging) {
        var start = paging.pageSize * (paging.currentPage - 1),
          end = start + paging.pageSize,
          $rows = $selector;
        $rows.hide();
        for (var i = start; i < end; i++) {
          $rows.eq(i).show();
        }
      },
    });
  }
  function paging1($selector) {
    var jumlah_tampil = $("#jumlah_tampil_konfirmasi").val();
    if (typeof $selector == "undefined") {
      $selector = $("#table-konfirmasi tbody tr");
    }
    window.tp = new Pagination("#pagination-konfirmasi", {
      itemsCount: $selector.length,
      pageSize: parseInt(jumlah_tampil),
      onPageSizeChange: function (ps) {
        console.log("changed to " + ps);
      },
      onPageChange: function (paging) {
        var start = paging.pageSize * (paging.currentPage - 1),
          end = start + paging.pageSize,
          $rows = $selector;
        $rows.hide();
        for (var i = start; i < end; i++) {
          $rows.eq(i).show();
        }
      },
    });
  }
  // Fungsi select untuk mengambil data poli
  function select() {
    $.get({
      url: "<?= base_url() ?>antrian/antrian/poli",
      dataType: "json",
      success: function (res) {
        if (res != null) {
          $('#poli-select').empty().append('<option value="-">Semua Poli</option>');
          res.data.forEach(item => {
            $('#poli-select').append($('<option>', {
              value: item.id,
              text: item.nama,
              'data-nama': item.nama,
              'data-id': item.id
            }))
          });
        }
      },
    })
  }
</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <?php echo $title; ?>
            </li>
          </ol>
        </div>
        <h4 class="page-title">
          <?php echo $title; ?>
        </h4>
      </div>
      <!--end page-title-box-->
    </div>
    <!--end col-->
  </div>
  <!-- end page title end breadcrumb -->
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 pb-3">
          <h4 class="card-title">
            <?php echo $title; ?>
          </h4>
        </div>
        <!--end card-header-->
        <div class="card-body">
          <ul class="nav nav-tabs mb-3" id="antrianTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="antrian-tab" data-bs-toggle="tab" data-bs-target="#tab-antrian"
                type="button" role="tab">
                Dipanggil
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="konfirmasi-tab" data-bs-toggle="tab" data-bs-target="#tab-konfirmasi"
                type="button" role="tab">
                Dikonfirmasi
              </button>
            </li>
            <li class="nav-item ms-auto poli-selector">
              <select class="form-select" id="poli-select">
              </select>
            </li>
          </ul>
          <!-- Tab Content -->
          <div class="tab-content">
            <!-- Tab 1: Antrian -->
            <div class="tab-pane fade show active" id="tab-antrian" role="tabpanel">
              <div class="table-responsive">
                <table class="table mb-0 table-hover" id="table-antrian">
                  <thead class="thead-light">
                    <tr>
                      <th>No Antrian</th>
                      <th>Nama Pasien</th>
                      <th>Nama Poli</th>
                      <th>Status</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
              <div class="row mt-3">
                <div class="col-sm-6">
                  <div id="pagination-antrian"></div>
                </div>
                <div class="col-sm-6">
                  <div class="row">
                    <div class="col-md-6">&nbsp;</div>
                    <label class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah
                      Tampil</label>
                    <div class="col-md-3 pull-right">
                      <select class="form-control" id="jumlah_tampil_antrian">
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
            <!-- Tab 2: Konfirmasi -->
            <div class="tab-pane fade" id="tab-konfirmasi" role="tabpanel">
              <div class="table-responsive">
                <table class="table mb-0 table-hover" id="table-konfirmasi">
                  <thead class="thead-light">
                    <tr>
                      <th>No Antrian</th>
                      <th>Nama Pasien</th>
                      <th>Nama Poli</th>
                      <th>Status</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
              <div class="row mt-3">
                <div class="col-sm-6">
                  <div id="pagination-konfirmasi"></div>
                </div>
                <div class="col-sm-6">
                  <div class="row">
                    <div class="col-md-6">&nbsp;</div>
                    <label class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah
                      Tampil</label>
                    <div class="col-md-3 pull-right">
                      <select class="form-control" id="jumlah_tampil_konfirmasi">
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
          </div>
          <!-- /.tab-content -->
        </div>
        <!--end card-body-->
      </div>
      <!--end card-->
    </div>
    <!--end col-->
  </div>
</div>
<!-- container -->