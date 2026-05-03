<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; }
        th { background:#f0f0f0; text-align:center; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }
        .no-border td { border:none; }
    </style>
</head>
<body>

<h3 style="text-align:center">PRODUCTION REPORT</h3>

<table class="no-border">
    <tr>
        <td width="20%">Production</td>
        <td width="30%">: <?= $header->PRODUCTION ?></td>
        <td width="20%">Plant</td>
        <td width="30%">: <?= $header->PLANT_NAME ?></td>
    </tr>
    <tr>
        <td>No Receive LB</td>
        <td>: <?= $header->RECEIVE_LB ?></td>
        <td>Tanggal Receive</td>
        <td>: <?= date('d/m/Y', strtotime($header->RECEIVE_DATE)) ?></td>
    </tr>
    <tr>
        <td>Supplier</td>
        <td>: <?= $header->SUPPLIER ?></td>
        <td>No DO</td>
        <td>: <?= $header->DO ?></td>
    </tr>
    <tr>
        <td>Remark</td>
        <td colspan="3">: <?= $header->REMARK ?></td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="30%">Item</th>
            <th width="15%">Qty</th>
            <th width="15%">Berat (Kg)</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $totalQty = 0;
            $totalBerat = 0;
            $no = 1;
        ?>
        <?php foreach ($detail as $row): ?>
            <?php
                $totalQty += $row->QTY;
                $totalBerat += $row->BERAT;
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $row->ITEM ?> - <?= $row->ITEM_NAME ?></td>
                <td class="text-right"><?= number_format($row->QTY, 2, ',', '.') ?></td>
                <td class="text-right"><?= number_format($row->BERAT, 2, ',', '.') ?></td>
                <td><?= $row->REMARK ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align:right"><b>TOTAL</b></td>
            <td style="text-align:right"><b><?= number_format($totalQty,2,',','.') ?></b></td>
            <td style="text-align:right"><b><?= number_format($totalBerat,2,',','.') ?></b></td>
            <td></td>
        </tr>
    </tfoot>
</table>

</body>
</html>