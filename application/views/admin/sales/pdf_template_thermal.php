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
| Epson LX-310
|
| Lebar  : 9.5 inch = 241.3 mm
| Tinggi : 11 inch  = 279.4 mm
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
        10px;

    color:
        #000;

    font-weight:
        bold;

}


body {

    overflow:
        hidden;

}


/*
|--------------------------------------------------------------------------
| SEMUA TEKS BOLD
|--------------------------------------------------------------------------
*/

* {

    font-weight:
        bold !important;

}


/*
|--------------------------------------------------------------------------
| AREA CETAK
|--------------------------------------------------------------------------
|
| KONFIGURASI INI DIPERTAHANKAN
| DARI HASIL PRINT YANG SUDAH BAGUS
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

}


td,
th {

    padding:
        0;

    vertical-align:
        top;

    font-weight:
        bold;

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
        30%;

    line-height:
        1.5;

}


.invoice {

    width:
        40%;

    text-align:
        center;

}


.info {

    width:
        30%;

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
        3px;

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
        70px;

}


.info-colon {

    width:
        10px;

}


/*
|--------------------------------------------------------------------------
| GARIS PEMBATAS
|--------------------------------------------------------------------------
*/

.separator-line {

    border-bottom:
        1px solid #000;

    margin-top:
        6mm;

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

    table-layout:
        fixed;

}


.detail th {

    border-bottom:
        1px solid #000;

    padding-bottom:
        2mm;

    font-weight:
        bold;

}


