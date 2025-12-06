<script type="text/javascript">
  $(document).ready(function () {
    tgl()
  })
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
		};
    $.ajax({
      url: '<?php echo base_url('resepsionis/pasien/tambah') ?>',
      method: 'POST',
      data: $('#form_tambah').serialize(),
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
              window.location.href = '<?php echo base_url() ?>resepsionis/pasien'
            }
          })
        } else {
          Swal.fire({
            title: 'Gagal!',
            html: res.message,
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
              console.log('Terjadi error!');
            }
          })
        }
      }
    });
  }
  function tgl() {
    const tanggalInput = document.getElementById('tgl_lahir');
    const datepicker = new Datepicker(tanggalInput, {
      format: 'dd-mm-yyyy',
      autohide: true,
    });
    tanggalInput.addEventListener('changeDate', function () {
      hitungUmur();
    })
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
            <li class="breadcrumb-item active">Tambah</li>
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
          <h4 class="card-title">Tambah <?php echo $title; ?></h4>
        </div><!--end card-header-->
        <div class="card-body">
          <div class="general-label">
            <form id="form_tambah">
              <!-- inputan start -->
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Pasien</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama_pasien" id="nama_pasien" placeholder="Nama pasien"
                    autocomplete="off" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">NIK</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nik" id="nik" placeholder="NIK" autocomplete="off" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                <div class="col-sm-10">
                  <select name="jk" id="jk" class="form-select" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                  </select>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="tgl_lahir" onclick="tgl()" id="tgl_lahir"
                    placeholder="Tanggal lahir" autocomplete="off" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Umur</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="umur" id="umur" placeholder="Umur" readonly>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                  <textarea name="alamat" id="alamat" placeholder="Alamat" class="form-control" required></textarea>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Pekerjaan</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="pekerjaan" id="pekerjaan" placeholder="Pekerjaan"
                    autocomplete="off" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">No Telpon</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="no_tp" id="no_tp" placeholder="No telpon"
                    autocomplete="off" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Status Perkawinan</label>
                <div class="col-sm-10">
                  <select name="st_perkawinan" id="st_perkawinan" class="form-select" required>
                    <option value="">Pilih Status</option>
                    <option value="Belum Kawin">Belum Kawin</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                  </select>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Wali</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama_wali" id="nama_wali" placeholder="Nama wali"
                    autocomplete="off" required>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Golongan Darah</label>
                <div class="col-sm-10">
                  <select name="golongan_darah" id="golongan_darah" class="form-select" required>
                    <option value="">Pilih Golongan Darah</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                  </select>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Alergi</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="alergi" id="alergi" placeholder="Alergi"
                    autocomplete="off" required>
                  <span class="form-text mt-1 text-danger"><strong>Perhatian: </strong>jika pasien tidak ada riwayat
                    alergi isi dengan "-"</span>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="tambah_contoh" class="col-sm-2 col-form-label">Status Operasi</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="status_op" id="status_op" placeholder="Status operasi"
                    autocomplete="off" required>
                  <span class="form-text mt-1 text-danger"><strong>Perhatian: </strong>jika pasien tidak ada riwayat
                    operasi isi dengan "-"</span>
                </div>
              </div>
              <!-- inputan end -->
              <div class="row">
                <div class="col-sm-10 ms-auto">
                  <button type="button" onclick="tambah(event);" class="btn btn-success"><i
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