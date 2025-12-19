<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; margin: 0; padding: 10px; }
        .container { width: 300px; margin: auto; }
        .header { text-align: center; padding-bottom: 5px; border-bottom: 1px dashed #000; }
        .header h4, .header p { margin: 0; }
        .content { margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .footer { text-align: center; margin-top: 10px; }
        @media print { body { margin: 0; } .container { margin: 0; width: 100%; } }
    </style>
</head>
<body onload="window.print()" onafterprint="window.close()">
    <div class="container">
        <div class="header">
            <h4>Apotek Metrica</h4>
            <p>Jln. Kesehatan No. 1, Lumajang</p>
            <p>Telp: (0123) 123456</p>
        </div>
        <div class="content">
            <table>
                <tr><td>Kode Invoice</td><td class="text-right"><?php echo $data['pembayaran']['kode_invoice']; ?></td></tr>
                <tr><td>Tanggal</td><td class="text-right"><?php echo $data['pembayaran']['tanggal'] . ' ' . $data['pembayaran']['waktu']; ?></td></tr>
                <tr><td>Pembeli</td><td class="text-right"><?php echo $data['pembayaran']['nama_customer']; ?></td></tr>
            </table>
            <div class="divider"></div>
            <?php if (!empty($data['resep'])): ?>
                <strong>Item:</strong>
                <table>
                    <?php foreach($data['resep'] as $item): $subtotal = $item['sub_total_harga']; ?>
                    <tr><td><?php echo $item['nama_barang'] . ' (x' . $item['jumlah'] . ')'; ?></td><td class="text-right">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td></tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            <div class="divider"></div>
            <table>
                <tr><td>Subtotal</td><td class="text-right">Rp <?php echo number_format($data['pembayaran']['total_invoice'], 0, ',', '.'); ?></td></tr>
                <tr><td>Metode</td><td class="text-right"><?php echo $data['pembayaran']['metode_pembayaran']; ?></td></tr>
                <tr><td>Bayar</td><td class="text-right">Rp <?php echo number_format($data['pembayaran']['bayar'], 0, ',', '.'); ?></td></tr>
                <tr><td>Kembali</td><td class="text-right">Rp <?php echo number_format($data['pembayaran']['kembali'], 0, ',', '.'); ?></td></tr>
            </table>
            <div class="divider"></div>
        </div>
        <div class="footer">
            <p>Terima kasih telah berbelanja.</p>
        </div>
    </div>
</body>
</html>