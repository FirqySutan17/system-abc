<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>
        Sales Invoice
    </title>

    <style>

        body {
            font-family: DejaVu Sans;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            width: 170px;
            height: auto;
        }

        .company-name {
            margin-top: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .invoice-title {
            font-size: 19px;
            font-weight: bold;
            text-align: right;
            padding-top: 10px;
        }

        .invoice-subtitle {
            text-align: right;
            font-size: 9px;
            color: #555;
            margin-top: 3px;
        }

        .info-table {
            margin-top: 15px;
        }

        .info-table td {
            padding: 4px 5px;
        }

        .info-label {
            width: 95px;
            font-weight: bold;
        }

        .detail-table {
            margin-top: 12px;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .detail-table th {
            background: #eeeeee;
            text-align: center;
        }

        .saving-table {
            margin-top: 15px;
        }

        .saving-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-wrapper {
            margin-top: 15px;
            width: 100%;
        }

        .summary-box {
            width: 48%;
            float: right;
        }

        .summary-box td {
            padding: 4px 5px;
        }

        .summary-box .separator td {
            border-top: 1px solid #999;
            padding-top: 7px;
        }

        .summary-box .total-sales td {
            border-top: 1px solid #000;
            font-weight: bold;
        }

        .summary-box .grand-total td {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 11px;
            padding-top: 7px;
        }

        .summary-box .outstanding-header td {
            border-top: 1px solid #999;
            padding-top: 7px;
            font-weight: bold;
        }

        .summary-box .outstanding-total td {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 11px;
            padding-top: 7px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .remark {
            margin-top: 25px;
            clear: both;
        }

        .footer {
            margin-top: 50px;
            clear: both;
            font-size: 8px;
            color: #555;
        }

        .small-text {
            font-size: 8px;
            color: #555;
        }

    </style>

</head>

<?php

$logo =
    FCPATH .
    'assets/img/abc-trans.png';

$logo64 =
    file_exists($logo)

        ? 'data:image/png;base64,' .
            base64_encode(
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

        <td width="55%">

            <?php if ($logo64): ?>

                <img
                    src="<?= $logo64 ?>"
                    class="logo">

            <?php endif; ?>

            <div class="company-name">

                PT. ABADI BERSAMA CERAH

            </div>

        </td>


        <td width="45%">

            <div class="invoice-title">

                SALES INVOICE

            </div>

            <div class="invoice-subtitle">

                ORIGINAL

            </div>

        </td>

    </tr>

</table>

<hr>


<!-- ====================================================== -->
<!-- INFORMATION -->
<!-- ====================================================== -->

<table
    class="info-table">

    <tr>

        <td class="info-label">

            Plant

        </td>

        <td colspan="3">

            :
            <?= $header->PLANT_NAME ?: '-'; ?>

        </td>


        <td class="info-label">

            Invoice Date

        </td>

        <td>

            :
            <?= date(
                'd-M-Y',
                strtotime(
                    $header->SALES_DATE
                )
            ); ?>

        </td>

    </tr>


    <tr>

        <td class="info-label">

            Customer

        </td>

        <td colspan="3">

            :
            (<?= $header->CUSTOMER; ?>)

            <?= $header->CUSTOMER_NAME ?: '-'; ?>

        </td>


        <td class="info-label">

            Invoice No

        </td>

        <td>

            :
            #<?= $header->SALES; ?>

        </td>

    </tr>


    <tr>

        <td class="info-label">

            Payment

        </td>

        <td colspan="3">

            :
            <?= $header->PAYMENT_INFO; ?>

        </td>


        <td class="info-label">

            Nota

        </td>

        <td>

            :
            <?= $header->NOTA ?: '-'; ?>

        </td>

    </tr>


    <tr>

        <td class="info-label">

            Status

        </td>

        <td>

            :
            <?= $header->STATUS ?: '-'; ?>

        </td>

    </tr>

</table>


<br>


<!-- ====================================================== -->
<!-- SALES DETAIL -->
<!-- ====================================================== -->

<table class="detail-table">

    <thead>

        <tr>

            <th width="4%">
                No
            </th>

            <th width="28%">
                Material
            </th>

            <th width="10%">
                Basis
            </th>

            <th width="10%">
                Qty
            </th>

            <th width="11%">
                Berat
            </th>

            <th width="15%">
                Harga
            </th>

            <th width="17%">
                Total
            </th>

        </tr>

    </thead>


    <tbody>

        <?php

        $no = 1;

        foreach (
            $detail
            as $d
        ):

        ?>

        <tr>

            <td class="text-center">

                <?= $no++; ?>

            </td>


            <td>

                <b>
                    <?= $d->MATERIAL_NAME ?: '-'; ?>
                </b>

                <br>

                <span class="small-text">

                    <?= $d->MATERIAL; ?>

                </span>

            </td>


            <td class="text-center">

                <?= $d->CALC_BASIS ?: '-'; ?>

            </td>


            <td class="text-right">

                <?= number_format(
                    $d->JUMLAH,
                    2,
                    ',',
                    '.'
                ); ?>

            </td>


            <td class="text-right">

                <?= number_format(
                    $d->BERAT,
                    2,
                    ',',
                    '.'
                ); ?>

            </td>


            <td class="text-right">

                Rp
                <?= number_format(
                    $d->HARGA,
                    0,
                    ',',
                    '.'
                ); ?>

                <br>

                <span class="small-text">

                    <?php if (
                        $d->CALC_BASIS === 'EKOR'
                    ): ?>

                        / Ekor

                    <?php else: ?>

                        / Kg

                    <?php endif; ?>

                </span>

            </td>


            <td class="text-right">

                Rp
                <?= number_format(
                    $d->TOTAL,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>

        <?php endforeach; ?>


        <?php if (empty($detail)): ?>

        <tr>

            <td
                colspan="7"
                class="text-center">

                Tidak ada detail Sales

            </td>

        </tr>

        <?php endif; ?>

    </tbody>

</table>


<!-- ====================================================== -->
<!-- SAVING -->
<!-- ====================================================== -->

<?php if (!empty($saving)): ?>

    <div
        class="saving-title"
        style="margin-top:15px;">

        CUSTOMER SAVING

    </div>


    <table class="detail-table saving-table">

        <thead>

            <tr>

                <th width="8%">
                    No
                </th>

                <th width="24%">
                    Saving No
                </th>

                <th width="15%">
                    Basis
                </th>

                <th width="20%">
                    Rate
                </th>

                <th width="18%">
                    Amount
                </th>

                <th width="15%">
                    Remaining
                </th>

            </tr>

        </thead>


        <tbody>

            <?php

            $savingNo =
                1;

            foreach (
                $saving
                as $sv
            ):

            ?>

            <tr>

                <td class="text-center">

                    <?= $savingNo++; ?>

                </td>


                <td>

                    <?= $sv->SV_NO; ?>

                </td>


                <td class="text-center">

                    <?= $sv->BASIS ?: '-'; ?>

                </td>


                <td class="text-right">

                    Rp
                    <?= number_format(
                        $sv->RATE,
                        0,
                        ',',
                        '.'
                    ); ?>

                    <br>

                    <span class="small-text">

                        <?php if (
                            $sv->BASIS === 'EKOR'
                        ): ?>

                            / Ekor

                        <?php else: ?>

                            / Kg

                        <?php endif; ?>

                    </span>

                </td>


                <td class="text-right">

                    Rp
                    <?= number_format(
                        $sv->AMOUNT,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>


                <td class="text-right">

                    Rp
                    <?= number_format(
                        $sv->REMAIN,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

            </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>


<!-- ====================================================== -->
<!-- SUMMARY -->
<!-- ====================================================== -->

<div class="summary-wrapper">

    <table class="summary-box">

        <!-- TOTAL QTY -->

        <tr>

            <td>

                Total Qty

            </td>

            <td class="text-right">

                <?= number_format(
                    $summary['total_qty'],
                    2,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- TOTAL BERAT -->

        <tr>

            <td>

                Total Berat

            </td>

            <td class="text-right">

                <?= number_format(
                    $summary['total_berat'],
                    2,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- BASE SALES -->

        <tr>

            <td>

                Base Sales

            </td>

            <td class="text-right">

                Rp
                <?= number_format(
                    $summary['base_sales'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- BIAYA -->

        <?php if (
            $summary['biaya'] > 0
        ): ?>

        <tr>

            <td>

                Biaya

            </td>

            <td class="text-right">

                + Rp
                <?= number_format(
                    $summary['biaya'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>

        <?php endif; ?>


        <!-- DISCOUNT -->

        <?php if (
            $summary['discount'] > 0
        ): ?>

        <tr>

            <td>

                Discount

            </td>

            <td class="text-right">

                - Rp
                <?= number_format(
                    $summary['discount'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>

        <?php endif; ?>


        <!-- ROUNDING -->

        <?php if (
            $summary['rounding'] > 0
        ): ?>

        <tr>

            <td>

                Pembulatan

            </td>

            <td class="text-right">

                + Rp
                <?= number_format(
                    $summary['rounding'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>

        <?php endif; ?>


        <!-- TOTAL SALES -->

        <tr class="total-sales">

            <td>

                Total Sales

            </td>

            <td class="text-right">

                Rp
                <?= number_format(
                    $summary['sales_amount'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- CUSTOMER SAVING -->

        <tr>

            <td>

                Customer Saving

            </td>

            <td class="text-right">

                Rp
                <?= number_format(
                    $summary['total_saving'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- GRAND TOTAL -->

        <tr class="grand-total">

            <td>

                Grand Total

            </td>

            <td class="text-right">

                Rp
                <?= number_format(
                    $summary['grand_total'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- OUTSTANDING HEADER -->

        <tr class="outstanding-header">

            <td colspan="2">

                OUTSTANDING

            </td>

        </tr>


        <!-- SALES OUTSTANDING -->

        <tr>

            <td>

                Outstanding Sales

            </td>

            <td class="text-right">

                Rp
                <?= number_format(
                    $summary['outstanding_sales'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- SAVING OUTSTANDING -->

        <tr>

            <td>

                Outstanding Saving

            </td>

            <td class="text-right">

                Rp
                <?= number_format(
                    $summary['outstanding_saving'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- TOTAL OUTSTANDING -->

        <tr class="outstanding-total">

            <td>

                Total Outstanding

            </td>

            <td class="text-right">

                Rp
                <?= number_format(
                    $summary['total_outstanding'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>

    </table>

</div>


<!-- ====================================================== -->
<!-- REMARK -->
<!-- ====================================================== -->

<div class="remark">

    <b>
        Remark:
    </b>

    <br><br>

    <?= $header->REMARK ?: '-'; ?>

</div>


<!-- ====================================================== -->
<!-- FOOTER -->
<!-- ====================================================== -->

<!-- <div class="footer">

    Printed At:
    <?= date(
        'd-M-Y H:i:s'
    ); ?>

</div> -->


</body>

</html>