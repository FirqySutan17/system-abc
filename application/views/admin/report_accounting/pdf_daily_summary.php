<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Accounting Summary</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h3, h4 {
            margin: 5px 0;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 5px;
        }
        .section-title {
            background: #f0f0f0;
            font-weight: bold;
        }
        .grand-total {
            background: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h3 class="text-center">PERINCIAN SETORAN HARIAN</h3>

<p>
Plant : <strong><?= $plant ?></strong><br>
Date  : <strong><?= date('d-m-Y', strtotime($date)) ?></strong>
</p>

<?php
$salesCash    = $summary->SALES_CASH ?? 0;
$salesTempo   = $summary->SALES_TEMPO ?? 0;
$arCollection = $summary->AR_COLLECTION ?? 0;
$costToday    = $summary->COST_TODAY ?? 0;
$depositToday = $summary->DEPOSIT_TODAY ?? 0;

$penjualan = $salesCash + $salesTempo;
$total     = $arCollection + $penjualan;
$saldo     = $total - ($costToday + $salesTempo);
$setoran   = $saldo - $depositToday;
?>

<table>
    <tbody>
        <tr>
            <td>Tagihan (Cash In)</td>
            <td class="text-end"><?= number_format($arCollection,0,',','.') ?></td>
        </tr>
        <tr>
            <td>Penjualan (Cash + Tempo)</td>
            <td class="text-end"><?= number_format($penjualan,0,',','.') ?></td>
        </tr>
        <tr class="section-title">
            <td>Total (Kas Masuk)</td>
            <td class="text-end"><?= number_format($total,0,',','.') ?></td>
        </tr>
        <tr>
            <td>Biaya</td>
            <td class="text-end">(<?= number_format($costToday,0,',','.') ?>)</td>
        </tr>
        <tr>
            <td>Piutang Hari Ini</td>
            <td class="text-end"><?= number_format($salesTempo,0,',','.') ?></td>
        </tr>
        <tr class="fw-bold">
            <td>Saldo</td>
            <td class="text-end"><?= number_format($saldo,0,',','.') ?></td>
        </tr>
        <tr>
            <td>Deposit</td>
            <td class="text-end"><?= number_format($depositToday,0,',','.') ?></td>
        </tr>
        <tr class="section-title">
            <td>Setoran</td>
            <td class="text-end"><?= number_format($setoran,0,',','.') ?></td>
        </tr>
    </tbody>
</table>

<hr>

<h4>RINCIAN METODE PEMASUKAN</h4>

<table width="100%" border="1" cellpadding="5" cellspacing="0">
    <tr>
        <td>Sales (Cash)</td>
        <td align="right"><?= number_format($summary->SALES_METHOD_CASH ?? 0,2) ?></td>
    </tr>
    <tr>
        <td>Sales (Transfer)</td>
        <td align="right"><?= number_format($summary->SALES_METHOD_TRANSFER ?? 0,2) ?></td>
    </tr>
    <tr>
        <td>Cash In (Cash)</td>
        <td align="right"><?= number_format($summary->CASHIN_METHOD_CASH ?? 0,2) ?></td>
    </tr>
    <tr>
        <td>Cash In (Transfer)</td>
        <td align="right"><?= number_format($summary->CASHIN_METHOD_TRANSFER ?? 0,2) ?></td>
    </tr>
    <tr>
        <td><b>Total Cash</b></td>
        <td align="right"><b><?= number_format($summary->TOTAL_METHOD_CASH ?? 0,2) ?></b></td>
    </tr>
    <tr>
        <td><b>Total Transfer</b></td>
        <td align="right"><b><?= number_format($summary->TOTAL_METHOD_TRANSFER ?? 0,2) ?></b></td>
    </tr>
</table>

<!-- ================= SALES ================= -->

<h4>A. SALES TODAY</h4>

<table>
    <thead>
        <tr>
            <th>Sales</th>
            <th>Customer</th>
            <th>Jenis</th>
            <th>Item</th>
            <th>Qty</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($sales)): ?>
            <tr>
                <td colspan="6" class="text-center">Tidak ada data Sales hari ini</td>
            </tr>
        <?php else: ?>
            <?php foreach($sales as $s): ?>
                <tr>
                    <td>#<?= $s->SALES ?></td>
                    <td><?= $s->CUSTOMER_NAME ?></td>
                    <td><?= $s->JENIS_PAY ?></td>
                    <td><?= $s->ITEM_NAME ?></td>
                    <td><?= number_format($s->DISPLAY_QTY,2,',','.') ?></td>
                    <td class="text-end"><?= number_format($s->DETAIL_AMOUNT,0,',','.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- ================= CASH ================= -->

<h4>B. CASH IN TODAY</h4>

<table>
    <thead>
        <tr>
            <th>Cash In</th>
            <th>Customer</th>
            <th>Invoice</th>
            <th class="text-end">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($cash)): ?>
            <tr>
                <td colspan="4" class="text-center">Tidak ada data Cash In hari ini</td>
            </tr>
        <?php else: ?>
            <?php foreach($cash as $c): ?>
                <tr>
                    <td>#<?= $c->CASH_IN ?></td>
                    <td><?= $c->CUSTOMER_NAME ?></td>
                    <td>#<?= $c->SALES ?></td>
                    <td class="text-end"><?= number_format($c->AMOUNT_OFFSET,0,',','.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- ================= COST ================= -->

<h4>C. COST TODAY</h4>

<table>
    <thead>
        <tr>
            <th>Cost</th>
            <th>Tipe</th>
            <th>Remark</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($cost)): ?>
            <tr>
                <td colspan="4" class="text-center">Tidak ada data Cost hari ini</td>
            </tr>
        <?php else: ?>
            <?php foreach($cost as $c): ?>
                <tr>
                    <td>#<?= $c->COST ?></td>
                    <td><?= $c->COST_NAME ?></td>
                    <td><?= $c->REMARK ?></td>
                    <td class="text-end"><?= number_format($c->TOTAL,0,',','.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>