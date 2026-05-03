<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
?>

<div class="row mb-3">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="rc_filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if(in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- CUSTOMER -->
    <div class="col-md-2">
        <label class="form-label">Customer</label>
        <select id="rc_filter_customer" class="form-control">
            <option value="">-- ALL CUSTOMER --</option>
            <?php foreach ($customers as $c): ?>
                <option value="<?= $c->CUST ?>">
                    <?= $c->CUST ?> - <?= $c->FULL_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- CASH IN -->
    <div class="col-md-2">
        <label class="form-label">No Cash In</label>
        <input type="text" id="rc_filter_cash_in" class="form-control" placeholder="Search Cash In">
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="rc_date_from" class="form-control">
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="rc_date_to" class="form-control">
    </div>

    <!-- FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="rc_btnFilter">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li>
                    <a class="dropdown-item" href="#" id="rc_exportExcel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="rc_exportPDF">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table class="table table-bordered" id="cashInTable">
        <thead>
            <tr>
                <th class="text-center">SALES</th>
                <th class="text-center">PLANT</th>
                <th class="text-center">DATE</th>
                <th class="text-center">CASH IN</th>
                <th class="text-center">CUSTOMER</th>
                <th class="text-center">PEMBAYARAN</th>
                <th class="text-center">NO REK</th>
                <th class="text-center">SLIP NO</th>
                <th class="text-center">INV</th>
                <th class="text-center">OFFSET</th>
            </tr>
        </thead>
        <tbody></tbody>
        <!-- <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="8" class="text-end">GRAND TOTAL</td>
                <td class="text-end" id="rc_gt_invoice">0</td>
                <td class="text-end" id="rc_gt_offset">0</td>
            </tr>
        </tfoot> -->
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="rc_info"></div>
    <div id="rc_pagination"></div>
</div>

<style>
.detail-row {
    border: 2px solid #efefef !important;
    vertical-align: middle !important;
}
</style>

<script>
    window.CashInReport = {

        loaded: false,

        state: {
            page: 1,
            limit: 10,
            plant: '',
            customer: '',
            cash_in: '',
            date_from: '',
            date_to: ''
        },

        init() {
            if (this.loaded) return;
            this.loaded = true;
            this.initFilter();
            this.bindEvent();
            this.load();
        },

        initFilter() {

            $('#rc_filter_plant, #rc_filter_customer')
                .select2({ width: '100%' });

            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth()+1).padStart(2,'0');
            const dd = String(today.getDate()).padStart(2,'0');

            const first = new Date(today.getFullYear(), today.getMonth(), 1);

            this.state.date_from =
                `${first.getFullYear()}-${String(first.getMonth()+1).padStart(2,'0')}-${String(first.getDate()).padStart(2,'0')}`;

            this.state.date_to =
                `${yyyy}-${mm}-${dd}`;

            $('#rc_date_from').val(this.state.date_from);
            $('#rc_date_to').val(this.state.date_to);
        },

        bindEvent() {

            let timer = null;

            $('#rc_btnFilter').on('click', () => {
                this.state.page = 1;
                this.updateState();
                this.load();
            });

            $('#rc_filter_cash_in').on('keyup', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    this.state.page = 1;
                    this.updateState();
                    this.load();
                }, 400);
            });

            $('#rc_filter_plant, #rc_filter_customer, #rc_date_from, #rc_date_to')
                .on('change', () => {
                    this.state.page = 1;
                    this.updateState();
                    this.load();
                });

            $('#rc_exportExcel').on('click', e => {
                e.preventDefault();
                this.updateState();
                window.open(
                    '<?= base_url("report-accounting/export_excel_cash_in"); ?>?'
                    + $.param(this.exportParams()),
                    '_blank'
                );
            });

            $('#rc_exportPDF').on('click', e => {
                e.preventDefault();
                this.updateState();
                window.open(
                    '<?= base_url("report-accounting/export_pdf_cash_in"); ?>?'
                    + $.param(this.exportParams()),
                    '_blank'
                );
            });
        },

        updateState() {
            this.state.plant     = $('#rc_filter_plant').val();
            this.state.customer  = $('#rc_filter_customer').val();
            this.state.cash_in   = $('#rc_filter_cash_in').val();
            this.state.date_from = $('#rc_date_from').val();
            this.state.date_to   = $('#rc_date_to').val();
        },

        params() {
            return {
                page      : this.state.page,
                limit     : this.state.limit,
                plant     : this.state.plant,
                customer  : this.state.customer,
                cash_in   : this.state.cash_in,
                date_from : this.state.date_from,
                date_to   : this.state.date_to
            };
        },

        exportParams() {
            return {
                plant     : this.state.plant,
                customer  : this.state.customer,
                cash_in   : this.state.cash_in,
                date_from : this.state.date_from,
                date_to   : this.state.date_to
            };
        },

        load(page = null) {

            if (page !== null) this.state.page = page;

            this.updateState();

            $.get(
                '<?= base_url("report-accounting/load_cash_in"); ?>',
                this.params(),
                resp => {

                    this.render(resp.rows || []);

                    $('#rc_pagination').html(resp.pagination || '');
                    $('#rc_info').text(`Total data : ${resp.total || 0}`);

                },
                'json'
            );
        },

        render(rows) {

            const tbody = $('#cashInTable tbody').empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="10" class="text-center">No data found</td></tr>');
                return;
            }

            const grouped = {};

            rows.forEach(r => {
                const key = `${r.SALES}|${r.PLANT}`;
                grouped[key] = grouped[key] || [];
                grouped[key].push(r);
            });

            Object.values(grouped).forEach(group => {

                const rowspan = group.length;

                group.forEach((r, i) => {

                    let tr = '<tr>';

                    if (i === 0) {
                        tr += `
                            <td rowspan="${rowspan}" class="text-center detail-row"><b>#${r.SALES}</b></td>
                            <td rowspan="${rowspan}" class="text-center detail-row"><b>${r.PLANT_NAME}</b></td>
                        `;
                    }

                    tr += `
                        <td class="text-center detail-row">${this.date(r.CASHIN_DATE)}</td>
                        <td class="text-center detail-row"><b>#${r.CASH_IN}</b></td>
                        <td class="detail-row text-center">${r.CUSTOMER_NAME}<br><b>${r.CUSTOMER}</b></td>
                        <td class="text-center detail-row">${r.PEMBAYARAN || ''}</td>
                        <td class="text-center detail-row">${r.NO_REK_NAME || ''}</td>
                        <td class="text-center detail-row">#${r.SLIP_NO}</td>
                        <td class="text-end detail-row">${this.decimal(r.AMOUNT_INVOICE)}</td>
                        <td class="text-end detail-row">${this.decimal(r.AMOUNT_OFFSET)}</td>
                    </tr>`;

                    tbody.append(tr);
                });
            });
        },

        date(d) {
            return d ? new Date(d).toLocaleDateString('id-ID') : '-';
        },

        decimal(x) {
            return parseFloat(x||0).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    $(document).on('click', '#rc_pagination a', function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if (page) CashInReport.load(page);
    });

    $(document).ready(() => CashInReport.init());
</script>