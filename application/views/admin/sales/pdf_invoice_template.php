<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>
    Faktur Penjualan
</title>


<style>

/*
|--------------------------------------------------------------------------
| HALAMAN
|--------------------------------------------------------------------------
*/

@page {

    margin:
        15mm
        15mm
        15mm
        15mm;

}


body {

    font-family:
        DejaVu Sans,
        sans-serif;

    font-size:
        9px;

    color:
        #000;

    margin:
        0;

    padding:
        0;

}


/*
|--------------------------------------------------------------------------
| UMUM
|--------------------------------------------------------------------------
*/

table {

    width:
        100%;

    border-collapse:
        collapse;

}


.text-left {

    text-align:
        left;

}


.text-center {

    text-align:
        center;

}


.text-right {

    text-align:
        right;

}


.bold {

    font-weight:
        bold;

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


.logo {

    width:
        145px;

}


.company-name {

    font-size:
        13px;

    font-weight:
        bold;

    margin-top:
        5px;

}


.company-address {

    margin-top:
        3px;

    font-size:
        8px;

    line-height:
        1.4;

}


.invoice-title {

    font-size:
        22px;

    font-weight:
        bold;

    text-align:
        right;

    padding-top:
        8px;

}


.invoice-number {

    font-size:
        10px;

    text-align:
        right;

    margin-top:
        4px;

    font-weight:
        bold;

}


.invoice-status {

    text-align:
        right;

    margin-top:
        3px;

    font-size:
        8px;

}


/*
|--------------------------------------------------------------------------
| GARIS HEADER
|--------------------------------------------------------------------------
*/

.header-line {

    border-top:
        2px solid #000;

    margin-top:
        10px;

    margin-bottom:
        10px;

}


/*
|--------------------------------------------------------------------------
| INFORMASI TRANSAKSI
|--------------------------------------------------------------------------
*/

.info-table {

    width:
        100%;

}


.info-table td {

    padding:
        3px 4px;

    vertical-align:
        top;

}


.info-label {

    font-weight:
        bold;

    width:
        85px;

}


.info-colon {

    width:
        8px;

}


/*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

.detail-table {

    margin-top:
        12px;

}


.detail-table th {

    background:
        #eeeeee;

    border:
        1px solid #000;

    padding:
        6px 5px;

    font-size:
        8.5px;

}


.detail-table td {

    border:
        1px solid #000;

    padding:
        5px;

    vertical-align:
        top;

}


.detail-table .material-name {

    font-weight:
        bold;

}


.material-code {

    font-size:
        7.5px;

    color:
        #555;

    margin-top:
        2px;

}


/*
|--------------------------------------------------------------------------
| TOTAL DETAIL
|--------------------------------------------------------------------------
*/

.detail-total {

    font-weight:
        bold;

    background:
        #f5f5f5;

}


/*
|--------------------------------------------------------------------------
| TABUNGAN
|--------------------------------------------------------------------------
*/

.saving-section {

    margin-top:
        15px;

}


.section-title {

    font-size:
        11px;

    font-weight:
        bold;

    margin-bottom:
        5px;

}


.saving-table {

    width:
        100%;

}


.saving-table th {

    background:
        #eeeeee;

    border:
        1px solid #000;

    padding:
        5px;

}


.saving-table td {

    border:
        1px solid #000;

    padding:
        5px;

}


/*
|--------------------------------------------------------------------------
| RINGKASAN
|--------------------------------------------------------------------------
*/

.summary-container {

    margin-top:
        15px;

}


.summary-box {

    width:
        48%;

    margin-left:
        auto;

}


.summary-box td {

    padding:
        4px 5px;

}


.summary-box .line-top td {

    border-top:
        1px solid #888;

    padding-top:
        6px;

}


.summary-box .total-sales td {

    border-top:
        1px solid #000;

    padding-top:
        6px;

    font-weight:
        bold;

}


.summary-box .grand-total td {

    border-top:
        2px solid #000;

    padding-top:
        7px;

    font-weight:
        bold;

    font-size:
        11px;

}


/*
|--------------------------------------------------------------------------
| TERBILANG + TANDA TANGAN
|--------------------------------------------------------------------------
*/

.bottom-section {

    width:
        100%;

    margin-top:
        18px;

}


.terbilang {

    width:
        60%;

    vertical-align:
        top;

    line-height:
        1.5;

}


.signature {

    width:
        40%;

    vertical-align:
        top;

    text-align:
        center;

}


.signature-space {

    height:
        25px;

}


/*
|--------------------------------------------------------------------------
| PEMBAYARAN
|--------------------------------------------------------------------------
*/

.bank {

    margin-top:
        12px;

    line-height:
        1.5;

}


/*
|--------------------------------------------------------------------------
| REMARK
|--------------------------------------------------------------------------
*/

.remark-section {

    margin-top:
        15px;

    line-height:
        1.5;

}


.remark-title {

    font-weight:
        bold;

}


.small {

    font-size:
        7.5px;

    color:
        #555;

}

</style>

</head>


<body>


<?php

/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
*/

$logo =
    FCPATH .
    'assets/img/abc-trans.png';


$logo64 =
    file_exists(
        $logo
    )

    ? 'data:image/png;base64,' .
      base64_encode(
          file_get_contents(
              $logo
          )
      )

    : '';

?>


<!-- ========================================================== -->
<!-- HEADER -->
<!-- ========================================================== -->

<table class="header">

    <tr>


        <!-- ================================================== -->
        <!-- PERUSAHAAN -->
        <!-- ================================================== -->

        <td width="55%">

            <?php if ($logo64): ?>

                <img
                    src="<?= $logo64 ?>"
                    class="logo">

            <?php endif; ?>


            <div class="company-name">

                PT. ABADI BERSAMA CERAH

            </div>


            <div class="company-address">

                Pangkalan Ayam

                <br>

                <?= strtoupper(
                    $header->PLANT_NAME
                    ?: 'PLANT'
                ); ?>

            </div>

        </td>


        <!-- ================================================== -->
        <!-- JUDUL -->
        <!-- ================================================== -->

        <td width="45%">

            <div class="invoice-title">

                FAKTUR PENJUALAN

            </div>


            <div class="invoice-number">

                Nomor :
                <?= $header->SALES; ?>

            </div>


            <div class="invoice-status">

                Status :
                <?= $header->STATUS ?: '-'; ?>

            </div>

        </td>

    </tr>

</table>


<div class="header-line"></div>


<!-- ========================================================== -->
<!-- INFORMASI TRANSAKSI -->
<!-- ========================================================== -->

<table class="info-table">

    <tr>

        <!-- PLANT -->

        <td class="info-label">

            Plant

        </td>

        <td class="info-colon">

            :

        </td>

        <td width="30%">

            <?= $header->PLANT_NAME ?: '-'; ?>

        </td>


        <!-- TANGGAL -->

        <td class="info-label">

            Tanggal

        </td>

        <td class="info-colon">

            :

        </td>

        <td>

            <?= date(
                'd-m-Y',
                strtotime(
                    $header->SALES_DATE
                )
            ); ?>

        </td>

    </tr>


    <tr>

        <!-- CUSTOMER -->

        <td class="info-label">

            Pelanggan

        </td>

        <td class="info-colon">

            :

        </td>

        <td>

            (<?= $header->CUSTOMER; ?>)

            <?= $header->CUSTOMER_NAME ?: '-'; ?>

        </td>


        <!-- NOMOR NOTA -->

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


    <tr>

        <!-- PEMBAYARAN -->

        <td class="info-label">

            Pembayaran

        </td>

        <td class="info-colon">

            :

        </td>

        <td>

            <?= $header->PAYMENT_INFO; ?>

        </td>


        <!-- JAM -->

        <td class="info-label">

            Jam Cetak

        </td>

        <td class="info-colon">

            :

        </td>

        <td>

            <?= date(
                'd-m-Y H:i:s'
            ); ?>

        </td>

    </tr>

</table>


<!-- ========================================================== -->
<!-- DETAIL PENJUALAN -->
<!-- ========================================================== -->

<table class="detail-table">

    <thead>

        <tr>

            <th width="4%">

                No

            </th>

            <th width="30%">

                Material

            </th>

            <th width="9%">

                Basis

            </th>

            <th width="9%">

                Ekor

            </th>

            <th width="12%">

                Berat

            </th>

            <th width="16%">

                Harga

            </th>

            <th width="20%">

                Jumlah (Rp)

            </th>

        </tr>

    </thead>


    <tbody>


    <?php

    $no =
        1;

    $detailQty =
        0;

    $detailBerat =
        0;

    $detailTotal =
        0;

    ?>


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


        $detailQty +=
            $qty;


        $detailBerat +=
            $berat;


        $detailTotal +=
            $total;

        ?>


        <tr>


            <!-- NO -->

            <td class="text-center">

                <?= $no++; ?>

            </td>


            <!-- MATERIAL -->

            <td>

                <div
                    class="material-name">

                    <?= strtoupper(
                        $d->MATERIAL_NAME
                        ?: '-'
                    ); ?>

                </div>


                <div
                    class="material-code">

                    Kode :
                    <?= $d->MATERIAL; ?>

                </div>

            </td>


            <!-- BASIS -->

            <td class="text-center">

                <?= $d->CALC_BASIS ?: '-'; ?>

            </td>


            <!-- EKOR -->

            <td class="text-right">

                <?= number_format(
                    $qty,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


            <!-- BERAT -->

            <td class="text-right">

                <?= number_format(
                    $berat,
                    2,
                    ',',
                    '.'
                ); ?>

                Kg

            </td>


            <!-- HARGA -->

            <td class="text-right">

                Rp
                <?= number_format(
                    $harga,
                    0,
                    ',',
                    '.'
                ); ?>


                <br>


                <span class="small">

                    <?php if (
                        strtoupper(
                            $d->CALC_BASIS
                        ) === 'EKOR'
                    ): ?>

                        per Ekor

                    <?php else: ?>

                        per Kg

                    <?php endif; ?>

                </span>

            </td>


            <!-- TOTAL -->

            <td class="text-right">

                Rp
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
                colspan="7"
                class="text-center">

                Tidak ada detail penjualan.

            </td>

        </tr>


    <?php endif; ?>


    <!-- ====================================================== -->
    <!-- TOTAL DETAIL -->
    <!-- ====================================================== -->

    <tr class="detail-total">


        <td
            colspan="3"
            class="text-right">

            Total

        </td>


        <td class="text-right">

            <?= number_format(
                $detailQty,
                0,
                ',',
                '.'
            ); ?>

        </td>


        <td class="text-right">

            <?= number_format(
                $detailBerat,
                2,
                ',',
                '.'
            ); ?>

            Kg

        </td>


        <td></td>


        <td class="text-right">

            Rp
            <?= number_format(
                $detailTotal,
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


<div class="saving-section">


    <div class="section-title">

        TABUNGAN PELANGGAN

    </div>


    <table class="saving-table">


        <thead>

            <tr>

                <th width="8%">

                    No

                </th>

                <th width="22%">

                    Nomor Tabungan

                </th>

                <th width="13%">

                    Basis

                </th>

                <th width="18%">

                    Tarif

                </th>

                <th width="18%">

                    Nilai

                </th>

                <th width="21%">

                    Sisa

                </th>

            </tr>

        </thead>


        <tbody>


        <?php

        $savingNo =
            1;

        ?>


        <?php foreach (
            $saving
            as $sv
        ): ?>


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


                    <span class="small">

                        <?php if (
                            strtoupper(
                                $sv->BASIS
                            ) === 'EKOR'
                        ): ?>

                            per Ekor

                        <?php else: ?>

                            per Kg

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


            <?php if (
                !empty(
                    $sv->REMARK
                )
            ): ?>


                <tr>

                    <td></td>

                    <td
                        colspan="5">

                        <span class="small">

                            Keterangan :
                            <?= $sv->REMARK; ?>

                        </span>

                    </td>

                </tr>


            <?php endif; ?>


        <?php endforeach; ?>


        </tbody>

    </table>

</div>


<?php endif; ?>


<!-- ========================================================== -->
<!-- RINGKASAN -->
<!-- ========================================================== -->

<div class="summary-container">


    <table class="summary-box">


        <!-- PENJUALAN DASAR -->

        <tr>

            <td>

                Penjualan Dasar

            </td>

            <td
                class="text-right">

                Rp
                <?= number_format(
                    $summary['base_sales'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <!-- MODAL -->

        <?php if (
            $summary['modal'] != 0
        ): ?>


        <tr>

            <td>

                Modal

            </td>

            <td
                class="text-right">

                Rp
                <?= number_format(
                    $summary['modal'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <?php endif; ?>


        <!-- BIAYA -->

        <?php if (
            $summary['biaya'] != 0
        ): ?>


        <tr>

            <td>

                Biaya

            </td>

            <td
                class="text-right">

                <?php if (
                    $summary['biaya'] > 0
                ): ?>

                    +

                <?php endif; ?>

                Rp
                <?= number_format(
                    abs(
                        $summary['biaya']
                    ),
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>


        <?php endif; ?>


        <!-- DISKON -->

        <?php if (
            $summary['discount'] != 0
        ): ?>


        <tr>

            <td>

                Diskon

            </td>

            <td
                class="text-right">

                -

                Rp
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


        <!-- PEMBULATAN -->

        <?php if (
            $summary['rounding'] != 0
        ): ?>


        <tr>

            <td>

                Pembulatan

            </td>

            <td
                class="text-right">

                <?php if (
                    $summary['rounding'] > 0
                ): ?>

                    +

                <?php else: ?>

                    -

                <?php endif; ?>

                Rp
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


        <!-- TOTAL PENJUALAN -->

        <tr class="total-sales">


            <td>

                Total Penjualan

            </td>


            <td
                class="text-right">

                Rp
                <?= number_format(
                    $summary['sales_amount'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


        </tr>


        <!-- TABUNGAN -->

        <?php if (
            $summary['total_saving'] != 0
        ): ?>


        <tr>


            <td>

                Tabungan Pelanggan

            </td>


            <td
                class="text-right">

                +
                Rp
                <?= number_format(
                    $summary['total_saving'],
                    0,
                    ',',
                    '.'
                ); ?>


            </td>


        </tr>


        <?php endif; ?>


        <!-- TOTAL AKHIR -->

        <tr class="grand-total">


            <td>

                TOTAL AKHIR

            </td>


            <td
                class="text-right">

                Rp
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
        <!-- TERBILANG -->
        <!-- ================================================== -->

        <td class="terbilang">


            <strong>
                Terbilang:
            </strong>


            <br>


            <?= ucwords(
                terbilang(
                    $summary['grand_total']
                )
            ); ?>

            Rupiah


            <!-- PEMBAYARAN -->

            <div class="bank">


                <strong>
                    Pembayaran melalui transfer ke:
                </strong>


                <br>


                BCA A/C 275757999


                <br>


                A/N SEGIHARTO


            </div>


        </td>


        <!-- ================================================== -->
        <!-- TANDA TANGAN -->
        <!-- ================================================== -->

        <td class="signature">


            Diterima Oleh,


            <div class="signature-space"></div>


            (____________________________)


        </td>


    </tr>


</table>


<!-- ========================================================== -->
<!-- REMARK -->
<!-- ========================================================== -->

<?php if (
    !empty(
        trim(
            $header->REMARK
            ?? ''
        )
    )
): ?>


<div class="remark-section">


    <span class="remark-title">

        Keterangan:

    </span>


    <br>


    <?= nl2br(
        htmlspecialchars(
            $header->REMARK
        )
    ); ?>


</div>


<?php endif; ?>


</body>

</html>