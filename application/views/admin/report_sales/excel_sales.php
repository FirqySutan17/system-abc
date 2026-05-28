<table border="1">

    <thead>

        <tr style="background:#ddd; font-weight:bold;">

            <th>SALES</th>
            <th>DATE</th>
            <th>CUSTOMER</th>
            <th>PAYMENT</th>
            <th>STATUS</th>
            <th>MATERIAL</th>
            <th>QTY</th>
            <th>BERAT</th>
            <th>HARGA</th>
            <th>DISCOUNT</th>
            <th>TOTAL</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach($rows as $row): ?>

            <?php foreach($row['DETAILS'] as $d): ?>

                <tr>

                    <td>
                        <?= $row['SALES']; ?>
                    </td>

                    <td>
                        <?= $row['SALES_DATE']; ?>
                    </td>

                    <td>
                        <?= $row['CUSTOMER_NAME']; ?>
                    </td>

                    <td>
                        <?= $row['PEMBAYARAN']; ?>
                    </td>

                    <td>
                        <?= $row['STATUS']; ?>
                    </td>

                    <td>
                        <?= $d['MATERIAL_NAME']; ?>
                    </td>

                    <td>
                        <?= number_format($d['QTY'],0); ?>
                    </td>

                    <td>
                        <?= number_format($d['BERAT'],2); ?>
                    </td>

                    <td>
                        <?= number_format($d['HARGA'],0); ?>
                    </td>

                    <td>
                        <?= number_format($d['DISCOUNT'],0); ?>
                    </td>

                    <td>
                        <?= number_format($d['TOTAL'],0); ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </tbody>

</table>