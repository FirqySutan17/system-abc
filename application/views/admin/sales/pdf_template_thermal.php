<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>
    SALES <?= $header->SALES; ?>
</title>

<style>

@page {
    size: 241.3mm 139.7mm;
    margin: 0;
}

html,
body {
    width: 241.3mm;
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
    margin-top: 7mm;

    /*
    |----------------------------------------
    | Tinggi area cetak aman
    |----------------------------------------
    */
    height: 125mm;

    overflow: hidden;
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

.left {

    text-align: left;

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
    margin-top: 6mm;
}

.detail th {
    border-bottom:
        1px solid #000;
    padding-bottom: 2mm;
    font-weight: normal;
}

.detail td {
    padding-top: 1mm;
}

/*
|--------------------------------------------------------------------------
| DETAIL TOTAL
|--------------------------------------------------------------------------
*/

.detail-total td {

    padding-top: 3mm;

    font-weight: bold;

    border-top:
        1px solid #000;

}


/*
|--------------------------------------------------------------------------
| SAVING
|--------------------------------------------------------------------------
*/

.saving {
    margin-top: 2mm;
}

.saving-title {
    font-weight: bold;
    margin-bottom: 1mm;
}

/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/

.summary {
    width: 80mm;
    margin-left: auto;
    margin-top: 2mm;
}

.summary td {

    padding-top: 1mm;

    padding-bottom: 1mm;

}

.summary .separator td {

    border-top:
        1px solid #000;

    padding-top: 2mm;

}

.summary .grand td {

    border-top:
        2px solid #000;

    padding-top: 2mm;

    font-weight: bold;

}

.summary .outstanding-header td {

    border-top:
        1px solid #000;

    padding-top: 2mm;

    font-weight: bold;

}

.summary .outstanding-total td {

    border-top:
        2px solid #000;

    padding-top: 2mm;

    font-weight: bold;

}


/*
|--------------------------------------------------------------------------
| SAY
|--------------------------------------------------------------------------
*/

.say {

    margin-top: 4mm;

    line-height: 1.4;

}


/*
|--------------------------------------------------------------------------
| BANK
|--------------------------------------------------------------------------
*/

.bank {

    margin-top: 3mm;

    line-height: 1.4;

}


/*
|--------------------------------------------------------------------------
| SIGNATURE
|--------------------------------------------------------------------------
*/

.signature {
    margin-top: 3mm;
}

.signature-space {
    height: 8mm;
}

.small {

    font-size: 8px;

}

</style>

</head>


<body>

<div class="print-area">


    <!-- ====================================================== -->
    <!-- HEADER -->
    <!-- ====================================================== -->

    <table class="header">

        <tr>


            <!-- COMPANY -->

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


            <!-- TITLE -->

            <td class="invoice">

                <div class="title">

                    INVOICES

                </div>

                <div class="invoice-no">

                    No <?= $header->SALES; ?>

                </div>

            </td>


            <!-- INFO -->

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
                                strtotime(
                                    $header->SALES_DATE
                                )
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
                            <?= date(
                                'H:i:s'
                            ); ?>
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
                                ?: $header->CUSTOMER
                            ); ?>

                        </td>

                    </tr>


                    <tr>

                        <td>
                            Pembayaran
                        </td>

                        <td>
                            :
                        </td>

                        <td>

                            <?= strtoupper(
                                $header->JENIS_PAY
                                ?: '-'
                            ); ?>

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>


    <!-- ====================================================== -->
    <!-- DETAIL -->
    <!-- ====================================================== -->

    <?php

    $totalQty =
        0;

    $totalBerat =
        0;

    $baseSales =
        0;

    ?>

    <table class="detail">

        <thead>

            <tr>

                <th
                    width="38%"
                    class="left">

                    Keterangan

                </th>

                <th
                    width="9%"
                    class="center">

                    Basis

                </th>

                <th
                    width="9%"
                    class="center">

                    Ekor

                </th>

                <th
                    width="11%"
                    class="center">

                    Berat

                </th>

                <th
                    width="13%"
                    class="right">

                    Price

                </th>

                <th
                    width="20%"
                    class="right">

                    Jumlah(Rp)

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

            $totalQty +=
                $qty;

            $totalBerat +=
                $berat;

            $baseSales +=
                $total;

            ?>


            <tr>

                <!-- MATERIAL -->

                <td>

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


                <!-- QTY -->

                <td class="center">

                    <?= number_format(
                        $qty,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>


                <!-- WEIGHT -->

                <td class="center">

                    <?= number_format(
                        $berat,
                        2,
                        ',',
                        '.'
                    ); ?>

                </td>


                <!-- PRICE -->

                <td class="right">

                    <?= number_format(
                        $harga,
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>


                <!-- TOTAL -->

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


        <!-- DETAIL TOTAL -->

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
    <!-- SAVING -->
    <!-- ====================================================== -->

    <?php if (
        !empty($saving)
    ): ?>


        <div class="saving">


            <div class="saving-title">

                CUSTOMER SAVING

            </div>


            <table>

                <thead>

                    <tr>

                        <th
                            width="32%"
                            class="left">

                            Saving No

                        </th>

                        <th
                            width="15%"
                            class="center">

                            Basis

                        </th>

                        <th
                            width="20%"
                            class="right">

                            Rate

                        </th>

                        <th
                            width="18%"
                            class="right">

                            Amount

                        </th>

                        <th
                            width="15%"
                            class="right">

                            Remain

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
                                $sv->BASIS === 'EKOR'
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

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>


    <!-- ====================================================== -->
    <!-- SUMMARY -->
    <!-- ====================================================== -->

    <div class="summary">

        <table>


            <!-- BASE SALES -->

            <tr>

                <td>

                    Base Sales

                </td>

                <td width="30">

                    IDR

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


            <!-- BIAYA -->

            <?php if (
                $summary['biaya'] > 0
            ): ?>

            <tr>

                <td>

                    Biaya

                </td>

                <td>

                    IDR

                </td>

                <td class="right">

                    +<?= number_format(
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

                <td>

                    IDR

                </td>

                <td class="right">

                    -<?= number_format(
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

                <td>

                    IDR

                </td>

                <td class="right">

                    +<?= number_format(
                        $summary['rounding'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

            </tr>

            <?php endif; ?>


            <!-- TOTAL SALES -->

            <tr>

                <td class="bold">

                    Total Sales

                </td>

                <td class="bold">

                    IDR

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


            <!-- SAVING -->

            <?php if (
                $summary['total_saving'] > 0
            ): ?>

            <tr>

                <td>

                    Customer Saving

                </td>

                <td>

                    IDR

                </td>

                <td class="right">

                    +<?= number_format(
                        $summary['total_saving'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

            </tr>

            <?php endif; ?>


            <!-- GRAND TOTAL -->

            <tr class="grand">

                <td>

                    Grand Total

                </td>

                <td>

                    IDR

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


            <!-- OUTSTANDING -->

            <tr class="outstanding-header">

                <td colspan="3">

                    OUTSTANDING

                </td>

            </tr>


            <!-- SALES OUTSTANDING -->

            <tr>

                <td>

                    Outstanding Sales

                </td>

                <td>

                    IDR

                </td>

                <td class="right">

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

                <td>

                    IDR

                </td>

                <td class="right">

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

                <td>

                    IDR

                </td>

                <td class="right">

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
    <!-- SAY -->
    <!-- ====================================================== -->

    <div class="say">

        Say :

        <?= ucwords(
            terbilang(
                $summary['grand_total']
            )
        ); ?>

        Rupiah

    </div>


    <!-- ====================================================== -->
    <!-- BANK -->
    <!-- ====================================================== -->

    <div class="bank">

        Pembayaran Transfer ke Rekening:

        <br>

        BCA A/C 275757999

        <br>

        A/N SEGIHARTO

    </div>


    <!-- ====================================================== -->
    <!-- SIGNATURE -->
    <!-- ====================================================== -->

    <div class="signature">

        <table>

            <tr>

                <td width="60%">

                    Jakarta,

                    <?= date(
                        'd/m/Y'
                    ); ?>

                </td>

                <td class="center">

                    Diterima Oleh,

                </td>

            </tr>


            <tr>

                <td></td>

                <td
                    class="
                        center
                        signature-space
                    ">

                </td>

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