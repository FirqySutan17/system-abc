<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
?>

<div class="row mb-3">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="rp_filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if(in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- PAYMENT TYPE -->
    <div class="col-md-2">
        <label class="form-label">Payment Type</label>
        <select id="rp_filter_type" class="form-control">
            <option value="">-- ALL TYPE --</option>
            <option value="RECEIVE">RECEIVE</option>
            <option value="RECEIVE_LB">RECEIVE_LB</option>
        </select>
    </div>

    <div class="col-md-2">
            <label class="form-label">Supplier</label>
            <select id="rp_filter_supplier" class="form-control">
                <option value="">-- ALL SUPPLIER --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s->CUST ?>">
                        <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

    <div class="col-md-2">
        <label class="form-label">No Payment</label>
        <input type="text" id="rp_filter_payment" class="form-control" placeholder="Search Payment">
    </div>

    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="rp_date_from" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="rp_date_to" class="form-control">
    </div>

    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="rp_btnFilter">
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
                    <a class="dropdown-item" href="#" id="rp_exportExcel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="rp_exportPDF">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table class="table table-bordered" id="paymentTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-center">DATE</th>
                <th class="text-center">NO PAYMENT</th>
                <th class="text-center">PEMBAYARAN</th>
                <th class="text-center">SUPPLIER</th>
                <th class="text-center">MATERIAL</th>
                <th class="text-center">JUMLAH</th>
                <th class="text-center">BERAT</th>
                <th class="text-center">HARGA</th>
                <th class="text-center">TOTAL</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="9" class="text-end detail-row">GRAND TOTAL</td>
                <td class="text-end detail-row" id="rp_gt_total">0</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="info"></div>
    <div id="rp_pagination"></div>
</div>

<script>
    window.PaymentReport = {

        loaded: false,

        state: {
            page: 1,
            limit: 10,
            plant: '',
            supplier: '',
            payment: '',
            payment_type: '',
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

            $('#rp_filter_plant, #rp_filter_supplier, #rp_filter_type')
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

            $('#rp_date_from').val(this.state.date_from);
            $('#rp_date_to').val(this.state.date_to);
        },

        bindEvent() {

            let timer = null;

            $('#rp_btnFilter').on('click', () => {
                this.state.page = 1;
                this.updateState();
                this.load();
            });

            $('#rp_filter_payment').on('keyup', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    this.state.page = 1;
                    this.updateState();
                    this.load();
                }, 400);
            });

            $('#rp_filter_plant, #rp_filter_supplier, #rp_filter_type, #rp_date_from, #rp_date_to')
                .on('change', () => {
                    this.state.page = 1;
                    this.updateState();
                    this.load();
                });

            $('#rp_exportExcel').on('click', e => {
                e.preventDefault();
                this.updateState();
                window.open(
                    '<?= base_url("report-accounting/export_excel_payment"); ?>?'
                    + $.param(this.exportParams()),
                    '_blank'
                );
            });

            $('#rp_exportPDF').on('click', e => {
                e.preventDefault();
                this.updateState();
                window.open(
                    '<?= base_url("report-accounting/export_pdf_payment"); ?>?'
                    + $.param(this.exportParams()),
                    '_blank'
                );
            });
        },

        updateState() {
            this.state.plant        = $('#rp_filter_plant').val();
            this.state.supplier     = $('#rp_filter_supplier').val();
            this.state.payment      = $('#rp_filter_payment').val();
            this.state.payment_type = $('#rp_filter_type').val();
            this.state.date_from    = $('#rp_date_from').val();
            this.state.date_to      = $('#rp_date_to').val();
        },

        params() {
            return {
                page        : this.state.page,
                limit       : this.state.limit,
                plant       : this.state.plant,
                supplier    : this.state.supplier,
                payment     : this.state.payment,
                payment_type: this.state.payment_type,
                date_from   : this.state.date_from,
                date_to     : this.state.date_to
            };
        },

        exportParams() {
            return {
                plant       : this.state.plant,
                supplier    : this.state.supplier,
                payment     : this.state.payment,
                payment_type: this.state.payment_type,
                date_from   : this.state.date_from,
                date_to     : this.state.date_to
            };
        },

        load(page = null) {

            if (page !== null) this.state.page = page;

            this.updateState();

            $.get(
                '<?= base_url("report-accounting/load_payment"); ?>',
                this.params(),
                resp => {

                    this.render(resp.rows || []);

                    $('#rp_gt_total').text(
                        this.rupiah(resp.grand?.total || 0)
                    );

                    $('#rp_pagination').html(resp.pagination || '');
                    $('#info').text(`Total data : ${resp.total || 0}`);
                },
                'json'
            );
        },

        render(rows) {

            const tbody = $('#paymentTable tbody').empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="10" class="text-center">No data found</td></tr>');
                return;
            }

            const grouped = {};

            rows.forEach(r => {
                const key = r.PAYMENT + '|' + r.PLANT;
                grouped[key] = grouped[key] || [];
                grouped[key].push(r);
            });

            Object.values(grouped).forEach(group => {

                const rowspan = group.length;

                group.forEach((r, i) => {

                    let tr = '<tr>';

                    if (i === 0) {
                        tr += `
                            <td rowspan="${rowspan}" class="detail-row text-center"><b>${r.PLANT_NAME}</b></td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${this.date(r.PAYMENT_DATE)}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center"><b>${r.PAYMENT}</b></td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.PEMBAYARAN}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.SUPPLIER_NAME}<br><b>${r.SUPPLIER}</b></td>
                        `;
                    }

                    tr += `
                        <td class="detail-row text-center">${r.MATERIAL_NAME}<br><b>${r.MATERIAL}</b></td>
                        <td class="detail-row text-end">${this.decimal(r.JUMLAH)}</td>
                        <td class="detail-row text-end">${this.decimal(r.BERAT)}</td>
                        <td class="detail-row text-end">${this.rupiah(r.HARGA)}</td>
                        <td class="detail-row text-end">${this.rupiah(r.DETAIL_TOTAL)}</td>
                    </tr>`;

                    tbody.append(tr);
                });
            });
        },

        date(d) {
            return d ? new Date(d).toLocaleDateString('id-ID') : '-';
        },

        rupiah(x) {
            return parseFloat(x || 0).toLocaleString('id-ID');
        },

        decimal(x) {
            return parseFloat(x || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    $(document).on('click', '#rp_pagination a', function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if (page) PaymentReport.load(page);
    });

    $(document).ready(() => PaymentReport.init());
</script>