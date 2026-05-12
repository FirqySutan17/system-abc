<?php
$logo = FCPATH.'assets/img/abc-trans.png';

$logo64 = file_exists($logo)
    ? 'data:image/png;base64,'.base64_encode(file_get_contents($logo))
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
        width:90px;
        font-weight:bold;
        white-space:nowrap;
    }

    .sep{
        width:10px;
        text-align:center;
        font-weight:bold;
    }

    .gap{
        width:35px;
    }

    .table{
        width:100%;
        border-collapse:collapse;
    }

    .table th,
    .table td{
        border:1px solid #d9dee5;
        padding:8px;
        font-size:11px;
    }

    .table th{
        background:#eef2f7;
        font-weight:bold;
        text-align:center;
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

    .section-title{
        margin-top:20px;
        margin-bottom:10px;
        font-size:13px;
        font-weight:bold;
        color:#0F4C81;
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

</style>

<div class="header">

    <table width="100%" border="0">

        <tr>

            <td width="70" style="border:none;">

                <?php if($logo64): ?>

                    <img src="<?= $logo64 ?>" height="60">

                <?php endif; ?>

            </td>

            <td style="border:none;">

                <div class="title">
                    PT. Abadi Bersama Cerah
                </div>

                <div class="subtitle">
                    PURCHASE ORDER
                </div>

            </td>

        </tr>

    </table>

</div>

<div class="card">

    <div class="card-head">
        #<?= $header->PO ?>
    </div>

    <div class="meta">

        <table class="meta-table">

            <tr>

                <td class="label">PLANT</td>
                <td class="sep">:</td>
                <td><?= $header->PLANT_NAME ?></td>

                <td class="gap"></td>

                <td class="label">PO DATE</td>
                <td class="sep">:</td>

                <td>
                    <?= strtoupper(
                        date(
                            'd F Y',
                            strtotime($header->PO_DATE)
                        )
                    ) ?>
                </td>

            </tr>

            <tr>

                <td class="label">SUPPLIER</td>
                <td class="sep">:</td>

                <td>
                    <?= $header->SUPPLIER ?>
                    -
                    <?= $header->SUPPLIER_NAME ?>
                </td>

                <td class="gap"></td>

                <td class="label">PO TYPE</td>
                <td class="sep">:</td>

                <td><?= $header->PO_NAME ?></td>

            </tr>

            <tr>

                <td class="label">MATERIAL</td>
                <td class="sep">:</td>

                <td>
                    <?= $header->MATERIAL ?>
                    -
                    <?= $header->MATERIAL_NAME ?>
                </td>

                <td class="gap"></td>

                <td class="label">TRUCK</td>
                <td class="sep">:</td>

                <td>
                    <?= $header->NO_TRUCK ?: '-' ?>
                </td>

            </tr>

            <tr>

                <td class="label">QTY</td>
                <td class="sep">:</td>

                <td>
                    <?= number_format(
                        $header->JUMLAH,
                        2,
                        ',',
                        '.'
                    ) ?>
                </td>

                <td class="gap"></td>

                <td class="label">DRIVER</td>
                <td class="sep">:</td>

                <td>
                    <?= $header->DRIVER ?: '-' ?>
                </td>

            </tr>

            <tr>

                <td class="label">WEIGHT</td>
                <td class="sep">:</td>

                <td>
                    <?= number_format(
                        $header->BERAT,
                        2,
                        ',',
                        '.'
                    ) ?>
                </td>

                <td class="gap"></td>

                <td class="label">PRICE</td>
                <td class="sep">:</td>

                <td>
                    Rp <?= number_format(
                        $header->HARGA,
                        0,
                        ',',
                        '.'
                    ) ?>
                </td>

            </tr>

            <tr>

                <td class="label">TOTAL</td>
                <td class="sep">:</td>

                <td colspan="5">
                    Rp <?= number_format(
                        $header->TOTAL,
                        0,
                        ',',
                        '.'
                    ) ?>
                </td>

            </tr>

            <tr>

                <td class="label">STATUS</td>
                <td class="sep">:</td>

                <td colspan="5">

                    <?= $header->STATUS
                        ? 'RECEIVED'
                        : 'OPEN'
                    ?>

                </td>

            </tr>

        </table>

    </div>

    <table class="table">

        <thead>

            <tr>

                <th width="6%">NO</th>

                <th>CUSTOMER</th>

                <th width="13%">QTY</th>

                <th width="13%">WEIGHT</th>

                <th width="15%">PRICE</th>

                <th width="18%">TOTAL</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach($detail as $i => $d): ?>

            <tr>

                <td class="center">
                    <?= $i + 1 ?>
                </td>

                <td>
                    <?= $d->CUSTOMER ?>
                    -
                    <?= $d->CUSTOMER_NAME ?>
                </td>

                <td class="right">
                    <?= number_format(
                        $d->JUMLAH,
                        2,
                        ',',
                        '.'
                    ) ?>
                </td>

                <td class="right">
                    <?= number_format(
                        $d->BERAT,
                        2,
                        ',',
                        '.'
                    ) ?>
                </td>

                <td class="right">
                    <?= number_format(
                        $d->HARGA,
                        0,
                        ',',
                        '.'
                    ) ?>
                </td>

                <td class="right">
                    <?= number_format(
                        $d->TOTAL,
                        0,
                        ',',
                        '.'
                    ) ?>
                </td>

            </tr>

            <?php endforeach; ?>

            <tr class="subtotal">

                <td colspan="2">
                    TOTAL CUSTOMER
                </td>

                <td class="right">

                    <?= number_format(
                        $subtotal['qty'],
                        2,
                        ',',
                        '.'
                    ) ?>

                </td>

                <td class="right">

                    <?= number_format(
                        $subtotal['weight'],
                        2,
                        ',',
                        '.'
                    ) ?>

                </td>

                <td></td>

                <td class="right">

                    <?= number_format(
                        $subtotal['total'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </td>

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
        : '-' ?>

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