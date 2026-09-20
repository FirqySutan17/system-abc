<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>
    Faktur <?= $header->SALES; ?>
</title>


<style>

/*
|--------------------------------------------------------------------------
| UKURAN KERTAS
|--------------------------------------------------------------------------
|
| 9.5 inch x 11 inch
| 241.3 mm x 279.4 mm
|
*/

@page {

    size:
        241.3mm
        279.4mm;

    margin:
        0;

}


/*
|--------------------------------------------------------------------------
| HTML & BODY
|--------------------------------------------------------------------------
*/

html,
body {

    width:
        241.3mm;

    margin:
        0;

    padding:
        0;

    font-family:
        "Courier New",
        Courier,
        monospace;

    font-size:
        10.5px;

    color:
        #000;

}


body {

    overflow:
        hidden;

}


/*
|--------------------------------------------------------------------------
| AREA CETAK
|--------------------------------------------------------------------------
|
| Printable width dibuat 203 mm
| agar lebih maksimal untuk Epson LX-310.
|
*/

.print-area {

    width:
        203mm;

    margin-left:
        13mm;

    margin-right:
        0;

    margin-top:
        7mm;

    min-height:
        260mm;

    overflow:
        hidden;

}


/*
|--------------------------------------------------------------------------
| TABLE GLOBAL
|--------------------------------------------------------------------------
*/

table {

    width:
        100%;

    border-collapse:
        collapse;

    table-layout:
        fixed;

}


td,
th {

    padding:
        0;

    vertical-align:
        top;

}


/*
|--------------------------------------------------------------------------
| ALIGNMENT
|--------------------------------------------------------------------------
*/

.left {

    text-align:
        left;

}


.center {

    text-align:
        center;

}


.right {

    text-align:
        right;

}


.bold {

    font-weight:
        bold;

}


