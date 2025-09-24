<script type="text/javascript">
	$(document).ready(function () {
		poli().then(() => {
			const idPoli = $('#id_poli').val();
			return dokter(idPoli); // tunggu dokter selesai dimuat
		})
	})
	function edit() {
		const dokter = $('#id_dokter').val();
		const poli = $('#id_poli').val();
		if (dokter == '' || poli == '') {
			Swal.fire({
				icon: "error",
				title: "Oops...",
				text: "Inputan Kosong",
			});
			return;
		}
		$.ajax({
			url: '<?php echo base_url('resepsionis/pendaftaran/edit') ?>',
			method: 'POST',
			data: $('#form_edit').serialize(),
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
							window.location.href = '<?php echo base_url() ?>resepsionis/pendaftaran'
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
	function poli() {
		return new Promise((resolve, reject) => {
			$.get({
				url: '<?= base_url(); ?>resepsionis/booking/poli',
				dataType: 'JSON',
				success: function (data) {
					if (data != null) {
						const select = '<?= $row['id_poli'] ?>';
						$('#id_poli').empty().append('<option value="">Pilih Poli</option>');
						data.forEach(item => {
							$('#id_poli').append($('<option>', {
								value: item.id,
								text: item.nama,
								'data-nama': item.nama,
								selected: item.id == select
							}));
						});
						// set input text nama poli
						const nama = $('#id_poli option:selected').data('nama');
						$('#nama_poli').val(nama || '');
						resolve(); // lanjut ke dokter setelah poli siap
					} else {
						reject('Data poli kosong');
					}
				},
				error: function (xhr) {
					reject(xhr.responseText);
				}
			});
		});
	}
	$(document).on('change', '#id_poli', function () {
		var nama = $('#id_poli option:selected').data('nama');
		var idPoli = $(this).val();
		$('#nama_poli').val(nama);
		dokter(idPoli);
	});
	function dokter(idPoli) {
		const idDokterPilih = '<?= $row['id_dokter'] ?>';
		const hari = '<?= $row['tanggal'] ?>';
		const jam_m = '<?= $row['waktu'] ?>';
		let dokterDropdown = $('#id_dokter');
		$.post({
			data: {
				id_poli: idPoli,
				hari: hari,
				jam_m: jam_m,
			},
			dataType: 'JSON',
			url: '<?= base_url("resepsionis/pendaftaran/dokter") ?>',
			success: function (data) {
				dokterDropdown.empty();
				if (data && data.length > 0) {
					dokterDropdown.append('<option value="">Pilih Dokter</option>');
					data.forEach(item => {
						if (item.id_poli == idPoli) {
							const isSelected = (item.id_pegawai == idDokterPilih) ? 'selected' : '';
							const option = `<option value="${item.id_pegawai}" data-nama="${item.nama_pegawai}" ${isSelected}>${item.nama_pegawai}</option>`;
							dokterDropdown.append(option);
						}
					});
					const nama = $('#id_dokter option:selected').data('nama');
					$('#nama_dokter').val(nama || '');
					$('#id_dokter').prop('disabled', false);
				} else {
					dokterDropdown.append('<option value="">Tidak ada dokter untuk poli ini </option>');
					$('#id_dokter').prop('disabled', true);
				}
			},
			error: function () {
				$('#id_dokter').empty().append('<option value="">Error loading data</option>');
			}
		})
	}
	$(document).on('change', '#id_dokter', function () {
		var nama = $('#id_dokter option:selected').data('nama');
		$('#nama_dokter').val(nama);
	});
	$('#idPoli, #tanggal, #waktu').change(dokter);
	$('#idPoli').trigger('change');
</script>
<div class="container-fluid">
	<!-- Page-Title -->
	<div class="row">
		<div class="col-sm-12">
			<div class="page-title-box">
				<div class="float-end">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="<?php echo base_url(); ?>resepsionis/pendaftaran">
								<?php echo $title; ?>
							</a>
						</li>
						<li class="breadcrumb-item active">Edit</li>
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
				<div class="card-header pt-3 pb-3">
					<h4 class="card-title">
						Edit
						<?php echo $title; ?>
					</h4>
				</div>
				<!--end card-header-->
				<div class="card-body">
					<div class="general-label">
						<form id="form_edit">
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>" readonly />
							<input type="hidden" name="kode_invoice" value="<?php echo $row['kode_invoice']; ?>"
								readonly />
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Pasien</label>
								<div class="col-sm-10">
									<input type="hidden" class="form-control" name="id_pasien" id="id_pasien"
										value="<?php echo $row['id_pasien'] ?>" placeholder="Input id pasien" readonly />
									<input type="text" class="form-control" name="nama_pasien" id="nama_pasien"
										value="<?php echo $row['nama_pasien'] ?>" placeholder="Input nama pasien"
										readonly />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="nik" id="nik"
										value="<?php echo $row['nik'] ?>" placeholder="NIK" readonly />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Poli</label>
								<div class="col-sm-10">
									<select name="id_poli" id="id_poli" class="form-control"></select>
									<input type="hidden" class="form-control" name="nama_poli" id="nama_poli"
										placeholder="nama poli" readonly />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Dokter</label>
								<div class="col-sm-10">
									<select name="id_dokter" id="id_dokter" class="form-control"></select>
									<input type="hidden" class="form-control" name="nama_dokter" id="nama_dokter"
										placeholder="Nama dokter" readonly />
								</div>
							</div>
							<div class="row">
								<div class="col-sm-10 ms-auto">
									<button type="button" onclick="edit(event);" class="btn btn-success">
										<i class="fas fa-save me-2"></i>Simpan
									</button>
									<a href="<?php echo base_url(); ?>resepsionis/pendaftaran"><button type="button"
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