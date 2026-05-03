<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Receipt</title>

    <style>
        @page {
            size: 58mm 800pt;
            margin: 2mm;
        }

        body {
            width: auto;
            margin: 0;
            padding: 0;
            font-family: monospace;
            font-size: 11px;
        }

        .center {
            text-align: center;
            font-size: 12px;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }
    </style>
</head>
<body>

<!-- ================= HEADER ================= -->
<div class="center bold">
    <img src="https://apja.co.id/assets/img/apja-logo.png" class="logo" style="width: 150px; margin-bottom: 10px">
    <br>
    <?= strtoupper($header->PLANT_NAME); ?>
</div>
<?php
function toNumber($val)
{
    if ($val === null || $val === '') return 0;

    // hapus titik ribuan
    return (float) str_replace('.', '', $val);
}
?>
<div class="line"></div>

<table>
    <tr>
        <td>No</td>
        <td>:</td>
        <td>#<?= $header->SALES; ?></td>
    </tr>
    <tr>
        <td>Tgl</td>
        <td>:</td>
        <td><?= date('d/M/Y', strtotime($header->SALES_DATE)); ?></td>
    </tr>
    <tr>
        <td>Cust</td>
        <td>:</td>
        <td><?= $header->CUSTOMER_NAME; ?></td>
    </tr>
    <tr>
        <td>Tipe</td>
        <td>:</td>
        <td><?= $header->PEMBAYARAN ?: '-'; ?></td>
    </tr>
</table>

<div class="line"></div>

<!-- ================= DETAIL ================= -->
<?php
$grandTotal   = 0;
$totalDiskon = 0;
?>

<table>
<?php foreach ($detail as $d): 
    $harga   = (float) str_replace('.', '', $d->HARGA);
    $diskon  = (float) str_replace('.', '', $d->DISCOUNT);
    $amount  = (float) str_replace('.', '', $d->AMOUNT);

    $grandTotal   += $amount;
    $totalDiskon  += $diskon;
?>
    <tr>
        <td colspan="2"><?= $d->ITEM; ?> <?= $d->FULL_NAME; ?></td>
    </tr>
    <tr>
        <td>
            <?= number_format($d->QTY, 2, ',', '.'); ?> x <?= number_format($harga, 0, ',', '.'); ?>
        </td>
        <td class="right">
            <?= number_format($amount, 0, ',', '.'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<div class="line"></div>

<!-- ================= TOTAL ================= -->
<table>
    <tr>
        <td>Total</td>
        <td class="right">
            <?= number_format($grandTotal, 0, ',', '.'); ?>
        </td>
    </tr>
    <tr>
        <td>Diskon</td>
        <td class="right">
            <?= number_format($totalDiskon, 0, ',', '.'); ?>
        </td>
    </tr>
    <tr class="bold">
        <td>Grand Total</td>
        <td class="right">
            <?= number_format(toNumber($header->AMOUNT), 0, ',', '.'); ?>
        </td>
    </tr>
    <!-- <tr>
        <td>Bayar</td>
        <td class="right">
            <?= number_format(toNumber($header->PEMBAYARAN), 0, ',', '.'); ?>
        </td>
    </tr>
    <tr>
        <td>Sisa</td>
        <td class="right">
            <?= number_format(toNumber($header->REMAIN), 0, ',', '.'); ?>
        </td>
    </tr> -->
</table>

<div class="line"></div>

<!-- ================= FOOTER ================= -->
<div class="center">
    <!-- <?= strtoupper($header->JENIS_PAY); ?><br> -->
    <!-- <?= strtoupper($header->STATUS); ?><br><br> -->
    Terima Kasih 🙏
</div>

<!-- <?php if ($header->REMARK): ?>
    <div class="line"></div>
    Remark:<br>
    <?= $header->REMARK; ?>
<?php endif; ?> -->

</body>
</html>