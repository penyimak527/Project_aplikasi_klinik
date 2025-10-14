<?php if (!empty($schedule_data)) { ?>
    <?php foreach ($schedule_data as $poli => $doctors) { ?>
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0"><?php echo $poli; ?></h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="width:100%;">
                        <thead>
                            <tr class="table-warning">
                                <th style="width: 20%;">Dokter Spesialis</th>
                                <th>Senin</th>
                                <th>Selasa</th>
                                <th>Rabu</th>
                                <th>Kamis</th>
                                <th>Jumat</th>
                                <th>Sabtu</th>
                                <th>Minggu</th>
                                <!-- <th style="width: 15%;">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($doctors as $doctor_data) { ?>
                                <tr>
                                    <td><?php echo $doctor_data['nama_dokter']; ?></td>
                                    <td class="text-center"><?php echo $doctor_data['jadwal']['Senin']; ?></td>
                                    <td class="text-center"><?php echo $doctor_data['jadwal']['Selasa']; ?></td>
                                    <td class="text-center"><?php echo $doctor_data['jadwal']['Rabu']; ?></td>
                                    <td class="text-center"><?php echo $doctor_data['jadwal']['Kamis']; ?></td>
                                    <td class="text-center"><?php echo $doctor_data['jadwal']['Jumat']; ?></td>
                                    <td class="text-center"><?php echo $doctor_data['jadwal']['Sabtu']; ?></td>
                                    <td class="text-center"><?php echo $doctor_data['jadwal']['Minggu']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table><!--end /table-->
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="card">
        <div class="card-body">
            <p class="text-center text-muted mt-3">Tidak ada jadwal yang sesuai dengan filter yang dipilih.</p>
        </div>
    </div>
<?php } ?>