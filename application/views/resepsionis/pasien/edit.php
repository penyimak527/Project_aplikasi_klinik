<script type="text/javascript">
  $(document).ready(function () {
    jenis(),
      golongan_darah(),
      st_perkawinan(),
      tgl()
  })
  function edit(e) {
    e.preventDefault()
    const nama = $('#nama_pasien').val();
    const nik = $('#nik').val();
    const alamat = $('#alamat').val();
    const tanggal_lahir = $('#tanggal_lahir').val();
    const nama_wali = $('#nama_wali').val();
    if (nama == "" || nik == '' || tanggal_lahir == '' || umur == '' || alamat == '' || nama_wali == '') {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Inputan Kosong",
      });
      return;
    }
    $.ajax({
      url: '<?php echo base_url('resepsionis/pasien/edit') ?>',
      method: 'POST',
      data: $('#form_edit').serialize(),
      dataType: 'json',
      success: function (res) {
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
              window.location.href = '<?php echo base_url() ?>resepsionis/pasien'
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
              location.reload()
            }
          })
        }
      }
    });
  }
  function jenis() {
    const jenis = ['Laki-laki', 'Perempuan']
    const select_d = "<?= $row['jenis_kelamin'] ?>";
    // Kosongkan dulu select-nya
    $('#jk').empty();
    //opsi
    $('#jk').append('<option value="">Pilih Jenis Kelamin</option>');
    //loop
    jenis.forEach(item => {
      const select = (item === select_d) ? 'selected' : '';
      $('#jk').append(`<option value="${item}" ${select}>${item}</option>`)
    });
  }
  function st_perkawinan() {
    const jenis = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']
    const select_d = "<?= $row['status_perkawinan'] ?>";
    // Kosongkan dulu select-nya
    $('#st_perkawinan').empty();
    //opsi
    $('#st_perkawinan').append('<option value="">Pilih Status Perkawinan</option>');
    //loop
    jenis.forEach(item => {
      const select = (item === select_d) ? 'selected' : '';
      $('#st_perkawinan').append(`<option value="${item}" ${select}>${item}</option>`)
    });
  }
  function golongan_darah() {
    const jenis = ['A', 'B', 'AB', 'O']
    const select_d = "<?= $row['golongan_darah'] ?>";
    // Kosongkan dulu select-nya
    $('#golongan_darah').empty();
    //opsi
    $('#golongan_darah').append('<option value="">Pilih Golongan Darah</option>');
    //loop
    jenis.forEach(item => {
      const select = (item === select_d) ? 'selected' : '';
      $('#golongan_darah').append(`<option value="${item}" ${select}>${item}</option>`)
    });
  }
  function tgl() {
    const tanggalInput = document.getElementById('tgl_lahir');
    const datepicker = new Datepicker(tanggalInput, {
      format: 'dd-mm-yyyy',
      autohide: true,
    });
    tanggalInput.addEventListener('changeDate', function () {
      console.log('tanggal yang dipilih ', tanggalInput.value);
      hitungUmur();
    });
    if (tanggalInput.value) {
      hitungUmur();
    }
  }
  function hitungUmur() {
    var tanggal_lahir = $('#tgl_lahir').val();
    if (tanggal_lahir) {
      const [day, month, year] = tanggal_lahir.split("-");
      var birthDate = new Date(`${year}-${month}-${day}`);
      var today = new Date();
      var age = today.getFullYear() - birthDate.getFullYear();
      var m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      $('#umur').val(age);
    }
  }
</script>
<div class="container-fluid">
  <!-- Page-Title -->
  <div class="row">
    <div class="col-sm-12">
      <div class="page-title-box">
        <div class="float-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>resepsionis/pasien"><?php echo $title; ?></a>
            </li>
            <li class="breadcrumb-item active">Edit</li>
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
        <div class="card-header pt-3 pb-3">
          <h4 class="card-title">Edit Data Pasien: <?php echo $row['nama_pasien']; ?> (<?php echo $row['no_rm']; ?>)</h4>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="general-label">
            <form id="form_edit">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>" readonly>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Pasien</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama_pasien" id="nama_pasien"
                    value="<?php echo $row['nama_pasien'] ?>" placeholder="Input nama pasien">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">NIK</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" name="nik" id="nik" value="<?php echo $row['nik'] ?>"
                    placeholder="Input NIK">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                <div class="col-sm-10">
                  <select name="jk" id="jk" class="form-select">
                    <option value="">Pilih Jenis Kelamin</option>
                  </select>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="tgl_lahir" id="tgl_lahir"
                    value="<?php echo date('d-m-Y', strtotime($row['tanggal_lahir'])) ?>"
                    placeholder="Input tanggal lahir" autocomplete="off">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Umur</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" name="umur" id="umur" value="<?php echo $row['umur'] ?>"
                    placeholder="Input umur" readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                  <textarea name="alamat" id="alamat" class="form-control"><?php echo $row['alamat'] ?></textarea>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Pekerjaan</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="pekerjaan" id="pekerjaan"
                    value="<?php echo $row['pekerjaan'] ?>" placeholder="Input pekerjaan">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">No Telpon</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="no_tp" id="no_tp" value="<?php echo $row['no_telp'] ?>"
                    placeholder="Input no telpon">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Status Perkawinan</label>
                <div class="col-sm-10">
                  <select name="st_perkawinan" id="st_perkawinan" class="form-select"></select>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Wali</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama_wali" id="nama_wali"
                    value="<?php echo $row['nama_wali'] ?>" placeholder="Input nama wali">
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Golongan Darah</label>
                <div class="col-sm-10">
                  <select name="golongan_darah" id="golongan_darah" class="form-select">
                  </select>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Alergi</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="alergi" id="alergi" value="<?php echo $row['alergi'] ?>"
                    placeholder="Input alergi">
                  <span class="form-text mt-1 text-danger"><strong>Perhatian: </strong>jika pasien tidak ada riwayat
                    alergi isi dengan "-"</span>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Status Operasi</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="status_op" id="status_op"
                    value="<?php echo $row['status_operasi'] ?>" placeholder="Input status operasi">
                  <span class="form-text mt-1 text-danger"><strong>Perhatian: </strong>jika pasien tidak ada riwayat
                    operasi isi dengan "-"</span>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="edit(event);" class="btn btn-success"><i
                      class="fas fa-save me-2"></i>Simpan</button>
                  <a href="<?php echo base_url(); ?>resepsionis/pasien"><button type="button" class="btn btn-warning"><i
                        class="fas fa-reply me-2"></i>Kembali</button></a>
                </div>
              </div>
            </form>
          </div>
        </div><!--end card-body-->
      </div><!--end card-->
    </div><!--end col-->
  </div>
</div><!-- container -->