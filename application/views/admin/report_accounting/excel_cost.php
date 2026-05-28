<table border="1">

    <thead>

        <tr style="background:#f2f2f2">

            <th>COST</th>

            <th>DATE</th>

            <th>PLANT</th>

            <th>SUPPLIER</th>

            <th>PAYMENT</th>

            <th>PO</th>

            <th>MATERIAL</th>

            <th>QTY</th>

            <th>BERAT</th>

            <th>HARGA</th>

            <th>TOTAL</th>

            <th>REMARK</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach($rows as $row): ?>

            <?php foreach($row['DETAILS'] as $d): ?>

                <tr>

                    <td>
                        <?= $row['COST']; ?>
                    </td>

                    <td>
                        <?= $row['COST_DATE']; ?>
                    </td>

                    <td>
                        <?= $row['PLANT_NAME']; ?>
                    </td>

                    <td>
                        <?= $row['SUPPLIER_NAME']; ?>
                    </td>

                    <td>
                        <?= $row['PEMBAYARAN']; ?>
                    </td>

                    <td>
                        <?= $d['PO']; ?>
                    </td>

                    <td>
                        <?= $d['MATERIAL_NAME']; ?>
                    </td>

                    <td>
                        <?= number_format($d['QTY'],2,',','.'); ?>
                    </td>

                    <td>
                        <?= number_format($d['BERAT'],2,',','.'); ?>
                    </td>

                    <td>
                        <?= number_format($d['HARGA'],0,',','.'); ?>
                    </td>

                    <td>
                        <?= number_format($d['TOTAL'],0,',','.'); ?>
                    </td>

                    <td>
                        <?= $d['REMARK']; ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </tbody>

</table>