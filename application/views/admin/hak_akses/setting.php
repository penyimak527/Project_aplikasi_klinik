<script type="text/javascript">
    function simpanSetting(e) {
        e.preventDefault();

        if ($('input[name="hak_akses[]"]:checked').length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal satu hak akses!', 'warning');
            return;
        }

        $.ajax({
            url: '<?php echo base_url('admin/hak_akses/setting_aksi') ?>',
            method: 'POST',
            data: $('#form_setting').serialize(),
            dataType: 'json',
            beforeSend: function() {
                Swal.showLoading();
            },
            success: function(res) {
                if (res.status) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        window.location.href = '<?php echo base_url('admin/level') ?>';
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            }
        });
    }

    function checkAllGroup(groupId, el) {
        $(`.group-${groupId}`).prop('checked', el.checked);
    }
</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('admin/level'); ?>">Level</a></li>
                        <li class="breadcrumb-item active">Setting Akses</li>
                    </ol>
                </div>
                <h4 class="page-title">Setting Akses: <span class="text-primary"><?php echo $level['nama_level']; ?></span></h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form id="form_setting">
                        <input type="hidden" name="id_level" value="<?php echo $level['id']; ?>">

                        <div class="row">
                            <?php if (!empty($grouped_akses)) : ?>
                                <?php $g_id = 0;
                                foreach ($grouped_akses as $grup_nama => $items) : $g_id++; ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" onchange="checkAllGroup(<?php echo $g_id; ?>, this)">
                                                    <label class="form-check-label fw-bold text-dark">
                                                        <?php echo $grup_nama; ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body p-3" style="max-height: 250px; overflow-y:auto;">
                                                <?php foreach ($items as $item) :
                                                    $checked = in_array($item->id_akses, $current_akses) ? 'checked' : '';
                                                ?>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input group-<?php echo $g_id; ?>" type="checkbox" name="hak_akses[]" value="<?php echo $item->id_akses; ?>" id="akses_<?php echo $item->id_akses; ?>" <?php echo $checked; ?>>
                                                        <label class="form-check-label" for="akses_<?php echo $item->id_akses; ?>">
                                                            <?php echo $item->nama_hak_akses; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">Data Hak Akses kosong.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-sm-10 ms-auto">
                                <button type="button" onclick="simpanSetting(event);" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Simpan</button>
                                <a href="<?php echo base_url(); ?>admin/level">
                                    <button type="button" class="btn btn-warning">
                                        <i class="fas fa-reply me-2"></i>Kembali
                                    </button>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>