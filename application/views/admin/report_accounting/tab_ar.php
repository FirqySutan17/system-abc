<?php $userPlant = $this->session->userdata('plant'); ?>

<div class="row mb-3">

    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="rp_filter_plant_ar" class="form-control" <?= ($this->session->userdata('role_id') != 1) ? 'disabled' : '' ?>>
            <?php foreach($plants as $p): ?>
                <option value="<?= $p->CODE ?>" <?= ($p->CODE==$userPlant)?'selected':'' ?>>
                    <?= $p->CODE_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Customer</label>
        <select id="rp_filter_customer_ar" class="form-control">
            <option value="">-- ALL CUSTOMER --</option>
            <?php foreach ($customers as $c): ?>
                <option value="<?= $c->CUST ?>">
                    <?= $c->CUST ?> - <?= $c->FULL_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Status</label>
        <select id="rp_filter_status_ar" class="form-control">
            <option value="ALL">ALL</option>
            <option value="PAID">PAID</option>
            <option value="OUTSTANDING">OUTSTANDING</option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="rp_date_from_ar" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="rp_date_to_ar" class="form-control">
    </div>

    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="rp_btnFilter_ar">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li>
                    <a class="dropdown-item" href="#" id="rp_exportExcel_ar">
                        Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="rp_exportPDF_ar">
                        Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table class="table table-bordered" id="arTable">
        <thead>
            <tr>
                <th class="text-center" style="width:40px;">+</th>
                <th class="text-center">PLANT</th>
                <th class="text-center">DATE</th>
                <th class="text-center">SALES</th>
                <th>CUSTOMER</th>
                <th class="text-end">INVOICE</th>
                <th class="text-end">PAID</th>
                <th class="text-end">OUTSTANDING</th>
                <th class="text-center" style="width:110px;">STATUS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="5" class="text-end">GRAND TOTAL</td>
                <td class="text-end" id="rp_gt_invoice_ar">0</td>
                <td class="text-end" id="rp_gt_paid_ar">0</td>
                <td class="text-end" id="rp_gt_outstanding_ar">0</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="rp_info_ar"></div>
    <div id="rp_pagination_ar"></div>
</div>

