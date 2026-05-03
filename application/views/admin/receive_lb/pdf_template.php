<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receive Live Bird</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            width: 90px;
        }

        .po-title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        .info-table td {
            padding: 3px 5px;
        }

        .info-label {
            width: 90px;
            font-weight: bold;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .detail-table th {
            background-color: #eee;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-box {
            margin-top: 10px;
            width: 40%;
            float: right;
        }

        .total-box td {
            padding: 4px;
        }

        .remark {
            margin-top: 30px;
            clear: both;
        }
    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
<table class="header-table">
    <tr>
        <td>
            <!-- GANTI LOGO SENDIRI -->
            <img src="https://apja.co.id/assets/img/apja-logo.png" class="logo" style="width: 190px; margin-bottom: 20px">
        </td>
        <td class="po-title" style="vertical-align: middle">
            RECEIVE LB
        </td>
    </tr>
</table>

<hr>

<!-- ================= INFO RECEIVE ================= -->
<table class="info-table" style="margin-top:15px">
    <tr>
        <td class="info-label">Plant</td>
        <td colspan="3">: <?= $header->PLANT_NAME; ?> - PT. ARTHA PRATAMA JAYA ABADI</td>
        <td class="info-label">Supplier</td>
        <td>: <?= $header->SUPPLIER ?: '-'; ?> - <?= $header->SUPPLIER_NAME; ?></td>
        
    </tr>
    <tr>
        <td class="info-label">Receive No</td>
        <td colspan="3">: #<?= $header->RECEIVE; ?></td>
        <td class="info-label">Slip / Nota</td>
        <td>: #<?= $header->SLIP_NO ?: '-'; ?></td>
    </tr>
    <tr>
        <td class="info-label">Receive Date</td>
        <td colspan="3">: <?= date('d-M-Y', strtotime($header->RECEIVE_DATE)); ?></td>
        <td class="info-label">Driver</td>
        <td>: <?= $header->DRIVER; ?></td>
    </tr>
    <tr>
        <td class="info-label">DO</td>
        <td colspan="3">: #<?= $header->DO; ?></td>
        <td class="info-label">No. Mobil</td>
        <td>: <?= $header->NO_CAR; ?></td>
    </tr>
    <tr>
        <td class="info-label">Pembayaran / Jenis</td>
        <td colspan="3">: <?= $header->PEMBAYARAN; ?> / <?= $header->JENIS_PAY; ?></td>
        <!-- <td class="info-label">Status</td>
        <td>: <?= $header->STATUS ?: '-'; ?></td> -->
    </tr>
</table>

<br>

<!-- ================= DETAIL TIMBANG ================= -->
<table class="detail-table" style="margin-bottom: 20px">
    <thead>

        <tr>
            <td class="info-label">Qty (Ekor)</td>
            <td colspan="3">: <?= number_format($header->QTY, 2, ',', '.'); ?></td>
            <td class="info-label">Avg. BW</td>
            <td>: <?= number_format($header->AVG_BW, 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="info-label">Total BW (Kg)</td>
            <td colspan="3">: <?= number_format($header->WEIGHT, 2, ',', '.'); ?></td>
            <td class="info-label">Harga</td>
            <td>: Rp <?= number_format($header->PRICE, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td colspan="4" class="info-label"></td>
            <td class="info-label">Total Harga</td>
            <td>: <b>Rp <?= number_format($header->AMOUNT, 0, ',', '.'); ?></b></td>
        </tr>
    </thead>
</table>

<table class="detail-table" style="margin-bottom: 20px">
    <thead>
        <tr>
            <td class="info-label">Mati (Ekor)</td>
            <td colspan="4">: <?= number_format($header->DEAD, 2, ',', '.'); ?></td>
            <td colspan="3" class="info-label">Mati (Kg)</td>
            <td>: <?= number_format($header->DEAD_WEIGHT, 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="info-label">Susut (Kg)</td>
            <td colspan="4">: <?= number_format($header->SHRINK, 2, ',', '.'); ?></td>
            <td colspan="3" class="info-label">Total Terima (Kg)</td>
            <td>: <b><?= number_format($header->RECEIVE_AMOUNT, 2, ',', '.'); ?></b></td>
        </tr>
    </thead>
</table>

<!-- ================= REMARK ================= -->
<div class="remark">
    <b>Remark:</b><br>
    <?= $header->REMARK ?: '-'; ?>
</div>

<?php if (!empty($header->ATTACHMENT_NAME)) : ?>
    <div class="remark" style="margin-top:25px">
        <b>Attachment:</b><br><br>

        <?php
            $ext = strtolower(pathinfo($header->ATTACHMENT_NAME, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg','jpeg','png']);
        ?>

        <?php if ($isImage && !empty($attachment_url)) : ?>
            <!-- IMAGE PREVIEW -->
            <img
                src="<?= $attachment_url ?>"
                style="
                    width: 100%;
                    border:1px solid #000;
                    padding:5px;
                "
            >
        <?php else : ?>
            <!-- FILE INFO -->
            <div style="font-size:11px">
                📎 <?= $header->ATTACHMENT_NAME ?><br>
                <i>(File terlampir, silakan unduh dari sistem)</i>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>