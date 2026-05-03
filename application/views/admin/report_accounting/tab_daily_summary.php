<?php $userPlant = $this->session->userdata('plant'); ?>

<div class="row mb-3">

    <div class="col-md-3">
        <label class="form-label">Plant</label>
        <select id="ds_filter_plant" class="form-control"
            <?= ($this->session->userdata('role_id') != 1) ? 'disabled' : '' ?>>
            <?php foreach($plants as $p): ?>
                <option value="<?= $p->CODE ?>" 
                    <?= ($p->CODE==$userPlant)?'selected':'' ?>>
                    <?= $p->CODE_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Date</label>
        <input type="date" id="ds_filter_date" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="ds_btnFilter">
            <i class="fa fa-search"></i> Load
        </button>
    </div>

    <div class="col-md-3"></div>
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li>
                    <a class="dropdown-item" href="#" id="ds_export_excel">
                        Export Excel
                    </a>
                </li>
                <!-- <li>
                    <a class="dropdown-item" href="#" id="rp_exportPDF_ar">
                        Export PDF
                    </a>
                </li> -->
            </ul>
        </div>
    </div>

</div>

<div id="ds_loading" class="text-center my-4 d-none">
    <div class="spinner-border text-primary"></div>
    <div class="mt-2 text-muted">Loading data...</div>
</div>

<div id="ds_summary_area" class="mb-4"></div>
<div id="ds_sales_area" class="mb-4"></div>
<div id="ds_cash_area" class="mb-4"></div>
<div id="ds_cost_area" class="mb-4"></div>

