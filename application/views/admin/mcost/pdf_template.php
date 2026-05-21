<?php
$logo = FCPATH . 'assets/img/abc-trans.png';

$logo64 = file_exists($logo)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo))
    : '';
?>

<style>

body{
    font-family:sans-serif;
    font-size:11px;
    color:#222;
}

.header{
    margin-bottom:20px;
}

.title{
    text-align:center;
    font-size:22px;
    font-weight:bold;
    margin-bottom:4px;
}

.subtitle{
    text-align:center;
    font-size:12px;
    color:#666;
}

.card{
    border:1px solid #dfe7ef;
    border-radius:12px;
    overflow:hidden;
}

.card-head{
    background:#0F4C81;
    color:#fff;
    padding:12px 15px;
    font-size:14px;
    font-weight:bold;
}

.meta{
    padding:15px;
    background:#f8fafc;
    border-bottom:1px solid #e5e7eb;
}

.meta-table{
    width:100%;
    border-collapse:collapse;
}

.meta-table td{
    border:none;
    padding:4px 0;
    vertical-align:top;
}

.label{
    width:110px;
    font-weight:bold;
    white-space:nowrap;
}

.sep{
    width:10px;
    text-align:center;
    font-weight:bold;
}

.gap{
    width:30px;
}

.summary-box{
    padding:5px 15px;
    border-bottom:1px solid #e5e7eb;
    background:#fff;
}

.summary-table{
    width:100%;
    border-collapse:collapse;
}

.summary-table td{
    border:none;
    text-align:center;
    padding:10px;
}

.summary-value{
    font-size:22px;
    font-weight:bold;
    color:#0F4C81;
}

.summary-label{
    font-size:11px;
    color:#666;
    margin-top:4px;
}

.table{
    width:100%;
    border-collapse:collapse;
}

.table th,
.table td{
    border:1px solid #d9dee5;
    padding:7px;
    font-size:10px;
}

.table th{
    background:#eef2f7;
    text-align:center;
    font-weight:bold;
}

.right{
    text-align:right;
}

.center{
    text-align:center;
}

.subtotal{
    background:#f8fafc;
    font-weight:bold;
}

.remark-box{
    margin-top:18px;
    padding:12px;
    border:1px dashed #cbd5e1;
    border-radius:10px;
    background:#fcfcfc;
}

.remark-title{
    font-weight:bold;
    margin-bottom:6px;
}

.sign{
    margin-top:55px;
    width:100%;
}

.sign td{
    width:50%;
    text-align:center;
    border:none;
}

.sign-line{
    margin-top:55px;
    font-weight:bold;
}

.badge{
    display:inline-block;
    padding:3px 8px;
    border-radius:10px;
    font-size:9px;
    font-weight:bold;
}

.badge-success{
    background:#d1fae5;
    color:#065f46;
}

.badge-primary{
    background:#dbeafe;
    color:#1d4ed8;
}

</style>

<div class="header">

    <table width="100%">

        <tr>

            <td width="70">

                <?php if($logo64): ?>

                    <img src="<?= $logo64 ?>" height="60">

                <?php endif; ?>

            </td>

            <td>

                <div class="title">
                    PT. Abadi Bersama Cerah
                </div>

                <div class="subtitle">
                    COST SLIP
                </div>

            </td>

        </tr>

    </table>

</div>

<div class="card">

    <div class="card-head">

        #<?= $header->COST ?>

    </div>

    <!-- META -->
    <div class="meta">

        <table class="meta-table">

            <tr>

                <td class="label">PLANT</td>
                <td class="sep">:</td>
                <td><?= $header->PLANT_NAME ?: '-' ?></td>

                <td class="gap"></td>

                <td class="label">PAYMENT</td>
                <td class="sep">:</td>
                <td><?= $header->PEMBAYARAN ?: '-' ?></td>

            </tr>

            <tr>

                <td class="label">COST DATE</td>
                <td class="sep">:</td>

                <td>
                    <?= strtoupper(date('d F Y', strtotime($header->COST_DATE))) ?>
                </td>

                <td class="gap"></td>

                <td class="label">SLIP NO</td>
                <td class="sep">:</td>

                <td>
                    <?= $header->SLIP_NO ?: '-' ?>
                </td>

            </tr>

            <tr>

                <td class="label">CREATED BY</td>
                <td class="sep">:</td>

                <td>
                    <?= strtoupper($header->CREATED_BY) ?>
                </td>

                <td class="gap"></td>

                <td class="label">STATUS</td>
                <td class="sep">:</td>

                <td>

                    <span class="badge badge-success">
                        POSTED
                    </span>

                </td>

            </tr>

        </table>

    </div>

    <!-- SUMMARY -->
    <div class="summary-box">

        <table class="summary-table">

            <tr>

                <td>

                    <div class="summary-value">
                        <?= number_format($summary['item'],0,',','.') ?>
                    </div>

                    <div class="summary-label">
                        Total Item
                    </div>

                </td>

                <td>

                    <div class="summary-value">
                        <?= number_format($summary['qty'],0,',','.') ?>
                    </div>

                    <div class="summary-label">
                        Total Qty
                    </div>

                </td>

                <td>

                    <div class="summary-value">
                        Rp <?= number_format($summary['total'],0,',','.') ?>
                    </div>

                    <div class="summary-label">
                        Grand Total
                    </div>

                </td>

            </tr>

        </table>

    </div>

    <!-- DETAIL -->
    <table class="table">

        <thead>

            <tr>

                <th width="5%">NO</th>

                <th width="35%">
                    COST TYPE
                </th>

                <th width="10%">
                    QTY
                </th>

                <th width="20%">
                    PRICE
                </th>

                <th width="20%">
                    TOTAL
                </th>

                <th width="10%">
                    REMARK
                </th>

            </tr>

        </thead>

        <tbody>

            <?php foreach($detail as $i => $d): ?>

                <tr>

                    <td class="center">
                        <?= $i + 1 ?>
                    </td>

                    <td>
                        <?= $d->TIPE_COST_NAME ?: '-' ?>
                    </td>

                    <td class="right">
                        <?= number_format($d->QTY,2,',','.') ?>
                    </td>

                    <td class="right">
                        <?= number_format($d->JUMLAH,0,',','.') ?>
                    </td>

                    <td class="right">
                        <?= number_format($d->TOTAL,0,',','.') ?>
                    </td>

                    <td>
                        <?= $d->REMARK ?: '-' ?>
                    </td>

                </tr>

            <?php endforeach; ?>

            <tr class="subtotal">

                <td colspan="2">
                    GRAND TOTAL
                </td>

                <td class="right">
                    <?= number_format($summary['qty'],2,',','.') ?>
                </td>

                <td></td>

                <td class="right">
                    <?= number_format($summary['total'],0,',','.') ?>
                </td>

                <td></td>

            </tr>

        </tbody>

    </table>

</div>

<div class="remark-box">

    <div class="remark-title">
        Remark
    </div>

    <?= !empty($header->REMARK)
        ? nl2br($header->REMARK)
        : '-'
    ?>

</div>

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

        <td class="sign-line">
            (_____________________)
        </td>

        <td class="sign-line">
            (_____________________)
        </td>

    </tr>

</table>