<script>
    window.ARReport = {

        loaded: false,
        page: 1,

        init() {
            if (this.loaded) return;
            this.loaded = true;

            this.initFilter();
            this.bindEvent();
            this.load();
        },

        initFilter() {
            $('#rp_filter_plant_ar, #rp_filter_customer_ar, #rp_filter_status_ar')
                .select2({ width: '100%' });

            const today = new Date();
            $('#rp_date_to_ar').val(today.toISOString().slice(0,10));

            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            $('#rp_date_from_ar').val(firstDay.toISOString().slice(0,10));
        },

        bindEvent() {

            $('#rp_btnFilter_ar').on('click', () => {
                this.page = 1;
                this.load();
            });

            $('#rp_filter_plant_ar, #rp_filter_customer_ar, #rp_filter_status_ar, #rp_date_from_ar, #rp_date_to_ar')
                .on('change', () => {
                    this.page = 1;
                    this.load();
                });

            $('#rp_exportExcel_ar').on('click', e => {
                e.preventDefault();
                window.open('<?= base_url("report-accounting/export_excel_ar"); ?>?' + $.param(this.params()));
            });

            $('#rp_exportPDF_ar').on('click', e => {
                e.preventDefault();
                window.open('<?= base_url("report-accounting/export_pdf_ar"); ?>?' + $.param(this.params()));
            });
        },

        params() {
            return {
                page      : this.page,
                limit     : 10,
                plant     : $('#rp_filter_plant_ar').val(),
                customer  : $('#rp_filter_customer_ar').val(),
                status    : $('#rp_filter_status_ar').val(),
                date_from : $('#rp_date_from_ar').val(),
                date_to   : $('#rp_date_to_ar').val()
            };
        },

        load(page = null) {

            if (page !== null) this.page = page;

            $.get('<?= base_url("report-accounting/load_ar"); ?>', this.params(), resp => {

                this.render(resp.rows || []);

                $('#rp_gt_invoice_ar').text(this.rupiah(resp.grand?.invoice || 0));
                $('#rp_gt_paid_ar').text(this.rupiah(resp.grand?.paid || 0));
                $('#rp_gt_outstanding_ar').text(this.rupiah(resp.grand?.outstanding || 0));

                $('#rp_pagination_ar').html(resp.pagination || '');
                $('#rp_info_ar').text(`Total data : ${resp.total || 0}`);

            }, 'json');
        },

        render(rows) {

            const tbody = $('#arTable tbody').empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="8" class="text-center">No data found</td></tr>');
                return;
            }

            rows.forEach(r => {

                const status = (parseFloat(r.OUTSTANDING) === 0)
                    ? '<span class="badge bg-success">PAID</span>'
                    : '<span class="badge bg-danger">OUTSTANDING</span>';

                tbody.append(`
                    <tr class="ar-row align-middle" data-sales="${r.SALES}" data-plant="${r.PLANT}">
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary toggle-detail text-center">
                                <i class="fa fa-chevron-right"></i> +
                            </button>
                        </td>
                        <td class="text-center">${r.PLANT_NAME ?? r.PLANT}</td>
                        <td class="text-center">${this.date(r.SALES_DATE)}</td>
                        <td class="text-center fw-bold">${r.SALES}</td>
                        <td>${r.CUSTOMER_NAME ?? r.CUSTOMER}</td>
                        <td class="text-end">${this.rupiah(r.INVOICE_AMOUNT)}</td>
                        <td class="text-end text-success">${this.rupiah(r.TOTAL_PAID)}</td>
                        <td class="text-end fw-bold text-danger">${this.rupiah(r.OUTSTANDING)}</td>
                        <td class="text-center">${status}</td>
                    </tr>

                    <tr class="ar-detail-row d-none bg-light">
                        <td colspan="9" class="p-3">
                            <div class="detail-container"></div>
                        </td>
                    </tr>
                `);
            });
        },

        date(d) {
            return d ? new Date(d).toLocaleDateString('id-ID') : '-';
        },

        rupiah(x) {
            return parseFloat(x || 0).toLocaleString('id-ID');
        }
    };

    $(document).on('click', '#rp_pagination_ar a', function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if (page) ARReport.load(page);
    });

    $(document).on('click', '.toggle-detail', function() {

        const btn = $(this);
        const icon = btn.find('i');
        const row = btn.closest('tr');
        const detailRow = row.next('.ar-detail-row');

        if (!detailRow.hasClass('d-none')) {
            detailRow.addClass('d-none');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            return;
        }

        const sales = row.data('sales');
        const plant = row.data('plant');

        $.get('<?= base_url("report-accounting/load_ar_detail"); ?>', {
            sales: sales,
            plant: plant
        }, function(resp) {

            let html = `
                <div class="border rounded p-2 bg-white">

                    <!-- ITEM DETAIL -->
                    <h6 class="fw-bold mb-2">SALES DETAIL</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Type</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            let invoiceTotal = 0;

            if (!resp.items || resp.items.length === 0) {
                html += `<tr><td colspan="4" class="text-center">No Item</td></tr>`;
            } else {

                resp.items.forEach(i => {

                    invoiceTotal += parseFloat(i.DETAIL_AMOUNT || 0);

                    html += `
                        <tr>
                            <td><b>${i.ITEM}</b> - ${i.ITEM_NAME}</td>
                            <td class="text-center">${i.DISPLAY_TYPE}</td>
                            <td class="text-end">${parseFloat(i.DISPLAY_QTY).toLocaleString('id-ID')}</td>
                            <td class="text-end">${parseFloat(i.DETAIL_AMOUNT).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                });
            }

            html += `
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">TOTAL INVOICE</td>
                                <td class="text-end">${invoiceTotal.toLocaleString('id-ID')}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- PAYMENT HISTORY -->
                    <h6 class="fw-bold mb-2">PAYMENT HISTORY</h6>
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Date</th>
                                <th class="text-center">Cash In</th>
                                <th class="text-center">Pembayaran</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            let paidTotal = 0;

            if (!resp.payments || resp.payments.length === 0) {
                html += `<tr><td colspan="4" class="text-center">No Payment</td></tr>`;
            } else {

                resp.payments.forEach(p => {

                    paidTotal += parseFloat(p.AMOUNT_OFFSET || 0);

                    html += `
                        <tr>
                            <td class="text-center">
                                ${new Date(p.CASHIN_DATE).toLocaleDateString('id-ID')}
                            </td>
                            <td class="text-center">${p.CASH_IN}</td>
                            <td class="text-center">${p.PEMBAYARAN ?? '-'}</td>
                            <td class="text-end">
                                ${parseFloat(p.AMOUNT_OFFSET).toLocaleString('id-ID')}
                            </td>
                        </tr>
                    `;
                });
            }

            html += `
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">TOTAL PAYMENT</td>
                                <td class="text-end">${paidTotal.toLocaleString('id-ID')}</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            `;

            detailRow.find('.detail-container').html(html);
            detailRow.removeClass('d-none');
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');

        }, 'json');
    });
</script>