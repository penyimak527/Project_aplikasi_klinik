<script type="text/javascript">
	$(document).ready(function () {
		const hariAwal = '<?php
		$timestamp = strtotime($row['tanggal']);
		$hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
		echo $hariNames[date('w', $timestamp)];
		?>';
		const waktuAwal = '<?php echo $row['waktu']; ?>';
		$('#hari').val(hariAwal);
		$('#waktu').val(waktuAwal);
		poli().then(() => {
			const idPoli = $('#id_poli').val();
			return dokter(idPoli); // tunggu dokter selesai dimuat
		}).then(() => {
			console.log("Cek data pasien...");
			try {
				const pasienData = <?php echo json_encode($row ? [
					'id' => $row['id_pasien'],
					'nama_pasien' => $row['nama_pasien'],
					'nik' => $row['nik'],
				] : null); ?>;
			} catch (err) {
				console.error("Gagal parsing pasienData:", err);
			}
		});
		const tanggalInput = document.getElementById('tanggal');
		const datepicker = new Datepicker(tanggalInput, {
			format: 'dd-mm-yyyy',
			autohide: true
		});

		// Event listener yang benar untuk vanillajs-datepicker
		tanggalInput.addEventListener('changeDate', function (e) {
			// Detail ada di e.detail
			const selectedDate = e.detail.date;
			const hari = selectedDate.getDate();
			const hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
			const namaHari = hariNames[selectedDate.getDay()];
			console.log('Nama hari:', namaHari);
			$('#hari').val(namaHari);
			const idPoli = $('#id_poli').val();
			if (idPoli) {
				dokter(idPoli);
			}
		});
		waktuu();
	})
	function waktuu() {
		var timeInput = document.getElementById('waktu');
		var timeMask = IMask(timeInput, {
			mask: 'HH:MM',
			blocks: {
				HH: {
					mask: IMask.MaskedRange,
					from: 0,
					to: 23,
					maxLength: 2
				},
				MM: {
					mask: IMask.MaskedRange,
					from: 0,
					to: 59,
					maxLength: 2
				}
			},
			lazy: false,
			placeholderChar: '_'
		});
	}
	$(document).on('change', '#waktu', function () {
		const idPoli = $('#id_poli').val();
		if (idPoli) {
			dokter(idPoli); // Panggil dengan parameter idPoli
		}
	});
	function edit(e) {
		e.preventDefault();
		const id_pasien = $('#id_pasien').val();
		const nama_pasien = $('#nama_pasien').val();
		const id_dokter = $('#id_dokter').val();
		const nama_dokter = $('#nama_dokter').val();
		const waktu = $('#waktu').val();
		const tanggal = $('#tanggal').val();

		if (id_dokter == '' || nama_pasien == '' || id_dokter == '' || nama_dokter == '' || waktu == '' || tanggal == '') {
			Swal.fire({
				icon: "error",
				title: "Oops...",
				text: "Inputan Kosong",
			});
			return;
		}
		$.ajax({
			url: '<?php echo base_url('resepsionis/booking/edit') ?>',
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
							window.location.href = '<?php echo base_url() ?>resepsionis/booking'
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
						$('#id_poli').empty().append('<option>Pilih Poli</option>');
						data.forEach(item => {
							$('#id_poli').append($('<option>', {
								value: item.id,
								text: item.nama,
								'data-nama': item.nama,
								selected: item.id == select
							}));
						});

						// set input hidden nama poli
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
	// Fungsi untuk memuat data dokter
	function dokter(idPoli) {
		// Dapatkan nilai waktu dan hari
		var waktu = $('#waktu').val();
		var hari = $('#hari').val();
		let dokterDropdown = $('#id_dokter');
		let idDokterPilih = '<?php echo $row['id_dokter']; ?>';
		$.ajax({
			url: '<?= base_url("resepsionis/booking/dokter") ?>',
			type: 'POST',
			data: {
				id_poli: idPoli,
				hari: hari,
				waktu: waktu + ':00',
			},
			dataType: 'JSON',
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
					dokterDropdown.append('<option value="">Tidak ada dokter</option>');
					$('#id_dokter').prop('disabled', true);
				}
			},
			error: function () {
				$('#id_dokter').empty().append('<option value="">Error loading data</option>');
				$('#id_dokter').prop('disabled', true);
			}
		});
	}
	$(document).on('change', '#id_dokter', function () {
		var nama = $('#id_dokter option:selected').data('nama');
		$('#nama_dokter').val(nama);
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
							<a href="<?php echo base_url(); ?>resepsionis/booking">
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
						:
						<?php echo $row['kode_booking'] ?>
						<input type="hidden" id="hari" name="hari" class="form-control" readonly/>
					</h4>
				</div>
				<!--end card-header-->
				<div class="card-body">
					<div class="general-label">
						<form id="form_edit">
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>" readonly
								autocomplete="off" />
							<input type="hidden" name="id_pasien" value="<?php echo $row['id_pasien']; ?>" readonly
								autocomplete="off" />
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Pasien</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="nama_pasien" id="nama_pasien"
										value="<?php echo $row['nama_pasien'] ?>" placeholder="Nama pasien"
										autocomplete="off" readonly />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="nik" id="nik"
										value="<?php echo $row['nik'] ?>" placeholder="Nik" autocomplete="off"
										readonly />
								</div>
							</div>
							<hr />
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Poli</label>
								<div class="col-sm-10">
									<select name="id_poli" id="id_poli" class="form-control">
										<option value=""></option>
									</select>
									<input type="hidden" class="form-select" name="nama_poli" id="nama_poli"
										placeholder="Input nama poli" readonly />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Nama Dokter</label>
								<div class="col-sm-10">
									<select name="id_dokter" id="id_dokter" class="form-control">
										Pilih Nama Dokter
									</select>
									<input type="hidden" class="form-select" name="nama_dokter" id="nama_dokter"
										readonly />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Tanggal Booking</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="tanggal" id="tanggal"
										value="<?php echo $row['tanggal'] ?>" placeholder="Tanggal booking"
										autocomplete="off" />
								</div>
							</div>
							<div class="mb-3 row">
								<label for="tambah_contoh" class="col-sm-2 col-form-label">Waktu Booking</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="waktu" id="waktu"
										value="<?php echo $row['waktu'] ?>" placeholder="Waktu booking"
										autocomplete="off" />
								</div>
							</div>
							<div class="row">
								<div class="col-sm-10 ms-auto">
									<button type="button" onclick="edit(event);" class="btn btn-success">
										<i class="fas fa-save me-2"></i>Simpan
									</button>
									<a href="<?php echo base_url(); ?>resepsionis/booking"><button type="button"
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