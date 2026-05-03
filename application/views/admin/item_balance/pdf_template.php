<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Receive - <?= $header['RECEIVE'] ?></title>
    <style>
        body{font-family: sans-serif; font-size: 12px;}
        table{width:100%; border-collapse: collapse;}
        th,td{border:1px solid #333; padding:6px; text-align:left;}
        .no-border td{border:none;}
        .right{text-align:right;}
        .center{text-align:center;}
    </style>
</head>
<body>
    <h3>SLIP RECEIVE</h3>
    <table class="no-border">
        <tr><td><strong>Receive</strong></td><td><?= $header['RECEIVE'] ?></td><td><strong>Slip No</strong></td><td><?= $header['SLIP_NO'] ?></td></tr>
        <tr><td><strong>NOTA</strong></td><td><?= $header['NOTA'] ?></td><td><strong>Tanggal</strong></td><td><?= $header['RECEIVE_DATE'] ?></td></tr>
        <tr><td><strong>PO</strong></td><td><?= $header['PO'] ?></td><td><strong>Supplier</strong></td><td><?= $header['SUPPLIER'] ?> - <?= $header['SUPPLIER_NAME'] ?></td></tr>
        <tr><td colspan="4"><strong>Remark:</strong> <?= $header['REMARK'] ?></td></tr>
    </table>

    <br/>

    <table>
        <thead>
            <tr>
                <th style="width:6%;">No</th>
                <th>Material</th>
                <th style="width:12%;">Jumlah</th>
                <th style="width:12%;">Berat</th>
                <th style="width:16%;">Harga</th>
                <th style="width:16%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; $grand=0; foreach($detail as $d): ?>
                <tr>
                    <td class="center"><?= $no++ ?></td>
                    <td><?= $d['MATERIAL'] ?> <?= isset($d['MATERIAL_NAME']) ? ' - '.$d['MATERIAL_NAME'] : '' ?></td>
                    <td class="right"><?= $d['JUMLAH'] ?></td>
                    <td class="right"><?= $d['BERAT'] ?></td>
                    <td class="right"><?= $d['HARGA'] ?></td>
                    <td class="right"><?= $d['TOTAL'] ?></td>
                </tr>
            <?php $grand += floatval(str_replace(',','',$d['TOTAL'])); endforeach; ?>
            <tr>
                <td colspan="5" class="right"><strong>Grand Total</strong></td>
                <td class="right"><strong><?= number_format($grand,2,',','.') ?></strong></td>
            </tr>
        </tbody>
    </table>

    <br/><br/>
    <table class="no-border">
        <tr><td>Prepared by</td><td>Checked</td><td>Received by</td></tr>
        <tr style="height:50px"><td></td><td></td><td></td></tr>
    </table>
</body>
</html>
