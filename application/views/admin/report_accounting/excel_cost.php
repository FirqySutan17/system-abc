<table border="1">

    <thead>

        <tr style="background:#f2f2f2">

            <th>COST</th>

            <th>PLANT</th>

            <th>PAYMENT</th>

            <th>SLIP NO</th>

            <th>COST DATE</th>

            <th>TIPE COST</th>

            <th>KUANTITAS</th>

            <th>HARGA</th>

            <th>TOTAL</th>

        </tr>

    </thead>


    <tbody>

        <?php foreach($rows as $row): ?>

            <?php

            $details =
                $row['DETAIL']
                ?? [];

            ?>


            <?php if(empty($details)): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $row['COST'] ?? ''
                        ); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $row['PLANT_NAME'] ?? ''
                        ); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $row['PEMBAYARAN'] ?? ''
                        ); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $row['SLIP_NO'] ?? ''
                        ); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $row['COST_DATE'] ?? ''
                        ); ?>
                    </td>

                    <td>-</td>

                    <td style="text-align:right;">
                        0
                    </td>

                    <td style="text-align:right;">
                        0
                    </td>

                    <td style="text-align:right;">
                        0
                    </td>

                </tr>


            <?php else: ?>


                <?php foreach($details as $detail): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                $row['COST'] ?? ''
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['PLANT_NAME'] ?? ''
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['PEMBAYARAN'] ?? ''
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['SLIP_NO'] ?? ''
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['COST_DATE'] ?? ''
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $detail['COST_NAME']
                                ?? $detail['TIPE_COST']
                                ?? ''
                            ); ?>
                        </td>

                        <td style="text-align:right;">

                            <?= number_format(
                                (float) (
                                    $detail['TOTAL_QTY']
                                    ?? 0
                                ),
                                2,
                                ',',
                                '.'
                            ); ?>

                        </td>

                        <td style="text-align:right;">

                            <?= number_format(
                                (float) (
                                    $detail['TOTAL_JUMLAH']
                                    ?? 0
                                ),
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>

                        <td style="text-align:right;">

                            <?= number_format(
                                (float) (
                                    $detail['TOTAL']
                                    ?? 0
                                ),
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


            <?php endif; ?>

        <?php endforeach; ?>

    </tbody>

</table>