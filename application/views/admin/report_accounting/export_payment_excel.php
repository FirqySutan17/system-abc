<table border="1">

    <thead>

        <tr
            style="
                background:#d9d9d9;
                font-weight:bold;
            ">

            <th>
                Payment
            </th>

            <th>
                Date
            </th>

            <th>
                Supplier
            </th>

            <th>
                PO
            </th>

            <th>
                Material
            </th>

            <th>
                Qty
            </th>

            <th>
                Berat
            </th>

            <th>
                Harga
            </th>

            <th>
                Total
            </th>

        </tr>

    </thead>

    <tbody>

        <?php foreach($rows as $r): ?>

            <tr>

                <td>

                    <?= $r['PAYMENT']; ?>

                </td>

                <td>

                    <?= date(
                        'd-M-Y',
                        strtotime(
                            $r['PAYMENT_DATE']
                        )
                    ); ?>

                </td>

                <td>

                    <?= $r['SUPPLIER_NAME']; ?>

                </td>

                <td>

                    <?= $r['PO_NO']; ?>

                </td>

                <td>

                    <?= $r['MATERIAL_NAME']; ?>

                </td>

                <td>

                    <?= number_format(
                        $r['JUMLAH'],
                        2,
                        ',',
                        '.'
                    ); ?>

                </td>

                <td>

                    <?= number_format(
                        $r['BERAT'],
                        2,
                        ',',
                        '.'
                    ); ?>

                </td>

                <td>

                    <?= number_format(
                        $r['HARGA'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

                <td>

                    <?= number_format(
                        $r['TOTAL'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>