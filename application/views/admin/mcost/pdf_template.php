<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>COST</title>

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
            width: 180px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        .info-table td {
            padding: 3px 5px;
        }

        .info-label {
            width: 110px;
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

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .total-box {
            margin-top: 10px;
            width: 40%;
            float: right;
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
            <img src="https://apja.co.id/assets/img/apja-logo.png"
                 class="logo"
                 style="margin-bottom: 15px">
        </td>
        <td class="title" style="vertical-align: middle">
            COST
        </td>
    </tr>
</table>

<hr>

<!-- ================= INFO ================= -->
<table class="info-table" style="margin-top: 15px">
    <tr>
        <td class="info-label">Plant</td>
        <td colspan="3">: <?= $header->PLANT_NAME; ?></td>
        <td class="info-label">Cost Date</td>
        <td>: <?= date('d-M-Y', strtotime($header->COST_DATE)); ?></td>
    </tr>
    <tr>
        <td class="info-label">Payment</td>
        <td colspan="3">: <?= $header->PEMBAYARAN; ?></td>
        <td class="info-label">Cost No</td>
        <td>: #<?= $header->COST; ?></td>
    </tr>
    <tr>
        <td class="info-label">Slip No</td>
        <td colspan="5">: #<?= $header->SLIP_NO ?: '-'; ?></td>
    </tr>
</table>

<br>

<!-- ================= DETAIL ================= -->
<table class="detail-table">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th>Cost Type</th>
            <th width="8%">Qty</th>
            <th width="15%">Jumlah</th>
            <th width="18%">Total</th>
            <th>Remark</th>
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
            <td><?= $d->TIPE_COST; ?> - <?= $d->COST_NAME; ?></td>
            <td class="text-center"><?= (int)$d->QTY; ?></td>
            <td class="text-right">
                Rp <?= number_format($d->JUMLAH, 0, ',', '.'); ?>
            </td>
            <td class="text-right">
                Rp <?= number_format($d->TOTAL, 0, ',', '.'); ?>
            </td>
            <td><?= $d->REMARK ?: '-'; ?></td>
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