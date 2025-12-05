<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('kepegawaian/dokter'); ?>">Dokter</a>
                        </li>
                        <li class="breadcrumb-item active"><?php echo $title; ?></li>
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
                    <h4 class="card-title">Jadwal Dokter: <?php echo $dokter['nama_pegawai']; ?>
                        (<?php echo $dokter['nama_poli']; ?>)</h4>
                    <!-- sesuai hari -->
                    <div class="d-flex">
                        <a href="<?php echo base_url('kepegawaian/dokter'); ?>"><button type="button"
                                class="btn btn-warning"><i class="fas fa-reply me-2"></i>Kembali</button></a>
                        <a href="<?php echo base_url(); ?>kepegawaian/dokter/view_tambaa/<?php echo $dokter['id'] ?>"><button
                                type="button" class="btn ms-1 btn-success"><i class="fas fa-plus"></i>
                                Tambah</button></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 table-centered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="data-jadwal">
                                <?php if (!empty($jadwal)) {
                                    foreach ($jadwal as $j) { ?>
                                        <tr>
                                            <td><?php echo $j['hari']; ?></td>
                                            <td><?php echo $j['jam_mulai']; ?></td>
                                            <td><?php echo $j['jam_selesai']; ?></td>
                                            <td class="text-center"><button class="btn btn-sm btn-info"
                                                    onclick="editJadwal('<?php echo $dokter['id']; ?>', '<?php echo $j['hari']; ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="hapusJadwal('<?php echo $j['id']; ?>', '<?php echo $j['hari']; ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Dokter ini tidak ada jadwal</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div id="pagination"></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-md-6">&nbsp;</div>
                                <label
                                    class="col-md-3 control-label d-flex align-items-center justify-content-end">Jumlah
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
                        </di>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let $rows = $('#data-jadwal tr');

            function paging($selector) {
                let jumlah_tampil = $('#jumlah_tampil').val();

                if (typeof $selector == 'undefined') {
                    $selector = $rows;
                }

                window.tp = new Pagination('#pagination', {
                    itemsCount: $selector.length,
                    pageSize: parseInt(jumlah_tampil),
                    onPageSizeChange: function (ps) {
                        console.log('Jumlah tampil berubah ke ' + ps);
                    },
                    onPageChange: function (paging) {
                        var start = paging.pageSize * (paging.currentPage - 1),
                            end = start + paging.pageSize;

                        $selector.hide();
                        for (var i = start; i < end; i++) {
                            $selector.eq(i).show();
                        }
                    }
                });
            }
            paging();
            $('#jumlah_tampil').on('change', function () {
                paging();
            });
        });



        function editJadwal(id, hari) {
            window.location.href = `<?= base_url('kepegawaian/dokter/jadwal_edit/') ?>${id}/${hari}`;
        }
        function hapusJadwal(id, hari) {
            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: `Jadwal hari ${hari} akan dihapus.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#aaa",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo base_url('kepegawaian/dokter/hapus_jadwal'); ?>',
                        method: 'POST',
                        data: {
                            id: id,
                            // hari: hari
                        },
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
                            if (res.status) {
                                Swal.fire("Berhasil!", res.message, "success").then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire("Gagal!", res.message, "error");
                            }
                        }
                    });
                }
            });
        }

    </script>