<script>
    window.DailySummary = {

        loaded: false,

        init() {
            if (this.loaded) return;
            this.loaded = true;

            const today = new Date();
            $('#ds_filter_date').val(today.toISOString().slice(0,10));
            $('#ds_filter_plant').select2({ width: '100%' });

            $('#ds_btnFilter').on('click', () => {
                this.load();
            });

            $('#ds_export_excel').on('click', e => {
                e.preventDefault();
                window.open(
                    '<?= base_url("report-accounting/export_daily_excel"); ?>?' +
                    $.param(this.params())
                );
            });

            $('#ds_export_pdf').on('click', e => {
                e.preventDefault();
                window.open(
                    '<?= base_url("report-accounting/export_daily_pdf"); ?>?' +
                    $.param(this.params())
                );
            });

            this.load();
        },

        params() {
            return {
                plant : $('#ds_filter_plant').val(),
                date  : $('#ds_filter_date').val()
            };
        },

        load() {

            $('#ds_loading').removeClass('d-none');

            $('#ds_summary_area').html('');
            $('#ds_sales_area').html('');
            $('#ds_cash_area').html('');
            $('#ds_cost_area').html('');

            $.get('<?= base_url("report-accounting/load_daily_summary"); ?>',
                this.params(),
                (resp) => {

                    $('#ds_loading').addClass('d-none');

                    if (resp.error) {
                        alert(resp.error);
                        return;
                    }

                    this.renderSummary(resp.summary);
                    this.renderSales(resp.sales);
                    this.renderCash(resp.cash);
                    this.renderCost(resp.cost);

                }, 'json');
        },

        /* =======================
        SUMMARY
        ======================== */
        renderSummary(data) {

            if (!data) return;

            const plantName = $('#ds_filter_plant option:selected').text();
            const reportDate = new Date($('#ds_filter_date').val())
                .toLocaleDateString('id-ID');

            const salesCash     = parseFloat(data.SALES_CASH || 0);
            const salesTempo    = parseFloat(data.SALES_TEMPO || 0);
            const arCollection  = parseFloat(data.AR_COLLECTION || 0);
            const costToday     = parseFloat(data.COST_TODAY || 0);
            const depositToday  = parseFloat(data.DEPOSIT_TODAY || 0);

            const penjualan = salesCash + salesTempo;
            const total     = arCollection + penjualan;
            const saldo     = total - (costToday + salesTempo);
            const setoran   = saldo - depositToday;

            const salesMethodCash      = parseFloat(data.SALES_METHOD_CASH || 0);
            const salesMethodTransfer  = parseFloat(data.SALES_METHOD_TRANSFER || 0);
            const cashinMethodCash     = parseFloat(data.CASHIN_METHOD_CASH || 0);
            const cashinMethodTransfer = parseFloat(data.CASHIN_METHOD_TRANSFER || 0);

            const totalMethodCash      = parseFloat(data.TOTAL_METHOD_CASH || 0);
            const totalMethodTransfer  = parseFloat(data.TOTAL_METHOD_TRANSFER || 0);

            let html = `
            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <div class="text-center mb-3">
                        <h5 class="fw-bold text-primary mb-1">
                            PERINCIAN SETORAN HARIAN
                        </h5>
                        <div class="text-muted">
                            Plant : <b>${plantName}</b><br>
                            Date  : <b>${reportDate}</b>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered mt-3">
                        <tbody>
                            <tr>
                                <td>Tagihan (Cash In)</td>
                                <td class="text-end">${this.rp(arCollection)}</td>
                            </tr>
                            <tr>
                                <td>Penjualan (Cash + Tempo)</td>
                                <td class="text-end">${this.rp(penjualan)}</td>
                            </tr>
                            <tr class="fw-bold table-light">
                                <td>Total (Kas Masuk)</td>
                                <td class="text-end">${this.rp(total)}</td>
                            </tr>
                            <tr>
                                <td>Biaya</td>
                                <td class="text-end text-danger">(${this.rp(costToday)})</td>
                            </tr>
                            <tr>
                                <td>Piutang Hari Ini (Sales Tempo)</td>
                                <td class="text-end">${this.rp(salesTempo)}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Saldo</td>
                                <td class="text-end ${saldo < 0 ? 'text-danger' : ''}">
                                    ${this.rp(saldo)}
                                </td>
                            </tr>
                            <tr>
                                <td>Lain-lain (Deposit)</td>
                                <td class="text-end">${this.rp(depositToday)}</td>
                            </tr>
                            <tr class="fw-bold table-success">
                                <td>Setoran</td>
                                <td class="text-end ${setoran < 0 ? 'text-danger' : ''}">
                                    ${this.rp(setoran)}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>

                    <h6 class="fw-bold text-secondary mt-3">RINCIAN METODE PEMASUKAN</h6>

                    <table class="table table-sm table-bordered mt-2">
                        <tbody>
                            <tr>
                                <td>Sales (Cash)</td>
                                <td class="text-end">${this.rp(salesMethodCash)}</td>
                            </tr>
                            <tr>
                                <td>Sales (Transfer)</td>
                                <td class="text-end">${this.rp(salesMethodTransfer)}</td>
                            </tr>
                            <tr>
                                <td>Cash In (Cash)</td>
                                <td class="text-end">${this.rp(cashinMethodCash)}</td>
                            </tr>
                            <tr>
                                <td>Cash In (Transfer)</td>
                                <td class="text-end">${this.rp(cashinMethodTransfer)}</td>
                            </tr>
                            <tr class="fw-bold table-light">
                                <td>Total Cash</td>
                                <td class="text-end">${this.rp(totalMethodCash)}</td>
                            </tr>
                            <tr class="fw-bold table-light">
                                <td>Total Transfer</td>
                                <td class="text-end">${this.rp(totalMethodTransfer)}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            `;

            $('#ds_summary_area').html(html);
        },

        /* =======================
        SALES
        ======================== */
        renderSales(rows) {

            let html = `
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold text-primary mb-3">A. SALES TODAY</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="40" style="text-align: center; vertical-align: middle"></th>
                                    <th style="text-align: center; vertical-align: middle">Sales</th>
                                    <th style="text-align: center; vertical-align: middle">Customer</th>
                                    <th style="text-align: center; vertical-align: middle">Jenis</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            if (!rows || rows.length === 0) {

                html += `
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Tidak ada data Sales hari ini
                        </td>
                    </tr>
                `;

                html += `</tbody></table></div></div></div>`;
                $('#ds_sales_area').html(html);
                return;
            }

            let grouped = {};
            rows.forEach(r => {
                if (!grouped[r.SALES]) grouped[r.SALES] = [];
                grouped[r.SALES].push(r);
            });

            let grandTotal = 0;

            Object.keys(grouped).forEach(key => {

                let header = grouped[key][0];
                let subtotal = parseFloat(header.SALES_TOTAL);
                grandTotal += subtotal;

                html += `
                    <tr class="table-primary">
                        <td class="text-center" style="vertical-align: middle">
                            <button class="btn btn-sm btn-light ds-toggle" data-target="sales-${key}">+</button>
                        </td>
                        <td style="text-align: center; vertical-align: middle"><b>#${header.SALES}</b></td>
                        <td style="text-align: center; vertical-align: middle">${header.CUSTOMER_NAME} <br> <b>${header.CUSTOMER}</b></td>
                        <td style="text-align: center; vertical-align: middle">${header.JENIS_PAY}</td>
                        <td class="text-end fw-bold" style="vertical-align: middle">${this.rp(subtotal)}</td>
                    </tr>
                `;

                grouped[key].forEach(d => {
                    html += `
                        <tr class="d-none detail-row sales-${key}">
                            <td></td>
                            <td colspan="2"><b>${d.ITEM} - ${d.ITEM_NAME}</b></td>
                            <td class="text-center"><b>${d.DISPLAY_TYPE}: ${parseFloat(d.DISPLAY_QTY).toLocaleString('id-ID')}</b></td>
                            <td class="text-end"><b>${this.rp(d.DETAIL_AMOUNT)}</b></td>
                        </tr>
                    `;
                });

            });

            html += `
                <tr class="table-secondary fw-bold">
                    <td colspan="4" class="text-end">GRAND TOTAL</td>
                    <td class="text-end">${this.rp(grandTotal)}</td>
                </tr>
            `;

            html += `</tbody></table></div></div></div>`;

            $('#ds_sales_area').html(html);
        },

        /* =======================
        CASH
        ======================== */
        renderCash(rows) {

            let html = `
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold text-primary mb-3">B. CASH IN TODAY</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="40" style="text-align: center; vertical-align: middle"></th>
                                    <th style="text-align: center; vertical-align: middle">Cash In</th>
                                    <th style="text-align: center; vertical-align: middle">Customer</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            if (!rows || rows.length === 0) {

                html += `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Tidak ada data Cash In hari ini
                        </td>
                    </tr>
                `;

                html += `</tbody></table></div></div></div>`;
                $('#ds_cash_area').html(html);
                return;
            }

            let grouped = {};
            rows.forEach(r => {
                if (!grouped[r.CASH_IN]) grouped[r.CASH_IN] = [];
                grouped[r.CASH_IN].push(r);
            });

            let grandTotal = 0;

            Object.keys(grouped).forEach(key => {

                let header = grouped[key][0];
                let subtotal = parseFloat(header.AMOUNT);
                grandTotal += subtotal;

                html += `
                    <tr class="table-success">
                        <td class="text-center" style="vertical-align: middle">
                            <button class="btn btn-sm btn-light ds-toggle" data-target="cash-${key}">+</button>
                        </td>
                        <td style="text-align: center; vertical-align: middle"><b>#${header.CASH_IN}</b></td>
                        <td style="text-align: center; vertical-align: middle">${header.CUSTOMER_NAME}<br> <b>${header.CUSTOMER}</b></td>
                        <td class="text-end fw-bold" style="vertical-align: middle">${this.rp(subtotal)}</td>
                    </tr>
                `;

                grouped[key].forEach(d => {
                    html += `
                        <tr class="d-none detail-row cash-${key}">
                            <td></td>
                            <td colspan="2"><b>Invoice: #${d.SALES}</b></td>
                            <td class="text-end"><b>${this.rp(d.AMOUNT_OFFSET)}</b></td>
                        </tr>
                    `;
                });

            });

            html += `
                <tr class="table-secondary fw-bold">
                    <td colspan="3" class="text-end">GRAND TOTAL</td>
                    <td class="text-end">${this.rp(grandTotal)}</td>
                </tr>
            `;

            html += `</tbody></table></div></div></div>`;

            $('#ds_cash_area').html(html);
        },

        /* =======================
        COST
        ======================== */
        renderCost(rows) {

            let html = `
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold text-primary mb-3">C. COST TODAY</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="40"></th>
                                    <th style="text-align: center; vertical-align: middle">Cost</th>
                                    <th style="text-align: center; vertical-align: middle">Pembayaran</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            if (!rows || rows.length === 0) {

                html += `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Tidak ada data Cost hari ini
                        </td>
                    </tr>
                `;

                html += `</tbody></table></div></div></div>`;
                $('#ds_cost_area').html(html);
                return;
            }

            let grouped = {};
            rows.forEach(r => {
                if (!grouped[r.COST]) grouped[r.COST] = [];
                grouped[r.COST].push(r);
            });

            let grandTotal = 0;

            Object.keys(grouped).forEach(key => {

                let header = grouped[key][0];
                let subtotal = 0;

                grouped[key].forEach(d => {
                    subtotal += parseFloat(d.TOTAL);
                });

                grandTotal += subtotal;

                html += `
                    <tr class="table-danger">
                        <td class="text-center" style="vertical-align: middle">
                            <button class="btn btn-sm btn-light ds-toggle" data-target="cost-${key}">+</button>
                        </td>
                        <td style="text-align: center; vertical-align: middle"><b>#${header.COST}</b></td>
                        <td style="text-align: center; vertical-align: middle">${header.PEMBAYARAN}</td>
                        <td class="text-end fw-bold" style="vertical-align: middle">${this.rp(subtotal)}</td>
                    </tr>
                `;

                grouped[key].forEach(d => {
                    html += `
                        <tr class="d-none detail-row cost-${key}">
                            <td></td>
                            <td colspan="2" style="vertical-align: middle"><b>${d.TIPE_COST} - ${d.COST_NAME}</b> <i>(${d.REMARK})</i></td>
                            <td class="text-end" style="vertical-align: middle"><b>${this.rp(d.TOTAL)}</b></td>
                        </tr>
                    `;
                });

            });

            html += `
                <tr class="table-secondary fw-bold">
                    <td colspan="3" class="text-end">GRAND TOTAL</td>
                    <td class="text-end">${this.rp(grandTotal)}</td>
                </tr>
            `;

            html += `</tbody></table></div></div></div>`;

            $('#ds_cost_area').html(html);
        },

        rp(x) {
            return 'Rp ' + parseFloat(x || 0).toLocaleString('id-ID');
        }

    };

    $(document).on('click', '.ds-toggle', function() {

        const target = $(this).data('target');
        const rows = $('.' + target);

        if (rows.hasClass('d-none')) {
            rows.removeClass('d-none');
            $(this).text('-');
        } else {
            rows.addClass('d-none');
            $(this).text('+');
        }

    });
</script>