.detail td {

    padding-top:
        1mm;

    padding-bottom:
        0.5mm;

    font-weight:
        bold;

    overflow:
        hidden;

    word-wrap:
        break-word;

    overflow-wrap:
        break-word;

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


.summary table {

    width:
        100%;

}


.summary td {

    padding-top:
        1mm;

    padding-bottom:
        1mm;

    white-space:
        nowrap;

    font-weight:
        bold;

}


/*
|--------------------------------------------------------------------------
| GARIS TOTAL PENJUALAN
|--------------------------------------------------------------------------
*/

.summary .separator-row td {

    border-top:
        1px solid #000;

    padding-top:
        2mm;

}


/*
|--------------------------------------------------------------------------
| TOTAL AKHIR
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| TERBILANG
|--------------------------------------------------------------------------
*/

.say {

    line-height:
        1.4;

    font-weight:
        bold;

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

    font-weight:
        bold;

}


/*
|--------------------------------------------------------------------------
| TANDA TANGAN
|--------------------------------------------------------------------------
*/

.signature-space {

    height:
        12mm;

}


/*
|--------------------------------------------------------------------------
| KETERANGAN
|--------------------------------------------------------------------------
*/

.remark {

    margin-top:
        4mm;

    line-height:
        1.4;

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

        font-weight:
            bold !important;

    }


    * {

        font-weight:
            bold !important;

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

        min-height:
            260mm !important;

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


/*
|--------------------------------------------------------------------------
| NILAI KEUANGAN
|--------------------------------------------------------------------------
*/

$modal =
    (float) (
        $summary['modal']
        ?? 0
    );


$biaya =
    (float) (
        $summary['biaya']
        ?? 0
    );


$discount =
    (float) (
        $summary['discount']
        ?? 0
    );


$rounding =
    (float) (
        $summary['rounding']
        ?? 0
    );


$totalSaving =
    (float) (
        $summary['total_saving']
        ?? 0
    );


/*
|--------------------------------------------------------------------------
| PENJUALAN DASAR
|--------------------------------------------------------------------------
|
| Diskon dan pembulatan digabung ke Penjualan Dasar.
|
| Penjualan Dasar =
| Base Sales - Diskon + Pembulatan
|
*/

$penjualanDasar =
    round(
        $baseSales
        -
        $discount
        +
        $rounding,
        2
    );


/*
|--------------------------------------------------------------------------
| TOTAL PENJUALAN
|--------------------------------------------------------------------------
|
| Penjualan Dasar + Biaya
|
*/

$totalPenjualan =
    round(
        $penjualanDasar
        +
        $biaya,
        2
    );


/*
|--------------------------------------------------------------------------
| TOTAL AKHIR
|--------------------------------------------------------------------------
|
| Total Penjualan + Tabungan Pelanggan
|
*/

$totalAkhir =
    round(
        $totalPenjualan
        +
        $totalSaving,
        2
    );


/*
|--------------------------------------------------------------------------
| BASIS TABUNGAN
|--------------------------------------------------------------------------
|
| CUSTOMER SAVING DETAIL SUDAH DIHAPUS.
| Yang ditampilkan hanya basis pada ringkasan.
|
*/

$savingBasis =
    '-';


if (
    !empty($saving)
) {

    $basisList =
        [];


    foreach (
        $saving
        as $sv
    ) {

        $basis =
            strtoupper(
                trim(
                    $sv->BASIS
                    ?? ''
                )
            );


        if (
            $basis !== ''
            &&
            !in_array(
                $basis,
                $basisList
            )
        ) {

            $basisList[] =
                $basis;

        }

    }


    if (
        count($basisList) === 1
    ) {

        $savingBasis =
            $basisList[0];

    }
    elseif (
        count($basisList) > 1
    ) {

        $savingBasis =
            'CAMPURAN';

    }

}

?>


<!-- ====================================================== -->
<!-- HEADER -->
<!-- ====================================================== -->

<table class="header">

    <tr>


        <!-- ================================================== -->
        <!-- PERUSAHAAN -->
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

                INVOICE

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


<!-- ====================================================== -->
<!-- GARIS -->
<!-- ====================================================== -->

<div class="separator-line"></div>


<!-- ====================================================== -->
<!-- DETAIL PENJUALAN -->
<!-- ====================================================== -->

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


            <th class="left">

                Keterangan

            </th>


            <th class="center">

                Basis

            </th>


            <th class="center">

                Ekor

            </th>


            <th class="center">

                Berat

            </th>


            <th class="right">

                Harga

            </th>


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


            <!-- KETERANGAN -->

            <td class="left">

                <?= $d->MATERIAL; ?>

                -

                <?= strtoupper(
                    $d->MATERIAL_NAME
                    ?: ''
                ); ?>

            </td>


            <!-- BASIS -->

            <td class="center">

                <?= $d->CALC_BASIS ?: '-'; ?>

            </td>


            <!-- EKOR -->

            <td class="center">

                <?= number_format(
                    $qty,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


            <!-- BERAT -->

            <td class="center">

                <?= number_format(
                    $berat,
                    2,
                    ',',
                    '.'
                ); ?>

            </td>


            <!-- HARGA -->

            <td class="right">

                <?= number_format(
                    $harga,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


            <!-- JUMLAH -->

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


    <!-- ================================================== -->
    <!-- TOTAL DETAIL -->
    <!-- ================================================== -->

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


<!-- ====================================================== -->
<!-- RINGKASAN NILAI -->
<!-- ====================================================== -->

<div class="summary">


    <table>


        <!-- ================================================== -->
        <!-- PENJUALAN DASAR -->
        <!-- ================================================== -->

        <tr>

            <td>

                Penjualan Dasar

            </td>


            <td width="30">

                Rp

            </td>


            <td class="right">

                <?= number_format(
                    $penjualanDasar,
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
            $modal != 0
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
                    abs($modal),
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
            $biaya != 0
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
                    abs($biaya),
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
                    $totalPenjualan,
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
            $totalSaving != 0
        ): ?>


        <tr>


            <td>

                Tabungan Pelanggan
                (<?= $savingBasis; ?>)

            </td>


            <td>

                Rp

            </td>


            <td class="right">

                +

                <?= number_format(
                    abs($totalSaving),
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
                    $totalAkhir,
                    0,
                    ',',
                    '.'
                ); ?>


            </td>


        </tr>


    </table>


</div>


<!-- ====================================================== -->
<!-- TERBILANG + DITERIMA OLEH -->
<!-- ====================================================== -->

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
                        $totalAkhir
                    )
                ); ?>

                Rupiah

            </div>


            <!-- PEMBAYARAN -->

            <div class="bank">

                Pembayaran melalui transfer ke rekening:

                <br>

                BCA A/C 275757999

                <br>

                A/N SEGIHARTO

            </div>


        </td>


        <!-- ================================================== -->
        <!-- DITERIMA OLEH -->
        <!-- ================================================== -->

        <td class="bottom-right">


            Diterima Oleh,


            <div class="signature-space"></div>


            (__________________)


        </td>


    </tr>

</table>


<!-- ====================================================== -->
<!-- KETERANGAN -->
<!-- ====================================================== -->

<?php if (
    !empty(
        trim(
            $header->REMARK
            ?? ''
        )
    )
): ?>


<div class="remark">


    Keterangan :

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