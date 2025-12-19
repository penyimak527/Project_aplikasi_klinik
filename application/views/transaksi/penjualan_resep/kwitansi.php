<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .container { width: 900px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header .info-klinik h2, .header .info-klinik p { margin: 0; }
        .header .title h1 { margin: 0; text-align: right; }
        .info-pasien { margin-top: 20px; display: flex; justify-content: space-between; }
        .detail { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background-color: #f2f2f2; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th.text-right, td.text-right { text-align: right; }
        .summary { margin-top: 20px; width: 40%; float: right; }
        .footer { margin-top: 120px; display: flex; justify-content: space-between; text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .container { border: none; width: 100%; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="info-klinik">
                <h2>Apotek Metrica</h2>
                <p>Jln. Kesehatan No. 1, Lumajang, Jawa Timur</p>
                <p>Telp: (0123) 123456</p>
            </div>
            <div class="title"><h1>KWITANSI</h1></div>
        </div>
        <div class="info-pasien">
            <div><strong>Pasien:</strong><p><?php echo $data['pembayaran']['nama_customer']; ?></p></div>
            <div><strong>Kode Invoice:</strong> <?php echo $data['pembayaran']['kode_invoice']; ?><br>
                 <strong>Tanggal:</strong> <?php echo $data['pembayaran']['tanggal']; ?><br>
                 <strong>Dokter:</strong> <?php echo $data['pembayaran']['nama_dokter']; ?>
            </div>
        </div>
        <div class="detail">
            <table>
                <thead><tr><th>Nomor</th><th>Deskripsi</th><th class="text-right">Harga</th></tr></thead>
                <tbody>
                    <?php
                        $no = 1;
                        if(!empty($data['tindakan'])) {
                            echo '<tr><td colspan="3"><strong>Tindakan</strong></td></tr>';
                            foreach($data['tindakan'] as $item) {
                                echo '<tr><td>'.$no++.'</td><td>'.$item['tindakan'].'</td><td class="text-right">Rp '.number_format($item['harga'], 0, ',', '.').'</td></tr>';
                            }
                        }
                        if(!empty($data['resep']) || !empty($data['racikan'])) {
                            echo '<tr><td colspan="3"><strong>Obat & Resep</strong></td></tr>';
                            foreach($data['resep'] as $item) {
                                $subtotal = ($item['harga'] + $item['laba']) * $item['jumlah'];
                                echo '<tr><td>'.$no++.'</td><td>'.$item['nama_barang'].' ('.$item['jumlah'].' '.$item['satuan_barang'].')</td><td class="text-right">Rp '.number_format($subtotal, 0, ',', '.').'</td></tr>';
                            }
                            foreach($data['racikan'] as $item) {
                                $subtotal = $item['sub_total_harga'] + $item['sub_total_laba'];
                                echo '<tr><td>'.$no++.'</td><td>'.$item['nama_racikan'].' ('.$item['jumlah'].')</td><td class="text-right">Rp '.number_format($subtotal, 0, ',', '.').'</td></tr>';
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="summary clearfix">
             <table>
                <tr><td><strong>Total Tagihan</strong></td><td class="text-right"><strong>Rp <?php echo number_format($data['pembayaran']['total_invoice'], 0, ',', '.'); ?></strong></td></tr>
                <tr><td>Bayar</td><td class="text-right">Rp <?php echo number_format($data['pembayaran']['bayar'], 0, ',', '.'); ?></td></tr>
                <tr><td>Kembali</td><td class="text-right">Rp <?php echo number_format($data['pembayaran']['kembali'], 0, ',', '.'); ?></td></tr>
            </table>
        </div>
        <div class="footer">
            <div><p><br><br>Penerima,</p><br><br><br><p>( <?php echo $data['pembayaran']['nama_customer']; ?> )</p></div>
            <div><p>Lumajang, <?php echo $data['pembayaran']['tanggal']; ?></p><p>Hormat Kami,</p><br><br><br><p>( Kasir )</p></div>
        </div>
    </div>
    <script>
        window.onload = () => window.print();
        window.onafterprint = () => window.close();
    </script>
</body>
</html>