.nowrap {

    white-space:
        nowrap;

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.header {

    width:
        100%;

}


.company {

    width:
        32%;

    line-height:
        1.35;

}


.invoice {

    width:
        36%;

    text-align:
        center;

}


.info {

    width:
        32%;

}


.title {

    font-size:
        18px;

    font-weight:
        bold;

    letter-spacing:
        2px;

}


.invoice-no {

    margin-top:
        2px;

    font-weight:
        bold;

}


/*
|--------------------------------------------------------------------------
| INFORMASI HEADER
|--------------------------------------------------------------------------
*/

.info-table {

    width:
        100%;

}


.info-table td {

    padding-bottom:
        1px;

}


.info-label {

    width:
        58px;

}


.info-colon {

    width:
        8px;

}


/*
|--------------------------------------------------------------------------
| GARIS PEMBATAS
|--------------------------------------------------------------------------
*/

.separator {

    border-bottom:
        1px solid #000;

    margin-top:
        4mm;

    margin-bottom:
        3mm;

}


/*
|--------------------------------------------------------------------------
| DETAIL PENJUALAN
|--------------------------------------------------------------------------
*/

.detail {

    width:
        100%;

}


.detail th {

    border-bottom:
        1px solid #000;

    padding-bottom:
        2mm;

    font-weight:
        normal;

}


.detail td {

    padding-top:
        1.2mm;

    padding-bottom:
        0.3mm;

    overflow:
        hidden;

    word-wrap:
        break-word;

    overflow-wrap:
        anywhere;

}


/*
|--------------------------------------------------------------------------
| LEBAR KOLOM DETAIL
|--------------------------------------------------------------------------
|
| Total = 100%
|
*/

.col-material {

    width:
        40%;

    white-space:
        normal;

    word-wrap:
        break-word;

    overflow-wrap:
        anywhere;

}


.col-basis {

    width:
        9%;

}


.col-qty {

    width:
        8%;

}


.col-weight {

    width:
        11%;

}


.col-price {

    width:
        14%;

}


.col-total {

    width:
        18%;

}


/*
|--------------------------------------------------------------------------
| TOTAL DETAIL
|--------------------------------------------------------------------------
*/

.detail-total td {

    padding-top:
        3mm;

    border-top:
        1px solid #000;

    font-weight:
        bold;

}


/*
|--------------------------------------------------------------------------
| TABUNGAN PELANGGAN
|--------------------------------------------------------------------------
*/

.saving {

    margin-top:
        4mm;

}


.saving-title {

    font-weight:
        bold;

    margin-bottom:
        1.5mm;

}


.saving-table {

    width:
        100%;

}


.saving-table th {

    border-bottom:
        1px solid #000;

    padding-bottom:
        1.5mm;

    font-weight:
        normal;

}


.saving-table td {

    padding-top:
        1mm;

    overflow:
        hidden;

    word-wrap:
        break-word;

    overflow-wrap:
        anywhere;

}


/*
|--------------------------------------------------------------------------
| RINGKASAN
|--------------------------------------------------------------------------
*/

.summary {

    width:
        88mm;

    margin-left:
        auto;

    margin-right:
        0;

    margin-top:
        4mm;

}


.summary td {

    padding-top:
        1mm;

    padding-bottom:
        1mm;

    white-space:
        nowrap;

}


.summary .separator-row td {

    border-top:
        1px solid #000;

    padding-top:
        2mm;

}


.summary .grand td {

    border-top:
        2px solid #000;

    padding-top:
        2mm;

    font-weight:
        bold;

}


/*
|--------------------------------------------------------------------------
| TERBILANG
|--------------------------------------------------------------------------
*/

.say {

    margin-top:
        4mm;

    line-height:
        1.4;

}


/*
|--------------------------------------------------------------------------
| PEMBAYARAN
|--------------------------------------------------------------------------
*/

.bank {

    margin-top:
        3mm;

    line-height:
        1.4;

}


/*
|--------------------------------------------------------------------------
| BAGIAN BAWAH
|--------------------------------------------------------------------------
*/

.bottom-section {

    width:
        100%;

    border-collapse:
        collapse;

    margin-top:
        4mm;

}


.bottom-left {

    width:
        60%;

    vertical-align:
        top;

    padding:
        0;

}


.bottom-right {

    width:
        40%;

    vertical-align:
        top;

    padding:
        0;

    text-align:
        center;

}


.signature-space {

    height:
        12mm;

}


/*
|--------------------------------------------------------------------------
| REMARK
|--------------------------------------------------------------------------
*/

.remark {

    margin-top:
        4mm;

    line-height:
        1.4;

}


.remark-title {

    font-weight:
        bold;

}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

@media print {

    html,
    body {

        margin:
            0 !important;

        padding:
            0 !important;

        width:
            241.3mm !important;

    }


    .print-area {

        width:
            203mm !important;

        margin-left:
            13mm !important;

        margin-right:
            0 !important;

        margin-top:
            7mm !important;

        overflow:
            hidden !important;

    }

}

</style>

</head>


<body>


<div class="print-area">


<?php

/*
|--------------------------------------------------------------------------
| HITUNG TOTAL DETAIL
|--------------------------------------------------------------------------
*/

$totalQty =
    0;


$totalBerat =
    0;


$baseSales =
    0;


foreach (
    $detail
    as $d
) {

    $totalQty +=
        (float) (
            $d->JUMLAH
            ?? 0
        );


    $totalBerat +=
        (float) (
            $d->BERAT
            ?? 0
        );


    $baseSales +=
        (float) (
            $d->TOTAL
            ?? 0
        );

}

?>


<!-- ========================================================== -->
<!-- HEADER -->
<!-- ========================================================== -->

<table class="header">

    <tr>


        <!-- ================================================== -->
        <!-- INFORMASI PERUSAHAAN -->
        <!-- ================================================== -->

        <td class="company">

            Pangkalan Ayam

            <br>

            <strong>
                PT. ABADI BERSAMA CERAH
            </strong>

            <br>

            <?= strtoupper(
                $header->PLANT_NAME
                ?: 'PLANT'
            ); ?>

        </td>


        <!-- ================================================== -->
        <!-- JUDUL -->
        <!-- ================================================== -->

        <td class="invoice">

            <div class="title">

                FAKTUR PENJUALAN

            </div>


            <div class="invoice-no">

                Nomor :

                <?= $header->SALES; ?>

            </div>

        </td>


        <!-- ================================================== -->
        <!-- INFORMASI TRANSAKSI -->
        <!-- ================================================== -->

        <td class="info">

            <table class="info-table">


                <!-- TANGGAL -->

                <tr>

                    <td class="info-label">

                        Tanggal

                    </td>

                    <td class="info-colon">

                        :

                    </td>

                    <td class="nowrap">

                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $header->SALES_DATE
                            )
                        ); ?>

                    </td>

                </tr>


                <!-- JAM -->

                <tr>

                    <td class="info-label">

                        Jam

                    </td>

                    <td class="info-colon">

                        :

                    </td>

                    <td class="nowrap">

                        <?= date(
                            'H:i:s'
                        ); ?>

                    </td>

                </tr>


                <!-- PELANGGAN -->

                <tr>

                    <td class="info-label">

                        Pelanggan

                    </td>

                    <td class="info-colon">

                        :

                    </td>

                    <td>

                        <?= strtoupper(
                            $header->CUSTOMER_NAME
                            ?: $header->CUSTOMER
                        ); ?>

                    </td>

                </tr>


                <!-- PEMBAYARAN -->

                <tr>

                    <td class="info-label">

                        Pembayaran

                    </td>

                    <td class="info-colon">

                        :

                    </td>

                    <td>

                        <?= strtoupper(
                            $header->JENIS_PAY
                            ?: '-'
                        ); ?>

                    </td>

                </tr>


                <!-- NOMOR NOTA -->

                <tr>

                    <td class="info-label">

                        Nomor Nota

                    </td>

                    <td class="info-colon">

                        :

                    </td>

                    <td>

                        <?= $header->NOTA ?: '-'; ?>

                    </td>

                </tr>


            </table>

        </td>

    </tr>

