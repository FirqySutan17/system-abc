<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash In</title>

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

        .doc-title {
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
            <img src="https://apja.co.id/assets/img/apja-logo.png"
                 class="logo"
                 style="width: 190px; margin-bottom: 20px">
        </td>
        <td class="doc-title" style="vertical-align: middle">
            CASH IN
        </td>
    </tr>
</table>

<hr>

<!-- ================= INFO ================= -->
<table class="info-table" style="margin-top: 15px">
    <tr>
        <td class="info-label">Plant</td>
        <td colspan="3">
            : <?= $header->PLANT_NAME; ?> - PT. ARTHA PRATAMA JAYA ABADI
        </td>
        <td class="info-label">Cash In Date</td>
        <td>
            : <?= date('d-M-Y', strtotime($header->CASHIN_DATE)); ?>
        </td>
    </tr>
    <tr>
        <td class="info-label">Customer</td>
        <td colspan="3">
            : <?= $header->CUSTOMER; ?> - <?= $header->FULL_NAME; ?>
        </td>
        <td class="info-label">Cash In No</td>
        <td>
            : #<?= $header->CASH_IN; ?>
        </td>
    </tr>
    <tr>
        <td class="info-label">Pembayaran</td>
        <td colspan="3">
            : <?= $header->PEMBAYARAN ?: '-'; ?>
        </td>
        <td class="info-label">Slip No</td>
        <td>
            : #<?= $header->SLIP_NO ?: '-'; ?>
        </td>
    </tr>
    <tr>
        <td class="info-label">No Rek</td>
        <td colspan="3">
            : <?= $header->REK_NAME ?: '-'; ?>
        </td>
        <td class="info-label">No. Bon</td>
        <td>
            : #<?= $header->BON ?: '-'; ?>
        </td>
    </tr>
</table>

<br>

<!-- ================= DETAIL ================= -->
<table class="detail-table">
    <thead>
        <tr>
            <th width="4%">No</th>
            <th width="15%">Sales</th>
            <th width="15%">Slip Asal</th>
            <th width="15%">Slip Offset</th>
            <th width="12%">Tanggal Offset</th>
            <th width="17%">Invoice Amount</th>
            <th width="17%">Offset Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalOffset = 0;
        $no = 1;
        foreach ($detail as $d):
            $totalOffset += (float)$d->AMOUNT_OFFSET;
        ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>
            <td><?= $d->SALES ?: '-'; ?></td>
            <td><?= $d->ORG_SLIP_NO; ?></td>
            <td><?= $d->SLIP_NO; ?></td>
            <td class="text-center">
                <?= date('d-M-Y', strtotime($d->DATE_OFFSET)); ?>
            </td>
            <td class="text-right">
                Rp <?= number_format($d->AMOUNT_INVOICE, 2, ',', '.'); ?>
            </td>
            <td class="text-right">
                Rp <?= number_format($d->AMOUNT_OFFSET, 2, ',', '.'); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- ================= TOTAL ================= -->
<table class="total-box">
    <tr>
        <td><b>Total Cash In</b></td>
        <td class="text-right">
            <b>Rp <?= number_format($totalOffset, 2, ',', '.'); ?></b>
        </td>
    </tr>
</table>

</body>
</html>