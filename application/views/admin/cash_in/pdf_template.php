<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>
        CASH IN
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

        .doc-title{
            font-size:18px;
            font-weight:bold;
            text-align:right;
        }

        .info-table td{
            padding:4px 6px;
        }

        .info-label{
            width:110px;
            font-weight:bold;
        }

        .detail-table th,
        .detail-table td{
            border:1px solid #000;
            padding:6px;
        }

        .detail-table th{
            background:#efefef;
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .total-box{
            margin-top:12px;
            width:40%;
            float:right;
        }

        .total-box td{
            padding:5px;
            border:1px solid #000;
        }

        .remark{
            margin-top:30px;
            clear:both;
        }

        .footer{
            margin-top:60px;
        }

        .header-table{
            margin-bottom: 20px;
        }

        .title{
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subtitle{
            text-align: center;
            font-size: 12px;
            color: #666;
        }

    </style>

</head>

<?php
$logo = FCPATH . 'assets/img/abc-trans.png';

$logo64 = file_exists($logo)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo))
    : '';
?>

<body>

<!-- ====================================================== -->
<!-- HEADER -->
<!-- ====================================================== -->

<table class="header-table">

    <tr>

            <td width="70" style="border:none;">

                <?php if($logo64): ?>

                    <img src="<?= $logo64 ?>" height="60">

                <?php endif; ?>

            </td>

            <td style="border:none;">

                <div class="title">
                    PT. Abadi Bersama Cerah
                </div>

                <div class="subtitle">
                    CASH IN SLIP
                </div>

            </td>

        </tr>

</table>

<hr>

<!-- ====================================================== -->
<!-- INFO -->
<!-- ====================================================== -->

<table
    class="info-table"
    style="margin-top:15px;">

    <tr>

        <td class="info-label">
            Plant
        </td>

        <td colspan="3">

            :
            <?= $header->PLANT_NAME; ?>

        </td>

        <td class="info-label">
            Cash In Date
        </td>

        <td>

            :
            <?= date(
                'd-M-Y',
                strtotime($header->CASHIN_DATE)
            ); ?>

        </td>

    </tr>

    <tr>

        <td class="info-label">
            Customer
        </td>

        <td colspan="3">

            :
            <?= $header->CUSTOMER; ?>
            -
            <?= $header->FULL_NAME; ?>

        </td>

        <td class="info-label">
            Cash In No
        </td>

        <td>

            :
            #<?= $header->CASH_IN; ?>

        </td>

    </tr>

    <tr>

        <td class="info-label">
            Pembayaran
        </td>

        <td colspan="3">

            :
            <?= $header->PEMBAYARAN ?: '-'; ?>

        </td>

        <td class="info-label">
            Slip No
        </td>

        <td>

            :
            <?= $header->SLIP_NO ?: '-'; ?>

        </td>

    </tr>

    <tr>

        <td class="info-label">
            No Bon
        </td>

        <td>

            :
            <?= $header->BON ?: '-'; ?>

        </td>

    </tr>

</table>

<br>

<!-- ====================================================== -->
<!-- DETAIL -->
<!-- ====================================================== -->

<table class="detail-table">

    <thead>

        <tr>

            <th width="5%">
                No
            </th>

            <th width="25%">
                Sales
            </th>

            <th width="23%">
                Invoice Amount
            </th>

            <th width="23%">
                Paid Amount
            </th>

            <th width="24%">
                Remaining
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

                    #<?= $d->SALES; ?>

                </td>

                <td class="text-right">

                    Rp
                    <?= number_format(
                        $d->AMOUNT_INVOICE,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

                <td class="text-right">

                    Rp
                    <?= number_format(
                        $d->AMOUNT_OFFSET,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

                <td class="text-right">

                    Rp
                    <?= number_format(
                        $d->REMAINING,
                        0,
                        ',',
                        '.'
                    ); ?>

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

        <td width="50%">

            <b>
                Total Cash In
            </b>

        </td>

        <td width="50%" class="text-right">

            <b>

                Rp
                <?= number_format(
                    $header->AMOUNT,
                    0,
                    ',',
                    '.'
                ); ?>

            </b>

        </td>

    </tr>

</table>

<!-- ====================================================== -->
<!-- REMARK -->
<!-- ====================================================== -->

<div class="remark">

    <b>
        Remark :
    </b>

    <br><br>

    <?= $header->REMARK ?: '-'; ?>

</div>

<!-- ====================================================== -->
<!-- FOOTER -->
<!-- ====================================================== -->

<div class="footer">

    Printed At :
    <?= date('d-M-Y H:i:s'); ?>

</div>

</body>
</html>