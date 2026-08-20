<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>
        CASH IN SLIP
    </title>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size: 9px;
            color:#000;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .header-table td{
            border:none;
            vertical-align:middle;
        }

        .title{
            text-align:center;
            font-size:20px;
            font-weight:bold;
        }

        .subtitle{
            text-align:center;
            font-size:11px;
            color:#555;
            margin-top:4px;
        }

        .info-table{
            margin-top:15px;
        }

        .info-table td{
            padding:4px 5px;
            vertical-align:top;
        }

        .label{
            width:100px;
            font-weight:bold;
        }

        .detail-table{
            margin-top:20px;
        }

        .detail-table th{
            border:1px solid #000;
            background:#eeeeee;
            padding:6px;
            text-align:center;
        }

        .detail-table td{
            border:1px solid #000;
            padding:6px;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .summary{
            width:45%;
            margin-top:15px;
            margin-left:auto;
        }

        .summary td{
            border:1px solid #000;
            padding:6px;
        }

        .summary-label{
            font-weight:bold;
        }

        .remark{
            margin-top:25px;
        }

        .footer{
            margin-top:55px;
            font-size:8px;
            color:#555;
        }

    </style>

</head>

<?php

$logo =
    FCPATH . 'assets/img/abc-trans.png';

$logo64 =
    file_exists($logo)
        ? 'data:image/png;base64,'
            . base64_encode(
                file_get_contents($logo)
            )
        : '';

?>

<body>

<!-- ====================================================== -->
<!-- HEADER -->
<!-- ====================================================== -->

<table class="header-table">

    <tr>

        <td width="80">

            <?php if($logo64): ?>

                <img
                    src="<?= $logo64 ?>"
                    height="55">

            <?php endif; ?>

        </td>

        <td>

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
<!-- INFORMATION -->
<!-- ====================================================== -->

<table class="info-table">

    <tr>

        <td class="label">
            Plant
        </td>

        <td>

            :
            <?= htmlspecialchars(
                $header->PLANT_NAME ?: '-'
            ); ?>

        </td>

        <td class="label">
            Cash In Date
        </td>

        <td>

            :
            <?= date(
                'd-M-Y',
                strtotime(
                    $header->CASHIN_DATE
                )
            ); ?>

        </td>

    </tr>

    <tr>

        <td class="label">
            Customer
        </td>

        <td>

            :
            <?= htmlspecialchars(
                $header->CUSTOMER
            ); ?>

            -
            <?= htmlspecialchars(
                $header->CUSTOMER_NAME
            ); ?>

        </td>

        <td class="label">
            Cash In No
        </td>

        <td>

            :
            #<?= htmlspecialchars(
                $header->CASH_IN
            ); ?>

        </td>

    </tr>

    <tr>

        <td class="label">
            Pembayaran
        </td>

        <td>

            :
            <?= htmlspecialchars(
                $header->PEMBAYARAN ?: '-'
            ); ?>

        </td>

        <td class="label">
            Status
        </td>

        <td>

            :
            <?= htmlspecialchars(
                $header->STATUS ?: '-'
            ); ?>

        </td>

        <!-- <td class="label">
            Slip No
        </td>

        <td>

            :
            <?= htmlspecialchars(
                $header->SLIP_NO ?: '-'
            ); ?>

        </td> -->

    </tr>

    <tr>

        <td class="label">
            No Bon
        </td>

        <td>

            :
            <?= htmlspecialchars(
                $header->BON ?: '-'
            ); ?>

        </td>

        

    </tr>

</table>

<!-- ====================================================== -->
<!-- INVOICE DETAIL -->
<!-- ====================================================== -->

<table class="detail-table">

    <thead>

        <tr>

            <th width="5%">
                No
            </th>

            <th width="25%">
                Sales / Invoice
            </th>

            <th width="25%">
                Outstanding
            </th>

            <th width="22%">
                Paid Amount
            </th>

            <th width="23%">
                Remaining
            </th>

        </tr>

    </thead>

    <tbody>

        <?php

        $no = 1;

        foreach(
            $detail
            as $d
        ):

        ?>

            <tr>

                <td class="text-center">
                    <?= $no++; ?>
                </td>

                <td>

                    #<?= htmlspecialchars(
                        $d->SALES
                    ); ?>

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
                        $d->PAID_AMOUNT,
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

        <?php if(empty($detail)): ?>

            <tr>

                <td
                    colspan="5"
                    class="text-center">

                    Tidak ada outstanding invoice.

                </td>

            </tr>

        <?php endif; ?>

    </tbody>

</table>

<!-- ====================================================== -->
<!-- SUMMARY -->
<!-- ====================================================== -->

<table class="summary">

    <tr>

        <td class="summary-label">
            Total Cash In
        </td>

        <td class="text-right">

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

    <tr>

        <td class="summary-label">
            Total Applied
        </td>

        <td class="text-right">

            <b>
                Rp
                <?= number_format(
                    $totalApplied,
                    0,
                    ',',
                    '.'
                ); ?>
            </b>

        </td>

    </tr>

    <tr>

        <td class="summary-label">
            Remaining Outstanding
        </td>

        <td class="text-right">

            <b>
                Rp
                <?= number_format(
                    array_sum(
                        array_map(
                            function($row){
                                return $row->REMAINING;
                            },
                            $detail
                        )
                    ),
                    0,
                    ',',
                    '.'
                ); ?>
            </b>

        </td>

    </tr>

    <?php if($excessAmount > 0): ?>

        <tr>

            <td class="summary-label">
                Excess Amount
            </td>

            <td class="text-right">

                <b>
                    Rp
                    <?= number_format(
                        $excessAmount,
                        0,
                        ',',
                        '.'
                    ); ?>
                </b>

            </td>

        </tr>

    <?php endif; ?>

</table>

<!-- ====================================================== -->
<!-- REMARK -->
<!-- ====================================================== -->

<div class="remark">

    <b>
        Remark
    </b>

    <br><br>

    <?= nl2br(
        htmlspecialchars(
            $header->REMARK ?: '-'
        )
    ); ?>

</div>

<!-- ====================================================== -->
<!-- FOOTER -->
<!-- ====================================================== -->

<div class="footer">

    Printed At :
    <?= date(
        'd-M-Y H:i:s'
    ); ?>

</div>

</body>

</html>