<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>
        Sales Invoice
    </title>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size: 10px;
            color:#000;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        .header-table td{
            vertical-align: top;
        }

        .logo{
            width:180px;
        }

        .title{
            font-size:16px;
            font-weight:bold;
            text-align:right;
        }

        .info-table td{
            padding:3px 5px;
        }

        .info-label{
            width:95px;
            font-weight:bold;
        }

        .detail-table th,
        .detail-table td{
            border:1px solid #000;
            padding:5px;
        }

        .detail-table th{
            background:#eee;
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .total-box{
            margin-top:10px;
            width:40%;
            float:right;
        }

        .total-box td{
            padding:4px;
        }

        .remark{
            margin-top:30px;
            clear:both;
        }

    </style>
</head>

<body>

<table class="header-table">

    <tr>

        <td>

            <img
                src="https://ptabc.co.id/assets/img/abc-trans.png"
                class="logo">
            <h4>PT. ABADI BERSAMA CERAH</h4>
        </td>

        <td class="title">

            SALES INVOICE

        </td>

    </tr>

</table>

<hr>

<table class="info-table" style="margin-top:15px">

    <tr>

        <td class="info-label">
            Plant
        </td>

        <td colspan="3">
            : <?= $header->PLANT_NAME; ?>
        </td>

        <td class="info-label">
            Invoice Date
        </td>

        <td>
            : <?= date('d-M-Y', strtotime($header->SALES_DATE)); ?>
        </td>

    </tr>

    <tr>

        <td class="info-label">
            Customer
        </td>

        <td colspan="3">
            : (<?= $header->CUSTOMER; ?>)
            <?= $header->CUSTOMER_NAME ?: '-'; ?>
        </td>

        <td class="info-label">
            Invoice No
        </td>

        <td>
            : #<?= $header->SALES; ?>
        </td>

    </tr>

    <tr>

        <td class="info-label">
            Payment
        </td>

        <td colspan="3">
            : <?= $header->PAYMENT_INFO; ?>
        </td>

        <td class="info-label">
            Nota
        </td>

        <td>
            : <?= $header->NOTA ?: '-'; ?>
        </td>

    </tr>

    <tr>

        <td class="info-label">
            Status
        </td>

        <td>
            : <?= $header->STATUS; ?>
        </td>

    </tr>

</table>

<br>

<table class="detail-table">

    <thead>

        <tr>

            <th width="4%">
                No
            </th>

            <th>
                Material
            </th>

            <th width="10%">
                Qty
            </th>

            <th width="10%">
                Berat
            </th>

            <th width="14%">
                Harga / Kg
            </th>

            <th width="15%">
                Total
            </th>

        </tr>

    </thead>

    <tbody>

        <?php
        $no = 1;

        foreach($detail as $d):
        ?>

        <tr>

            <td class="text-center">
                <?= $no++; ?>
            </td>

            <td>
                <b>
                    <?= $d->MATERIAL_NAME; ?>
                </b>
                (<?= $d->MATERIAL; ?>)
            </td>

            <td class="text-right">
                <?= number_format($d->JUMLAH, 2, ',', '.'); ?>
            </td>

            <td class="text-right">
                <?= number_format($d->BERAT, 2, ',', '.'); ?>
            </td>

            <td class="text-right">
                Rp <?= number_format($d->HARGA, 0, ',', '.'); ?>
            </td>

            <td class="text-right">
                Rp <?= number_format($d->TOTAL, 0, ',', '.'); ?>
            </td>

        </tr>

        <?php endforeach; ?>

    </tbody>

</table>

<table class="total-box">

    <tr>

        <td>
            <b>Total Qty</b>
        </td>

        <td class="text-right">
            <?= number_format($summary['total_qty'], 2, ',', '.'); ?>
        </td>

    </tr>

    <tr>

        <td>
            <b>Total Berat</b>
        </td>

        <td class="text-right">
            <?= number_format($summary['total_berat'], 2, ',', '.'); ?>
        </td>

    </tr>

    <tr>

        <td>
            <b>Grand Total</b>
        </td>

        <td class="text-right">
            <b>
                Rp <?= number_format($summary['grand_total'], 0, ',', '.'); ?>
            </b>
        </td>

    </tr>

</table>

<div class="remark">

    <b>Remark:</b><br>

    <?= $header->REMARK ?: '-'; ?>

</div>

</body>
</html>