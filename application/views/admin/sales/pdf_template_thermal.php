<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>SALES <?= $header->SALES; ?></title>

<style>

@page {
    size: 241.3mm 139.7mm;
    margin: 0;
}

html,
body {

    width: 241.3mm;
    height: 139.7mm;

    margin: 0;
    padding: 0;

    font-family: "Courier New", monospace;
    font-size: 10px;

    color: #000;

}

body {

    overflow: hidden;

}

.print-area {

    width: 205mm;

    margin-left: 18mm;
    margin-top: 10mm;

}

/*
|--------------------------------------------------------------------------
| GLOBAL
|--------------------------------------------------------------------------
*/

table {

    width: 100%;

    border-collapse: collapse;

}

td,
th {

    padding: 0;

    vertical-align: top;

}

.center {

    text-align: center;

}

.right {

    text-align: right;

}

.bold {

    font-weight: bold;

}

.nowrap {

    white-space: nowrap;

}

/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.header {

    width: 100%;

}

.company {

    width: 30%;

    line-height: 1.5;

}

.invoice {

    width: 40%;

    text-align: center;

}

.info {

    width: 30%;

}

.title {

    font-size: 20px;

    font-weight: bold;

    letter-spacing: 3px;

}

.invoice-no {

    margin-top: 3px;

    font-weight: bold;

}

/*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

.detail {

    margin-top: 10mm;

}

.detail th {

    border-bottom: 1px solid #000;

    padding-bottom: 2mm;

    font-weight: normal;

}

.detail td {

    padding-top: 1.5mm;

}

/*
|--------------------------------------------------------------------------
| TOTAL
|--------------------------------------------------------------------------
*/

.detail-total td {

    padding-top: 3mm;

    font-weight: bold;

}

/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/

.summary {

    width: 75mm;

    margin-left: auto;

    margin-top: 5mm;

}

.summary td {

    padding-top: 1mm;

    padding-bottom: 1mm;

}

/*
|--------------------------------------------------------------------------
| SAY
|--------------------------------------------------------------------------
*/

.say {

    margin-top: 5mm;

}

/*
|--------------------------------------------------------------------------
| BANK
|--------------------------------------------------------------------------
*/

.bank {

    margin-top: 4mm;

    line-height: 1.5;

}

/*
|--------------------------------------------------------------------------
| SIGNATURE
|--------------------------------------------------------------------------
*/

.signature {

    margin-top: 6mm;

}

.signature-space {

    height: 15mm;

}

</style>

</head>

<body>

<div class="print-area">

    <!-- HEADER -->

    <table class="header">

        <tr>

            <td class="company">

                Pangkalan Ayam<br>

                <strong>
                    PT. ABADI BERSAMA CERAH
                </strong>

                <br>

                Jakarta Timur

            </td>

            <td class="invoice">

                <div class="title">
                    INVOICES
                </div>

                <div class="invoice-no">

                    No <?= $header->SALES; ?>

                </div>

            </td>

            <td class="info">

                <table>

                    <tr>

                        <td width="70">
                            Tanggal
                        </td>

                        <td width="10">
                            :
                        </td>

                        <td>
                            <?= date(
                                'd/m/Y',
                                strtotime($header->SALES_DATE)
                            ); ?>
                        </td>

                    </tr>

                    <tr>

                        <td>
                            Jam
                        </td>

                        <td>
                            :
                        </td>

                        <td>
                            <?= date('H:i:s'); ?>
                        </td>

                    </tr>

                    <tr>

                        <td>
                            Customer
                        </td>

                        <td>
                            :
                        </td>

                        <td>
                            <?= strtoupper(
                                $header->CUSTOMER_NAME
                            ); ?>
                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>


    <!-- DETAIL -->

    <?php

    $totalQty    = 0;
    $totalBerat  = 0;
    $grandTotal  = 0;

    ?>

    <table class="detail">

        <thead>

            <tr>

                <th width="45%" align="left">
                    Keterangan
                </th>

                <th width="10%" class="center">
                    Ekor
                </th>

                <th width="12%" class="center">
                    Berat
                </th>

                <th width="13%" class="right">
                    Price
                </th>

                <th width="20%" class="right">
                    Jumlah(Rp)
                </th>

            </tr>

        </thead>

        <tbody>

        <?php foreach ($detail as $d): ?>

            <?php

            $qty   = (float) $d->JUMLAH;
            $berat = (float) $d->BERAT;
            $harga = (float) $d->HARGA;
            $total = (float) $d->TOTAL;

            $totalQty   += $qty;
            $totalBerat += $berat;
            $grandTotal += $total;

            ?>

            <tr>

                <td>

                    <?= $d->MATERIAL; ?>
                    -
                    <?= strtoupper(
                        $d->MATERIAL_NAME
                    ); ?>

                </td>

                <td class="center">

                    <?= number_format(
                        $qty,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

                <td class="center">

                    <?= number_format(
                        $berat,
                        2,
                        ',',
                        '.'
                    ); ?>

                </td>

                <td class="right">

                    <?= number_format(
                        $harga,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

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


        <!-- TOTAL DETAIL -->

        <tr class="detail-total">

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
                    $grandTotal,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

        </tr>

        </tbody>

    </table>


    <!-- SUMMARY -->

    <div class="summary">

        <table>

            <tr>

                <td>
                    Subtotal
                </td>

                <td width="30">
                    IDR
                </td>

                <td class="right">

                    <?= number_format(
                        $grandTotal,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

            </tr>

            <tr>

                <td class="bold">
                    Total
                </td>

                <td class="bold">
                    IDR
                </td>

                <td class="right bold">

                    <?= number_format(
                        $grandTotal,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

            </tr>

        </table>

    </div>


    <!-- SAY -->

    <div class="say">

        Say :
        <?= ucwords(
            terbilang($grandTotal)
        ); ?>
        Rupiah

    </div>


    <!-- BANK -->

    <div class="bank">

        Pembayaran Transfer ke Rekening:

        <br>

        BCA A/C 275757999

        <br>

        A/N SEGIHARTO

    </div>


    <!-- SIGNATURE -->

    <div class="signature">

        <table>

            <tr>

                <td width="60%">

                    Jakarta,
                    <?= date('d/m/Y'); ?>

                </td>

                <td class="center">

                    Diterima Oleh,

                </td>

            </tr>

            <tr>

                <td></td>

                <td class="center signature-space"></td>

            </tr>

            <tr>

                <td></td>

                <td class="center">

                    (__________________)

                </td>

            </tr>

        </table>

    </div>

</div>


<script>

window.onload = function () {

    window.print();

};

</script>

</body>

</html>