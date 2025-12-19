<script type="text/javascript">
	$(document).ready(function () {
		jabatan();
		poli();
		$('#nama_p').hide();
	});
	function tambah(e) {
		 let btn = $(e.target).closest('button');
    e.preventDefault();
    btn.prop("disabled", true).text("Mengirim...");
		const nama_p = $('#nama_pegawai').val();
		const tp = $('#no_tp').val();
		const nama_j = $('#nama_jabatan').val();
		const id_j = $('#id_jabatan').val();
		const alamat = $('#alamat').val();
		if (nama_p == '' || tp == '' || nama_j == '' || id_j == '' || alamat == '') {
			Swal.fire({
				icon: "error",
				title: "Oops...",
				text: "Inputan Kosong",
			});
			btn.prop("disabled", false).html('<i class="fas fa-save me-2"></i>Simpan');
			return;
		}
		$.ajax({
			url: '<?php echo base_url('kepegawaian/pegawai/tambah') ?>',
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
			success: function (res) {
				console.log(res);
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
							window.location.href = '<?php echo base_url() ?>kepegawaian/pegawai'
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
							console.log('Terjadi error!');
						}
					})
				}
			}
		});
	}
	function jabatan() {
		$.get({
			url: '<?= base_url(); ?>kepegawaian/pegawai/jabatan',
			dataType: 'JSON',
			success: function (data) {
				if (data != null) {
					data.forEach(item => {
						$('#id_jabatan').append($('<option>', {
							value: item.id,
							text: item.nama,
							'data-nama': item.nama,
						}));
					});
				}
			}
		})
	}
	$(document).on('change', '#id_jabatan', function () {
		var nama = $('#id_jabatan option:selected').data('nama');
		$('#nama_jabatan').val(nama);
		if (nama == 'Dokter') {
			$('#nama_p').slideDown();
		}
		else {
			$('#nama_p').hide();
			return;
		}
	});
	function poli() {
		$.get({
			url: '<?= base_url(); ?>kepegawaian/pegawai/poli',
			dataType: 'JSON',
			success: function (data) {
				if (data != null) {
					data.forEach(item => {
						$('#id_poli').append($('<option>', {
							value: item.id,
							text: item.nama,
							'data-nama': item.nama,
						}));
					});
				}
			}
		})
	}
	$(document).on('change', '#id_poli', function () {
		var nama = $('#id_poli option:selected').data('nama');
		$('#nama_poli').val(nama);
	});
</script>
<div class="container-fluid">
	<!-- Page-Title -->
	<div class="row">
		<div class="col-sm-12">
			<div class="page-title-box">
				<div class="float-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="<?php echo base_url(); ?>kepegawaian/pegawai"><?php echo $title; ?></a>
						</li>
						<li class="breadcrumb-item active">Tambah</li>
					</ol>
				</div>
				<h4 class="page-title"><?php echo $title; ?></h4>
			</div>
			<!--end page-title-box-->
		</div>
		<!--end col-->
	</div>
	<!-- end page title end breadcrumb -->
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header pt-3 pb-3">
					<h4 class="card-title">
						Tambah
						<?php echo $title; ?>
					</h4>
				</div>
				<!--end card-header-->
				<div class="card-body">
					<div class="general-label">
						<form id="form_tambah">
							<!-- inputan start -->
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama pegawai</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="nama_pegawai" id="nama_pegawai"
										placeholder="Nama pegawai" required autocomplete="off" />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nomor telpon</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="no_tp" id="no_tp"
										placeholder="Nomor telpon" required autocomplete="off"  inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama jabatan</label>
								<div class="col-sm-10">
									<select name="id_jabatan" id="id_jabatan" class="form-control">
										<option value="">Pilih Jabatan</option>
									</select>
									<input type="hidden" class="form-control" name="nama_jabatan" id="nama_jabatan"
										placeholder="Nama jabatan" readonly autocomplete="off" />
								</div>
							</div>
							<div class="mb-3 row" id="nama_p">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Poli</label>
								<div class="col-sm-10">
									<select name="id_poli" id="id_poli" class="form-control">
										<option value="">Pilih Poli</option>
									</select>
									<input type="hidden" class="form-control" name="nama_poli" id="nama_poli"
										placeholder="Nama Poli" readonly />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Alamat</label>
								<div class="col-sm-10">
									<textarea class="form-control" name="alamat" id="alamat"
										placeholder="Alamat"></textarea>
								</div>
							</div>
							<!-- inputan end -->
							<div class="row">
								<div class="col-sm-10 ms-auto">
									<button type="button" onclick="tambah(event);" class="btn btn-success">
										<i class="fas fa-save me-2"></i>Simpan
									</button>
									<a href="<?php echo base_url(); ?>kepegawaian/pegawai"><button type="button"
											class="btn btn-warning">
											<i class="fas fa-reply me-2"></i>Kembali
										</button></a>
								</div>
							</div>
						</form>
					</div>
				</div>
				<!--end card-body-->
			</div>
			<!--end card-->
		</div>
		<!--end col-->
	</div>
</div>
<!-- container -->