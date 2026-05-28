<table border="1">

    <thead>

        <tr style="background:#d9edf7">

            <th>Cash In</th>

            <th>Date</th>

            <th>Customer</th>

            <th>Payment</th>

            <th>Sales</th>

            <th>Invoice</th>

            <th>Bayar</th>

            <th>Sisa</th>

            <th>Status</th>

            <th>Remark</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach($rows as $row): ?>

            <?php foreach($row['DETAILS'] as $d): ?>

                <tr>

                    <td>
                        <?= $row['CASH_IN']; ?>
                    </td>

                    <td>
                        <?= $row['CASHIN_DATE']; ?>
                    </td>

                    <td>
                        <?= $row['CUSTOMER_NAME']; ?>
                    </td>

                    <td>
                        <?= $row['PEMBAYARAN']; ?>
                    </td>

                    <td>
                        <?= $d['SALES']; ?>
                    </td>

                    <td>
                        <?= number_format($d['AMOUNT_INVOICE'],0,',','.'); ?>
                    </td>

                    <td>
                        <?= number_format($d['AMOUNT_OFFSET'],0,',','.'); ?>
                    </td>

                    <td>
                        <?= number_format($d['REMAINING'],0,',','.'); ?>
                    </td>

                    <td>
                        <?= $d['SALES_STATUS']; ?>
                    </td>

                    <td>
                        <?= $d['REMARK']; ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </tbody>

</table>