</table>


<!-- ========================================================== -->
<!-- GARIS -->
<!-- ========================================================== -->

<div class="separator"></div>


<!-- ========================================================== -->
<!-- DETAIL PENJUALAN -->
<!-- ========================================================== -->

<table class="detail">


    <colgroup>

        <col class="col-material">

        <col class="col-basis">

        <col class="col-qty">

        <col class="col-weight">

        <col class="col-price">

        <col class="col-total">

    </colgroup>


    <thead>

        <tr>


            <!-- KETERANGAN -->

            <th class="left">

                Keterangan

            </th>


            <!-- BASIS -->

            <th class="center">

                Basis

            </th>


            <!-- EKOR -->

            <th class="center">

                Ekor

            </th>


            <!-- BERAT -->

            <th class="center">

                Berat

            </th>


            <!-- HARGA -->

            <th class="right">

                Harga

            </th>


            <!-- JUMLAH -->

            <th class="right">

                Jumlah (Rp)

            </th>


        </tr>

    </thead>


    <tbody>


    <?php foreach (
        $detail
        as $d
    ): ?>


        <?php

        $qty =
            (float) (
                $d->JUMLAH
                ?? 0
            );


        $berat =
            (float) (
                $d->BERAT
                ?? 0
            );


        $harga =
            (float) (
                $d->HARGA
                ?? 0
            );


        $total =
            (float) (
                $d->TOTAL
                ?? 0
            );

        ?>


        <tr>


            <!-- ================================================= -->
            <!-- KETERANGAN -->
            <!-- ================================================= -->

            <td class="left">

                <?= $d->MATERIAL; ?>

                -

                <?= strtoupper(
                    $d->MATERIAL_NAME
                    ?: ''
                ); ?>

            </td>


            <!-- ================================================= -->
            <!-- BASIS -->
            <!-- ================================================= -->

            <td class="center">

                <?= $d->CALC_BASIS ?: '-'; ?>

            </td>


            <!-- ================================================= -->
            <!-- EKOR -->
            <!-- ================================================= -->

            <td class="center">

                <?= number_format(
                    $qty,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


            <!-- ================================================= -->
            <!-- BERAT -->
            <!-- ================================================= -->

            <td class="center">

                <?= number_format(
                    $berat,
                    2,
                    ',',
                    '.'
                ); ?>

            </td>


            <!-- ================================================= -->
            <!-- HARGA -->
            <!-- ================================================= -->

            <td class="right">

                <?= number_format(
                    $harga,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


            <!-- ================================================= -->
            <!-- JUMLAH -->
            <!-- ================================================= -->

            <td class="right">

                <?= number_format(
                    $total,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


        </tr>


    <?php endforeach; ?>


    <?php if (
        empty($detail)
    ): ?>


        <tr>

            <td
                colspan="6"
                class="center">

                Tidak ada detail penjualan.

            </td>

        </tr>


    <?php endif; ?>


    <!-- ====================================================== -->
    <!-- TOTAL DETAIL -->
    <!-- ====================================================== -->

    <tr class="detail-total">


        <td></td>


        <td></td>


        <td class="center">

            <?= number_format(
                $totalQty,
                0,
                ',',
                '.'
            ); ?>

        </td>


        <td class="center">

            <?= number_format(
                $totalBerat,
                2,
                ',',
                '.'
            ); ?>

        </td>


        <td></td>


        <td class="right">

            <?= number_format(
                $baseSales,
                0,
                ',',
                '.'
            ); ?>

        </td>


    </tr>


    </tbody>

</table>


<!-- ========================================================== -->
<!-- TABUNGAN PELANGGAN -->
<!-- ========================================================== -->

<?php if (
    !empty($saving)
): ?>


<div class="saving">


    <div class="saving-title">

        TABUNGAN PELANGGAN

    </div>


    <table class="saving-table">


        <thead>

            <tr>


                <th
                    width="32%"
                    class="left">

                    Nomor Tabungan

                </th>


                <th
                    width="15%"
                    class="center">

                    Basis

                </th>


                <th
                    width="20%"
                    class="right">

                    Tarif

                </th>


                <th
                    width="18%"
                    class="right">

                    Nilai

                </th>


                <th
                    width="15%"
                    class="right">

                    Sisa

                </th>


            </tr>

        </thead>


        <tbody>


        <?php foreach (
            $saving
            as $sv
        ): ?>


            <tr>


                <td>

                    <?= $sv->SV_NO; ?>

                </td>


                <td class="center">

                    <?= $sv->BASIS ?: '-'; ?>

                </td>


                <td class="right">

                    <?= number_format(
                        $sv->RATE,
                        0,
                        ',',
                        '.'
                    ); ?>


                    <?php if (
                        strtoupper(
                            $sv->BASIS
                        ) === 'EKOR'
                    ): ?>

                        /Ekor

                    <?php else: ?>

                        /Kg

                    <?php endif; ?>

                </td>


                <td class="right">

                    <?= number_format(
                        $sv->AMOUNT,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>


                <td class="right">

                    <?= number_format(
                        $sv->REMAIN,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>


            </tr>


            <?php if (
                !empty(
                    $sv->REMARK
                )
            ): ?>


                <tr>

                    <td></td>

                    <td
                        colspan="4">

                        Keterangan :

                        <?= $sv->REMARK; ?>

                    </td>

                </tr>


            <?php endif; ?>


        <?php endforeach; ?>


        </tbody>

    </table>

</div>


<?php endif; ?>


<!-- ========================================================== -->
<!-- RINGKASAN NILAI -->
<!-- ========================================================== -->

<div class="summary">


    <table>


        <!-- ================================================== -->
        <!-- PENJUALAN DASAR -->
        <!-- ================================================== -->

        <tr>

            <td>

                Penjualan Dasar

            </td>


            <td width="28">

                Rp

            </td>


            <td class="right">

                <?= number_format(
                    $summary['base_sales'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- ================================================== -->
        <!-- MODAL -->
        <!-- ================================================== -->

        <?php if (
            $summary['modal'] != 0
        ): ?>


        <tr>

            <td>

                Modal

            </td>


            <td>

                Rp

            </td>


            <td class="right">

                <?= number_format(
                    $summary['modal'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <?php endif; ?>


        <!-- ================================================== -->
        <!-- BIAYA -->
        <!-- ================================================== -->

        <?php if (
            $summary['biaya'] != 0
        ): ?>


        <tr>

            <td>

                Biaya

            </td>


            <td>

                Rp

            </td>


            <td class="right">

                <?= number_format(
                    $summary['biaya'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <?php endif; ?>


        <!-- ================================================== -->
        <!-- DISKON -->
        <!-- ================================================== -->

        <?php if (
            $summary['discount'] != 0
        ): ?>


        <tr>

            <td>

                Diskon

            </td>


            <td>

                Rp

            </td>


            <td class="right">

                -

                <?= number_format(
                    abs(
                        $summary['discount']
                    ),
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <?php endif; ?>


        <!-- ================================================== -->
        <!-- PEMBULATAN -->
        <!-- ================================================== -->

        <?php if (
            $summary['rounding'] != 0
        ): ?>


        <tr>

            <td>

                Pembulatan

            </td>


            <td>

                Rp

            </td>


            <td class="right">


                <?php if (
                    $summary['rounding'] > 0
                ): ?>

                    +

                <?php else: ?>

                    -

                <?php endif; ?>


                <?= number_format(
                    abs(
                        $summary['rounding']
                    ),
                    0,
                    ',',
                    '.'
                ); ?>


            </td>

        </tr>


        <?php endif; ?>


        <!-- ================================================== -->
        <!-- TOTAL PENJUALAN -->
        <!-- ================================================== -->

        <tr class="separator-row">


            <td class="bold">

                Total Penjualan

            </td>


            <td class="bold">

                Rp

            </td>


            <td class="right bold">

                <?= number_format(
                    $summary['sales_amount'],
                    0,
                    ',',
                    '.'
                ); ?>


            </td>


        </tr>


        <!-- ================================================== -->
        <!-- TABUNGAN PELANGGAN -->
        <!-- ================================================== -->

        <?php if (
            $summary['total_saving'] != 0
        ): ?>


        <tr>


            <td>

                Tabungan Pelanggan

            </td>


            <td>

                Rp

            </td>


            <td class="right">

                +

                <?= number_format(
                    $summary['total_saving'],
                    0,
                    ',',
                    '.'
                ); ?>


            </td>


        </tr>


        <?php endif; ?>


        <!-- ================================================== -->
        <!-- TOTAL AKHIR -->
        <!-- ================================================== -->

        <tr class="grand">


            <td>

                Total Akhir

            </td>


            <td>

                Rp

            </td>


            <td class="right">

                <?= number_format(
                    $summary['grand_total'],
                    0,
                    ',',
                    '.'
                ); ?>


            </td>


        </tr>


    </table>


</div>


<!-- ========================================================== -->
<!-- TERBILANG + TANDA TANGAN -->
<!-- ========================================================== -->

<table class="bottom-section">


    <tr>


        <!-- ================================================== -->
        <!-- TERBILANG + PEMBAYARAN -->
        <!-- ================================================== -->

        <td class="bottom-left">


            <div class="say">

                Terbilang :

                <?= ucwords(
                    terbilang(
                        $summary['grand_total']
                    )
                ); ?>

                Rupiah

            </div>


            <div class="bank">

                Pembayaran melalui transfer ke rekening:

                <br>

                BCA A/C 275757999

                <br>

                A/N SEGIHARTO

            </div>


        </td>


        <!-- ================================================== -->
        <!-- TANDA TANGAN -->
        <!-- ================================================== -->

        <td class="bottom-right">


            Diterima Oleh,


            <div class="signature-space"></div>


            (__________________)


        </td>


    </tr>


</table>


<!-- ========================================================== -->
<!-- KETERANGAN / REMARK -->
<!-- ========================================================== -->

<?php if (
    !empty(
        trim(
            $header->REMARK
            ?? ''
        )
    )
): ?>


<div class="remark">


    <span class="remark-title">

        Keterangan :

    </span>


    <br>


    <?= nl2br(
        htmlspecialchars(
            $header->REMARK,
            ENT_QUOTES,
            'UTF-8'
        )
    ); ?>


</div>


<?php endif; ?>


</div>


<script>

window.onload = function () {

    window.print();

};

</script>


</body>

</html>