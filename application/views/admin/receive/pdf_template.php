<!-- application/views/admin/receive/pdf_template.php -->

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
        width:95px;
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
                <div class="title">PT. Abadi Bersama Cerah</div>
                <div class="subtitle">RECEIVE SLIP</div>
            </td>
        </tr>
    </table>
</div>

<div class="card">

    <div class="card-head">
        #<?= $header->RECEIVE ?>
    </div>

    <div class="meta">
        <table class="meta-table">

            <tr>
                <td class="label">PLANT</td>
                <td class="sep">:</td>
                <td><?= $header->PLANT_NAME ?></td>

                <td class="gap"></td>

                <td class="label">SUPPLIER</td>
                <td class="sep">:</td>
                <td><?= $header->SUPPLIER ?> - <?= $header->SUPPLIER_NAME ?></td>
            </tr>

            <tr>
                <td class="label">RECEIVE DATE</td>
                <td class="sep">:</td>
                <td><?= strtoupper(date('d F Y', strtotime($header->RECEIVE_DATE))) ?></td>

                <td class="gap"></td>

                <td class="label">PO</td>
                <td class="sep">:</td>
                <td><?= $header->PO_TEXT ?></td>
            </tr>

            <tr>
                <td class="label">NOTA</td>
                <td class="sep">:</td>
                <td><?= $header->NOTA ?: '-' ?></td>

                <td class="gap"></td>

                <td class="label">REF NO</td>
                <td class="sep">:</td>
                <td><?= $header->NO_REF ?: '-' ?></td>
            </tr>

            <tr>
                <td class="label">PAYMENT</td>
                <td class="sep">:</td>
                <td><?= $header->PEMBAYARAN ?: '-' ?></td>

                <td class="gap"></td>

                <td class="label">PAY TYPE</td>
                <td class="sep">:</td>
                <td><?= $header->JENIS_PAY ?: '-' ?></td>
            </tr>

            <tr>
                <td class="label">SLIP NO</td>
                <td class="sep">:</td>
                <td><?= $header->SLIP_NO ?: '-' ?></td>

                <td class="gap"></td>

                <td class="label">ATTACHMENT</td>
                <td class="sep">:</td>
                <td><?= $header->ATTACH_FILE_NAME ? 'Available' : '-' ?></td>
            </tr>

            <tr>
                <td class="label">STATUS</td>
                <td class="sep">:</td>
                <td colspan="5"><?= $header->STATUS_TEXT ?></td>
            </tr>

        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th>MATERIAL</th>
                <th width="10%">QTY</th>
                <th width="10%">WEIGHT</th>
                <th width="10%">SHRINK QTY</th>
                <th width="10%">SHRINK WEIGHT</th>
                <th width="12%">PRICE</th>
                <th width="12%">TOTAL</th>
                <th width="11%">REMARK</th>
                <th width="10%">STATUS</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach($detail as $i => $d): ?>
            <tr>
                <td class="center"><?= $i+1 ?></td>
                <td><?= $d->MATERIAL_NAME ?></td>
                <td class="right"><?= number_format($d->JUMLAH,2,',','.') ?></td>
                <td class="right"><?= number_format($d->BERAT,2,',','.') ?></td>
                <td class="right"><?= number_format($d->SUSUT_JUMLAH,2,',','.') ?></td>
                <td class="right"><?= number_format($d->SUSUT_BERAT,2,',','.') ?></td>
                <td class="right"><?= number_format($d->HARGA,0,',','.') ?></td>
                <td class="right"><?= number_format($d->TOTAL,0,',','.') ?></td>
                <td><?= $d->KETERANGAN ?: '-' ?></td>
                <td class="center"><?= $d->STATUS ?: '-' ?></td>
            </tr>
            <?php endforeach; ?>

            <tr class="subtotal">
                <td colspan="2">TOTAL</td>
                <td class="right"><?= number_format($summary['qty'],2,',','.') ?></td>
                <td class="right"><?= number_format($summary['weight'],2,',','.') ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right"><?= number_format($summary['total'],0,',','.') ?></td>
                <td></td>
                <td></td>
            </tr>

        </tbody>
    </table>

</div>

<div class="remark-box">
    <div class="remark-title">Remark</div>
    <?= !empty($header->REMARK) ? nl2br($header->REMARK) : '-' ?>
</div>

<table class="sign">
    <tr>
        <td>Prepared By</td>
        <td>Approved By</td>
    </tr>
    <tr>
        <td class="sign-line">(_____________________)</td>
        <td class="sign-line">(_____________________)</td>
    </tr>
</table>