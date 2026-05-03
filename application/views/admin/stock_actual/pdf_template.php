<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Actual</title>

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

        .title {
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
            <img src="https://apja.co.id/assets/img/apja-logo.png"
                 style="width: 190px; margin-bottom: 20px">
        </td>
        <td class="title" style="vertical-align: middle">
            STOCK ACTUAL
        </td>
    </tr>
</table>

<hr>

<!-- ================= INFO ================= -->
<table class="info-table" style="margin-top: 15px">
    <tr>
        <td class="info-label">Plant</td>
        <td colspan="3">
            : <?= $header->PLANT_NAME; ?> - PT . ARTHA PRATAMA JAYA ABADI
        </td>
        <td class="info-label">Date</td>
        <td>: <?= date('d-M-Y', strtotime($header->SA_DATE)); ?></td>
    </tr>
    <tr>
        <td class="info-label">PIC</td>
        <td colspan="3">: <?= $header->PIC ?: '-'; ?></td>
        <td class="info-label">Stock Actual</td>
        <td>: #<?= $header->STOCK_ACTUAL; ?></td>
    </tr>
</table>

<br>

<!-- ================= DETAIL ================= -->
<table class="detail-table">
    <thead>
        <tr>
            <th width="18%">Item</th>
            <th width="8%">Stock Qty</th>
            <th width="8%">Stock BW</th>
            <th width="8%">Actual Qty</th>
            <th width="8%">Actual BW</th>
            <th width="8%">Margin Qty</th>
            <th width="8%">Margin BW</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detail as $d):

            // ===== LOGIC SAMA PERSIS DENGAN EDIT =====
            $stockQty = (float) $d->STOCK_QTY;
            $stockBw  = (float) $d->STOCK_BW;

            $actualQty = (float) $d->ACTUAL_QTY;
            $actualBw  = (float) $d->ACTUAL_BERAT;

            if ($actualQty == 0) {
                $actualQty = $stockQty;
            }

            if ($actualBw == 0) {
                $actualBw = $stockBw;
            }

            $marginQty = $actualQty - $stockQty;
            $marginBw  = $actualBw - $stockBw;
        ?>
        <tr>
            <td>
                (<?= $d->ITEM; ?>) <?= $d->FULL_NAME; ?>
            </td>
            <td class="text-right"><?= number_format($stockQty, 2, ',', '.'); ?></td>
            <td class="text-right"><?= number_format($stockBw, 2, ',', '.'); ?></td>
            <td class="text-right"><?= number_format($actualQty, 2, ',', '.'); ?></td>
            <td class="text-right"><?= number_format($actualBw, 2, ',', '.'); ?></td>
            <td class="text-right"><?= number_format($marginQty, 2, ',', '.'); ?></td>
            <td class="text-right"><?= number_format($marginBw, 2, ',', '.'); ?></td>
            <td><?= $d->REMARK ?: '-'; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- ================= REMARK ================= -->
<div class="remark">
    <b>Remark:</b><br>
    <?= $header->REMARK ?: '-'; ?>
</div>

</body>
</html>