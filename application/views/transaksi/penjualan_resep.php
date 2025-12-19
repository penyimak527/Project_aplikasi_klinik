<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end"><ol class="breadcrumb">
                    <li class="breadcrumb-item"><?php echo $title; ?></li>
                </div>
                <h4 class="page-title"><?php echo $title; ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Data Resep Menunggu Pembayaran</h4></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="cari" placeholder="Cari Kode, Pasien, atau Dokter...">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover" id="table-data">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th><th>Kode Invoice</th><th>Nama Pasien</th><th>Dokter</th><th>Tanggal</th><th>Total Tagihan</th><th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
         
    <div class="container-fluid">
    <div class="row mt-3">
      <div class="col-sm-6"><div id="pagination"></div></div>
    <div class="col-sm-6">
        <div class="row">
        <!-- <div class="col-md-6">&nbsp;</div> -->
        <label class="col-md-9 control-label d-flex align-items-center justify-content-end">Jumlah Tampil</label>
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
</div>
   </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
      $("#jumlah_tampil").change(function () {
            get_data();
        })
    


    get_data();
    $('#cari').keyup(() => get_data());

});
 function get_data() {
         let count_header = $(`#table-data thead tr th`).length;
        $.ajax({
            url: '<?php echo base_url(); ?>transaksi/penjualan_resep/result_data',
            type: "POST",
            data: { cari: $('#cari').val() },
            dataType: "json",
            beforeSend: () => $('#table-data tbody').html(`<tr><td colspan="${count_header}" class="text-center"><img src="<?php echo base_url(); ?>assets/loading-table.gif" width="60"></td></tr>`),
            success: (res) => {
                let table = "";
                if (res.result) {
                    res.data.forEach((item, i) => {
                        let totalHarga = parseInt(item.total_harga).toLocaleString('id-ID');
                        
                        let tanggal;
                        const tgl_parts = String(item.tanggal).split('-'); 
                        
                        if (tgl_parts.length === 3) {
                            const tgl_js = new Date(`${tgl_parts[2]}-${tgl_parts[1]}-${tgl_parts[0]}`);
                            
                            if (!isNaN(tgl_js)) {
                                const day = String(tgl_js.getDate()).padStart(2, '0');
                                const month = String(tgl_js.getMonth() + 1).padStart(2, '0');
                                const year = tgl_js.getFullYear();
                                tanggal = `${day}-${month}-${year}`; 
                            } else {
                                tanggal = 'Invalid Date';
                            }
                        } else {
                            tanggal = 'Invalid Date';  
                        }
                        
                        let prosesUrl = `<?php echo base_url(); ?>transaksi/penjualan_resep/proses/${item.id}`;

                        table += `
                            <tr>
                                <td>${i + 1}</td>
                                <td>${item.kode_invoice}</td>
                                <td>${item.nama_customer}</td>
                                <td>${item.nama_dokter}</td>
                                <td>${tanggal}</td>
                                <td>Rp ${totalHarga}</td>
                                <td class="text-center">
                                    <a href="${prosesUrl}" class="btn btn-success btn-sm">
                                        <i class="fas fa-cash-register"></i> Proses Bayar
                                    </a>
                                </td>
                            </tr>`;
                    });
                } else {
                    table = `<tr><td colspan="7" class="text-center">${res.message}</td></tr>`;
                }
                $('#table-data tbody').html(table);
            paging(); 
            }
        });
    }
        function paging($selector){
        var jumlah_tampil = $('#jumlah_tampil').val();
        if(typeof $selector == 'undefined') { $selector = $("#table-data tbody tr"); }
        window.tp = new Pagination('#pagination', {
            itemsCount:$selector.length,
            pageSize : parseInt(jumlah_tampil),
            onPageChange: function (paging) {
                var start = paging.pageSize * (paging.currentPage - 1), end = start + paging.pageSize, $rows = $selector;
                $rows.hide();
                for (var i = start; i < end; i++) { $rows.eq(i).show(); }
            }
        });
    }
</script>   