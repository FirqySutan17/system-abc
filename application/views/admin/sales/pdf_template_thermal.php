<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<style>

@page{

    size: 241.3mm 139.7mm;

    margin:0;

}

html,
body{

    width:241.3mm;
    height:139.7mm;

    margin:0;
    padding:0;

    font-family:"Courier New", monospace;

    font-size:11px;

    color:#000;

}

body{

    position:relative;

}

.wrapper{

    position:absolute;

    top:12mm;
    left:18mm;
    right:18mm;

}

/*
|--------------------------------------------------------------------------
| GLOBAL
|--------------------------------------------------------------------------
*/

table{

    width:100%;

    border-collapse:collapse;

}

td,
th{

    vertical-align:top;

}

.right{
    text-align:right;
}

.center{
    text-align:center;
}

.bold{
    font-weight:bold;
}

.nowrap{
    white-space:nowrap;
}

/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.header-table td{

    padding:0;

}

.company{

    line-height:1.5;

}

.company-name{

    margin-top:4px;

}

.company-plant{

    margin-top:10px;

    font-weight:bold;

}

.title{

    text-align:center;

    font-size:26px;

    font-weight:bold;

    letter-spacing:5px;

    margin-top:2px;

}

.invoice-no{

    text-align:center;

    font-size:16px;

    font-weight:bold;

    margin-top:5px;

}

.info-table td{

    padding-top:2px;
    padding-bottom:2px;

}

/*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

.detail-table{

    margin-top:20px;

}

.detail-table thead th{

    border-bottom:1px solid #000;

    padding-bottom:7px;

    font-weight:normal;

}

.detail-table tbody td{

    padding-top:6px;
    padding-bottom:6px;

}

.detail-total td{

    padding-top:10px;

    font-weight:bold;

}

/*
|--------------------------------------------------------------------------
| SAY
|--------------------------------------------------------------------------
*/

.say{

    margin-top:28px;

    line-height:1.7;

}

/*
|--------------------------------------------------------------------------
| BANK
|--------------------------------------------------------------------------
*/

.bank{

    margin-top:24px;

    line-height:1.8;

}

/*
|--------------------------------------------------------------------------
| TOTAL
|--------------------------------------------------------------------------
*/

.total-wrapper{

    width:320px;

    margin-left:auto;

    margin-top:40px;

}

.total-wrapper td{

    padding-top:5px;
    padding-bottom:5px;

}

/*
|--------------------------------------------------------------------------
| SIGNATURE
|--------------------------------------------------------------------------
*/

.signature{

    margin-top:38px;

}

.signature-line{

    margin-top:60px;

    text-align:center;

}

</style>

</head>

<body>

<div class="wrapper">

    <!-- HEADER -->
    <table class="header-table">

        <tr>

            <!-- LEFT -->
            <td width="20%">

                <div class="company">

                    Pangkalan Ayam

                    <div class="company-name">

                        PT. ABADI BERSAMA CERAH

                    </div>

                    <!-- <div class="company-plant">

                        <?= strtoupper($header->PLANT_NAME); ?>

                    </div> -->

                    Jakarta Timur

                </div>

            </td>

            <!-- CENTER -->
            <td width="55%" style="text-align: center">

                <div class="title">

                    INVOICES

                </div>

                <div class="invoice-no">

                    No <?= $header->SALES; ?>

                </div>

            </td>

            <!-- RIGHT -->
            <td width="25%">

                <table class="info-table">

                    <tr>

                        <td width="90">
                            Tanggal
                        </td>

                        <td width="10">
                            :
                        </td>

                        <td>
                            <?= date('d/m/Y', strtotime($header->SALES_DATE)); ?>
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
                            <?= strtoupper($header->CUSTOMER_NAME); ?>
                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <!-- DETAIL -->
    <table class="detail-table">

        <thead>

            <tr>

                <th align="left" width="48%">
                    Keterangan
                </th>

                <th class="center" width="10%">
                    Ekor
                </th>

                <th class="center" width="12%">
                    Berat
                </th>

                <th class="right" width="12%">
                    Price
                </th>

                <th class="right" width="18%">
                    Jumlah(Rp)
                </th>

            </tr>

        </thead>

        <tbody>

        <?php

        $totalQty = 0;
        $totalBerat = 0;
        $grandTotal = 0;

        foreach($detail as $d):

            $qty    = (float)$d->JUMLAH;
            $berat  = (float)$d->BERAT;
            $harga  = (float)$d->HARGA;
            $total  = (float)$d->TOTAL;

            $totalQty += $qty;
            $totalBerat += $berat;
            $grandTotal += $total;

        ?>

        <tr>

            <td>

                <?= $d->MATERIAL; ?>
                -
                <?= strtoupper($d->MATERIAL_NAME); ?>

            </td>

            <td class="center">

                <?= number_format($qty,0,',','.'); ?>

            </td>

            <td class="center">

                <?= number_format($berat,2,',','.'); ?>

            </td>

            <td class="right">

                <?= number_format($harga,0,',','.'); ?>

            </td>

            <td class="right">

                <?= number_format($total,0,',','.'); ?>

            </td>

        </tr>

        <?php endforeach; ?>

        <!-- TOTAL -->
        <tr class="detail-total">

            <td></td>

            <td class="center">

                <?= number_format($totalQty,0,',','.'); ?>

            </td>

            <td class="center">

                <?= number_format($totalBerat,2,',','.'); ?>

            </td>

            <td></td>

            <td class="right">

                <?= number_format($grandTotal,0,',','.'); ?>

            </td>

        </tr>

        </tbody>

    </table>

    <!-- TOTAL -->
    <div class="total-wrapper">

        <table>

            <tr>

                <td width="120">
                    Subtotal
                </td>

                <td width="40">
                    IDR
                </td>

                <td class="right">

                    <?= number_format($grandTotal,0,',','.'); ?>

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

                    <?= number_format($grandTotal,0,',','.'); ?>

                </td>

            </tr>

        </table>

    </div>

    <!-- SAY -->
    <div class="say">

        Say :
        <?= ucwords(terbilang($grandTotal)); ?>
        Rupiah

    </div>

    <!-- BANK -->
    <div class="bank">

        Pembayaran Transfer ke Rekening:

        <br><br>

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

        </table>

        <div class="signature-line">

            

        </div>

    </div>

    <div class="signature">

        <table>
            <tr>

                <td width="60%">

                </td>

                <td class="center" height="50%">

                    (__________________)

                </td>

            </tr>

        </table>
    </div>

</div>

</body>
</html>