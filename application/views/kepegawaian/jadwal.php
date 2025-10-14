<script type="text/javascript">
    function filterJadwal() {
        let id_poli = $('#filter_poli').val();
        // let jam = $('#filter_jam').val();
        $.ajax({
            url: '<?php echo base_url("kepegawaian/jadwal/filter_jadwal"); ?>',
            type: 'POST',
            data: {
                id_poli: id_poli,
            },
            beforeSend: function () {
                $('#schedule-container').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p>Memuat jadwal...</p></div>');
            },
            success: function (response) {
                $('#schedule-container').html(response);
            },
            error: function () {
                $('#schedule-container').html('<p class="text-danger text-center">Gagal memuat jadwal. Silakan coba lagi</p>');
            }
        });
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><?php echo $title; ?></li>
                    </ol>
                </div>
                <h4 class="page-title"><?php echo $title; ?></h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 pb-3">
                    <h4 class="card-title">Data <?php echo $title ?></h4>

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="md-row2">
                            <label for="filter_poli" class="form-label">Filter Berdasarkan Poli</label>
                            <select id="filter_poli" class="form-select" onchange="filterJadwal()">
                                <option value="">Semua Poli</option>
                                <?php foreach ($data_poli as $poli) { ?>
                                    <option value="<?php echo $poli->id; ?>"><?php echo $poli->nama; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-warning w-100 "
                                onclick="$('#filter_poli').val(''); $('#filter_jam').val(''); filterJadwal();"><i
                                    class="fas fa-search"></i> Reset Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="schedule-container">
        <?php $this->load->view('kepegawaian/dokter/partial_jadwal', ['schedule_data' => $schedule_data]); ?>
    </div>
</div>