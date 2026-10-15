<?php

$logo = FCPATH . 'assets/img/abc-trans.svg';

$logoSvg = '';

if (file_exists($logo) && is_readable($logo)) {
    $logoSvg = file_get_contents($logo);
}

?>

<style>

    body{
        font-family: DejaVu Sans, sans-serif;
        font-size:10px;
        color:#222;
        margin:0;
        padding:0;
    }

    .header{
        width:100%;
        border-bottom:3px solid #0F4C81;
        padding-bottom:10px;
        margin-bottom:15px;
    }

    .logo{
        width:70px;
    }

    .company{
        text-align:center;
    }

    .company-name{
        font-size:20px;
        font-weight:bold;
        color:#0F4C81;
    }

    .company-sub{
        font-size:12px;
        color:#666;
    }

    .doc-title{
        margin-top:5px;
        font-size:18px;
        font-weight:bold;
        letter-spacing:1px;
    }

    .section{
        margin-top:18px;
        margin-bottom:15px;
    }

    .section-title{
        background:#0F4C81;
        color:#fff;
        padding:7px 10px;
        font-size:11px;
        font-weight:bold;
    }

    .info-table{
        width:100%;
        border-collapse:collapse;
    }

    .info-table td{
        padding:5px 8px;
        border:1px solid #d9dee5;
    }

    .label{
        width:22%;
        background:#f4f7fb;
        font-weight:bold;
    }

    .value{
        width:28%;
    }

    .right{
        text-align:right;
    }

    .center{
        text-align:center;
    }

    .remark{
        border:1px solid #d9dee5;
        min-height:55px;
        padding:10px;
    }

    .sign{
        margin-top:60px;
        width:100%;
        border-collapse:collapse;
    }

    .sign td{
        text-align:center;
        border:none;
        width:33%;
    }

    .sign-line{
        padding-top:55px;
    }

    .info-table tr:nth-child(even){
        background:#fcfcfc;
    }

    .logo-svg{
        width:65px;
        height:auto;
    }

</style>


<!-- ==========================================================
     HEADER
========================================================== -->

<div class="header">

    <table width="100%">

        <tr>

            <td width="80">

                <?php if(file_exists($logo)): ?>

                    <?= $logoSvg ?>

                <?php endif; ?>

            </td>

            <td class="company">

                <div class="company-name">

                    PT. ABADI BERSAMA CERAH

                </div>

                <div class="company-sub">

                    Purchase Order Management System

                </div>

                <div class="doc-title">

                    PURCHASE ORDER

                </div>

            </td>

        </tr>

    </table>

</div>


<!-- ==========================================================
     PO INFORMATION
========================================================== -->

<div class="section">

    <div class="section-title">

        PO INFORMATION

    </div>

    <table class="info-table">

        <tr>

            <td class="label">

                PO Number

            </td>

            <td class="value">

                <?= $header->PO ?>

            </td>

            <td class="label">

                PO Date

            </td>

            <td class="value">

                <?= !empty($header->PO_DATE)
                    ? date('d-m-Y', strtotime($header->PO_DATE))
                    : '-'
                ?>

            </td>

        </tr>


        <tr>

            <td class="label">

                Plant

            </td>

            <td>

                <?= $header->PLANT_NAME ?: $header->PLANT ?>

            </td>

            <td class="label">

                PO Type

            </td>

            <td>

                <?= $header->PO_NAME ?: $header->PO_TYPE ?>

            </td>

        </tr>


        <tr>

            <td class="label">

                Supplier

            </td>

            <td>

                <?= $header->SUPPLIER ?>

                -

                <?= $header->SUPPLIER_NAME ?: '-' ?>

            </td>

            <td class="label">

                Material

            </td>

            <td>

                <?= $header->MATERIAL ?>

                -

                <?= $header->MATERIAL_NAME ?: '-' ?>

            </td>

        </tr>

    </table>

</div>

<div class="section">

    <div class="section-title">
        JANTAN &amp; BETINA (QTY &amp; BW)
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Qty Jantan</td>
            <td class="value right">
                <?= number_format(
                    (float)($header->JANTAN_QTY ?? 0),
                    2,
                    ',',
                    '.'
                ) ?>
            </td>

            <td class="label">BW Jantan</td>
            <td class="value right">
                <?= number_format(
                    (float)($header->JANTAN_BW ?? 0),
                    2,
                    ',',
                    '.'
                ) ?>
            </td>
        </tr>

        <tr>
            <td class="label">Qty Betina</td>
            <td class="value right">
                <?= number_format(
                    (float)($header->BETINA_QTY ?? 0),
                    2,
                    ',',
                    '.'
                ) ?>
            </td>

            <td class="label">BW Betina</td>
            <td class="value right">
                <?= number_format(
                    (float)($header->BETINA_BW ?? 0),
                    2,
                    ',',
                    '.'
                ) ?>
            </td>
        </tr>
    </table>

