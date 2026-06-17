<?php
$logo =
    FCPATH .
    'assets/img/abc-trans.png';

$logo64 =
    file_exists($logo)
        ? 'data:image/png;base64,' .
            base64_encode(
                file_get_contents(
                    $logo
                )
            )
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
        padding:5px 0;
    }

    .label{
        width:100px;
        font-weight:bold;
    }

    .sep{
        width:15px;
    }

    .summary-box{
        padding:15px 15px 0px 15px;
    }

    .summary-table{
        width:100%;
    }

    .summary-table td{
        text-align:center;
        border:none;
    }

    .summary-value{
        font-size:24px;
        color:#0F4C81;
        font-weight:bold;
    }

    .summary-label{
        font-size:11px;
        color:#666;
        margin-top:4px;
    }

    .remark-box{
        margin-top:20px;
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
        margin-top:60px;
        width:100%;
    }

    .sign td{
        width:50%;
        text-align:center;
        border:none;
    }

    .sign-line{
        margin-top:60px;
        font-weight:bold;
    }

</style>

<div class="header">

<table width="100%">

<tr>

<td width="70" style="border:none">

<?php if($logo64): ?>

<img src="<?= $logo64 ?>" height="60">

<?php endif; ?>

</td>

<td style="border:none">

<div class="title">
PT. Abadi Bersama Cerah
</div>

<div class="subtitle">
CULLING SLIP
</div>

</td>

</tr>

</table>

</div>

<div class="card">

<div class="card-head">

# CULLING ID :
<?= $header->idno ?>

</div>

<div class="meta">

<table class="meta-table">

<tr>

<td class="label">PLANT</td>
<td class="sep">:</td>
<td>
<?= $header->PLANT_NAME ?: '-' ?>
</td>

</tr>

<tr>

<td class="label">DATE</td>
<td class="sep">:</td>
<td>
<?= strtoupper(
    date(
        'd F Y',
        strtotime(
            $header->ymd
        )
    )
) ?>
</td>

</tr>

<tr>

<td class="label">CLASS OUT</td>
<td class="sep">:</td>
<td>
<?= $header->CLASS_OUT_NAME ?: '-' ?>
</td>

</tr>

<tr>

<td class="label">CREATED BY</td>
<td class="sep">:</td>
<td>
<?= $header->CREATED_BY ?: '-' ?>
</td>

</tr>

<tr>

<td class="label">CREATED AT</td>
<td class="sep">:</td>
<td>

<?= !empty($header->CREATED_AT)
        ? date(
            'd F Y H:i',
            strtotime(
                $header->CREATED_AT
            )
        )
        : '-'
?>

</td>

</tr>

</table>

</div>

<div class="summary-box">

<table class="summary-table">

<tr>

<td>

<div class="summary-value">

<?= number_format(
    $summary['jumlah'],
    2,
    ',',
    '.'
) ?>

</div>

<div class="summary-label">
Jumlah
</div>

</td>

<td>

<div class="summary-value">

<?= number_format(
    $summary['berat'],
    2,
    ',',
    '.'
) ?>

</div>

<div class="summary-label">
Berat
</div>

</td>

</tr>

</table>

</div>

<div class="remark-box">

<div class="remark-title">
Remark
</div>

<?= !empty($header->remark)
        ? nl2br(
            $header->remark
        )
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

</div>