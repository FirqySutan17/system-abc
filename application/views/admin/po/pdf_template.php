<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            width: 90px;
        }

        .po-title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        .info-table td {
            padding: 3px 5px;
        }

        .info-label {
            width: 90px;
            font-weight: bold;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .detail-table th {
            background-color: #eee;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-box {
            margin-top: 10px;
            width: 40%;
            float: right;
        }

        .total-box td {
            padding: 4px;
        }

        .remark {
            margin-top: 30px;
            clear: both;
        }
    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
<table class="header-table">
    <tr>
        <td>
            <!-- GANTI LOGO SENDIRI -->
            <img src="https://apja.co.id/assets/img/apja-logo.png" class="logo" style="width: 190px; margin-bottom: 20px">
        </td>
        <td class="po-title" style="vertical-align: middle">
            PURCHASE ORDER
        </td>
    </tr>
</table>

<hr>

<!-- ================= INFO PO ================= -->
<table class="info-table" style="margin-top: 15px">
    <tr>
        <td class="info-label">Plant</td>
        <td colspan="3">: <?= $header->PLANT_NAME; ?> - PT . ARTHA PRATAMA JAYA ABADI</td>
        <td class="info-label">PO Date</td>
        <td>: <?= date('d-M-Y', strtotime($header->PO_DATE)); ?></td>
    </tr>
    <tr>
        <td class="info-label">Supplier</td>
        <td colspan="3">: (<?= $header->SUPPLIER; ?>) <?= $header->SUPPLIER_NAME; ?></td>
        <td class="info-label">PO Number</td>
        <td>: #<?= $header->PO; ?></td>
    </tr>
    <tr>
        
    </tr>
    
</table>

<br>

<!-- ================= DETAIL ================= -->
<table class="detail-table">
    <thead>
        <tr>
            <th width="4%">No</th>
            <th>Material</th>
            <th width="10%">Qty</th>
            <th width="10%">Berat</th>
            <th width="15%">Harga</th>
            <th width="15%">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grandTotal = 0;
        $no = 1;
        foreach ($detail as $d):
            $grandTotal += (float)$d->TOTAL;
        ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>
            <td>(<?= $d->MATERIAL; ?>) <?= $d->MATERIAL_NAME; ?></td>
            <td class="text-right"><?= number_format($d->JUMLAH, 2, ',', '.'); ?></td>
            <td class="text-right"><?= number_format($d->BERAT, 2, ',', '.'); ?></td>
            <td class="text-right">Rp <?= number_format($d->HARGA, 0, ',', '.'); ?></td>
            <td class="text-right">Rp <?= number_format($d->TOTAL, 0, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- ================= TOTAL ================= -->
<table class="total-box">
    <tr>
        <td><b>Grand Total</b></td>
        <td class="text-right">
            <b>Rp <?= number_format($grandTotal, 0, ',', '.'); ?></b>
        </td>
    </tr>
</table>

<!-- ================= REMARK ================= -->
<div class="remark">
    <b>Remark:</b><br>
    <?= $header->REMARK ?: '-'; ?>
</div>

</body>
</html>