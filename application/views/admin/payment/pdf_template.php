<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>
        Payment Slip
    </title>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:11px;
            color:#000;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .header-table td{
            vertical-align:top;
        }

        .logo{
            width:170px;
        }

        .title{
            text-align:right;
            font-size:20px;
            font-weight:bold;
        }

        .sub-title{
            text-align:right;
            font-size:11px;
            color:#666;
        }

        .info-table{
            margin-top:20px;
        }

        .info-table td{
            padding:4px;
        }

        .label{
            width:110px;
            font-weight:bold;
        }

        .detail-table{
            margin-top:20px;
        }

        .detail-table th{
            background:#efefef;
            border:1px solid #000;
            padding:7px;
            text-align:center;
        }

        .detail-table td{
            border:1px solid #000;
            padding:6px;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .total-box{
            width:35%;
            float:right;
            margin-top:15px;
        }

        .total-box td{
            border:1px solid #000;
            padding:8px;
        }

        .remark{
            margin-top:80px;
        }

        .signature{
            margin-top:80px;
        }

        .signature td{
            text-align:center;
            width:33%;
        }

    </style>

</head>

<body>

<!-- ====================================================== -->
<!-- HEADER -->
<!-- ====================================================== -->

<table class="header-table">

    <tr>

        <td width="50%">

            <img
                src="https://ptabc.co.id/assets/img/abc-trans.png"
                class="logo">
<h3>PT. ABADI BERSAMA CERAH</h3>
        </td>

        <td width="50%">

            <div class="title">
                PAYMENT SLIP
            </div>

            <div class="sub-title">
                ACCOUNTING DOCUMENT
            </div>

        </td>

    </tr>

</table>

<hr>

<!-- ====================================================== -->
<!-- INFORMATION -->
<!-- ====================================================== -->

<table class="info-table">

    <tr>

        <td class="label">
            Payment No
        </td>

        <td>
            : <?= $header->PAYMENT; ?>
        </td>

        <td class="label">
            Payment Date
        </td>

        <td>
            : <?= date('d M Y', strtotime($header->PAYMENT_DATE)); ?>
        </td>

    </tr>

    <tr>

        <td class="label">
            Plant
        </td>

        <td>
            : <?= $header->PLANT_NAME; ?>
        </td>

        <td class="label">
            Slip No
        </td>

        <td>
            : <?= $header->SLIP_NO ?: '-'; ?>
        </td>

    </tr>

    <tr>

        <td class="label">
            Supplier
        </td>

        <td>
            :
            <?= $header->SUPPLIER; ?>
            -
            <?= $header->SUPPLIER_NAME; ?>
        </td>

        <td class="label">
            Pembayaran
        </td>

        <td>
            : <?= $header->PEMBAYARAN; ?>
        </td>

    </tr>

</table>

<!-- ====================================================== -->
<!-- DETAIL -->
<!-- ====================================================== -->

<table class="detail-table">

    <thead>

        <tr>

            <th width="4%">
                No
            </th>

            <th width="18%">
                PO No
            </th>

            <th>
                Material
            </th>

            <th width="12%">
                Berat
            </th>

            <th width="12%">
                Harga
            </th>

            <th width="16%">
                Total
            </th>

        </tr>

    </thead>

    <tbody>

        <?php

        $no = 1;

        $grandTotal = 0;

        foreach($detail as $row):

            $grandTotal += $row->TOTAL;

        ?>

        <tr>

            <td class="text-center">

                <?= $no++; ?>

            </td>

            <td class="text-center">

                <?= $row->PO_NO; ?>

            </td>

            <td>

                <?= $row->MATERIAL; ?>
                -
                <?= $row->MATERIAL_NAME; ?>

            </td>

            <td class="text-right">

                <?= number_format($row->BERAT, 2, ',', '.'); ?>

            </td>

            <td class="text-right">

                Rp
                <?= number_format($row->HARGA, 0, ',', '.'); ?>

            </td>

            <td class="text-right">

                Rp
                <?= number_format($row->TOTAL, 0, ',', '.'); ?>

            </td>

        </tr>

        <?php endforeach; ?>

    </tbody>

</table>

<!-- ====================================================== -->
<!-- TOTAL -->
<!-- ====================================================== -->

<table class="total-box">

    <tr>

        <td width="40%">
            <b>Grand Total</b>
        </td>

        <td class="text-right">
            <b>
                Rp
                <?= number_format($grandTotal, 0, ',', '.'); ?>
            </b>
        </td>

    </tr>

</table>

<div style="clear:both"></div>

<!-- ====================================================== -->
<!-- REMARK -->
<!-- ====================================================== -->

<div class="remark">

    <b>Remark :</b>

    <br><br>

    <?= $header->REMARK ?: '-'; ?>

</div>

<!-- ====================================================== -->
<!-- SIGNATURE -->
<!-- ====================================================== -->

<table class="signature">

    <tr>

        <td>
            Dibuat Oleh
        </td>

        <td>
            Diperiksa Oleh
        </td>

        <td>
            Disetujui Oleh
        </td>

    </tr>

    <tr>

        <td style="height:80px"></td>
        <td></td>
        <td></td>

    </tr>

    <tr>

        <td>
            ___________________
        </td>

        <td>
            ___________________
        </td>

        <td>
            ___________________
        </td>

    </tr>

</table>

</body>
</html>