</div>


<!-- ==========================================================
     ACTUAL INFORMATION
========================================================== -->

<div class="section">

    <div class="section-title">

        ACTUAL INFORMATION

    </div>

    <table class="info-table">

        <tr>

            <td class="label">

                Qty / Ekor

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->JUMLAH,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

            <td class="label">

                Weight / BW

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->BERAT,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

        </tr>


        <tr>

            <td class="label">

                Average BW

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->AVG_BW,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

            <td class="label">

                Mati Qty

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->MATI_QTY,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

        </tr>


        <tr>

            <td class="label">

                Mati BW

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->MATI_BW,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

            <td class="label">

                Actual Hasil Timbang

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->ACTUAL_HASIL_TIMBANG,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

        </tr>


        <tr>

            <td class="label">

                Susut BW

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->SUSUT_BW,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

            <td class="label">

                Claim Qty

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->CLAIM_QTY,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

        </tr>


        <tr>

            <td class="label">

                Claim BW

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->CLAIM_BW,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

            <td class="label">

                Total Terima Qty

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->TOTAL_TERIMA_QTY,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

        </tr>


        <tr>

            <td class="label">

                Total Terima BW

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->TOTAL_TERIMA_BW,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

            <td class="label">

                Total Bayar BW

            </td>

            <td class="value right">

                <?= number_format(
                    (float)$header->TOTAL_BAYAR_BW,
                    2,
                    ',',
                    '.'
                ) ?>

            </td>

        </tr>

    </table>

</div>


<!-- ==========================================================
     PRICE INFORMATION
========================================================== -->

<div class="section">

    <div class="section-title">

        PRICE INFORMATION

    </div>

    <table class="info-table">

        <tr>

            <td class="label">

                Harga / Kg

            </td>

            <td class="value right">

                Rp <?= number_format(
                    (float)$header->HARGA,
                    0,
                    ',',
                    '.'
                ) ?>

            </td>

            <td class="label">

                Total

            </td>

            <td class="value right">

                Rp <?= number_format(
                    (float)$header->TOTAL,
                    0,
                    ',',
                    '.'
                ) ?>

            </td>

        </tr>

    </table>

</div>


<!-- ==========================================================
     TRANSPORT
========================================================== -->

<div class="section">

    <div class="section-title">

        TRANSPORT

    </div>

    <table class="info-table">

        <tr>

            <td class="label">

                Truck

            </td>

            <td class="value">

                <?= !empty($header->NO_TRUCK)
                    ? $header->NO_TRUCK
                    : '-'
                ?>

            </td>

            <td class="label">

                Driver

            </td>

            <td class="value">

                <?= !empty($header->DRIVER)
                    ? $header->DRIVER
                    : '-'
                ?>

            </td>

        </tr>

    </table>

</div>


<!-- ==========================================================
     STATUS
========================================================== -->

<div class="section">

    <div class="section-title">

        STATUS

    </div>

    <table class="info-table">

        <tr>

            <td class="label">

                Payment Status

            </td>

            <td colspan="3">

                <strong>

                    <?= !empty($header->PAYMENT_STATUS)
                        ? $header->PAYMENT_STATUS
                        : '-'
                    ?>

                </strong>

            </td>

        </tr>

    </table>

</div>


<!-- ==========================================================
     REMARK
========================================================== -->

<div class="section">

    <div class="section-title">

        REMARK

    </div>

    <div class="remark">

        <?= !empty($header->REMARK)

            ? nl2br(htmlspecialchars($header->REMARK))

            : '-'

        ?>

    </div>

</div>


<!-- ==========================================================
     SIGNATURE
========================================================== -->

<table class="sign">

    <tr>

        <td>

            Prepared By

        </td>

        <td>

            Checked By

        </td>

        <td>

            Approved By

        </td>

    </tr>


    <tr>

        <td class="sign-line">

            ________________________

        </td>

        <td class="sign-line">

            ________________________

        </td>

        <td class="sign-line">

            ________________________

        </td>

    </tr>

</table>


<!-- ==========================================================
     FOOTER
========================================================== -->

<div style="
    margin-top:20px;
    text-align:center;
    font-size:9px;
    color:#888;
">

    This document is generated automatically by

    <strong>ABC ERP System</strong>

    <br>

    Printed on

    <?= date('d-m-Y H:i:s') ?>

</div>