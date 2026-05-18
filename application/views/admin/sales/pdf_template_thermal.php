<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>
        Sales Receipt
    </title>

    <style>

        @page{
            size:58mm 800pt;
            margin:2mm;
        }

        body{
            font-family: monospace;
            font-size:11px;
        }

        .center{
            text-align:center;
        }

        .right{
            text-align:right;
        }

        .bold{
            font-weight:bold;
        }

        .line{
            border-top:1px dashed #000;
            margin:5px 0;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        td{
            padding:2px 0;
            vertical-align: top;
        }

    </style>

</head>

<body>

<div class="center bold">

    <img
        src="https://ptabc.co.id/assets/img/abc-trans.png"
        style="width:140px; margin-bottom:10px;">

    <br>
    <h3>PT. ABADI BERSAMA CERAH</h3>
    <?= strtoupper($header->PLANT_NAME); ?>

</div>

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
        <td><?= date('d/m/Y', strtotime($header->SALES_DATE)); ?></td>
    </tr>

    <tr>
        <td>Cust</td>
        <td>:</td>
        <td><?= $header->CUSTOMER_NAME; ?></td>
    </tr>

    <tr>
        <td>Bayar</td>
        <td>:</td>
        <td><?= $header->PEMBAYARAN; ?></td>
    </tr>

</table>

<div class="line"></div>

<?php $grandTotal = 0; ?>

<table>

<?php foreach($detail as $d):

    $total = (float) $d->TOTAL;

    $grandTotal += $total;

?>

<tr>

    <td colspan="2">
        <?= $d->MATERIAL_NAME; ?>
    </td>

</tr>

<tr>

    <td>

        <?= number_format($d->BERAT, 2, ',', '.'); ?>
        x
        <?= number_format($d->HARGA, 0, ',', '.'); ?>

    </td>

    <td class="right">

        <?= number_format($total, 0, ',', '.'); ?>

    </td>

</tr>

<?php endforeach; ?>

</table>

<div class="line"></div>

<table>

    <tr class="bold">

        <td>
            Grand Total
        </td>

        <td class="right">

            <?= number_format($grandTotal, 0, ',', '.'); ?>

        </td>

    </tr>

</table>

<div class="line"></div>

<div class="center">

    Terima Kasih 🙏

</div>

</body>
</html>