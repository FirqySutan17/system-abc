<?php $userPlant = $this->session->userdata('plant'); ?>
<!-- <h5 class="card-title fw-semibold mb-4">REPORT PO - INVENTORY</h5> -->

<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="rc_lb_filter_plant" class="form-control" <?= ($this->session->userdata('role_id') != 1) ? 'disabled' : '' ?>>
            <?php foreach($plants as $p): ?>
                <option value="<?= $p->CODE ?>" <?= ($p->CODE==$userPlant)?'selected':'' ?>>
                    <?= $p->CODE_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- SUPPLIER -->
    <div class="col-md-2">
        <label class="form-label">Supplier</label>
        <select id="rc_lb_filter_supplier" class="form-control">
            <option value="">-- ALL SUPPLIER --</option>
            <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s->CUST ?>"><?= $s->CUST ?> - <?= $s->FULL_NAME ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- NO RECEIVE / DO -->
    <div class="col-md-2">
        <label class="form-label">No Receive / DO</label>
        <input type="text" id="rc_lb_filter_receive" class="form-control" placeholder="Search Receive or DO">
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="rc_lb_date_from" class="form-control">
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="rc_lb_date_to" class="form-control">
    </div>

    <!-- BUTTON FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="rc_lb_btnFilter">
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
                    <a class="dropdown-item" href="#" id="rc_lb_exportExcel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="rc_lb_exportPDF">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-bordered" id="receiveLBTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-center">DATE</th>
                <th class="text-center">NO RECEIVE</th>
                <th class="text-center">PEMBAYARAN</th>
                <th class="text-center">JENIS PAY</th>
                <th class="text-center">SLIP NO</th>
                <th class="text-center">DO</th>
                <th class="text-center">SUPPLIER</th>
                <th class="text-center">DRIVER</th>
                <th class="text-center">NO CAR</th>
                <th class="text-center">QTY</th>
                <th class="text-center">WEIGHT</th>
                <th class="text-center">AVG BW</th>
                <th class="text-center">PRICE</th>
                <th class="text-center">AMOUNT</th>
            </tr>
        </thead>
        <tbody></tbody>

        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="11" class="text-center detail-row" style="vertical-align: middle">GRAND TOTAL</td>
                <td class="text-end detail-row" style="vertical-align: middle" id="lb_total_weight">0.00</td>
                <td class="text-end detail-row" style="vertical-align: middle"></td>
                <td class="text-end detail-row" style="vertical-align: middle"></td>
                <td class="text-end detail-row" style="vertical-align: middle" id="lb_total_amount">0</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="info"></div>
    <div id="pagination_lb"></div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
    #receiveLBTable {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
    }

    #receiveLBTable th,
    #receiveLBTable td {
        white-space: nowrap;
        word-wrap: break-word;
    }
</style>

<script>
    window.ReceiveLBReport = {

        loaded: false,
        timer: null,

        init() {
            if (this.loaded) return;
            this.loaded = true;

            this.initFilter();
            this.bindEvent();
            this.load(1);
        },

        initFilter() {
            $('#rc_lb_filter_plant, #rc_lb_filter_supplier').select2({
                width: '100%',
                allowClear: true
            });

            const today = new Date();
            $('#rc_lb_date_to').val(today.toISOString().slice(0,10));

            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            $('#rc_lb_date_from').val(firstDay.toISOString().slice(0,10));
        },

        bindEvent() {

            $('#rc_lb_btnFilter').on('click', () => this.load(1));

            $('#rc_lb_filter_receive').on('keyup', () => {
                clearTimeout(this.timer);
                this.timer = setTimeout(() => this.load(1), 300);
            });

            $('#rc_lb_filter_plant, #rc_lb_filter_supplier, #rc_lb_date_from, #rc_lb_date_to')
                .on('change', () => this.load(1));

            // EXPORT EXCEL
            $('#rc_lb_exportExcel').on('click', e => {
                e.preventDefault();
                window.open(this.buildExportUrl('export_excel_receive_lb'), '_blank');
            });

            // EXPORT PDF
            $('#rc_lb_exportPDF').on('click', e => {
                e.preventDefault();
                window.open(this.buildExportUrl('export_pdf_receive_lb'), '_blank');
            });

            // PAGINATION
            $(document).on('click', '#pagination_lb a', e => {
                e.preventDefault();
                const page = $(e.currentTarget).data('page');
                if (page) this.load(page);
            });
        },

        buildExportUrl(endpoint) {
            const params = $.param({
                plant     : $('#rc_lb_filter_plant').val(),
                supplier  : $('#rc_lb_filter_supplier').val(),
                receive   : $('#rc_lb_filter_receive').val(),
                date_from : $('#rc_lb_date_from').val(),
                date_to   : $('#rc_lb_date_to').val()
            });

            return `<?= base_url("report-inventory/"); ?>${endpoint}?${params}`;
        },

        load(page = 1) {
            const params = {
                page      : page,
                limit     : 10,
                plant     : $('#rc_lb_filter_plant').val(),
                supplier  : $('#rc_lb_filter_supplier').val(),
                receive   : $('#rc_lb_filter_receive').val(),
                date_from : $('#rc_lb_date_from').val(),
                date_to   : $('#rc_lb_date_to').val()
            };

            $.getJSON('<?= base_url("report-inventory/load_receive_lb"); ?>', params, resp => {
                this.render(resp.rows || []);
                this.renderGrand(resp.grand || {});
                $('#pagination_lb').html(resp.pagination || '');
                $('#info').text(`Total data : ${resp.total || 0}`);
            });
        },

        render(rows) {
            const tbody = $('#receiveLBTable tbody').empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="15" class="text-center">No data found</td></tr>');
                $('#lb_total_weight').text('0.00');
                $('#lb_total_amount').text('0');
                return;
            }

            const grouped = {};
            rows.forEach(r => {
                const key = r.RECEIVE + '|' + r.PLANT;
                grouped[key] = grouped[key] || [];
                grouped[key].push(r);
            });

            Object.values(grouped).forEach(group => {
                const rowspan = group.length;

                group.forEach((r,i) => {
                    let tr = '<tr>';

                    if (i === 0) {
                        tr += `
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.PLANT_NAME}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${this.date(r.RECEIVE_DATE)}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center"><b>${r.RECEIVE}</b></td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.PEMBAYARAN}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.JENIS_PAY}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.SLIP_NO || ''}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.DO}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.SUPPLIER_NAME}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.DRIVER}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.NO_CAR}</td>
                        `;
                    }

                    tr += `
                        <td class="detail-row text-end">${this.decimal(r.QTY)}</td>
                        <td class="detail-row text-end">${this.decimal(r.WEIGHT)}</td>
                        <td class="detail-row text-end">${this.decimal(r.AVG_BW)}</td>
                        <td class="detail-row text-end">${this.rupiah(r.PRICE)}</td>
                        <td class="detail-row text-end">${this.rupiah(r.AMOUNT)}</td>
                    </tr>`;

                    tbody.append(tr);
                });
            });
        },

        renderGrand(grand) {
            $('#lb_total_weight').text(
                this.decimal(grand.total_berat || 0)
            );
            $('#lb_total_amount').text(
                this.rupiah(grand.total_amount || 0)
            );
        },

        date(d) {
            return d ? new Date(d).toLocaleDateString('id-ID') : '-';
        },

        rupiah(x) {
            return parseFloat(x||0).toLocaleString('id-ID');
        },

        decimal(x) {
            return parseFloat(x||0).toFixed(2);
        }
    };

    $(document).ready(() => ReceiveLBReport.init());
</script>


