<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; margin: 0; padding: 10px; }
        .container { width: 300px; margin: auto; }
        .header { text-align: center; padding-bottom: 5px; border-bottom: 1px dashed #000; }
        .header h4, .header p { margin: 0; }
        .content { margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .footer { text-align: center; margin-top: 10px; }
        @media print {
            body { margin: 0; }
            .container { margin: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h4>Klinik Metrica</h4>
            <p>Jln. Kesehatan No. 1, Lumajang</p>
            <p>Telp: (0123) 123456</p>
        </div>
        <div class="content">
            <table>
                <tr>
                    <td>Kode Invoice</td>
                    <td class="text-right"><?php echo $data['pembayaran']['kode_invoice']; ?></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td class="text-right"><?php echo $data['pembayaran']['tanggal'] . ' ' . $data['pembayaran']['waktu']; ?></td>
                </tr>
                <tr>
                    <td>Pasien</td>
                    <td class="text-right"><?php echo $data['pembayaran']['nama_pasien']; ?></td>
                </tr>
                 <tr>
                    <td>Dokter</td>
                    <td class="text-right"><?php echo $data['pembayaran']['nama_dokter']; ?></td>
                </tr>
            </table>
            <div class="divider"></div>
            <?php if (!empty($data['tindakan'])): ?>
                <strong>Tindakan:</strong>
                <table>
                    <?php foreach($data['tindakan'] as $item): ?>
                    <tr>
                        <td><?php echo $item['tindakan']; ?></td>
                        <td class="text-right">Rp <?php echo number_format($item['harga'], 0, '.', ','); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
             <?php endif; ?>
            <!-- <php if (!empty($data['resep']) || !empty($data['racikan'])): ?>
                <div class="divider"></div>
                <strong>Obat:</strong>
                <table>
                    <php foreach($data['resep'] as $item): 
                        $subtotal = ($item['harga'] + $item['laba']) * $item['jumlah'];
                    ?>
                    <tr>
                        <td><php echo $item['nama_barang'] . ' (x' . $item['jumlah'] . ')'; ?></td>
                        <td class="text-right">Rp <php echo number_format($subtotal, 0, '.', ','); ?></td>
                    </tr>
                    <php endforeach; ?>
                    <php foreach($data['racikan'] as $item): 
                        $subtotal = $item['sub_total_harga'] + $item['sub_total_laba'];
                    ?>
                    <tr>
                        <td><php echo $item['nama_racikan'] . ' (x' . $item['jumlah'] . ')'; ?></td>
                        <td class="text-right">Rp <php echo number_format($subtotal, 0, '.', ','); ?></td>
                    </tr>
                    <php endforeach; ?>
                </table>
            <php endif; ?> -->
            <div class="divider"></div>
            <table>
                <tr>
                    <td>Subtotal</td>
                    <!-- <td class="text-right">Rp <php echo number_format($data['pembayaran']['total_invoice'], 0, '.', ','); ?></td> -->
                    <td class="text-right">Rp <?php echo number_format($data['pembayaran']['biaya_tindakan'], 0, '.', ','); ?></td>
                </tr>
                <tr>
                    <td>Total Bayar</td>
                    <td class="text-right">Rp <?php echo number_format($data['pembayaran']['biaya_tindakan'], 0, '.', ','); ?></td>
                </tr>
                <tr>
                    <td>Metode</td>
                    <td class="text-right"><?php echo $data['pembayaran']['metode_pembayaran']; ?></td>
                </tr>
                 <tr>
                    <td>Bayar</td>
                    <td class="text-right">Rp <?php echo number_format($data['pembayaran']['bayar'], 0, '.', ','); ?></td>
                </tr>
                <tr>
                    <td>Kembali</td>
                    <td class="text-right">Rp <?php echo number_format($data['pembayaran']['kembali'], 0, '.', ','); ?></td>
                </tr>
            </table>
            <div class="divider"></div>
        </div>
        <div class="footer">
            <p>Terima kasih banyak atas kunjungannya.</p>
            <p>Semoga lekas sembuh.</p>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>