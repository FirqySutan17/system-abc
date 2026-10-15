<?php
/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/
function pdf_num($value, $decimal = 2)
{
    return number_format(
        (float) ($value ?? 0),
        $decimal,
        ',',
        '.'
    );
}

function pdf_money($value)
{
    return 'Rp ' . number_format(
        (float) ($value ?? 0),
        0,
        ',',
        '.'
    );
}

function pdf_text($value, $default = '-')
{
    $value = trim((string) ($value ?? ''));

    return $value !== ''
        ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
        : $default;
}
?>

<style>

    @page{
        margin: 25px 25px 30px 25px;
    }

    body{
        font-family: DejaVu Sans, sans-serif;
        font-size: 9px;
        color: #222;
        margin: 0;
        padding: 0;
    }

    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

    .header{
        margin-bottom: 15px;
    }

    .header-table{
        width: 100%;
        border-collapse: collapse;
    }

    .header-table td{
        border: none;
        vertical-align: middle;
    }

    .logo-cell{
        width: 75px;
    }

    .logo{
        max-height: 55px;
        max-width: 70px;
    }

    .company{
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }

    .subtitle{
        text-align: center;
        font-size: 11px;
        color: #666;
        margin-top: 3px;
        font-weight: bold;
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN CARD
    |--------------------------------------------------------------------------
    */

    .card{
        border: 1px solid #cfd8e3;
        overflow: hidden;
    }

    .card-head{
        background: #0F4C81;
        color: #fff;
        padding: 9px 12px;
        font-size: 12px;
        font-weight: bold;
    }

    /*
    |--------------------------------------------------------------------------
    | META
    |--------------------------------------------------------------------------
    */

    .meta{
        padding: 10px 12px;
        background: #f8fafc;
        border-bottom: 1px solid #dfe5eb;
    }

    .meta-table{
        width: 100%;
        border-collapse: collapse;
    }

    .meta-table td{
        border: none;
        padding: 3px 0;
        vertical-align: top;
    }

    .meta-label{
        width: 80px;
        font-weight: bold;
        white-space: nowrap;
    }

    .meta-sep{
        width: 8px;
        text-align: center;
        font-weight: bold;
    }

    .meta-value{
        font-weight: bold;
    }

    .meta-gap{
        width: 25px;
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    .summary-box{
        padding: 7px 10px;
        background: #fff;
        border-bottom: 1px solid #dfe5eb;
    }

    .summary-table{
        width: 100%;
        border-collapse: collapse;
    }

    .summary-table td{
        border: none;
        text-align: center;
        padding: 6px;
    }

    .summary-value{
        font-size: 15px;
        font-weight: bold;
        color: #0F4C81;
    }

    .summary-label{
        font-size: 8px;
        color: #666;
        margin-top: 2px;
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL TABLE
    |--------------------------------------------------------------------------
    */

    .table{
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td{
        border: 1px solid #d5dbe2;
        padding: 4px 5px;
        font-size: 7.5px;
        vertical-align: middle;
    }

    .table th{
        background: #eef2f7;
        text-align: center;
        font-weight: bold;
    }

    .table td{
        background: #fff;
    }

    .right{
        text-align: right;
    }

    .center{
        text-align: center;
    }

    .bold{
        font-weight: bold;
    }

    .subtotal{
        background: #f1f5f9 !important;
        font-weight: bold;
    }

    /*
    |--------------------------------------------------------------------------
    | REMARK
    |--------------------------------------------------------------------------
    */

    .remark-box{
        margin-top: 12px;
        padding: 8px 10px;
        border: 1px dashed #cbd5e1;
        background: #fcfcfc;
    }

    .remark-title{
        font-weight: bold;
        margin-bottom: 4px;
    }

    .remark-content{
        line-height: 1.4;
    }

    /*
    |--------------------------------------------------------------------------
    | SAVING
    |--------------------------------------------------------------------------
    */

    .section-title{
        margin-top: 14px;
        margin-bottom: 5px;
        padding: 6px 8px;
        background: #eef2f7;
        border: 1px solid #d5dbe2;
        font-weight: bold;
        font-size: 9px;
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT
    |--------------------------------------------------------------------------
    */

    .payment-table th,
    .payment-table td{
        font-size: 7.5px;
    }

    /*
    |--------------------------------------------------------------------------
    | BADGE
    |--------------------------------------------------------------------------
    */

    .badge{
        display: inline-block;
        padding: 2px 6px;
        font-size: 7px;
        font-weight: bold;
    }

    .badge-success{
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning{
        background: #fef3c7;
        color: #92400e;
    }

    .badge-info{
        background: #dbeafe;
        color: #1e40af;
    }

    /*
    |--------------------------------------------------------------------------
    | SIGNATURE
    |--------------------------------------------------------------------------
    */

    .sign{
        margin-top: 30px;
        width: 100%;
        border-collapse: collapse;
    }

    .sign td{
        width: 50%;
        text-align: center;
        border: none;
        vertical-align: bottom;
    }

    .sign-space{
        height: 45px;
    }

    .sign-line{
        font-weight: bold;
    }

    /*
    |--------------------------------------------------------------------------
    | PAGE BREAK
    |--------------------------------------------------------------------------
    */

    .page-break{
        page-break-before: always;
    }

</style>


<!-- =========================================================
HEADER
========================================================= -->

<div class="header">

    <table class="header-table">

        <tr>

            <td style="border:none; text-align:center;">

                <div class="company">
                    PT. Abadi Bersama Cerah
                </div>

                <div class="subtitle">
                    RECEIVE SLIP
                </div>

            </td>

        </tr>

    </table>

</div>

<!-- =========================================================
MAIN CARD
========================================================= -->

<div class="card">


    <!-- =====================================================
    CARD HEADER
    ====================================================== -->

    <div class="card-head">

        RECEIVE #
        <?= pdf_text($header->RECEIVE) ?>

    </div>


    <!-- =====================================================
    META
    ====================================================== -->

    <div class="meta">

        <table class="meta-table">

            <!-- ROW 1 -->

            <tr>

                <td class="meta-label">
                    PLANT
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?= pdf_text($header->PLANT_NAME) ?>

                </td>


                <td class="meta-gap"></td>


                <td class="meta-label">
                    SUPPLIER
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?= pdf_text($header->SUPPLIER) ?>

                    -

                    <?= pdf_text($header->SUPPLIER_NAME) ?>

                </td>

            </tr>


            <!-- ROW 2 -->

            <tr>

                <td class="meta-label">
                    RECEIVE DATE
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?php
                    if (!empty($header->RECEIVE_DATE)) {

                        $timestamp = strtotime($header->RECEIVE_DATE);

                        echo $timestamp
                            ? strtoupper(date('d F Y', $timestamp))
                            : '-';

                    } else {

                        echo '-';

                    }
                    ?>

                </td>


                <td class="meta-gap"></td>


                <td class="meta-label">
                    PO
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?php if (!empty($header->PO)): ?>

                        #<?= pdf_text($header->PO) ?>

                    <?php else: ?>

                        DIRECT RECEIVE

                    <?php endif; ?>

                </td>

            </tr>


            <!-- ROW 3 -->

            <tr>

                <td class="meta-label">
                    NOTA
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?= pdf_text($header->NOTA) ?>

                </td>


                <td class="meta-gap"></td>


                <td class="meta-label">
                    REF NO
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?= pdf_text($header->NO_REF) ?>

                </td>

            </tr>


            <!-- ROW 4 -->

            <tr>

                <td class="meta-label">
                    SLIP NO
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?= pdf_text($header->SLIP_NO) ?>

                </td>


                <td class="meta-gap"></td>


                <td class="meta-label">
                    STATUS
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td>

                    <?php
                    $status = strtoupper(
                        trim($header->STATUS_TEXT ?? $header->STATUS_RECEIVE ?? 'OPEN')
                    );
                    ?>

                    <?php if ($status === 'RECEIVED' || $status === 'POSTED'): ?>

                        <span class="badge badge-success">
                            <?= pdf_text($status) ?>
                        </span>

                    <?php elseif ($status === 'OPEN'): ?>

                        <span class="badge badge-warning">
                            OPEN
                        </span>

                    <?php else: ?>

                        <span class="badge badge-info">
                            <?= pdf_text($status) ?>
                        </span>

                    <?php endif; ?>

                </td>

            </tr>


            <!-- ROW 5 -->

            <tr>

                <td class="meta-label">
                    ATTACHMENT
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?= !empty($header->ATTACH_FILE_NAME)
                        ? 'AVAILABLE'
                        : '-'
                    ?>

                </td>


                <td class="meta-gap"></td>


                <td class="meta-label">
                    MATERIAL
                </td>

                <td class="meta-sep">
                    :
                </td>

                <td class="meta-value">

                    <?= pdf_text($header->MATERIAL_NAME) ?>

                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
    SUMMARY
    ====================================================== -->

    <div class="summary-box">

        <table class="summary-table">

            <tr>

                <td>

                    <div class="summary-value">

                        <?= pdf_num(
                            $header->TOTAL_QTY ?? $summary['qty'],
                            2
                        ) ?>

                    </div>

                    <div class="summary-label">
                        TOTAL QTY
                    </div>

                </td>


                <td>

                    <div class="summary-value">

                        <?= pdf_num(
                            $header->TOTAL_BW ?? $summary['weight'],
                            2
                        ) ?>

                    </div>

                    <div class="summary-label">
                        TOTAL WEIGHT / BW
                    </div>

                </td>


                <td>

                    <div class="summary-value">

                        <?= pdf_num(
                            $header->TOTAL_AVG_BW ?? $summary['avg_bw'],
                            2
                        ) ?>

                    </div>

                    <div class="summary-label">
                        AVG BW
                    </div>

                </td>


                <td>

                    <div class="summary-value">

                        <?= pdf_num(
                            $summary['total_customer'],
                            0
                        ) ?>

                    </div>

                    <div class="summary-label">
                        CUSTOMER
                    </div>

                </td>


                <td>

                    <div class="summary-value">

                        <?= pdf_money(
                            $summary['total']
                        ) ?>

                    </div>

                    <div class="summary-label">
                        GRAND TOTAL
                    </div>

                </td>

            </tr>

        </table>

    </div>


    <!-- =====================================================
    DETAIL RECEIVE
    ====================================================== -->

    <table class="table">

        <thead>

            <tr>

                <th width="3%">
                    NO
                </th>

                <th width="15%">
                    CUSTOMER
                </th>

                <th width="8%">
                    PO TYPE
                </th>

                <th width="14%">
                    MATERIAL
                </th>

                <th width="7%">
                    QTY
                </th>

                <th width="7%">
                    WEIGHT
                </th>

                <th width="7%">
                    AVG BW
                </th>

                <th width="8%">
                    PRICE
                </th>

                <th width="7%">
                    DISCOUNT
                </th>

                <th width="10%">
                    TOTAL
                </th>

                <th width="8%">
                    SALES
                </th>

                <th width="12%">
                    REMARK
                </th>

            </tr>

        </thead>


        <tbody>

            <?php if (!empty($detail)): ?>

                <?php foreach ($detail as $i => $d): ?>

                    <tr>

                        <!-- NO -->

                        <td class="center">

                            <?= $i + 1 ?>

                        </td>


                        <!-- CUSTOMER -->

                        <td>

                            <div class="bold">

                                <?= pdf_text(
                                    $d->CUSTOMER_NAME ?? $d->CUSTOMER
                                ) ?>

                            </div>

                        </td>


                        <!-- PO TYPE -->

                        <td class="center">

                            <?= pdf_text(
                                $d->PO_TYPE_NAME ?? $d->PO_TYPE
                            ) ?>

                        </td>


                        <!-- MATERIAL -->

                        <td>

                            <?= pdf_text(
                                $d->MATERIAL_NAME ?? $d->MATERIAL
                            ) ?>

                        </td>


                        <!-- QTY -->

                        <td class="right">

                            <?= pdf_num(
                                $d->JUMLAH ?? 0,
                                2
                            ) ?>

                        </td>


                        <!-- WEIGHT -->

                        <td class="right">

                            <?= pdf_num(
                                $d->BERAT ?? 0,
                                2
                            ) ?>

                        </td>


                        <!-- AVG BW -->

                        <td class="right">

                            <?php
                            $detailQty = (float) ($d->JUMLAH ?? 0);
                            $detailWeight = (float) ($d->BERAT ?? 0);

                            $detailAvgBw = $detailQty > 0
                                ? $detailWeight / $detailQty
                                : 0;
                            ?>

                            <?= pdf_num(
                                $d->AVG_BW ?? $detailAvgBw,
                                2
                            ) ?>

                        </td>


                        <!-- PRICE -->

                        <td class="right">

                            <?= pdf_money(
                                $d->HARGA ?? 0
                            ) ?>

                        </td>


                        <!-- DISCOUNT -->

                        <td class="right">

                            <?= pdf_money(
                                $d->DISCOUNT ?? 0
                            ) ?>

                        </td>


                        <!-- TOTAL -->

                        <td class="right bold">

                            <?= pdf_money(
                                $d->TOTAL ?? 0
                            ) ?>

                        </td>


                        <!-- SALES -->

                        <td class="center">

                            <?= !empty($d->SALES_NO)
                                ? pdf_text($d->SALES_NO)
                                : '-'
                            ?>

                        </td>


                        <!-- REMARK -->

                        <td>

                            <?php
                            if (!empty($d->REMARK)) {

                                echo nl2br(
                                    pdf_text($d->REMARK)
                                );

                            } else {

                                echo '-';

                            }
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <!-- =================================================
                GRAND TOTAL
                ================================================== -->

                <tr class="subtotal">

                    <td colspan="4" class="center">

                        GRAND TOTAL

                    </td>


                    <td class="right">

                        <?= pdf_num(
                            $summary['qty'],
                            2
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_num(
                            $summary['weight'],
                            2
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_num(
                            $summary['avg_bw'],
                            2
                        ) ?>

                    </td>


                    <td></td>


                    <td class="right">

                        <?= pdf_money(
                            $summary['discount']
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_money(
                            $summary['total']
                        ) ?>

                    </td>


                    <td colspan="2"></td>

                </tr>


            <?php else: ?>

                <tr>

                    <td
                        colspan="12"
                        class="center"
                    >

                        No Receive Detail

                    </td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>


</div>


<!-- =========================================================
HEADER REMARK
========================================================== -->

<div class="remark-box">

    <div class="remark-title">

        REMARK

    </div>

    <div class="remark-content">

        <?php if (!empty($header->REMARK)): ?>

            <?= nl2br(
                pdf_text($header->REMARK)
            ) ?>

        <?php else: ?>

            -

        <?php endif; ?>

    </div>

</div>


<!-- =========================================================
SAVING
========================================================== -->

<?php if (!empty($saving)): ?>

    <div class="section-title">

        CUSTOMER SAVING

    </div>


    <table class="table">

        <thead>

            <tr>

                <th width="5%">
                    NO
                </th>

                <th width="25%">
                    CUSTOMER
                </th>

                <th width="15%">
                    BASIS
                </th>

                <th width="10%">
                    RATE
                </th>

                <th width="12%">
                    TOTAL QTY
                </th>

                <th width="12%">
                    TOTAL WEIGHT
                </th>

                <th width="15%">
                    AMOUNT
                </th>

                <th width="20%">
                    REMARK
                </th>

            </tr>

        </thead>


        <tbody>

            <?php
            $savingTotal = 0;
            ?>


            <?php foreach ($saving as $i => $row): ?>

                <?php
                $savingAmount = (float) (
                    $row->AMOUNT ?? 0
                );

                $savingTotal += $savingAmount;
                ?>


                <tr>

                    <td class="center">

                        <?= $i + 1 ?>

                    </td>


                    <td>

                        <?= pdf_text(
                            $row->CUSTOMER_NAME
                                ?? $row->CUSTOMER
                        ) ?>

                    </td>


                    <td class="center">

                        <?= pdf_text(
                            $row->BASIS
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_num(
                            $row->RATE ?? 0,
                            2
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_num(
                            $row->TOTAL_QTY ?? 0,
                            2
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_num(
                            $row->TOTAL_WEIGHT ?? 0,
                            2
                        ) ?>

                    </td>


                    <td class="right bold">

                        <?= pdf_money(
                            $savingAmount
                        ) ?>

                    </td>


                    <td>

                        <?= !empty($row->REMARK)
                            ? nl2br(
                                pdf_text($row->REMARK)
                            )
                            : '-'
                        ?>

                    </td>

                </tr>

            <?php endforeach; ?>


            <tr class="subtotal">

                <td colspan="6" class="right">

                    TOTAL CUSTOMER SAVING

                </td>

                <td class="right">

                    <?= pdf_money(
                        $savingTotal
                    ) ?>

                </td>

                <td></td>

            </tr>

        </tbody>

    </table>

<?php endif; ?>


<!-- =========================================================
PAYMENT / SALES
========================================================== -->

<?php if (!empty($payment)): ?>

    <div class="section-title">

        PAYMENT / SALES

    </div>


    <table class="table payment-table">

        <thead>

            <tr>

                <th width="5%">
                    NO
                </th>

                <th width="12%">
                    SALES
                </th>

                <th width="18%">
                    CUSTOMER
                </th>

                <th width="12%">
                    AMOUNT
                </th>

                <th width="10%">
                    REMAIN
                </th>

                <th width="9%">
                    STATUS
                </th>

                <th width="10%">
                    MODAL
                </th>

                <th width="10%">
                    BIAYA
                </th>

                <th width="10%">
                    DISCOUNT
                </th>

                <th width="12%">
                    PEMBAYARAN
                </th>

                <th width="10%">
                    PAY TYPE
                </th>

                <th width="15%">
                    REMARK
                </th>

            </tr>

        </thead>


        <tbody>

            <?php foreach ($payment as $i => $row): ?>

                <tr>

                    <td class="center">

                        <?= $i + 1 ?>

                    </td>


                    <td class="center">

                        <?= pdf_text(
                            $row->SALES
                        ) ?>

                    </td>


                    <td>

                        <?= pdf_text(
                            $row->CUSTOMER_NAME
                                ?? $row->CUSTOMER
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_money(
                            $row->AMOUNT ?? 0
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_money(
                            $row->REMAIN ?? 0
                        ) ?>

                    </td>


                    <td class="center">

                        <?= pdf_text(
                            $row->STATUS
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_money(
                            $row->MODAL ?? 0
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_money(
                            $row->BIAYA ?? 0
                        ) ?>

                    </td>


                    <td class="right">

                        <?= pdf_money(
                            $row->DISCOUNT ?? 0
                        ) ?>

                    </td>


                    <td class="right bold">

                        <?= pdf_money(
                            $row->PEMBAYARAN ?? 0
                        ) ?>

                    </td>


                    <td class="center">

                        <?= pdf_text(
                            $row->JENIS_PAY
                        ) ?>

                    </td>


                    <td>

                        <?= !empty($row->REMARK)
                            ? nl2br(
                                pdf_text($row->REMARK)
                            )
                            : '-'
                        ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>


<!-- =========================================================
SIGNATURE
========================================================== -->

<table class="sign">

    <tr>

        <td>
            Prepared By
        </td>

        <td>
            Approved By
        </td>

    </tr>


    <tr>

        <td class="sign-space"></td>

        <td class="sign-space"></td>

    </tr>


    <tr>

        <td class="sign-line">

            (________________________)

        </td>

        <td class="sign-line">

            (________________________)

        </td>

    </tr>

</table>